<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\User;
use App\Services\ProjectService;
use App\Services\R2StorageService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class R2StorageIndicatorTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_view_dashboard_or_sync_storage(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect('/login');

        $syncResponse = $this->post(route('manage.storage.sync-r2'));
        $syncResponse->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard_with_r2_storage_card(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('r2Storage');
        $response->assertViewHas('completedPercentage');
        $response->assertViewHas('ongoingPercentage');
        $response->assertSee('Cloud Media Storage (R2)');
        $response->assertSee('10.00 GB');
        $response->assertSee('role="progressbar"', false);
        $response->assertSee('btn-sync-r2');
        $response->assertSee('Total Projects');
        $response->assertSee('Completed');
        $response->assertSee('Ongoing');
    }

    public function test_r2_storage_service_calculates_empty_state_cleanly(): void
    {
        $service = app(R2StorageService::class);
        $usage = $service->getStorageUsage(true);

        $this->assertEquals(0, $usage['total_bytes']);
        $this->assertEquals('0.00 MB', $usage['total_formatted']);
        $this->assertEquals(0.0, $usage['percentage']);
        $this->assertEquals(0, $usage['video_count']);
        $this->assertEquals('optimal', $usage['status']);
        $this->assertEquals('Optimal', $usage['status_label']);
        $this->assertEquals('check-circle', $usage['icon']);
    }

    public function test_r2_storage_service_correctly_transitions_adaptive_statuses(): void
    {
        $service = app(R2StorageService::class);

        // Optimal: < 70%
        $statusOptimal = $service->resolveStatus(69.9);
        $this->assertEquals('optimal', $statusOptimal['status']);
        $this->assertEquals('Optimal', $statusOptimal['label']);
        $this->assertEquals('check-circle', $statusOptimal['icon']);
        $this->assertEquals('bg-primary', $statusOptimal['bar_color']);

        // Warning: 70% to < 90%
        $statusWarning = $service->resolveStatus(70.0);
        $this->assertEquals('warning', $statusWarning['status']);
        $this->assertEquals('Warning', $statusWarning['label']);
        $this->assertEquals('alert-triangle', $statusWarning['icon']);
        $this->assertEquals('bg-amber-500', $statusWarning['bar_color']);

        $statusWarningEdge = $service->resolveStatus(89.9);
        $this->assertEquals('warning', $statusWarningEdge['status']);

        // Critical: >= 90%
        $statusCritical = $service->resolveStatus(90.0);
        $this->assertEquals('critical', $statusCritical['status']);
        $this->assertEquals('Critical', $statusCritical['label']);
        $this->assertEquals('alert-octagon', $statusCritical['icon']);
        $this->assertEquals('bg-danger', $statusCritical['bar_color']);

        $statusCriticalHigh = $service->resolveStatus(98.5);
        $this->assertEquals('critical', $statusCriticalHigh['status']);
    }

    public function test_r2_storage_service_calculates_usage_with_existing_project_images(): void
    {
        $category = Category::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id]);

        // Add 2 R2 video records (using decimal SI units matching Cloudflare R2)
        ProjectImage::create([
            'project_id' => $project->id,
            'type' => 'video',
            'storage_disk' => 'r2',
            'image_path' => 'projects/videos/sample1.mp4',
            'video_url' => 'https://pub-r2.dev/projects/videos/sample1.mp4',
            'file_size' => 1000000000, // 1.00 GB
        ]);

        ProjectImage::create([
            'project_id' => $project->id,
            'type' => 'video',
            'storage_disk' => 'r2',
            'image_path' => 'projects/videos/sample2.mp4',
            'video_url' => 'https://pub-r2.dev/projects/videos/sample2.mp4',
            'file_size' => 500000000, // 0.50 GB
        ]);

        $service = app(R2StorageService::class);
        $usage = $service->getStorageUsage(true);

        $this->assertEquals(2, $usage['video_count']);
        $this->assertEquals(1500000000, $usage['total_bytes']); // 1.50 GB
        $this->assertEquals('1.50 GB', $usage['total_formatted']);
        $this->assertEquals(15.0, $usage['percentage']);
        $this->assertEquals('optimal', $usage['status']);
    }

    public function test_cache_is_invalidated_when_project_service_stores_or_deletes_video(): void
    {
        $service = app(R2StorageService::class);
        $service->getStorageUsage(true);

        $this->assertTrue(Cache::has(R2StorageService::CACHE_KEY));

        $category = Category::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id]);

        $projectService = app(ProjectService::class);

        // Store project with video media -> invalidates cache
        $projectData = Project::factory()->raw([
            'category_id' => $category->id,
            'video_key' => 'projects/videos/vid1.mp4',
            'video_file_size' => 100000000,
            'video_mime_type' => 'video/mp4',
        ]);
        $project = $projectService->createProject($projectData);

        $this->assertFalse(Cache::has(R2StorageService::CACHE_KEY));

        // Re-populate cache
        $service->getStorageUsage();
        $this->assertTrue(Cache::has(R2StorageService::CACHE_KEY));

        // Delete video -> invalidates cache
        $videoImage = $project->images()->where('storage_disk', 'r2')->first();
        Storage::fake('r2');
        $projectService->deleteGalleryImage($project, $videoImage->id);

        $this->assertFalse(Cache::has(R2StorageService::CACHE_KEY));
    }

    public function test_authenticated_user_can_trigger_on_demand_sync(): void
    {
        Storage::fake('r2');
        Storage::disk('r2')->put('projects/videos/unrecorded.mp4', str_repeat('A', 5000000)); // 5 MB

        $category = Category::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id]);

        $image = ProjectImage::create([
            'project_id' => $project->id,
            'type' => 'video',
            'storage_disk' => 'r2',
            'image_path' => 'projects/videos/unrecorded.mp4',
            'video_url' => 'https://pub-r2.dev/projects/videos/unrecorded.mp4',
            'file_size' => null, // Initially null
        ]);

        $response = $this->actingAs($this->user)->postJson(route('manage.storage.sync-r2'));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'updated_records' => 1,
            'deleted_orphans' => 0,
            'physical_count' => 1,
        ]);

        $image->refresh();
        $this->assertEquals(5000000, $image->file_size);
    }

    public function test_sync_cleans_up_orphaned_database_records_when_missing_from_r2(): void
    {
        Storage::fake('r2');
        Storage::disk('r2')->put('projects/videos/existing.mp4', str_repeat('B', 3000000)); // 3 MB

        $category = Category::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id]);

        // Valid DB record that exists in R2
        $validImage = ProjectImage::create([
            'project_id' => $project->id,
            'type' => 'video',
            'storage_disk' => 'r2',
            'image_path' => 'projects/videos/existing.mp4',
            'video_url' => 'https://pub-r2.dev/projects/videos/existing.mp4',
            'file_size' => 3000000,
        ]);

        // Orphaned DB record that does NOT exist in R2
        $orphanImage = ProjectImage::create([
            'project_id' => $project->id,
            'type' => 'video',
            'storage_disk' => 'r2',
            'image_path' => 'projects/videos/orphan_not_in_r2.mp4',
            'video_url' => 'https://pub-r2.dev/projects/videos/orphan_not_in_r2.mp4',
            'file_size' => 1234567,
        ]);

        $response = $this->actingAs($this->user)->postJson(route('manage.storage.sync-r2'));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'deleted_orphans' => 1,
            'physical_count' => 1,
            'data' => [
                'video_count' => 1,
                'total_formatted' => '3.00 MB',
            ],
        ]);

        // Orphan record must be deleted from MySQL
        $this->assertDatabaseMissing('project_images', [
            'id' => $orphanImage->id,
        ]);

        // Valid record remains
        $this->assertDatabaseHas('project_images', [
            'id' => $validImage->id,
        ]);
    }

    public function test_sync_handles_cloud_failure_safely(): void
    {
        Storage::shouldReceive('disk')
            ->with('r2')
            ->andThrow(new Exception('Cloudflare R2 unreachable'));

        $response = $this->actingAs($this->user)->postJson(route('manage.storage.sync-r2'));

        $response->assertStatus(500);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringNotContainsString('CLOUDFLARE_R2_SECRET_ACCESS_KEY', $response->getContent());
    }
}
