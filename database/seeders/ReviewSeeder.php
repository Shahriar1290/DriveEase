<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $comments = [
            "Excellent vehicle! Very clean and well-maintained. Would definitely rent again.",
            "Great experience overall. The car was comfortable and fuel-efficient.",
            "Good value for money. The booking process was smooth and hassle-free.",
            "Amazing service! The vehicle was exactly as described.",
            "Very satisfied with my rental. The car performed great on the highway.",
            "The vehicle was clean and in perfect condition. Highly recommend!",
            "Professional service and a fantastic vehicle. Five stars!",
            "Smooth ride, great AC, and easy pickup. Loved the experience.",
            "Rented for a week - the car was reliable and comfortable.",
            "Decent vehicle. A bit older but well-maintained and clean.",
        ];

        $completedBookings = DB::select("
            SELECT id, user_id, vehicle_id FROM bookings
            WHERE booking_status = 'completed'
            ORDER BY RAND()
            LIMIT 50
        ");

        $touchedVehicleIds = [];

        foreach ($completedBookings as $booking) {
            $alreadyReviewed = DB::select("
                SELECT id FROM reviews WHERE user_id = ? AND vehicle_id = ? LIMIT 1
            ", [$booking->user_id, $booking->vehicle_id]);

            if (empty($alreadyReviewed)) {
                DB::insert("
                    INSERT INTO reviews (user_id, vehicle_id, booking_id, rating, review, is_approved, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
                ", [
                    $booking->user_id,
                    $booking->vehicle_id,
                    $booking->id,
                    rand(3, 5),
                    $comments[array_rand($comments)],
                    rand(0, 1),
                ]);

                $touchedVehicleIds[$booking->vehicle_id] = true;
            }
        }

        // Recalculate average_rating for every vehicle that received a review,
        // using raw SQL aggregation instead of an Eloquent model method.
        foreach (array_keys($touchedVehicleIds) as $vehicleId) {
            $avgRow = DB::select("
                SELECT COALESCE(AVG(rating), 0) AS avg_rating
                FROM reviews
                WHERE vehicle_id = ? AND is_approved = 1
            ", [$vehicleId]);

            $avg = round($avgRow[0]->avg_rating ?? 0, 2);

            DB::update("UPDATE vehicles SET average_rating = ?, updated_at = NOW() WHERE id = ?", [$avg, $vehicleId]);
        }
    }
}
