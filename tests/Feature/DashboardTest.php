<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_dashboard_with_stats(): void
    {
        // Seed some projects with different statuses
        Project::factory()->create([
            'title' => 'Project A',
            'status' => 'Completed',
            'meta_title' => 'Project A SEO',
            'meta_description' => 'SEO Desc A',
        ]);
        Project::factory()->create([
            'title' => 'Project B',
            'status' => 'Ongoing',
            'meta_title' => null, // Not optimized
            'meta_description' => null,
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Total Projects');
        $response->assertSee('2'); // Total count
        $response->assertSee('Completed');
        $response->assertSee('Ongoing');
        $response->assertSee('Project A');
        $response->assertSee('Project B');
        $response->assertSee('50%'); // SEO Percentage (1 out of 2)
    }

    public function test_dashboard_does_not_divide_by_zero_with_no_projects(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Total Projects');
        $response->assertSee('0');
        $response->assertSee('0%'); // SEO Percentage is 0
    }
}
