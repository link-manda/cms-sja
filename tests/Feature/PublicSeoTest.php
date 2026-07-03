<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_has_public_seo_tags(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<link rel="canonical" href="http://localhost:8000">', false);
        $response->assertSee('<meta property="og:title" content="PT Sistem Jaya Abadi - Professional Contractor">', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('"@type":"Organization"', false);
    }

    public function test_projects_index_has_public_seo_tags(): void
    {
        $response = $this->get('/projects');

        $response->assertOk();
        $response->assertSee('<link rel="canonical" href="http://localhost:8000/projects">', false);
        $response->assertSee('<meta property="og:title" content="Our Projects - PT Sistem Jaya Abadi">', false);
        $response->assertSee('<meta property="og:url" content="http://localhost:8000/projects">', false);
    }

    public function test_project_detail_uses_project_seo_tags(): void
    {
        $project = Project::factory()->create([
            'title' => 'Factory Warehouse Build',
            'slug' => 'factory-warehouse-build',
            'description' => 'Fallback project description for SEO metadata.',
            'image' => 'factory.jpg',
            'meta_title' => 'Factory Warehouse SEO Title',
            'meta_description' => 'Factory warehouse SEO description.',
        ]);

        $response = $this->get(route('public.projects.show', $project->slug));

        $response->assertOk();
        $response->assertSee('<title>Factory Warehouse SEO Title</title>', false);
        $response->assertSee('<meta name="description" content="Factory warehouse SEO description.">', false);
        $response->assertSee('<link rel="canonical" href="http://localhost:8000/case-study/factory-warehouse-build">', false);
        $response->assertSee('<meta property="og:image" content="http://localhost:8000/storage/projects/factory.jpg">', false);
        $response->assertSee('<meta property="og:type" content="article">', false);
    }
}
