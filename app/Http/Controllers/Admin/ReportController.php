<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function revenue(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->get('to', now()->endOfMonth()->format('Y-m-d'));

        $payments = DB::select("
            SELECT p.*, b.booking_number, u.name AS user_name, v.vehicle_name
            FROM payments p
            INNER JOIN bookings b ON b.id = p.booking_id
            INNER JOIN users u ON u.id = b.user_id
            INNER JOIN vehicles v ON v.id = b.vehicle_id
            WHERE p.payment_status = 'completed'
              AND p.payment_date BETWEEN ? AND ?
            ORDER BY p.payment_date DESC
        ", [$from, $to . ' 23:59:59']);

        $total = array_sum(array_column($payments, 'amount'));

        // Group by payment method
        $byMethod = [];
        foreach ($payments as $p) {
            $byMethod[$p->payment_method]['count']  = ($byMethod[$p->payment_method]['count'] ?? 0) + 1;
            $byMethod[$p->payment_method]['total']  = ($byMethod[$p->payment_method]['total'] ?? 0) + $p->amount;
        }

        // Group by day for chart
        $daily = [];
        foreach ($payments as $p) {
            $day = substr($p->payment_date, 0, 10);
            $daily[$day] = ($daily[$day] ?? 0) + $p->amount;
        }
        ksort($daily);

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.revenue-pdf', compact('payments', 'total', 'byMethod', 'from', 'to'));
            return $pdf->download('revenue-report.pdf');
        }

        return view('admin.reports.revenue', compact('payments', 'total', 'byMethod', 'daily', 'from', 'to'));
    }

    public function vehicles(Request $request)
    {
        $vehicles = DB::select("
            SELECT v.*, c.category_name,
                   (SELECT vi.image FROM vehicle_images vi
                     WHERE vi.vehicle_id = v.id
                     ORDER BY vi.is_primary DESC, vi.sort_order ASC LIMIT 1) AS primary_image,
                   (SELECT COUNT(*) FROM bookings b WHERE b.vehicle_id = v.id AND b.booking_status = 'completed') AS completed_bookings,
                   (SELECT COALESCE(SUM(b.final_amount),0) FROM bookings b WHERE b.vehicle_id = v.id AND b.booking_status = 'completed') AS total_revenue
            FROM vehicles v
            INNER JOIN vehicle_categories c ON c.id = v.category_id
            ORDER BY v.total_rentals DESC
        ");

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.vehicles-pdf', compact('vehicles'));
            return $pdf->download('vehicle-report.pdf');
        }

        return view('admin.reports.vehicles', compact('vehicles'));
    }

    public function customers(Request $request)
    {
        $customers = DB::select("
            SELECT u.*,
                   (SELECT COUNT(*) FROM bookings b WHERE b.user_id = u.id) AS bookings_count,
                   (SELECT COALESCE(SUM(b.final_amount),0) FROM bookings b WHERE b.user_id = u.id AND b.booking_status = 'completed') AS total_spent
            FROM users u
            WHERE u.role = 'customer'
            ORDER BY total_spent DESC
        ");

        return view('admin.reports.customers', compact('customers'));
    }

    public function maintenance(Request $request)
    {
        $from = $request->get('from', now()->startOfYear()->format('Y-m-d'));
        $to   = $request->get('to', now()->format('Y-m-d'));

        $records = DB::select("
            SELECT m.*, v.vehicle_name
            FROM maintenance_records m
            INNER JOIN vehicles v ON v.id = m.vehicle_id
            WHERE m.maintenance_date BETWEEN ? AND ?
            ORDER BY m.maintenance_date DESC
        ", [$from, $to]);

        $totalCost = array_sum(array_column($records, 'cost'));

        $byType = [];
        foreach ($records as $r) {
            $byType[$r->maintenance_type]['count'] = ($byType[$r->maintenance_type]['count'] ?? 0) + 1;
            $byType[$r->maintenance_type]['cost']  = ($byType[$r->maintenance_type]['cost'] ?? 0) + $r->cost;
        }

        return view('admin.reports.maintenance', compact('records', 'totalCost', 'byType', 'from', 'to'));
    }
}
