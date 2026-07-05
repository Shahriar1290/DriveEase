<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            // Sedans (cat 1)
            ['Toyota Camry','Toyota','Camry','sedan',1,'petrol','automatic',5,85.00,'available',2022,'Silver'],
            ['Honda Accord','Honda','Accord','sedan',1,'petrol','automatic',5,80.00,'available',2021,'White'],
            ['Nissan Altima','Nissan','Altima','sedan',1,'petrol','automatic',5,70.00,'available',2020,'Black'],
            ['Ford Fusion','Ford','Fusion','sedan',1,'hybrid','automatic',5,75.00,'available',2021,'Blue'],
            ['Hyundai Sonata','Hyundai','Sonata','sedan',1,'petrol','automatic',5,65.00,'available',2022,'Red'],
            ['Mazda 6','Mazda','6','sedan',1,'petrol','automatic',5,72.00,'available',2021,'Gray'],
            ['Chevrolet Malibu','Chevrolet','Malibu','sedan',1,'petrol','automatic',5,68.00,'available',2020,'White'],
            // SUVs (cat 2)
            ['Toyota RAV4','Toyota','RAV4','suv',2,'petrol','automatic',5,110.00,'available',2022,'White'],
            ['Honda CR-V','Honda','CR-V','suv',2,'hybrid','automatic',5,115.00,'available',2023,'Silver'],
            ['Ford Explorer','Ford','Explorer','suv',2,'petrol','automatic',7,130.00,'available',2022,'Black'],
            ['Chevrolet Equinox','Chevrolet','Equinox','suv',2,'petrol','automatic',5,105.00,'available',2021,'Blue'],
            ['Hyundai Tucson','Hyundai','Tucson','suv',2,'petrol','automatic',5,95.00,'available',2022,'Red'],
            ['Nissan Rogue','Nissan','Rogue','suv',2,'petrol','automatic',5,100.00,'available',2021,'Gray'],
            ['Mazda CX-5','Mazda','CX-5','suv',2,'petrol','automatic',5,108.00,'available',2022,'White'],
            ['Kia Sportage','Kia','Sportage','suv',2,'petrol','automatic',5,95.00,'available',2021,'Black'],
            // Hatchbacks (cat 3)
            ['Toyota Yaris','Toyota','Yaris','hatch',3,'petrol','manual',5,45.00,'available',2021,'Red'],
            ['Honda Jazz','Honda','Jazz','hatch',3,'petrol','automatic',5,50.00,'available',2022,'Blue'],
            ['Hyundai i20','Hyundai','i20','hatch',3,'petrol','manual',5,42.00,'available',2021,'White'],
            ['Ford Fiesta','Ford','Fiesta','hatch',3,'petrol','manual',5,40.00,'available',2020,'Yellow'],
            ['Volkswagen Polo','Volkswagen','Polo','hatch',3,'petrol','automatic',5,55.00,'available',2022,'Silver'],
            // Pickup Trucks (cat 4)
            ['Ford F-150','Ford','F-150','pickup',4,'petrol','automatic',5,150.00,'available',2022,'White'],
            ['Toyota Hilux','Toyota','Hilux','pickup',4,'diesel','manual',5,120.00,'available',2021,'Silver'],
            ['Nissan Navara','Nissan','Navara','pickup',4,'diesel','manual',5,115.00,'available',2020,'Black'],
            ['Chevrolet Colorado','Chevrolet','Colorado','pickup',4,'diesel','automatic',5,130.00,'available',2022,'Blue'],
            ['Mitsubishi Triton','Mitsubishi','Triton','pickup',4,'diesel','manual',5,110.00,'available',2021,'White'],
            // Minivans (cat 5)
            ['Toyota Sienna','Toyota','Sienna','minivan',5,'hybrid','automatic',8,140.00,'available',2022,'White'],
            ['Honda Odyssey','Honda','Odyssey','minivan',5,'petrol','automatic',8,135.00,'available',2021,'Silver'],
            ['Chrysler Pacifica','Chrysler','Pacifica','minivan',5,'hybrid','automatic',8,145.00,'available',2022,'Black'],
            // Luxury (cat 6)
            ['BMW 5 Series','BMW','5 Series','luxury',6,'petrol','automatic',5,200.00,'available',2022,'Black'],
            ['Mercedes C-Class','Mercedes','C-Class','luxury',6,'petrol','automatic',5,220.00,'available',2023,'Silver'],
            ['Audi A6','Audi','A6','luxury',6,'petrol','automatic',5,210.00,'available',2022,'White'],
            ['Lexus ES','Lexus','ES','luxury',6,'hybrid','automatic',5,190.00,'available',2022,'Pearl'],
            ['BMW 3 Series','BMW','3 Series','luxury',6,'petrol','automatic',5,180.00,'available',2021,'Blue'],
            ['Audi A4','Audi','A4','luxury',6,'petrol','automatic',5,170.00,'available',2022,'Gray'],
            ['Mercedes E-Class','Mercedes','E-Class','luxury',6,'petrol','automatic',5,230.00,'available',2023,'Black'],
            // Electric (cat 7)
            ['Tesla Model 3','Tesla','Model 3','electric',7,'electric','automatic',5,160.00,'available',2023,'White'],
            ['Tesla Model Y','Tesla','Model Y','electric',7,'electric','automatic',5,170.00,'available',2023,'Red'],
            ['Hyundai Ioniq 5','Hyundai','Ioniq 5','electric',7,'electric','automatic',5,150.00,'available',2022,'Silver'],
            ['Kia EV6','Kia','EV6','electric',7,'electric','automatic',5,155.00,'available',2022,'Black'],
            ['BMW iX','BMW','iX','electric',7,'electric','automatic',5,195.00,'available',2023,'White'],
            ['Nissan Leaf','Nissan','Leaf','electric',7,'electric','automatic',5,90.00,'available',2022,'Blue'],
            // Convertibles (cat 8)
            ['Ford Mustang GT','Ford','Mustang GT','convert',8,'petrol','automatic',4,175.00,'available',2022,'Red'],
            ['Chevrolet Camaro','Chevrolet','Camaro','convert',8,'petrol','automatic',4,165.00,'available',2021,'Yellow'],
            ['BMW Z4','BMW','Z4','convert',8,'petrol','automatic',2,195.00,'available',2022,'Silver'],
            ['Mazda MX-5','Mazda','MX-5','convert',8,'petrol','manual',2,120.00,'available',2021,'Red'],
            ['Porsche Boxster','Porsche','Boxster','convert',8,'petrol','automatic',2,250.00,'available',2022,'Silver'],
            // Extra mixed
            ['Toyota Corolla','Toyota','Corolla','extra',1,'petrol','automatic',5,62.00,'available',2021,'White'],
            ['Honda Civic','Honda','Civic','extra',1,'petrol','automatic',5,65.00,'available',2022,'Black'],
            ['Kia Cerato','Kia','Cerato','extra',1,'petrol','automatic',5,60.00,'available',2021,'Blue'],
            ['Mitsubishi Outlander','Mitsubishi','Outlander','extra',2,'petrol','automatic',7,115.00,'available',2022,'Gray'],
            ['Toyota Land Cruiser','Toyota','Land Cruiser','extra',2,'diesel','automatic',8,200.00,'available',2022,'White'],
        ];

        $imageUrls = ['vehicles/car1.jpg', 'vehicles/car2.jpg', 'vehicles/car3.jpg', 'vehicles/car4.jpg', 'vehicles/car5.jpg'];
        $regPrefix = 'DHA-';

        foreach ($vehicles as $i => $v) {
            [$name, $brand, $model, $type, $catId, $fuel, $trans, $seats, $price, $status, $year, $color] = $v;

            $reg  = $regPrefix . str_pad($i + 1001, 4, '0', STR_PAD_LEFT);
            $slug = Str::slug($name . '-' . $reg);

            $description = "The {$name} is a {$year} {$brand} {$model}. "
                . "Featuring modern amenities and reliable performance, this vehicle is perfect for any journey.";

            DB::insert("
                INSERT INTO vehicles (
                    category_id, vehicle_name, slug, brand, model, year, registration_number,
                    description, fuel_type, transmission, seating_capacity, price_per_day, status,
                    color, mileage, engine_cc, air_conditioning, gps, bluetooth, usb_charger, child_seat,
                    total_rentals, average_rating, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, 1, 1, ?, ?, ?, NOW(), NOW())
            ", [
                $catId, $name, $slug, $brand, $model, $year, $reg, $description,
                $fuel, $trans, $seats, $price, $status, $color,
                rand(10, 20) . ' km/l', rand(12, 35) * 100 . ' cc',
                $price > 100 ? 1 : 0,
                $seats >= 7 ? 1 : 0,
                rand(0, 80),
                round(rand(35, 50) / 10, 1),
            ]);

            $vehicleId = DB::getPdo()->lastInsertId();

            DB::insert("
                INSERT INTO vehicle_images (vehicle_id, image, is_primary, sort_order, created_at, updated_at)
                VALUES (?, ?, 1, 0, NOW(), NOW())
            ", [$vehicleId, $imageUrls[$i % count($imageUrls)]]);
        }
    }
}
