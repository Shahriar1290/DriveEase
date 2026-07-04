<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $stats = [
            'total_bookings' => DB::select(
                "SELECT COUNT(*) AS total FROM bookings WHERE user_id = ?", [$userId]
            )[0]->total,

            'active_bookings' => DB::select(
                "SELECT COUNT(*) AS total FROM bookings WHERE user_id = ? AND booking_status IN ('approved','active')", [$userId]
            )[0]->total,

            'completed_bookings' => DB::select(
                "SELECT COUNT(*) AS total FROM bookings WHERE user_id = ? AND booking_status = 'completed'", [$userId]
            )[0]->total,

            'pending_bookings' => DB::select(
                "SELECT COUNT(*) AS total FROM bookings WHERE user_id = ? AND booking_status = 'pending'", [$userId]
            )[0]->total,

            'wishlist_count' => DB::select(
                "SELECT COUNT(*) AS total FROM wishlists WHERE user_id = ?", [$userId]
            )[0]->total,

            'unread_notifications' => DB::select(
                "SELECT COUNT(*) AS total FROM notifications WHERE user_id = ? AND status = 'unread'", [$userId]
            )[0]->total,
        ];

        $recentBookings = DB::select("
            SELECT b.*, v.vehicle_name,
                   (SELECT vi.image FROM vehicle_images vi
                     WHERE vi.vehicle_id = v.id
                     ORDER BY vi.is_primary DESC, vi.sort_order ASC LIMIT 1) AS primary_image
            FROM bookings b
            INNER JOIN vehicles v ON v.id = b.vehicle_id
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC
            LIMIT 5
        ", [$userId]);

        $notifications = DB::select("
            SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10
        ", [$userId]);

        return view('customer.dashboard.index', compact('stats', 'recentBookings', 'notifications'));
    }
}
