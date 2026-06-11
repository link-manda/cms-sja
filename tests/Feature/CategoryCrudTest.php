<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_unauthenticated_user_cannot_access_categories(): void
    {
        $response = $this->get(route('categories.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_category_list(): void
    {
        Category::factory()->create([
            'name' => 'Villa Projects',
            'slug' => 'villa-projects',
        ]);

        $response = $this->actingAs($this->user)->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Villa Projects');
        $response->assertSee('villa-projects');
    }

    public function test_authenticated_user_can_view_create_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('categories.create'));

        $response->assertStatus(200);
        $response->assertSee('Category Name');
    }

    public function test_authenticated_user_can_store_category(): void
    {
        $response = $this->actingAs($this->user)->post(route('categories.store'), [
            'name' => 'Highrise Buildings',
            'slug' => 'highrise-buildings',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Highrise Buildings',
            'slug' => 'highrise-buildings',
        ]);
    }

    public function test_authenticated_user_can_view_edit_form(): void
    {
        $category = Category::factory()->create([
            'name' => 'Renovation Works',
            'slug' => 'renovation-works',
        ]);

        $response = $this->actingAs($this->user)->get(route('categories.edit', $category->id));

        $response->assertStatus(200);
        $response->assertSee('Renovation Works');
    }

    public function test_authenticated_user_can_update_category(): void
    {
        $category = Category::factory()->create([
            'name' => 'Old Category',
            'slug' => 'old-category',
        ]);

        $response = $this->actingAs($this->user)->put(route('categories.update', $category->id), [
            'name' => 'Updated Category',
            'slug' => 'updated-category',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'slug' => 'updated-category',
        ]);
    }

    public function test_authenticated_user_can_delete_category_without_projects(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('categories.destroy', $category->id));

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_authenticated_user_cannot_delete_category_with_projects(): void
    {
        $category = Category::factory()->create();
        Project::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('categories.destroy', $category->id));

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('error', 'Cannot delete category because it has projects assigned to it!');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
