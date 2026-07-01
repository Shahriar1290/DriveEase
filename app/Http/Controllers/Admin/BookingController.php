<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 15;
        $page    = max(1, (int) $request->get('page', 1));
        $offset  = ($page - 1) * $perPage;

        $where    = ['1=1'];
        $bindings = [];

        if ($s = $request->search) {
            $where[]    = "(b.booking_number LIKE ? OR u.name LIKE ?)";
            $like       = "%{$s}%";
            $bindings[] = $like; $bindings[] = $like;
        }
        if ($status = $request->status) {
            $where[]    = "b.booking_status = ?";
            $bindings[] = $status;
        }
        if ($request->date_from) {
            $where[]    = "DATE(b.created_at) >= ?";
            $bindings[] = $request->date_from;
        }
        if ($request->date_to) {
            $where[]    = "DATE(b.created_at) <= ?";
            $bindings[] = $request->date_to;
        }
        $whereSql = implode(' AND ', $where);

        $total = DB::select("
            SELECT COUNT(*) AS total FROM bookings b
            INNER JOIN users u ON u.id = b.user_id
            WHERE {$whereSql}
        ", $bindings)[0]->total;

        $rows = DB::select("
            SELECT b.*, u.name AS user_name, u.email AS user_email, v.vehicle_name
            FROM bookings b
            INNER JOIN users u ON u.id = b.user_id
            INNER JOIN vehicles v ON v.id = b.vehicle_id
            WHERE {$whereSql}
            ORDER BY b.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $bindings);

        $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(int $id)
    {
        $booking = DB::select("
            SELECT b.*, u.name AS user_name, u.email AS user_email, u.phone AS user_phone,
                   v.vehicle_name, v.registration_number
            FROM bookings b
            INNER JOIN users u ON u.id = b.user_id
            INNER JOIN vehicles v ON v.id = b.vehicle_id
            WHERE b.id = ? LIMIT 1
        ", [$id]);
        abort_if(empty($booking), 404);
        $booking = $booking[0];

        $vehicleImage = DB::select("
            SELECT image FROM vehicle_images WHERE vehicle_id = ?
            ORDER BY is_primary DESC, sort_order ASC LIMIT 1
        ", [$booking->vehicle_id]);
        $booking->primary_image = $vehicleImage[0]->image ?? null;

        $payment = DB::select("SELECT * FROM payments WHERE booking_id = ? LIMIT 1", [$id]);
        $payment = $payment[0] ?? null;

        return view('admin.bookings.show', compact('booking', 'payment'));
    }

    public function approve(int $id)
    {
        $booking = DB::select("SELECT * FROM bookings WHERE id = ? LIMIT 1", [$id]);
        abort_if(empty($booking), 404);
        $booking = $booking[0];

        DB::beginTransaction();
        try {
            DB::update("UPDATE bookings SET booking_status = 'approved', updated_at = NOW() WHERE id = ?", [$id]);
            DB::update("UPDATE payments SET payment_status = 'completed', payment_date = NOW(), updated_at = NOW() WHERE booking_id = ?", [$id]);
            DB::update("UPDATE vehicles SET status = 'rented', total_rentals = total_rentals + 1, updated_at = NOW() WHERE id = ?", [$booking->vehicle_id]);

            DB::insert("
                INSERT INTO notifications (user_id, title, message, type, link, status, created_at, updated_at)
                VALUES (?, 'Booking Approved', ?, 'success', ?, 'unread', NOW(), NOW())
            ", [
                $booking->user_id,
                "Your booking #{$booking->booking_number} has been approved!",
                route('customer.bookings.show', $id),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve booking.');
        }

        return back()->with('success', 'Booking approved successfully.');
    }

    public function reject(Request $request, int $id)
    {
        $request->validate(['admin_notes' => 'required|string|max:500']);

        $booking = DB::select("SELECT * FROM bookings WHERE id = ? LIMIT 1", [$id]);
        abort_if(empty($booking), 404);
        $booking = $booking[0];

        DB::update("
            UPDATE bookings SET booking_status = 'rejected', admin_notes = ?, updated_at = NOW() WHERE id = ?
        ", [$request->admin_notes, $id]);

        DB::update("UPDATE payments SET payment_status = 'failed', updated_at = NOW() WHERE booking_id = ?", [$id]);

        DB::insert("
            INSERT INTO notifications (user_id, title, message, type, link, status, created_at, updated_at)
            VALUES (?, 'Booking Rejected', ?, 'danger', ?, 'unread', NOW(), NOW())
        ", [
            $booking->user_id,
            "Your booking #{$booking->booking_number} has been rejected. Reason: {$request->admin_notes}",
            route('customer.bookings.show', $id),
        ]);

        return back()->with('success', 'Booking rejected.');
    }

    public function complete(int $id)
    {
        $booking = DB::select("SELECT * FROM bookings WHERE id = ? LIMIT 1", [$id]);
        abort_if(empty($booking), 404);
        $booking = $booking[0];

        DB::update("UPDATE bookings SET booking_status = 'completed', updated_at = NOW() WHERE id = ?", [$id]);
        DB::update("UPDATE vehicles SET status = 'available', updated_at = NOW() WHERE id = ?", [$booking->vehicle_id]);

        DB::insert("
            INSERT INTO notifications (user_id, title, message, type, status, created_at, updated_at)
            VALUES (?, 'Booking Completed', ?, 'info', 'unread', NOW(), NOW())
        ", [$booking->user_id, "Your booking #{$booking->booking_number} has been marked as completed."]);

        return back()->with('success', 'Booking marked as completed.');
    }
}
