<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Buat user terautentikasi untuk keperluan testing
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_projects_routes(): void
    {
        $this->get(route('projects.index'))->assertRedirect(route('login'));
        $this->get(route('projects.create'))->assertRedirect(route('login'));
        $this->post(route('projects.store'), [])->assertRedirect(route('login'));
    }

    public function test_guest_can_view_public_project_case_study(): void
    {
        $project = Project::factory()->create([
            'title' => 'Ocean Residence',
            'slug' => 'ocean-residence',
            'location' => 'Pecatu, Bali',
            'description' => 'A stunning ocean residence case study details.',
            'image' => 'villa.jpg',
            'status' => 'Ongoing',
        ]);

        $response = $this->get(route('public.projects.show', $project->slug));

        $response->assertStatus(200);
        $response->assertSee('Ocean Residence');
        $response->assertSee('Pecatu, Bali');
        $response->assertSee('A stunning ocean residence case study details.');
    }

    public function test_invalid_slug_returns_404(): void
    {
        $response = $this->get('/case-study/non-existent-slug');
        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_view_projects_list(): void
    {
        $project = Project::factory()->create([
            'title' => 'Villa Canggu',
            'slug' => 'villa-canggu',
            'location' => 'Bali',
            'description' => 'A beautiful tropical villa.',
            'image' => 'villa.jpg',
            'status' => 'Ongoing',
        ]);

        $response = $this->actingAs($this->user)->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertSee('Villa Canggu');
        $response->assertSee('Bali');
    }

    public function test_authenticated_user_can_view_create_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('projects.create'));

        $response->assertStatus(200);
        $response->assertSee('Project Name');
    }

    public function test_authenticated_user_can_store_project(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('project.jpg');
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Modern Residence',
            'slug' => 'modern-residence',
            'category_id' => $category->id,
            'location' => 'Jakarta',
            'description' => 'Minimalist modern townhouse.',
            'image' => $image,
            'status' => 'Ongoing',
            'meta_title' => 'Modern Residence SEO',
            'meta_description' => 'Modern Residence description SEO',
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'title' => 'Modern Residence',
            'slug' => 'modern-residence',
            'category_id' => $category->id,
            'location' => 'Jakarta',
        ]);

        // Ambil project yang berhasil dibuat
        $project = Project::first();
        $this->assertNotNull($project->image);
        Storage::disk('public')->assertExists('projects/'.$project->image);
    }

    public function test_authenticated_user_can_view_edit_form(): void
    {
        $project = Project::factory()->create([
            'title' => 'Office Building',
            'slug' => 'office-building',
            'location' => 'Surabaya',
            'description' => 'Commercial high-rise.',
            'image' => 'office.jpg',
            'status' => 'Completed',
        ]);

        $response = $this->actingAs($this->user)->get(route('projects.edit', $project->id));

        $response->assertStatus(200);
        $response->assertSee('Office Building');
    }

    public function test_authenticated_user_can_update_project(): void
    {
        Storage::fake('public');

        $project = Project::factory()->create([
            'title' => 'Old House',
            'slug' => 'old-house',
            'location' => 'Bandung',
            'description' => 'Retro house.',
            'image' => 'old.jpg',
            'status' => 'Ongoing',
        ]);

        $newImage = UploadedFile::fake()->image('new_house.jpg');
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->put(route('projects.update', $project->id), [
            'title' => 'Renovated House',
            'slug' => 'renovated-house',
            'category_id' => $category->id,
            'location' => 'Bandung',
            'description' => 'Modern renovated house.',
            'image' => $newImage,
            'status' => 'Completed',
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Renovated House',
            'slug' => 'renovated-house',
            'category_id' => $category->id,
            'status' => 'Completed',
        ]);

        $project->refresh();
        Storage::disk('public')->assertExists('projects/'.$project->image);
        // Gambar lama old.jpg harus terhapus dari disk
        Storage::disk('public')->assertMissing('projects/old.jpg');
    }

    public function test_authenticated_user_can_delete_project(): void
    {
        Storage::fake('public');

        $project = Project::factory()->create([
            'title' => 'Temporary Project',
            'slug' => 'temporary-project',
            'location' => 'Depok',
            'description' => 'To be deleted.',
            'image' => 'temp.jpg',
            'status' => 'Ongoing',
        ]);

        // Buat file palsu di public storage untuk menguji penghapusan
        Storage::disk('public')->put('projects/temp.jpg', 'fake content');

        $response = $this->actingAs($this->user)->delete(route('projects.destroy', $project->id));

        $response->assertRedirect(route('projects.index'));
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
        Storage::disk('public')->assertExists('projects/temp.jpg');
    }
}
