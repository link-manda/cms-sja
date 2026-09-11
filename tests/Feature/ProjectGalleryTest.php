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
            ->assertSessionHasErrors(['gallery_images' => 'Gallery may not contain more than 10 photos per project.']);

        $this->assertCount(10, $project->refresh()->images);
    }

    public function test_gallery_upload_rejects_non_image_file_with_jpg_extension(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->from(route('projects.create'))->post(route('projects.store'), [
            'title' => 'Invalid Gallery Project',
            'slug' => 'invalid-gallery-project',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'Project with invalid gallery image.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Ongoing',
            'gallery_images' => [UploadedFile::fake()->create('bad.jpg', 10, 'text/plain')],
        ]);

        $response
            ->assertRedirect(route('projects.create'))
            ->assertSessionHasErrors('gallery_images.0');
    }

    public function test_gallery_upload_rejects_unsupported_extension(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->from(route('projects.create'))->post(route('projects.store'), [
            'title' => 'Unsafe Extension Project',
            'slug' => 'unsafe-extension-project',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'Project with unsafe extension.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Ongoing',
            'gallery_images' => [UploadedFile::fake()->image('shell.php')],
        ]);

        $response
            ->assertRedirect(route('projects.create'))
            ->assertSessionHasErrors(['gallery_images.0' => 'Gallery photo extension must be .jpg, .jpeg, .png, or .webp.']);
    }

    public function test_gallery_upload_rejects_files_larger_than_ten_mb(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->from(route('projects.create'))->post(route('projects.store'), [
            'title' => 'Large Gallery Project',
            'slug' => 'large-gallery-project',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'Project with large gallery image.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Ongoing',
            'gallery_images' => [UploadedFile::fake()->image('large.jpg')->size(10241)],
        ]);

        $response
            ->assertRedirect(route('projects.create'))
            ->assertSessionHasErrors(['gallery_images.0' => 'Each gallery photo may not be greater than 10 MB.']);
    }

    public function test_gallery_upload_has_clear_message_when_php_upload_limit_rejects_file(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();
        $tmpFile = tmpfile();
        $tmpPath = stream_get_meta_data($tmpFile)['uri'];
        $file = new UploadedFile($tmpPath, 'too-large-for-php.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true);

        $response = $this->actingAs($this->user)->from(route('projects.create'))->post(route('projects.store'), [
            'title' => 'PHP Limit Gallery Project',
            'slug' => 'php-limit-gallery-project',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'Project with PHP rejected gallery image.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Ongoing',
            'gallery_images' => [$file],
        ]);

        $response
            ->assertRedirect(route('projects.create'))
            ->assertSessionHasErrors(['gallery_images.0' => 'Gallery photo failed to upload. Each gallery photo may not be greater than 10 MB and the server upload limit must allow 10 MB files.']);
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

    public function test_public_project_case_study_does_not_double_prefix_stored_main_image_path(): void
    {
        $project = Project::factory()->create([
            'slug' => 'legacy-main-image-project',
            'image' => 'projects/legacy-main.jpg',
        ]);

        $response = $this->get(route('public.projects.show', $project->slug));

        $response->assertOk();
        $response->assertSee('storage/projects/legacy-main.jpg', false);
        $response->assertDontSee('storage/projects/projects/legacy-main.jpg', false);
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

    public function test_authenticated_user_can_store_project_with_youtube_videos(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Mixed Media Project',
            'slug' => 'mixed-media-project',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'Project with mixed media gallery.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Ongoing',
            'gallery_images' => [UploadedFile::fake()->image('photo-one.jpg')],
            'gallery_videos' => [
                'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'https://youtu.be/dQw4w9WgXcQ',
                '', // empty row should be filtered out
            ],
        ]);

        $response->assertRedirect(route('projects.index'));

        $project = Project::where('slug', 'mixed-media-project')->firstOrFail();
        $this->assertCount(3, $project->images);

        $this->assertDatabaseHas('project_images', [
            'project_id' => $project->id,
            'type' => 'image',
        ]);

        $this->assertDatabaseHas('project_images', [
            'project_id' => $project->id,
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $this->assertDatabaseHas('project_images', [
            'project_id' => $project->id,
            'type' => 'video',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
        ]);
    }

    public function test_authenticated_user_can_append_youtube_videos_when_updating_project(): void
    {
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
            'gallery_videos' => ['https://www.youtube.com/shorts/dQw4w9WgXcQ'],
        ]);

        $response->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('project_images', [
            'project_id' => $project->id,
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/shorts/dQw4w9WgXcQ',
        ]);
    }

    public function test_gallery_upload_rejects_more_than_ten_combined_media_on_store(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $images = [];
        for ($i = 1; $i <= 6; $i++) {
            $images[] = UploadedFile::fake()->image("p{$i}.jpg");
        }

        $videos = [
            'https://www.youtube.com/watch?v=dQw4w9WgXc1',
            'https://www.youtube.com/watch?v=dQw4w9WgXc2',
            'https://www.youtube.com/watch?v=dQw4w9WgXc3',
            'https://www.youtube.com/watch?v=dQw4w9WgXc4',
            'https://www.youtube.com/watch?v=dQw4w9WgXc5',
        ];

        $response = $this->actingAs($this->user)->from(route('projects.create'))->post(route('projects.store'), [
            'title' => 'Too Many Media Store',
            'slug' => 'too-many-media-store',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'Project with too many media.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Ongoing',
            'gallery_images' => $images,
            'gallery_videos' => $videos,
        ]);

        $response->assertRedirect(route('projects.create'));
        $response->assertSessionHasErrors(['gallery_images' => 'Gallery may not contain more than 10 total items (photos and videos combined).']);
    }

    public function test_gallery_upload_rejects_more_than_ten_combined_media_on_update(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'image' => 'main.jpg',
        ]);

        // Existing 8 media
        for ($i = 1; $i <= 8; $i++) {
            ProjectImage::create([
                'project_id' => $project->id,
                'type' => 'video',
                'video_url' => "https://www.youtube.com/watch?v=dQw4w9WgX{$i}",
            ]);
        }

        // Add 2 images and 1 video (total new = 3, existing = 8, total = 11 > 10)
        $response = $this->actingAs($this->user)->from(route('projects.edit', $project))->put(route('projects.update', $project), [
            'title' => $project->title,
            'slug' => $project->slug,
            'category_id' => $category->id,
            'location' => $project->location,
            'description' => $project->description,
            'status' => $project->status,
            'gallery_images' => [
                UploadedFile::fake()->image('new1.jpg'),
                UploadedFile::fake()->image('new2.jpg'),
            ],
            'gallery_videos' => ['https://www.youtube.com/watch?v=dQw4w9WgXc9'],
        ]);

        $response
            ->assertRedirect(route('projects.edit', $project))
            ->assertSessionHasErrors(['gallery_images' => 'Gallery may not contain more than 10 total items (photos and videos combined) per project. Currently has 8 items.']);
    }

    public function test_gallery_video_rejects_non_youtube_url(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->from(route('projects.create'))->post(route('projects.store'), [
            'title' => 'Invalid Video URL',
            'slug' => 'invalid-video-url',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'Project with invalid video URL.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Ongoing',
            'gallery_videos' => ['https://vimeo.com/123456789'],
        ]);

        $response
            ->assertRedirect(route('projects.create'))
            ->assertSessionHasErrors(['gallery_videos.0']);
    }

    public function test_deleting_video_gallery_item_does_not_trigger_flysystem_error(): void
    {
        $project = Project::factory()->create();
        $videoItem = ProjectImage::create([
            'project_id' => $project->id,
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'image_path' => null,
        ]);

        $response = $this->actingAs($this->user)->delete(route('projects.gallery.delete', [$project, $videoItem->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Gallery image deleted successfully!');
        $this->assertDatabaseMissing('project_images', ['id' => $videoItem->id]);
    }

    public function test_public_project_case_study_renders_mixed_media_payload_and_video_facade(): void
    {
        $project = Project::factory()->create([
            'title' => 'Cinema Showcase Project',
            'slug' => 'cinema-showcase-project',
            'image' => 'main.jpg',
        ]);

        ProjectImage::create([
            'project_id' => $project->id,
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response = $this->get(route('public.projects.show', $project->slug));

        $response->assertOk();
        $response->assertSee('id="project-carousel"', false);
        $response->assertSee('id="video-facade"', false);
        $response->assertSee('VIDEO', false);
        $response->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false);
        $response->assertSee('img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg', false);
        $response->assertSee('gallery-lightbox', false);
        $response->assertSee('lightbox-video-container', false);
    }

    public function test_admin_project_detail_shows_video_badge_and_thumbnail(): void
    {
        $project = Project::factory()->create([
            'title' => 'Admin Video Project',
        ]);

        ProjectImage::create([
            'project_id' => $project->id,
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response = $this->actingAs($this->user)->get(route('projects.show', $project));

        $response->assertOk();
        $response->assertSee('Project Gallery');
        $response->assertSee('VIDEO');
        $response->assertSee('img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg', false);
    }
}
