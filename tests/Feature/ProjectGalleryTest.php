<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectGalleryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_store_project_with_gallery_images(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();
        $galleryImages = [
            UploadedFile::fake()->image('gallery-one.jpg'),
            UploadedFile::fake()->image('gallery-two.png'),
        ];

        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Gallery Project',
            'slug' => 'gallery-project',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'Project with gallery images.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Ongoing',
            'gallery_images' => $galleryImages,
        ]);

        $response->assertRedirect(route('projects.index'));

        $project = Project::where('slug', 'gallery-project')->firstOrFail();
        $this->assertCount(2, $project->images);

        foreach ($project->images as $image) {
            $this->assertStringStartsWith('projects/gallery/', $image->image_path);
            Storage::disk('public')->assertExists($image->image_path);
        }
    }

    public function test_authenticated_user_can_append_gallery_images_when_updating_project(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'image' => 'main.jpg',
        ]);

        $response = $this->actingAs($this->user)->put(route('projects.update', $project), [
            'title' => $project->title,
            'slug' => $project->slug,
            'category_id' => $category->id,
            'location' => $project->location,
            'description' => $project->description,
            'status' => $project->status,
            'gallery_images' => [UploadedFile::fake()->image('new-gallery.jpg')],
        ]);

        $response->assertRedirect(route('projects.index'));

        $project->refresh();
        $this->assertCount(1, $project->images);
        Storage::disk('public')->assertExists($project->images->first()->image_path);
    }

    public function test_gallery_upload_rejects_more_than_ten_total_images_on_update(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'image' => 'main.jpg',
        ]);

        for ($i = 1; $i <= 10; $i++) {
            $path = "projects/gallery/existing-{$i}.jpg";
            Storage::disk('public')->put($path, 'fake image');
            ProjectImage::create([
                'project_id' => $project->id,
                'image_path' => $path,
            ]);
        }

        $response = $this->actingAs($this->user)->from(route('projects.edit', $project))->put(route('projects.update', $project), [
            'title' => $project->title,
            'slug' => $project->slug,
            'category_id' => $category->id,
            'location' => $project->location,
            'description' => $project->description,
            'status' => $project->status,
            'gallery_images' => [UploadedFile::fake()->image('too-many.jpg')],
        ]);

        $response
            ->assertRedirect(route('projects.edit', $project))
            ->assertSessionHasErrors('gallery_images');

        $this->assertCount(10, $project->refresh()->images);
    }

    public function test_public_project_case_study_shows_gallery_carousel_images(): void
    {
        $project = Project::factory()->create([
            'title' => 'Public Gallery Project',
            'slug' => 'public-gallery-project',
            'image' => 'main.jpg',
        ]);

        ProjectImage::create([
            'project_id' => $project->id,
            'image_path' => 'projects/gallery/public-one.jpg',
        ]);

        $response = $this->get(route('public.projects.show', $project->slug));

        $response->assertOk();
        $response->assertSee('id="project-carousel"', false);
        $response->assertSee('storage/projects/gallery/public-one.jpg', false);
        $response->assertSee('carousel-thumb', false);
        $response->assertSee('gallery-lightbox', false);
        $response->assertSee('loading="lazy"', false);
        $response->assertSee('decoding="async"', false);
    }

    public function test_authenticated_user_can_delete_scoped_gallery_image(): void
    {
        Storage::fake('public');

        $project = Project::factory()->create();
        Storage::disk('public')->put('projects/gallery/delete-me.jpg', 'fake image');
        $image = ProjectImage::create([
            'project_id' => $project->id,
            'image_path' => 'projects/gallery/delete-me.jpg',
        ]);

        $response = $this->actingAs($this->user)->delete(route('projects.gallery.delete', [$project, $image->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Gallery image deleted successfully!');
        $this->assertDatabaseMissing('project_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('projects/gallery/delete-me.jpg');
    }

    public function test_force_delete_project_removes_gallery_files(): void
    {
        Storage::fake('public');

        $project = Project::factory()->create([
            'image' => 'main.jpg',
        ]);
        Storage::disk('public')->put('projects/main.jpg', 'fake main image');
        Storage::disk('public')->put('projects/gallery/remove-me.jpg', 'fake gallery image');
        ProjectImage::create([
            'project_id' => $project->id,
            'image_path' => 'projects/gallery/remove-me.jpg',
        ]);

        $this->actingAs($this->user)->delete(route('projects.destroy', $project));
        $response = $this->actingAs($this->user)->delete(route('projects.force-delete', $project->id));

        $response->assertRedirect(route('projects.archive'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('project_images', ['project_id' => $project->id]);
        Storage::disk('public')->assertMissing('projects/main.jpg');
        Storage::disk('public')->assertMissing('projects/gallery/remove-me.jpg');
    }

    public function test_admin_project_detail_shows_gallery_images(): void
    {
        $project = Project::factory()->create([
            'title' => 'Admin Gallery Project',
        ]);

        ProjectImage::create([
            'project_id' => $project->id,
            'image_path' => 'projects/gallery/admin-one.jpg',
        ]);

        $response = $this->actingAs($this->user)->get(route('projects.show', $project));

        $response->assertOk();
        $response->assertSee('Project Gallery');
        $response->assertSee('storage/projects/gallery/admin-one.jpg', false);
        $response->assertSee('Manage Gallery');
    }
}
