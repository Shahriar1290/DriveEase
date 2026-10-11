<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
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

        $payments = Payment::with(['booking.user:id,name', 'booking.vehicle:id,vehicle_name'])
            ->where('payment_status', 'completed')
            ->whereBetween('payment_date', [$from, $to . ' 23:59:59'])
            ->orderByDesc('payment_date')
            ->get();

        $total = $payments->sum('amount');

        $byMethod = $payments->groupBy('payment_method')->map(fn($p) => [
            'count' => $p->count(),
            'total' => $p->sum('amount'),
        ])->toArray();

        $daily = $payments->groupBy(fn($p) => $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('Y-m-d') : 'Unknown')
            ->map(fn($p) => $p->sum('amount'))
            ->sortKeys()
            ->toArray();

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.revenue-pdf', ['payments' => $payments, 'total' => $total, 'byMethod' => $byMethod, 'from' => $from, 'to' => $to]);
            return $pdf->download('revenue-report.pdf');
        }

        return view('admin.reports.revenue', compact('payments', 'total', 'byMethod', 'daily', 'from', 'to'));
    }

    public function vehicles(Request $request)
    {
        $vehicles = Vehicle::with(['category:id,category_name'])
            ->withCount(['bookings as completed_bookings' => fn($q) => $q->where('booking_status', 'completed')])
            ->withSum(['bookings as total_revenue' => fn($q) => $q->where('booking_status', 'completed')], 'final_amount')
            ->with('firstImage')
            ->orderByDesc('total_rentals')
            ->get();

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.vehicles-pdf', compact('vehicles'));
            return $pdf->download('vehicle-report.pdf');
        }

        return view('admin.reports.vehicles', compact('vehicles'));
    }

    public function customers(Request $request)
    {
        $customers = User::where('role', 'customer')
            ->withCount('bookings')
            ->withSum(['bookings as total_spent' => fn($q) => $q->where('booking_status', 'completed')], 'final_amount')
            ->orderByDesc('total_spent')
            ->get();

        return view('admin.reports.customers', compact('customers'));
    }

    public function maintenance(Request $request)
    {
        $from = $request->get('from', now()->startOfYear()->format('Y-m-d'));
        $to   = $request->get('to', now()->format('Y-m-d'));

        $records = \App\Models\MaintenanceRecord::with('vehicle:id,vehicle_name')
            ->whereBetween('maintenance_date', [$from, $to])
            ->orderByDesc('maintenance_date')
            ->get();

        $totalCost = $records->sum('cost');

        $byType = $records->groupBy('maintenance_type')->map(fn($r) => [
            'count' => $r->count(),
            'cost' => $r->sum('cost'),
        ])->toArray();

        return view('admin.reports.maintenance', compact('records', 'totalCost', 'byType', 'from', 'to'));
    }
}
