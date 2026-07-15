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
            ['Toyota Camry','Toyota','Camry','sedan',1,'petrol','automatic',5,10200,'available',2022,'Silver'],
            ['Honda Accord','Honda','Accord','sedan',1,'petrol','automatic',5,9600,'available',2021,'White'],
            ['Nissan Altima','Nissan','Altima','sedan',1,'petrol','automatic',5,8400,'available',2020,'Black'],
            ['Ford Fusion','Ford','Fusion','sedan',1,'hybrid','automatic',5,9000,'available',2021,'Blue'],
            ['Hyundai Sonata','Hyundai','Sonata','sedan',1,'petrol','automatic',5,7800,'available',2022,'Red'],
            ['Mazda 6','Mazda','6','sedan',1,'petrol','automatic',5,8640,'available',2021,'Gray'],
            ['Chevrolet Malibu','Chevrolet','Malibu','sedan',1,'petrol','automatic',5,8160,'available',2020,'White'],
            // SUVs (cat 2)
            ['Toyota RAV4','Toyota','RAV4','suv',2,'petrol','automatic',5,13200,'available',2022,'White'],
            ['Honda CR-V','Honda','CR-V','suv',2,'hybrid','automatic',5,13800,'available',2023,'Silver'],
            ['Ford Explorer','Ford','Explorer','suv',2,'petrol','automatic',7,15600,'available',2022,'Black'],
            ['Chevrolet Equinox','Chevrolet','Equinox','suv',2,'petrol','automatic',5,12600,'available',2021,'Blue'],
            ['Hyundai Tucson','Hyundai','Tucson','suv',2,'petrol','automatic',5,11400,'available',2022,'Red'],
            ['Nissan Rogue','Nissan','Rogue','suv',2,'petrol','automatic',5,12000,'available',2021,'Gray'],
            ['Mazda CX-5','Mazda','CX-5','suv',2,'petrol','automatic',5,12960,'available',2022,'White'],
            ['Kia Sportage','Kia','Sportage','suv',2,'petrol','automatic',5,11400,'available',2021,'Black'],
            // Hatchbacks (cat 3)
            ['Toyota Yaris','Toyota','Yaris','hatch',3,'petrol','manual',5,5400,'available',2021,'Red'],
            ['Honda Jazz','Honda','Jazz','hatch',3,'petrol','automatic',5,6000,'available',2022,'Blue'],
            ['Hyundai i20','Hyundai','i20','hatch',3,'petrol','manual',5,5040,'available',2021,'White'],
            ['Ford Fiesta','Ford','Fiesta','hatch',3,'petrol','manual',5,4800,'available',2020,'Yellow'],
            ['Volkswagen Polo','Volkswagen','Polo','hatch',3,'petrol','automatic',5,6600,'available',2022,'Silver'],
            // Pickup Trucks (cat 4)
            ['Ford F-150','Ford','F-150','pickup',4,'petrol','automatic',5,18000,'available',2022,'White'],
            ['Toyota Hilux','Toyota','Hilux','pickup',4,'diesel','manual',5,14400,'available',2021,'Silver'],
            ['Nissan Navara','Nissan','Navara','pickup',4,'diesel','manual',5,13800,'available',2020,'Black'],
            ['Chevrolet Colorado','Chevrolet','Colorado','pickup',4,'diesel','automatic',5,15600,'available',2022,'Blue'],
            ['Mitsubishi Triton','Mitsubishi','Triton','pickup',4,'diesel','manual',5,13200,'available',2021,'White'],
            // Minivans (cat 5)
            ['Toyota Sienna','Toyota','Sienna','minivan',5,'hybrid','automatic',8,16800,'available',2022,'White'],
            ['Honda Odyssey','Honda','Odyssey','minivan',5,'petrol','automatic',8,16200,'available',2021,'Silver'],
            ['Chrysler Pacifica','Chrysler','Pacifica','minivan',5,'hybrid','automatic',8,17400,'available',2022,'Black'],
            // Luxury (cat 6)
            ['BMW 5 Series','BMW','5 Series','luxury',6,'petrol','automatic',5,24000,'available',2022,'Black'],
            ['Mercedes C-Class','Mercedes','C-Class','luxury',6,'petrol','automatic',5,26400,'available',2023,'Silver'],
            ['Audi A6','Audi','A6','luxury',6,'petrol','automatic',5,25200,'available',2022,'White'],
            ['Lexus ES','Lexus','ES','luxury',6,'hybrid','automatic',5,22800,'available',2022,'Pearl'],
            ['BMW 3 Series','BMW','3 Series','luxury',6,'petrol','automatic',5,21600,'available',2021,'Blue'],
            ['Audi A4','Audi','A4','luxury',6,'petrol','automatic',5,20400,'available',2022,'Gray'],
            ['Mercedes E-Class','Mercedes','E-Class','luxury',6,'petrol','automatic',5,27600,'available',2023,'Black'],
            // Electric (cat 7)
            ['Tesla Model 3','Tesla','Model 3','electric',7,'electric','automatic',5,19200,'available',2023,'White'],
            ['Tesla Model Y','Tesla','Model Y','electric',7,'electric','automatic',5,20400,'available',2023,'Red'],
            ['Hyundai Ioniq 5','Hyundai','Ioniq 5','electric',7,'electric','automatic',5,18000,'available',2022,'Silver'],
            ['Kia EV6','Kia','EV6','electric',7,'electric','automatic',5,18600,'available',2022,'Black'],
            ['BMW iX','BMW','iX','electric',7,'electric','automatic',5,23400,'available',2023,'White'],
            ['Nissan Leaf','Nissan','Leaf','electric',7,'electric','automatic',5,10800,'available',2022,'Blue'],
            // Convertibles (cat 8)
            ['Ford Mustang GT','Ford','Mustang GT','convert',8,'petrol','automatic',4,21000,'available',2022,'Red'],
            ['Chevrolet Camaro','Chevrolet','Camaro','convert',8,'petrol','automatic',4,19800,'available',2021,'Yellow'],
            ['BMW Z4','BMW','Z4','convert',8,'petrol','automatic',2,23400,'available',2022,'Silver'],
            ['Mazda MX-5','Mazda','MX-5','convert',8,'petrol','manual',2,14400,'available',2021,'Red'],
            ['Porsche Boxster','Porsche','Boxster','convert',8,'petrol','automatic',2,30000,'available',2022,'Silver'],
            // Extra mixed
            ['Toyota Corolla','Toyota','Corolla','extra',1,'petrol','automatic',5,7440,'available',2021,'White'],
            ['Honda Civic','Honda','Civic','extra',1,'petrol','automatic',5,7800,'available',2022,'Black'],
            ['Kia Cerato','Kia','Cerato','extra',1,'petrol','automatic',5,7200,'available',2021,'Blue'],
            ['Mitsubishi Outlander','Mitsubishi','Outlander','extra',2,'petrol','automatic',7,13800,'available',2022,'Gray'],
            ['Toyota Land Cruiser','Toyota','Land Cruiser','extra',2,'diesel','automatic',8,24000,'available',2022,'White'],
        ];

        $vehicleImages = [
            // Sedans
            'Toyota Camry'          => 'vehicles/camry.jpg',
            'Honda Accord'          => 'vehicles/accord.jpg',
            'Nissan Altima'         => 'vehicles/altima.jpg',
            'Ford Fusion'           => 'vehicles/fusion.jpg',
            'Hyundai Sonata'        => 'vehicles/sonata.jpg',
            'Mazda 6'               => 'vehicles/mazda6.jpg',
            'Chevrolet Malibu'      => 'vehicles/malibu.jpg',
            // SUVs
            'Toyota RAV4'           => 'vehicles/rav4.jpg',
            'Honda CR-V'            => 'vehicles/crv.jpg',
            'Ford Explorer'         => 'vehicles/explorer.jpg',
            'Chevrolet Equinox'     => 'vehicles/equinox.jpg',
            'Hyundai Tucson'        => 'vehicles/tucson.jpg',
            'Nissan Rogue'          => 'vehicles/rogue.jpg',
            'Mazda CX-5'            => 'vehicles/suv1.jpg',
            'Kia Sportage'          => 'vehicles/suv2.jpg',
            // Hatchbacks
            'Toyota Yaris'          => 'vehicles/yaris.jpg',
            'Honda Jazz'            => 'vehicles/jazz.jpg',
            'Hyundai i20'           => 'vehicles/i20.jpg',
            'Ford Fiesta'           => 'vehicles/fiesta.jpg',
            'Volkswagen Polo'       => 'vehicles/polo.jpg',
            // Pickup Trucks
            'Ford F-150'            => 'vehicles/pickup1.jpg',
            'Toyota Hilux'          => 'vehicles/hilux.jpg',
            'Nissan Navara'         => 'vehicles/navara.jpg',
            'Chevrolet Colorado'    => 'vehicles/colorado.jpg',
            'Mitsubishi Triton'     => 'vehicles/triton.jpg',
            // Minivans
            'Toyota Sienna'         => 'vehicles/sienna.jpg',
            'Honda Odyssey'         => 'vehicles/odyssey.jpg',
            'Chrysler Pacifica'     => 'vehicles/pacific.jpg',
            // Luxury
            'BMW 5 Series'          => 'vehicles/bmw5.jpg',
            'Mercedes C-Class'      => 'vehicles/mercC.jpg',
            'Audi A6'               => 'vehicles/audiA6.jpg',
            'Lexus ES'              => 'vehicles/lexus.jpg',
            'BMW 3 Series'          => 'vehicles/bmw3.jpg',
            'Audi A4'               => 'vehicles/audiA4.jpg',
            'Mercedes E-Class'      => 'vehicles/mercE.jpg',
            // Electric
            'Tesla Model 3'         => 'vehicles/tesla3.jpg',
            'Tesla Model Y'         => 'vehicles/teslaY.jpg',
            'Hyundai Ioniq 5'       => 'vehicles/ioniq5.jpg',
            'Kia EV6'               => 'vehicles/ev6.jpg',
            'BMW iX'                => 'vehicles/bmwix.jpg',
            'Nissan Leaf'           => 'vehicles/leaf.jpg',
            // Convertibles
            'Ford Mustang GT'       => 'vehicles/mustang.jpg',
            'Chevrolet Camaro'      => 'vehicles/camaro.jpg',
            'BMW Z4'                => 'vehicles/bmwz4.jpg',
            'Mazda MX-5'            => 'vehicles/convert2.jpg',
            'Porsche Boxster'       => 'vehicles/boxster.jpg',
            // Extra
            'Toyota Corolla'        => 'vehicles/corolla.jpg',
            'Honda Civic'           => 'vehicles/civic.jpg',
            'Kia Cerato'            => 'vehicles/cerato.jpg',
            'Mitsubishi Outlander'  => 'vehicles/outlander.jpg',
            'Toyota Land Cruiser'   => 'vehicles/landcruiser.jpg',
        ];

        $fallbackImages = [
            'sedan'  => 'vehicles/sedan1.jpg',
            'suv'    => 'vehicles/suv1.jpg',
            'hatch'  => 'vehicles/hatch1.jpg',
            'pickup' => 'vehicles/pickup1.jpg',
            'minivan'=> 'vehicles/minivan1.jpg',
            'luxury' => 'vehicles/luxury1.jpg',
            'electric'=> 'vehicles/electric1.jpg',
            'convert'=> 'vehicles/convert1.jpg',
            'extra'  => 'vehicles/sedan2.jpg',
        ];

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

            $img = $vehicleImages[$name] ?? ($fallbackImages[$type] ?? 'vehicles/sedan1.jpg');

            DB::insert("
                INSERT INTO vehicle_images (vehicle_id, image, is_primary, sort_order, created_at, updated_at)
                VALUES (?, ?, 1, 0, NOW(), NOW())
            ", [$vehicleId, $img]);
        }
    }
}
