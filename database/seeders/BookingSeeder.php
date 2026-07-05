<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $customerIds = array_map(fn($r) => $r->id, DB::select("SELECT id FROM users WHERE role = 'customer'"));
        $vehicles    = DB::select("SELECT id, price_per_day FROM vehicles");

        $statuses = ['pending', 'approved', 'rejected', 'cancelled', 'completed', 'completed', 'completed', 'approved'];
        $methods  = ['cash', 'card', 'mobile_banking', 'bank_transfer'];

        for ($i = 0; $i < 100; $i++) {
            $userId  = $customerIds[array_rand($customerIds)];
            $vehicle = $vehicles[array_rand($vehicles)];

            $pickupDays = rand(-90, 30);
            $pickup     = now()->addDays($pickupDays)->format('Y-m-d');
            $return     = now()->addDays($pickupDays + rand(1, 14))->format('Y-m-d');
            $days       = \Carbon\Carbon::parse($pickup)->diffInDays(\Carbon\Carbon::parse($return));
            $total      = $days * $vehicle->price_per_day;
            $tax        = $total * 0.05;
            $final      = $total + $tax;
            $status     = $statuses[array_rand($statuses)];
            $bookingNumber = 'BK-' . strtoupper(Str::random(8));

            DB::insert("
                INSERT INTO bookings (
                    booking_number, user_id, vehicle_id, pickup_date, return_date, total_days,
                    price_per_day, total_cost, discount, tax, final_amount,
                    pickup_location, return_location, booking_status, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, 'Dhaka Airport', 'Dhaka Airport', ?, NOW(), NOW())
            ", [
                $bookingNumber, $userId, $vehicle->id, $pickup, $return, $days,
                $vehicle->price_per_day, $total, $tax, $final, $status,
            ]);

            $bookingId = DB::getPdo()->lastInsertId();

            $paymentStatus = match ($status) {
                'completed', 'approved' => 'completed',
                'rejected', 'cancelled' => 'failed',
                default                 => 'pending',
            };

            $paymentDate = $paymentStatus === 'completed'
                ? now()->addDays($pickupDays)->subDays(rand(0, 2))->format('Y-m-d H:i:s')
                : null;

            DB::insert("
                INSERT INTO payments (
                    transaction_id, booking_id, amount, payment_method, payment_status,
                    payment_date, receipt_number, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                'TXN-' . strtoupper(Str::random(10)),
                $bookingId,
                $final,
                $methods[array_rand($methods)],
                $paymentStatus,
                $paymentDate,
                'RCP-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            ]);
        }
    }
}
