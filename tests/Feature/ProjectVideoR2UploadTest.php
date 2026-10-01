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

class ProjectVideoR2UploadTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_request_presigned_url(): void
    {
        $response = $this->post(route('projects.video.presign-upload'), [
            'filename' => 'showcase.mp4',
            'file_size' => 10485760,
            'mime_type' => 'video/mp4',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_request_presigned_url_with_valid_metadata(): void
    {
        Storage::fake('r2');

        $response = $this->actingAs($this->user)->postJson(route('projects.video.presign-upload'), [
            'filename' => 'showcase.mp4',
            'file_size' => 20971520, // 20 MB
            'mime_type' => 'video/mp4',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'upload_url',
            'key',
            'public_url',
        ]);

        $this->assertEquals('success', $response->json('status'));
        $this->assertStringStartsWith('projects/videos/', $response->json('key'));
        $this->assertStringEndsWith('.mp4', $response->json('key'));
        $this->assertNotEmpty($response->json('upload_url'));
        $this->assertNotEmpty($response->json('public_url'));
    }

    public function test_presigned_endpoint_rejects_unsupported_mime_types(): void
    {
        Storage::fake('r2');

        $response = $this->actingAs($this->user)->postJson(route('projects.video.presign-upload'), [
            'filename' => 'document.pdf',
            'file_size' => 1048576,
            'mime_type' => 'application/pdf',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['mime_type']);
    }

    public function test_presigned_endpoint_rejects_files_over_50mb(): void
    {
        Storage::fake('r2');

        $response = $this->actingAs($this->user)->postJson(route('projects.video.presign-upload'), [
            'filename' => 'huge_video.mp4',
            'file_size' => 55000000, // > 50 MB
            'mime_type' => 'video/mp4',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file_size']);
    }

    public function test_authenticated_user_can_create_project_with_r2_video(): void
    {
        Storage::fake('public');
        Storage::fake('r2');

        $category = Category::factory()->create();

        $videoKey = 'projects/videos/custom_key_12345.mp4';
        Storage::disk('r2')->put($videoKey, 'fake video stream content');

        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'R2 Video Project',
            'slug' => 'r2-video-project',
            'category_id' => $category->id,
            'location' => 'Jakarta',
            'description' => 'A showcase project featuring direct R2 video.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Ongoing',
            'video_key' => $videoKey,
            'video_file_size' => 15728640,
            'video_mime_type' => 'video/mp4',
        ]);

        $response->assertRedirect(route('projects.index'));

        $project = Project::where('slug', 'r2-video-project')->firstOrFail();
        $videoImage = $project->images()->where('type', 'video')->first();

        $this->assertNotNull($videoImage);
        $this->assertEquals('r2', $videoImage->storage_disk);
        $this->assertEquals($videoKey, $videoImage->image_path);
        $this->assertEquals(15728640, $videoImage->file_size);
        $this->assertEquals('video/mp4', $videoImage->mime_type);
        $this->assertStringContainsString($videoKey, $videoImage->video_stream_url);
    }

    public function test_authenticated_user_can_update_project_with_r2_video(): void
    {
        Storage::fake('public');
        Storage::fake('r2');

        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'image' => 'main.jpg',
        ]);

        $videoKey = 'projects/videos/updated_key_67890.webm';
        Storage::disk('r2')->put($videoKey, 'fake webm stream content');

        $response = $this->actingAs($this->user)->put(route('projects.update', $project), [
            'title' => $project->title,
            'slug' => $project->slug,
            'category_id' => $category->id,
            'location' => $project->location,
            'description' => $project->description,
            'status' => $project->status,
            'video_key' => $videoKey,
            'video_file_size' => 8388608,
            'video_mime_type' => 'video/webm',
        ]);

        $response->assertRedirect(route('projects.index'));

        $videoImage = $project->images()->where('type', 'video')->first();
        $this->assertNotNull($videoImage);
        $this->assertEquals('r2', $videoImage->storage_disk);
        $this->assertEquals($videoKey, $videoImage->image_path);
        $this->assertEquals(8388608, $videoImage->file_size);
        $this->assertEquals('video/webm', $videoImage->mime_type);
    }

    public function test_deleting_r2_video_image_purges_r2_file(): void
    {
        Storage::fake('public');
        Storage::fake('r2');

        $project = Project::factory()->create([
            'image' => 'main.jpg',
        ]);

        $r2Key = 'projects/videos/to_delete.mp4';
        Storage::disk('r2')->put($r2Key, 'video content');
        Storage::disk('r2')->assertExists($r2Key);

        $image = ProjectImage::create([
            'project_id' => $project->id,
            'type' => 'video',
            'storage_disk' => 'r2',
            'image_path' => $r2Key,
            'video_url' => 'https://pub-r2.test/'.$r2Key,
            'file_size' => 1024,
            'mime_type' => 'video/mp4',
        ]);

        $response = $this->actingAs($this->user)->delete(route('projects.gallery.delete', [$project, $image->id]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('project_images', ['id' => $image->id]);
        Storage::disk('r2')->assertMissing($r2Key);
    }

    public function test_force_deleting_project_purges_r2_video(): void
    {
        Storage::fake('public');
        Storage::fake('r2');

        $project = Project::factory()->create([
            'image' => 'main.jpg',
        ]);

        $r2Key = 'projects/videos/project_force_delete.mp4';
        Storage::disk('r2')->put($r2Key, 'video file content');
        Storage::disk('r2')->assertExists($r2Key);

        ProjectImage::create([
            'project_id' => $project->id,
            'type' => 'video',
            'storage_disk' => 'r2',
            'image_path' => $r2Key,
            'video_url' => 'https://pub-r2.test/'.$r2Key,
            'file_size' => 2048,
            'mime_type' => 'video/mp4',
        ]);

        // Soft delete first
        $this->actingAs($this->user)->delete(route('projects.destroy', $project));

        // Force delete
        $response = $this->actingAs($this->user)->delete(route('projects.force-delete', $project->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        Storage::disk('r2')->assertMissing($r2Key);
    }

    public function test_public_show_view_renders_r2_video_with_native_video_player(): void
    {
        $project = Project::factory()->create([
            'slug' => 'r2-showcase-project',
            'image' => 'main.jpg',
        ]);

        $r2Key = 'projects/videos/public_display.mp4';
        $r2Url = 'https://pub-r2.test/'.$r2Key;

        ProjectImage::create([
            'project_id' => $project->id,
            'type' => 'video',
            'storage_disk' => 'r2',
            'image_path' => $r2Key,
            'video_url' => $r2Url,
            'file_size' => 5242880,
            'mime_type' => 'video/mp4',
        ]);

        $response = $this->get(route('public.projects.show', $project->slug));

        $response->assertOk();
        $response->assertSee('main-native-video');
        $response->assertSee('lightbox-native-video');
        $response->assertSee($r2Url);
    }

    public function test_combined_quota_rejects_exceeding_10_items(): void
    {
        Storage::fake('public');
        Storage::fake('r2');

        $category = Category::factory()->create();

        // 10 images + 1 video = 11 items
        $images = [];
        for ($i = 0; $i < 10; $i++) {
            $images[] = UploadedFile::fake()->image("img_{$i}.jpg");
        }

        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Over Quota Project',
            'slug' => 'over-quota-project',
            'category_id' => $category->id,
            'location' => 'Bandung',
            'description' => 'Test quota limit.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Ongoing',
            'gallery_images' => $images,
            'video_key' => 'projects/videos/eleventh_item.mp4',
            'video_file_size' => 1024,
            'video_mime_type' => 'video/mp4',
        ]);

        $response->assertSessionHasErrors(['gallery_images']);
    }
}
