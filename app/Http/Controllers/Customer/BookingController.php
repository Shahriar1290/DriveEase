<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(Request $request, string $slug)
    {
        $vehicle = DB::select("
            SELECT v.*,
                   (SELECT vi.image FROM vehicle_images vi
                     WHERE vi.vehicle_id = v.id
                     ORDER BY vi.is_primary DESC, vi.sort_order ASC LIMIT 1) AS primary_image
            FROM vehicles v WHERE v.slug = ? LIMIT 1
        ", [$slug]);
        abort_if(empty($vehicle), 404);
        $vehicle = $vehicle[0];

        if ($vehicle->status !== 'available') {
            return back()->with('error', 'This vehicle is not currently available.');
        }

        return view('customer.bookings.create', compact('vehicle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id'       => 'required|integer',
            'pickup_date'      => 'required|date|after_or_equal:today',
            'return_date'      => 'required|date|after:pickup_date',
            'pickup_location'  => 'nullable|string|max:255',
            'return_location'  => 'nullable|string|max:255',
            'special_requests' => 'nullable|string|max:1000',
            'payment_method'   => 'required|in:cash,card,mobile_banking,bank_transfer',
        ]);

        $vehicleRow = DB::select("SELECT * FROM vehicles WHERE id = ? LIMIT 1", [$request->vehicle_id]);
        if (empty($vehicleRow)) {
            return back()->with('error', 'Vehicle not found.')->withInput();
        }
        $vehicle = $vehicleRow[0];

        if ($vehicle->status !== 'available') {
            return back()->with('error', 'This vehicle is not currently available.')->withInput();
        }

        // Conflict check — raw SQL date-overlap detection
        $conflict = DB::select("
            SELECT id FROM bookings
            WHERE vehicle_id = ?
              AND booking_status IN ('approved', 'active')
              AND (
                    pickup_date BETWEEN ? AND ?
                 OR return_date BETWEEN ? AND ?
                 OR (pickup_date <= ? AND return_date >= ?)
              )
            LIMIT 1
        ", [
            $request->vehicle_id,
            $request->pickup_date, $request->return_date,
            $request->pickup_date, $request->return_date,
            $request->pickup_date, $request->return_date,
        ]);

        if (!empty($conflict)) {
            return back()->with('error', 'This vehicle is already booked for the selected dates.')->withInput();
        }

        $pickup = \Carbon\Carbon::parse($request->pickup_date);
        $return = \Carbon\Carbon::parse($request->return_date);
        $days   = $pickup->diffInDays($return);
        $total  = $days * $vehicle->price_per_day;
        $tax    = $total * 0.05;
        $final  = $total + $tax;

        $bookingNumber = 'BK-' . strtoupper(Str::random(8));

        DB::beginTransaction();
        try {
            DB::insert("
                INSERT INTO bookings (
                    booking_number, user_id, vehicle_id, pickup_date, return_date,
                    total_days, price_per_day, total_cost, discount, tax, final_amount,
                    pickup_location, return_location, special_requests, booking_status,
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())
            ", [
                $bookingNumber, auth()->id(), $vehicle->id,
                $request->pickup_date, $request->return_date,
                $days, $vehicle->price_per_day, $total, $tax, $final,
                $request->pickup_location, $request->return_location, $request->special_requests,
            ]);
            $bookingId = DB::getPdo()->lastInsertId();

            $transactionId = 'TXN-' . strtoupper(Str::random(10));
            $receiptNumber = 'RCP-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            DB::insert("
                INSERT INTO payments (
                    transaction_id, booking_id, amount, payment_method, payment_status,
                    receipt_number, created_at, updated_at
                ) VALUES (?, ?, ?, ?, 'pending', ?, NOW(), NOW())
            ", [$transactionId, $bookingId, $final, $request->payment_method, $receiptNumber]);

            DB::insert("
                INSERT INTO notifications (user_id, title, message, type, link, status, created_at, updated_at)
                VALUES (?, 'Booking Submitted', ?, 'info', ?, 'unread', NOW(), NOW())
            ", [
                auth()->id(),
                "Your booking #{$bookingNumber} has been submitted and is awaiting approval.",
                route('customer.bookings.show', $bookingId),
            ]);

            // Notify all admins
            $admins = DB::select("SELECT id FROM users WHERE role = 'admin'");
            foreach ($admins as $admin) {
                DB::insert("
                    INSERT INTO notifications (user_id, title, message, type, link, status, created_at, updated_at)
                    VALUES (?, 'New Booking', ?, 'warning', ?, 'unread', NOW(), NOW())
                ", [
                    $admin->id,
                    "New booking #{$bookingNumber} requires your approval.",
                    route('admin.bookings.show', $bookingId),
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong while creating your booking. Please try again.')->withInput();
        }

        return redirect()->route('customer.bookings.index')->with('success', 'Booking submitted successfully! Awaiting admin approval.');
    }

    public function index(Request $request)
    {
        $perPage = 10;
        $page    = max(1, (int) $request->get('page', 1));
        $offset  = ($page - 1) * $perPage;

        $total = DB::select("SELECT COUNT(*) AS total FROM bookings WHERE user_id = ?", [auth()->id()])[0]->total;

        $rows = DB::select("
            SELECT b.*,
                   v.vehicle_name, v.slug AS vehicle_slug,
                   (SELECT vi.image FROM vehicle_images vi
                     WHERE vi.vehicle_id = v.id
                     ORDER BY vi.is_primary DESC, vi.sort_order ASC LIMIT 1) AS primary_image,
                   p.payment_method, p.payment_status
            FROM bookings b
            INNER JOIN vehicles v ON v.id = b.vehicle_id
            LEFT JOIN payments p ON p.booking_id = b.id
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", [auth()->id()]);

        $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('customer.bookings.index', compact('bookings'));
    }

    public function show(int $bookingId)
    {
        $booking = $this->authorizedBooking($bookingId);

        $vehicle = DB::select("
            SELECT v.*,
                   (SELECT vi.image FROM vehicle_images vi
                     WHERE vi.vehicle_id = v.id
                     ORDER BY vi.is_primary DESC, vi.sort_order ASC LIMIT 1) AS primary_image
            FROM vehicles v WHERE v.id = ? LIMIT 1
        ", [$booking->vehicle_id])[0];
        $payment = DB::select("SELECT * FROM payments WHERE booking_id = ? LIMIT 1", [$booking->id]);
        $payment = $payment[0] ?? null;

        return view('customer.bookings.show', compact('booking', 'vehicle', 'payment'));
    }

    public function cancel(int $bookingId)
    {
        $booking = $this->authorizedBooking($bookingId, requireOwnerOnly: true);

        if (!in_array($booking->booking_status, ['pending', 'approved'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        DB::update("UPDATE bookings SET booking_status = 'cancelled', updated_at = NOW() WHERE id = ?", [$booking->id]);

        DB::insert("
            INSERT INTO notifications (user_id, title, message, type, status, created_at, updated_at)
            VALUES (?, 'Booking Cancelled', ?, 'warning', 'unread', NOW(), NOW())
        ", [auth()->id(), "Your booking #{$booking->booking_number} has been cancelled."]);

        return back()->with('success', 'Booking cancelled successfully.');
    }

    public function invoice(int $bookingId)
    {
        $booking = $this->authorizedBooking($bookingId);

        $vehicle = DB::select("SELECT * FROM vehicles WHERE id = ? LIMIT 1", [$booking->vehicle_id])[0];
        $payment = DB::select("SELECT * FROM payments WHERE booking_id = ? LIMIT 1", [$booking->id]);
        $payment = $payment[0] ?? null;
        $user    = DB::select("SELECT * FROM users WHERE id = ? LIMIT 1", [$booking->user_id])[0];

        return view('customer.bookings.invoice', compact('booking', 'vehicle', 'payment', 'user'));
    }

    /**
     * Fetch a booking by id and manually enforce that it belongs to the
     * logged-in customer (or to an admin), replicating what a Laravel
     * Policy would do — but as a plain raw-SQL ownership check.
     */
    private function authorizedBooking(int $bookingId, bool $requireOwnerOnly = false)
    {
        $row = DB::select("SELECT * FROM bookings WHERE id = ? LIMIT 1", [$bookingId]);
        abort_if(empty($row), 404);
        $booking = $row[0];

        $isOwner = (int) $booking->user_id === (int) auth()->id();
        $isAdmin = auth()->user()->isAdmin();

        if ($requireOwnerOnly) {
            abort_unless($isOwner, 403);
        } else {
            abort_unless($isOwner || $isAdmin, 403);
        }

        return $booking;
    }
}
