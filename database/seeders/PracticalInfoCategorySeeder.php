<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PracticalInfoCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PracticalInfoCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Worship Places',
                'slug' => 'worship-places',
                'title' => 'Worship Places in Sentul City Area',
                'description' => 'Various places of worship for different religions available in and around Sentul City',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Hotels',
                'slug' => 'hotels',
                'title' => 'Hotels & Accommodations',
                'description' => 'Hotels and lodging facilities near Sentul City for visitors and guests',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Tourist Attractions',
                'slug' => 'tourist-attractions',
                'title' => 'Tourist Attractions & Recreation',
                'description' => 'Popular tourist destinations and recreational spots around Sentul City',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Restaurants',
                'slug' => 'restaurants',
                'title' => 'Restaurants & Dining',
                'description' => 'Dining options and restaurants offering various cuisines in Sentul City area',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Shopping Centers',
                'slug' => 'shopping-centers',
                'title' => 'Shopping Centers & Markets',
                'description' => 'Shopping malls, markets, and retail centers for daily needs and shopping',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Healthcare',
                'slug' => 'healthcare',
                'title' => 'Healthcare Facilities',
                'description' => 'Hospitals, clinics, and medical facilities serving Sentul City residents',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Educational Institutions',
                'slug' => 'educational-institutions',
                'title' => 'Schools & Educational Facilities',
                'description' => 'Schools, universities, and educational institutions in the area',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Government Offices',
                'slug' => 'government-offices',
                'title' => 'Government Offices & Services',
                'description' => 'Government offices and public services for administrative needs',
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            PracticalInfoCategory::create($category);
        }
    }
}
