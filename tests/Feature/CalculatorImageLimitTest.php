<?php

namespace Tests\Feature;

use App\Models\CalculatorImage;
use App\Models\CalculatorOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Regression untuk BUG-001: batas 10 gambar tidak boleh bisa di-bypass saat Update
 * dengan hanya mengirim sedikit file baru sementara di DB sudah banyak gambar.
 */
class CalculatorImageLimitTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    private function makeOption(int $existingImages = 0): CalculatorOption
    {
        $option = CalculatorOption::create([
            'name' => 'Type 36',
            'price_range' => 'IDR 150jt - 250jt',
            'description' => 'Standard package.',
        ]);

        for ($i = 1; $i <= $existingImages; $i++) {
            CalculatorImage::create([
                'calculator_option_id' => $option->id,
                'image_path' => "calculator/existing-{$i}.jpg",
                'type' => '2d',
            ]);
        }

        return $option;
    }

    public function test_create_uses_one_file_field_for_all_zones(): void
    {
        $response = $this->actingAs($this->user)->get(route('calculator.create'));

        $response->assertOk()
            ->assertSee('name="images[]"', false)
            ->assertDontSee('name="images_2d[]"', false)
            ->assertDontSee('name="images_3d[]"', false)
            ->assertDontSee('name="images_proses[]"', false);
    }

    public function test_store_preserves_zone_for_files_from_one_upload_field(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user)->post(route('calculator.store'), [
            'name' => 'Mixed Package',
            'price_range' => 'IDR 250M',
            'description' => 'Mixed visuals.',
            'images' => [
                UploadedFile::fake()->image('floor-plan.jpg'),
                UploadedFile::fake()->image('render.jpg'),
                UploadedFile::fake()->image('site.jpg'),
            ],
            'image_zones' => ['2d', '3d', 'proses'],
        ]);

        $response->assertRedirect(route('calculator.index'));
        $this->assertSame(
            ['2d', '3d', 'proses'],
            CalculatorImage::query()->orderBy('id')->pluck('type')->all(),
        );
    }

    public function test_store_rejects_scalar_zone_metadata_without_server_error(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user)->from(route('calculator.create'))->post(route('calculator.store'), [
            'name' => 'Malformed Package',
            'price_range' => 'IDR 1',
            'description' => 'Invalid zone payload.',
            'images' => [UploadedFile::fake()->image('plan.jpg')],
            'image_zones' => '2d',
        ]);

        $response->assertRedirect(route('calculator.create'))
            ->assertSessionHasErrors('image_zones');
        $this->assertSame(0, CalculatorImage::count());
    }

    public function test_store_rejects_more_than_ten_new_images_across_zones(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user)->from(route('calculator.create'))->post(route('calculator.store'), [
            'name' => 'Big Package',
            'price_range' => 'IDR 1M',
            'description' => 'Too many images.',
            'images' => array_map(fn ($i) => UploadedFile::fake()->image("image-{$i}.jpg"), range(1, 12)),
            'image_zones' => array_fill(0, 12, '2d'),
        ]);

        $response->assertRedirect(route('calculator.create'))
            ->assertSessionHasErrors('images');

        $this->assertSame(0, CalculatorImage::count());
    }

    public function test_update_rejects_when_existing_plus_new_exceeds_ten(): void
    {
        Storage::fake('public');

        // 8 existing + 4 new = 12 → harus ditolak (skenario persis BUG-001).
        $option = $this->makeOption(existingImages: 8);

        $response = $this->actingAs($this->user)->from(route('calculator.edit', $option))->put(route('calculator.update', $option), [
            'name' => $option->name,
            'price_range' => $option->price_range,
            'description' => $option->description,
            'images' => array_map(fn ($i) => UploadedFile::fake()->image("new-{$i}.jpg"), range(1, 4)),
            'image_zones' => array_fill(0, 4, '2d'),
        ]);

        $response->assertRedirect(route('calculator.edit', $option))
            ->assertSessionHasErrors('images');

        // Tidak ada gambar baru tersimpan; tetap 8.
        $this->assertSame(8, $option->refresh()->images()->count());
    }

    public function test_update_allows_new_images_within_total_limit(): void
    {
        Storage::fake('public');

        // 6 existing + 3 new = 9 → boleh.
        $option = $this->makeOption(existingImages: 6);

        $response = $this->actingAs($this->user)->put(route('calculator.update', $option), [
            'name' => $option->name,
            'price_range' => $option->price_range,
            'description' => $option->description,
            'images' => array_map(fn ($i) => UploadedFile::fake()->image("ok-{$i}.jpg"), range(1, 3)),
            'image_zones' => array_fill(0, 3, '3d'),
        ]);

        $response->assertRedirect(route('calculator.index'));
        $this->assertSame(9, $option->refresh()->images()->count());
    }
}
