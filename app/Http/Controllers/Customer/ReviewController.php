<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;

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

        $hasBooking = Booking::where('user_id', $userId)
            ->where('vehicle_id', $request->vehicle_id)
            ->where('booking_status', 'completed')
            ->exists();

        if (!$hasBooking) {
            return back()->with('error', 'You can only review vehicles you have rented.');
        }

        if (Review::where('user_id', $userId)->where('vehicle_id', $request->vehicle_id)->exists()) {
            return back()->with('error', 'You have already reviewed this vehicle.');
        }

        Review::create([
            'user_id' => $userId,
            'vehicle_id' => $request->vehicle_id,
            'booking_id' => $request->booking_id,
            'rating' => $request->rating,
            'review' => $request->review,
            'is_approved' => false,
        ]);

        return back()->with('success', 'Review submitted! It will appear after approval.');
    }
}
