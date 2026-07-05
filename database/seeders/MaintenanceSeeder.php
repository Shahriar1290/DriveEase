<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = DB::select("SELECT id FROM vehicles ORDER BY RAND() LIMIT 20");
        $types    = ['Oil Change', 'Tire Rotation', 'Brake Service', 'Engine Tune-up', 'Battery Replacement', 'AC Service', 'Full Service'];
        $statuses = ['scheduled', 'in_progress', 'completed', 'completed', 'completed'];

        foreach ($vehicles as $vehicle) {
            $count = rand(1, 3);
            for ($i = 0; $i < $count; $i++) {
                $date   = now()->subDays(rand(0, 180))->format('Y-m-d');
                $next   = now()->addMonths(rand(3, 6))->format('Y-m-d');
                $status = $statuses[array_rand($statuses)];
                $type   = $types[array_rand($types)];

                DB::insert("
                    INSERT INTO maintenance_records (
                        vehicle_id, maintenance_date, next_maintenance_date, maintenance_type,
                        description, cost, status, mechanic_name, service_center, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ", [
                    $vehicle->id, $date, $next, $type,
                    'Routine ' . strtolower($type) . ' service performed.',
                    rand(50, 800), $status,
                    'Tech ' . rand(1, 10),
                    'DriveEase Service Center ' . rand(1, 5),
                ]);
            }
        }
    }
}
