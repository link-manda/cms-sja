<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarNavigationActiveTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_dashboard_route_activates_dashboard_menu(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();

        // Dashboard menu item and link should have active class
        $response->assertSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('dashboard').'">', false);

        // Projects menu item should NOT be active
        $response->assertDontSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('projects.index').'">', false);
    }

    public function test_projects_index_activates_projects_menu(): void
    {
        $response = $this->actingAs($this->user)->get(route('projects.index'));

        $response->assertOk();
        $response->assertSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('projects.index').'">', false);
        $response->assertDontSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('dashboard').'">', false);
    }

    public function test_projects_create_activates_projects_menu(): void
    {
        $response = $this->actingAs($this->user)->get(route('projects.create'));

        $response->assertOk();
        $response->assertSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('projects.index').'">', false);
        $response->assertDontSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('dashboard').'">', false);
    }

    public function test_projects_archive_activates_projects_menu(): void
    {
        $response = $this->actingAs($this->user)->get(route('projects.archive'));

        $response->assertOk();
        $response->assertSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('projects.index').'">', false);
        $response->assertDontSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('dashboard').'">', false);
    }

    public function test_projects_edit_activates_projects_menu(): void
    {
        $category = Category::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->user)->get(route('projects.edit', $project));

        $response->assertOk();
        $response->assertSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('projects.index').'">', false);
        $response->assertDontSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('dashboard').'">', false);
    }

    public function test_categories_index_activates_categories_menu(): void
    {
        $response = $this->actingAs($this->user)->get(route('categories.index'));

        $response->assertOk();
        $response->assertSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('categories.index').'">', false);
        $response->assertDontSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('projects.index').'">', false);
    }

    public function test_categories_create_activates_categories_menu(): void
    {
        $response = $this->actingAs($this->user)->get(route('categories.create'));

        $response->assertOk();
        $response->assertSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('categories.index').'">', false);
        $response->assertDontSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('projects.index').'">', false);
    }

    public function test_categories_edit_activates_categories_menu(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->get(route('categories.edit', $category));

        $response->assertOk();
        $response->assertSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('categories.index').'">', false);
        $response->assertDontSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('projects.index').'">', false);
    }

    public function test_calculator_index_activates_calculator_menu(): void
    {
        $response = $this->actingAs($this->user)->get(route('calculator.index'));

        $response->assertOk();
        $response->assertSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('calculator.index').'">', false);
        $response->assertDontSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('projects.index').'">', false);
    }

    public function test_calculator_create_activates_calculator_menu(): void
    {
        $response = $this->actingAs($this->user)->get(route('calculator.create'));

        $response->assertOk();
        $response->assertSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('calculator.index').'">', false);
        $response->assertDontSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('projects.index').'">', false);
    }

    public function test_settings_index_activates_settings_menu(): void
    {
        $response = $this->actingAs($this->user)->get(route('settings.index'));

        $response->assertOk();
        $response->assertSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('settings.index').'">', false);
        $response->assertDontSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('projects.index').'">', false);
    }

    public function test_profile_edit_activates_profile_menu(): void
    {
        $response = $this->actingAs($this->user)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('profile.edit').'">', false);
        $response->assertDontSee('<li class="menu-item active">'."\n".'                    <a class="menu-link active" href="'.route('projects.index').'">', false);
    }

    public function test_phantom_categories_show_route_returns_404(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->get('/categories/'.$category->id);

        $response->assertNotFound();
    }
}
