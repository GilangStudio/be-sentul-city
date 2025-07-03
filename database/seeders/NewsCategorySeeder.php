<?php

namespace Database\Seeders;

use App\Models\NewsCategory;
use Illuminate\Database\Seeder;

class NewsCategorySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'News',
                'slug' => 'news',
                'description' => 'General news and announcements',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Events',
                'slug' => 'events',
                'description' => 'Upcoming events and activities',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'City Concierge',
                'slug' => 'city-concierge',
                'description' => 'City concierge services and updates',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Property Updates',
                'slug' => 'property-updates',
                'description' => 'Latest property development updates',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Community',
                'slug' => 'community',
                'description' => 'Community news and activities',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            NewsCategory::create($category);
        }
    }
}