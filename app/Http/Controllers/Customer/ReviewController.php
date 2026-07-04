<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|integer',
            'booking_id' => 'nullable|integer',
            'rating'     => 'required|integer|min:1|max:5',
            'review'     => 'required|string|min:10|max:1000',
        ]);

        $userId = auth()->id();

        // Verify the user completed a booking for this vehicle
        $hasBooking = DB::select("
            SELECT id FROM bookings
            WHERE user_id = ? AND vehicle_id = ? AND booking_status = 'completed'
            LIMIT 1
        ", [$userId, $request->vehicle_id]);

        if (empty($hasBooking)) {
            return back()->with('error', 'You can only review vehicles you have rented.');
        }

        // Prevent duplicate review
        $alreadyReviewed = DB::select("
            SELECT id FROM reviews WHERE user_id = ? AND vehicle_id = ? LIMIT 1
        ", [$userId, $request->vehicle_id]);

        if (!empty($alreadyReviewed)) {
            return back()->with('error', 'You have already reviewed this vehicle.');
        }

        DB::insert("
            INSERT INTO reviews (user_id, vehicle_id, booking_id, rating, review, is_approved, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, 0, NOW(), NOW())
        ", [$userId, $request->vehicle_id, $request->booking_id, $request->rating, $request->review]);

        return back()->with('success', 'Review submitted! It will appear after approval.');
    }
}
