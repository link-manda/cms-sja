<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ensure admin user exists
        if (! User::where('email', 'admin@sja.com')->exists()) {
            User::factory()->create([
                'name' => 'Administrator',
                'email' => 'admin@sja.com',
                'password' => bcrypt('password123'),
            ]);
        }

        // Seed default categories
        $categories = [
            [
                'name' => 'Villa Development',
                'slug' => 'villa-development',
            ],
            [
                'name' => 'Residential Construction',
                'slug' => 'residential-construction',
            ],
            [
                'name' => 'Commercial Building',
                'slug' => 'commercial-building',
            ],
            [
                'name' => 'Infrastructure Works',
                'slug' => 'infrastructure-works',
            ],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['slug']] = Category::firstOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name']]
            );
        }

        // Seed featured projects
        $villaCategory = $categoryModels['villa-development'];

        if (! Project::where('slug', 'modern-tropical-villa')->exists()) {
            Project::create([
                'title' => 'Modern Tropical Villa',
                'slug' => 'modern-tropical-villa',
                'category_id' => $villaCategory->id,
                'location' => 'Canggu, Bali',
                'description' => "A 3-bedroom luxury villa featuring a sunken living room, infinity pool, and sustainable bamboo architecture details.",
                'image' => 'villa_canggu.png',
                'status' => 'Completed',
                'client' => 'Private Client',
                'year' => '2024',
                'building_area' => '450 sqm',
                'land_area' => '800 sqm',
                'execution_team' => 'SJA Bali Engineering Unit',
                'meta_title' => 'Modern Tropical Villa in Canggu | Sistem Jaya Abadi',
                'meta_description' => 'A luxury 3-bedroom villa built by PT. Sistem Jaya Abadi featuring tropical design and bamboo elements in Canggu, Bali.',
            ]);
        }

        if (! Project::where('slug', 'ocean-view-cliffhouse')->exists()) {
            Project::create([
                'title' => 'Ocean View Cliffhouse',
                'slug' => 'ocean-view-cliffhouse',
                'category_id' => $villaCategory->id,
                'location' => 'Pecatu, Bali',
                'description' => "A massive structural undertaking on the limestone cliffs of Pecatu. Engineered for extreme durability while maximizing the Indian Ocean view.",
                'image' => 'cliffhouse_pecatu.png',
                'status' => 'Ongoing',
                'client' => 'PT. Cliffside Bali Resort',
                'year' => 'In Progress',
                'building_area' => '1,200 sqm',
                'land_area' => '2,500 sqm',
                'execution_team' => 'SJA Bali Engineering Unit',
                'meta_title' => 'Ocean View Cliffhouse in Pecatu | Sistem Jaya Abadi',
                'meta_description' => 'Limestone cliff-front villa construction by PT. Sistem Jaya Abadi. Structural expertise meets high-end luxury in Pecatu, Bali.',
            ]);
        }
    }
}
