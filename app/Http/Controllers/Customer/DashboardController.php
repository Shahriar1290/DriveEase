<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Wishlist;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $stats = [
            'total_bookings' => Booking::where('user_id', $userId)->count(),
            'active_bookings' => Booking::where('user_id', $userId)->whereIn('booking_status', ['approved', 'active'])->count(),
            'completed_bookings' => Booking::where('user_id', $userId)->where('booking_status', 'completed')->count(),
            'pending_bookings' => Booking::where('user_id', $userId)->where('booking_status', 'pending')->count(),
            'wishlist_count' => Wishlist::where('user_id', $userId)->count(),
            'unread_notifications' => Notification::where('user_id', $userId)->where('status', 'unread')->count(),
        ];

        $recentBookings = Booking::with(['vehicle:id,vehicle_name', 'vehicle.firstImage'])
            ->where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        $notifications = Notification::where('user_id', $userId)
            ->latest()
            ->limit(10)
            ->get();

        return view('customer.dashboard.index', compact('stats', 'recentBookings', 'notifications'));
    }
}
