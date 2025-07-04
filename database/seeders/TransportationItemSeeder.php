<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransportationItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransportationItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transportationItems = [
            [
                'title' => 'Sinar Jaya',
                'description' => 'Route: Rawamangun, Grogol, Blok M',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Agra Mas',
                'description' => 'Route: Pasar Senen',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Bus PDD',
                'description' => 'Route: Blok M',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Bus MGI',
                'description' => 'Route: Bandung',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Bus Trans Pakuan',
                'description' => 'Route: Stasiun UI Cilebut dan Kota Bogor',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Ojek Online',
                'description' => 'Available: Gojek, Grab, and other ride-sharing services',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'title' => 'Taxi',
                'description' => 'Various taxi services available 24/7',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'title' => 'Angkot',
                'description' => 'Local public transportation (angkutan kota)',
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($transportationItems as $item) {
            TransportationItem::create($item);
        }
    }
}
