<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MaintenanceRecord;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_vehicles'     => Vehicle::count(),
            'available_vehicles' => Vehicle::where('status', 'available')->count(),
            'total_customers'    => User::where('role', 'customer')->count(),
            'total_bookings'     => Booking::count(),
            'pending_bookings'   => Booking::where('booking_status', 'pending')->count(),
            'active_bookings'    => Booking::whereIn('booking_status', ['approved', 'active'])->count(),
            'completed_bookings' => Booking::where('booking_status', 'completed')->count(),
            'total_revenue'      => Payment::where('payment_status', 'completed')->sum('amount'),
            'monthly_revenue'    => Payment::where('payment_status', 'completed')
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
            'pending_reviews'    => Review::where('is_approved', false)->count(),
        ];

        $monthlyMap = Payment::where('payment_status', 'completed')
            ->where('payment_date', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw('YEAR(payment_date) AS yr, MONTH(payment_date) AS mo, SUM(amount) AS total')
            ->groupByRaw('YEAR(payment_date), MONTH(payment_date)')
            ->orderByRaw('yr, mo')
            ->get()
            ->mapWithKeys(fn($row) => [
                date('M Y', mktime(0, 0, 0, (int) $row->mo, 1, (int) $row->yr)) => $row->total
            ]);

        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $label = now()->subMonths($i)->format('M Y');
            $chartData[$label] = $monthlyMap[$label] ?? 0;
        }

        $bookingStats = Booking::selectRaw('booking_status, COUNT(*) AS total')
            ->groupBy('booking_status')
            ->pluck('total', 'booking_status')
            ->toArray();

        $topVehicles = Vehicle::with('firstImage')
            ->orderByDesc('total_rentals')
            ->limit(5)
            ->get();

        $recentBookings = Booking::with(['user:id,name', 'vehicle:id,vehicle_name'])
            ->latest()
            ->limit(10)
            ->get();

        $recentCustomers = User::where('role', 'customer')
            ->latest()
            ->limit(5)
            ->get();

        $upcomingMaintenance = MaintenanceRecord::with('vehicle:id,vehicle_name')
            ->where('status', 'scheduled')
            ->where('maintenance_date', '>=', now()->toDateString())
            ->orderBy('maintenance_date')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'stats', 'chartData', 'bookingStats',
            'topVehicles', 'recentBookings', 'recentCustomers', 'upcomingMaintenance'
        ));
    }
}
