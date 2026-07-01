<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 15;
        $page    = max(1, (int) $request->get('page', 1));
        $offset  = ($page - 1) * $perPage;

        $where    = ['1=1'];
        $bindings = [];

        if ($s = $request->search) {
            $where[]    = "(p.transaction_id LIKE ? OR b.booking_number LIKE ?)";
            $like       = "%{$s}%";
            $bindings[] = $like; $bindings[] = $like;
        }
        if ($status = $request->status) {
            $where[]    = "p.payment_status = ?";
            $bindings[] = $status;
        }
        if ($method = $request->method) {
            $where[]    = "p.payment_method = ?";
            $bindings[] = $method;
        }
        $whereSql = implode(' AND ', $where);

        $total = DB::select("
            SELECT COUNT(*) AS total FROM payments p
            INNER JOIN bookings b ON b.id = p.booking_id
            WHERE {$whereSql}
        ", $bindings)[0]->total;

        $rows = DB::select("
            SELECT p.*, b.booking_number, u.name AS user_name, v.vehicle_name
            FROM payments p
            INNER JOIN bookings b ON b.id = p.booking_id
            INNER JOIN users u ON u.id = b.user_id
            INNER JOIN vehicles v ON v.id = b.vehicle_id
            WHERE {$whereSql}
            ORDER BY p.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $bindings);

        $payments = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $totalRevenue = DB::select("SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE payment_status = 'completed'")[0]->total;

        return view('admin.payments.index', compact('payments', 'totalRevenue'));
    }

    public function show(int $id)
    {
        $payment = DB::select("
            SELECT p.*, b.booking_number, b.id AS booking_id, u.name AS user_name
            FROM payments p
            INNER JOIN bookings b ON b.id = p.booking_id
            INNER JOIN users u ON u.id = b.user_id
            WHERE p.id = ? LIMIT 1
        ", [$id]);
        abort_if(empty($payment), 404);

        return view('admin.payments.show', ['payment' => $payment[0]]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['payment_status' => 'required|in:pending,completed,failed,refunded']);

        if ($request->payment_status === 'completed') {
            DB::update("
                UPDATE payments SET payment_status = ?, payment_date = NOW(), updated_at = NOW() WHERE id = ?
            ", [$request->payment_status, $id]);
        } else {
            DB::update("
                UPDATE payments SET payment_status = ?, updated_at = NOW() WHERE id = ?
            ", [$request->payment_status, $id]);
        }

        return back()->with('success', 'Payment status updated.');
    }
}
