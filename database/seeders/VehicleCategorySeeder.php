<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['category_name' => 'Sedan',        'icon' => 'fas fa-car',          'description' => 'Comfortable 4-door sedans for city and highway travel.'],
            ['category_name' => 'SUV',           'icon' => 'fas fa-truck-pickup', 'description' => 'Spacious SUVs for family trips and off-road adventures.'],
            ['category_name' => 'Hatchback',     'icon' => 'fas fa-car-side',     'description' => 'Compact and fuel-efficient hatchbacks for city driving.'],
            ['category_name' => 'Pickup Truck',  'icon' => 'fas fa-truck',        'description' => 'Heavy-duty pickup trucks for cargo and off-road use.'],
            ['category_name' => 'Minivan',       'icon' => 'fas fa-shuttle-van',  'description' => 'Spacious minivans perfect for large families.'],
            ['category_name' => 'Luxury',        'icon' => 'fas fa-gem',          'description' => 'Premium luxury vehicles for special occasions.'],
            ['category_name' => 'Electric',      'icon' => 'fas fa-bolt',         'description' => 'Eco-friendly electric vehicles.'],
            ['category_name' => 'Convertible',   'icon' => 'fas fa-sun',          'description' => 'Open-top convertibles for a thrilling drive.'],
        ];

        foreach ($categories as $i => $cat) {
            DB::insert("
                INSERT INTO vehicle_categories (category_name, slug, description, icon, is_active, sort_order, created_at, updated_at)
                VALUES (?, ?, ?, ?, 1, ?, NOW(), NOW())
            ", [
                $cat['category_name'],
                Str::slug($cat['category_name']),
                $cat['description'],
                $cat['icon'],
                $i + 1,
            ]);
        }
    }
}
