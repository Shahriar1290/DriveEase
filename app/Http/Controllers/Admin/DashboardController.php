<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_vehicles'     => DB::select("SELECT COUNT(*) AS total FROM vehicles")[0]->total,
            'available_vehicles' => DB::select("SELECT COUNT(*) AS total FROM vehicles WHERE status = 'available'")[0]->total,
            'total_customers'    => DB::select("SELECT COUNT(*) AS total FROM users WHERE role = 'customer'")[0]->total,
            'total_bookings'     => DB::select("SELECT COUNT(*) AS total FROM bookings")[0]->total,
            'pending_bookings'   => DB::select("SELECT COUNT(*) AS total FROM bookings WHERE booking_status = 'pending'")[0]->total,
            'active_bookings'    => DB::select("SELECT COUNT(*) AS total FROM bookings WHERE booking_status IN ('approved','active')")[0]->total,
            'completed_bookings' => DB::select("SELECT COUNT(*) AS total FROM bookings WHERE booking_status = 'completed'")[0]->total,
            'total_revenue'      => DB::select("SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE payment_status = 'completed'")[0]->total,
            'monthly_revenue'    => DB::select("
                SELECT COALESCE(SUM(amount),0) AS total FROM payments
                WHERE payment_status = 'completed' AND MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())
            ")[0]->total,
            'pending_reviews'    => DB::select("SELECT COUNT(*) AS total FROM reviews WHERE is_approved = 0")[0]->total,
        ];

        // Monthly revenue for last 12 months
        $monthlyRows = DB::select("
            SELECT YEAR(payment_date) AS yr, MONTH(payment_date) AS mo, SUM(amount) AS total
            FROM payments
            WHERE payment_status = 'completed' AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
            GROUP BY YEAR(payment_date), MONTH(payment_date)
            ORDER BY yr, mo
        ");

        $monthlyMap = [];
        foreach ($monthlyRows as $r) {
            $label = date('M Y', mktime(0, 0, 0, $r->mo, 1, $r->yr));
            $monthlyMap[$label] = $r->total;
        }

        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $label = now()->subMonths($i)->format('M Y');
            $chartData[$label] = $monthlyMap[$label] ?? 0;
        }

        // Booking status distribution
        $bookingStatRows = DB::select("
            SELECT booking_status, COUNT(*) AS total FROM bookings GROUP BY booking_status
        ");
        $bookingStats = [];
        foreach ($bookingStatRows as $r) {
            $bookingStats[$r->booking_status] = $r->total;
        }

        // Top 5 most rented vehicles
        $topVehicles = DB::select("
            SELECT v.*,
                   (SELECT vi.image FROM vehicle_images vi
                     WHERE vi.vehicle_id = v.id
                     ORDER BY vi.is_primary DESC, vi.sort_order ASC LIMIT 1) AS primary_image
            FROM vehicles v
            ORDER BY v.total_rentals DESC
            LIMIT 5
        ");

        // Recent bookings
        $recentBookings = DB::select("
            SELECT b.*, u.name AS user_name, v.vehicle_name
            FROM bookings b
            INNER JOIN users u ON u.id = b.user_id
            INNER JOIN vehicles v ON v.id = b.vehicle_id
            ORDER BY b.created_at DESC
            LIMIT 10
        ");

        // Recent customers
        $recentCustomers = DB::select("
            SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC LIMIT 5
        ");

        // Upcoming maintenance
        $upcomingMaintenance = DB::select("
            SELECT m.*, v.vehicle_name
            FROM maintenance_records m
            INNER JOIN vehicles v ON v.id = m.vehicle_id
            WHERE m.status = 'scheduled' AND m.maintenance_date >= CURDATE()
            ORDER BY m.maintenance_date ASC
            LIMIT 5
        ");

        return view('admin.dashboard.index', compact(
            'stats', 'chartData', 'bookingStats',
            'topVehicles', 'recentBookings', 'recentCustomers', 'upcomingMaintenance'
        ));
    }
}
