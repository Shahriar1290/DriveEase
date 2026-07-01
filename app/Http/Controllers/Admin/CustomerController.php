<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 15;
        $page    = max(1, (int) $request->get('page', 1));
        $offset  = ($page - 1) * $perPage;

        $where    = ["role = 'customer'"];
        $bindings = [];

        if ($s = $request->search) {
            $where[]    = "(name LIKE ? OR email LIKE ?)";
            $like       = "%{$s}%";
            $bindings[] = $like; $bindings[] = $like;
        }
        if ($request->status === 'active') {
            $where[] = "is_active = 1";
        }
        if ($request->status === 'inactive') {
            $where[] = "is_active = 0";
        }
        $whereSql = implode(' AND ', $where);

        $total = DB::select("SELECT COUNT(*) AS total FROM users WHERE {$whereSql}", $bindings)[0]->total;

        $rows = DB::select("
            SELECT u.*, (SELECT COUNT(*) FROM bookings b WHERE b.user_id = u.id) AS bookings_count
            FROM users u
            WHERE {$whereSql}
            ORDER BY u.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $bindings);

        $customers = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.customers.index', compact('customers'));
    }

    public function show(int $id)
    {
        $customer = DB::select("SELECT * FROM users WHERE id = ? AND role = 'customer' LIMIT 1", [$id]);
        abort_if(empty($customer), 404);
        $customer = $customer[0];

        $bookings = DB::select("
            SELECT b.*, v.vehicle_name
            FROM bookings b
            INNER JOIN vehicles v ON v.id = b.vehicle_id
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC
            LIMIT 10
        ", [$id]);

        $stats = [
            'total_bookings'     => DB::select("SELECT COUNT(*) AS total FROM bookings WHERE user_id = ?", [$id])[0]->total,
            'completed_bookings' => DB::select("SELECT COUNT(*) AS total FROM bookings WHERE user_id = ? AND booking_status = 'completed'", [$id])[0]->total,
            'total_spent'        => DB::select("SELECT COALESCE(SUM(final_amount),0) AS total FROM bookings WHERE user_id = ? AND booking_status = 'completed'", [$id])[0]->total,
        ];

        return view('admin.customers.show', compact('customer', 'bookings', 'stats'));
    }

    public function toggleStatus(int $id)
    {
        $customer = DB::select("SELECT * FROM users WHERE id = ? AND role = 'customer' LIMIT 1", [$id]);
        abort_if(empty($customer), 404);
        $customer = $customer[0];

        $newStatus = $customer->is_active ? 0 : 1;
        DB::update("UPDATE users SET is_active = ?, updated_at = NOW() WHERE id = ?", [$newStatus, $id]);

        $statusText = $newStatus ? 'activated' : 'deactivated';
        return back()->with('success', "Customer account {$statusText}.");
    }
}
