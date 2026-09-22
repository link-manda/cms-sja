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

class ProjectStagedUploadTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_upload_temp_gallery_image(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('temp-image.jpg', 800, 600);

        $response = $this->actingAs($this->user)->postJson(route('projects.upload-temp-gallery'), [
            'file' => $file,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['status', 'temp_path', 'filename']);
        $this->assertEquals('success', $response->json('status'));

        $tempPath = $response->json('temp_path');
        Storage::disk('public')->assertExists($tempPath);
    }

    public function test_upload_temp_gallery_validates_file_is_image(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->user)->postJson(route('projects.upload-temp-gallery'), [
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('status', 'error');
        $this->assertNotEmpty($response->json('message'));
    }

    public function test_guest_cannot_upload_temp_gallery_image(): void
    {
        $file = UploadedFile::fake()->image('temp-image.jpg');

        $response = $this->post(route('projects.upload-temp-gallery'), [
            'file' => $file,
        ]);

        $response->assertRedirect('/login');
    }

    public function test_storing_project_with_temp_gallery_images_moves_files_and_attaches_media(): void
    {
        Storage::fake('public');

        // Prepare temporary images
        $file1 = UploadedFile::fake()->image('temp1.jpg');
        $file2 = UploadedFile::fake()->image('temp2.png');

        $path1 = $file1->storeAs('projects/temp-gallery', 'temp1-random.jpg', 'public');
        $path2 = $file2->storeAs('projects/temp-gallery', 'temp2-random.png', 'public');

        Storage::disk('public')->assertExists($path1);
        Storage::disk('public')->assertExists($path2);

        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Staged Upload Project',
            'slug' => 'staged-upload-project',
            'category_id' => $category->id,
            'location' => 'Badung, Bali',
            'description' => 'Testing asynchronous staged upload migration.',
            'image' => UploadedFile::fake()->image('main-photo.jpg'),
            'status' => 'Ongoing',
            'temp_gallery_images' => [$path1, $path2],
        ]);

        $response->assertRedirect(route('projects.index'));

        $project = Project::where('slug', 'staged-upload-project')->firstOrFail();

        // Ensure ProjectImage records exist
        $this->assertCount(2, $project->images);

        // Ensure files were moved from temp-gallery to gallery
        Storage::disk('public')->assertMissing($path1);
        Storage::disk('public')->assertMissing($path2);
        Storage::disk('public')->assertExists('projects/gallery/temp1-random.jpg');
        Storage::disk('public')->assertExists('projects/gallery/temp2-random.png');
    }

    public function test_youtube_urls_are_normalized_and_accepted_in_store_request(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        // Testing YouTube URLs: mobile without https, youtu.be, shorts
        $videos = [
            'm.youtube.com/watch?v=dQw4w9WgXcQ',
            'youtu.be/dQw4w9WgXcQ',
            'https://youtube.com/shorts/dQw4w9WgXcQ',
        ];

        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Video Normalize Project',
            'slug' => 'video-normalize-project',
            'category_id' => $category->id,
            'location' => 'Denpasar, Bali',
            'description' => 'Testing YouTube normalization and regex acceptance.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Completed',
            'gallery_videos' => $videos,
        ]);

        $response->assertRedirect(route('projects.index'));

        $project = Project::where('slug', 'video-normalize-project')->firstOrFail();
        $this->assertCount(3, $project->images()->where('type', 'video')->get());

        // First video should have https:// auto-prepended
        $this->assertTrue($project->images()->where('video_url', 'https://m.youtube.com/watch?v=dQw4w9WgXcQ')->exists());
    }

    public function test_youtube_live_url_is_accepted_and_embed_url_generated(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Live Video Project',
            'slug' => 'live-video-project',
            'category_id' => $category->id,
            'location' => 'Kuta, Bali',
            'description' => 'Testing YouTube /live/ URL parsing.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Ongoing',
            'gallery_videos' => ['https://www.youtube.com/live/dQw4w9WgXcQ?feature=share'],
        ]);

        $response->assertRedirect(route('projects.index'));

        $project = Project::where('slug', 'live-video-project')->firstOrFail();
        $media = $project->images()->where('type', 'video')->firstOrFail();

        $this->assertEquals('dQw4w9WgXcQ', ProjectImage::extractYouTubeId($media->video_url));
        $this->assertEquals('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0', $media->embed_url);
        $this->assertEquals('https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg', $media->thumbnail_url);
    }

    public function test_storing_project_rejects_non_existent_temporary_images(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Non Existent Temp Project',
            'slug' => 'non-existent-temp-project',
            'category_id' => $category->id,
            'location' => 'Denpasar, Bali',
            'description' => 'Testing non existent temp image validation.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Ongoing',
            'temp_gallery_images' => ['projects/temp-gallery/ghost-file.jpg'],
        ]);

        $response->assertSessionHasErrors('temp_gallery_images.0');
    }

    public function test_prune_temp_gallery_command_deletes_expired_files(): void
    {
        Storage::fake('public');

        // Create a temporary file
        Storage::disk('public')->put('projects/temp-gallery/expired-file.jpg', 'fake-content');
        Storage::disk('public')->assertExists('projects/temp-gallery/expired-file.jpg');

        $this->artisan('projects:prune-temp-gallery', ['--hours' => 0])
            ->assertExitCode(0);

        Storage::disk('public')->assertMissing('projects/temp-gallery/expired-file.jpg');
    }
}
