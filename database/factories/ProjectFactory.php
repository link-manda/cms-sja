<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);

        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'location' => $this->faker->city.', '.$this->faker->state,
            'description' => $this->faker->paragraphs(3, true),
            'image' => 'project_placeholder.jpg',
            'status' => $this->faker->randomElement(['Completed', 'Ongoing']),
            'meta_title' => $title,
            'meta_description' => $this->faker->sentence(10),
            'client' => $this->faker->company,
            'year' => (string) $this->faker->year,
            'building_area' => $this->faker->numberBetween(100, 1000) . ' sqm',
            'land_area' => $this->faker->numberBetween(200, 2000) . ' sqm',
            'execution_team' => 'SJA ' . $this->faker->word . ' Team',
        ];
    }
}
