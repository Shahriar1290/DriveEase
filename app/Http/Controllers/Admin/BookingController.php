<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user:id,name,email', 'vehicle:id,vehicle_name']);

        if ($s = $request->search) {
            $query->where(function ($q) use ($s) {
                $q->where('booking_number', 'like', "%{$s}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$s}%"));
            });
        }
        if ($status = $request->status) {
            $query->where('booking_status', $status);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(int $id)
    {
        $booking = Booking::with(['user:id,name,email,phone', 'vehicle:id,vehicle_name,registration_number', 'vehicle.firstImage'])
            ->findOrFail($id);

        $payment = $booking->payment;

        return view('admin.bookings.show', compact('booking', 'payment'));
    }

    public function approve(int $id)
    {
        $booking = Booking::findOrFail($id);

        if (!$booking->canBeApproved()) {
            return back()->with('error', 'This booking cannot be approved in its current status.');
        }

        // Verify no overlapping confirmed bookings exist for this vehicle
        $conflict = Booking::where('vehicle_id', $booking->vehicle_id)
            ->where('id', '!=', $booking->id)
            ->whereIn('booking_status', ['approved', 'active'])
            ->where(function ($q) use ($booking) {
                $q->where('pickup_date', '<', $booking->return_date)
                  ->where('return_date', '>', $booking->pickup_date);
            })
            ->first();

        if ($conflict) {
            $pDate = \Carbon\Carbon::parse($conflict->pickup_date)->format('M d, Y');
            $rDate = \Carbon\Carbon::parse($conflict->return_date)->format('M d, Y');
            return back()->with('error', "Cannot approve: Conflicting booking #{$conflict->booking_number} already exists for this vehicle from {$pDate} to {$rDate}.");
        }

        DB::beginTransaction();
        try {
            $today = now()->toDateString();
            $pickupDate = \Carbon\Carbon::parse($booking->pickup_date)->toDateString();
            $returnDate = \Carbon\Carbon::parse($booking->return_date)->toDateString();
            $isOngoingToday = ($pickupDate <= $today && $returnDate >= $today);

            $newStatus = $isOngoingToday ? 'active' : 'approved';
            $booking->update(['booking_status' => $newStatus]);

            // Update payment: only mark completed if paid via card/mobile banking, otherwise keep pending
            if ($booking->payment) {
                if (in_array($booking->payment->payment_method, ['card', 'mobile_banking'])) {
                    $booking->payment->update(['payment_status' => 'completed', 'payment_date' => now()]);
                }
            }

            // Only mark vehicle 'rented' if rental is actively in-progress today
            if ($isOngoingToday) {
                $booking->vehicle->update(['status' => 'rented']);
            }
            $booking->vehicle->increment('total_rentals');

            Notification::create([
                'user_id' => $booking->user_id,
                'title' => 'Booking Approved',
                'message' => "Your booking #{$booking->booking_number} has been approved!",
                'type' => 'success',
                'link' => route('customer.bookings.show', $id),
                'status' => 'unread',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Failed to approve booking.');
        }

        return back()->with('success', 'Booking approved successfully.');
    }

    public function reject(Request $request, int $id)
    {
        $request->validate(['admin_notes' => 'required|string|max:500']);

        $booking = Booking::findOrFail($id);

        if (!$booking->canBeRejected()) {
            return back()->with('error', 'This booking cannot be rejected in its current status.');
        }

        $booking->update([
            'booking_status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        if ($booking->payment) {
            $booking->payment->update(['payment_status' => 'failed']);
        }

        Notification::create([
            'user_id' => $booking->user_id,
            'title' => 'Booking Rejected',
            'message' => "Your booking #{$booking->booking_number} has been rejected. Reason: {$request->admin_notes}",
            'type' => 'danger',
            'link' => route('customer.bookings.show', $id),
            'status' => 'unread',
        ]);

        return back()->with('success', 'Booking rejected.');
    }

    public function complete(int $id)
    {
        $booking = Booking::findOrFail($id);

        if (!$booking->canBeCompleted()) {
            return back()->with('error', 'This booking cannot be completed in its current status.');
        }

        DB::beginTransaction();
        try {
            $booking->update(['booking_status' => 'completed']);

            // Auto-complete payment on rental completion if it was cash on delivery
            if ($booking->payment && $booking->payment->payment_status === 'pending') {
                $booking->payment->update(['payment_status' => 'completed', 'payment_date' => now()]);
            }

            // Check if vehicle has any other active rental today
            $today = now()->toDateString();
            $hasOtherActive = Booking::where('vehicle_id', $booking->vehicle_id)
                ->where('id', '!=', $booking->id)
                ->whereIn('booking_status', ['active', 'approved'])
                ->where('pickup_date', '<=', $today)
                ->where('return_date', '>=', $today)
                ->exists();

            if (!$hasOtherActive) {
                $booking->vehicle->update(['status' => 'available']);
            }

            Notification::create([
                'user_id' => $booking->user_id,
                'title' => 'Booking Completed',
                'message' => "Your booking #{$booking->booking_number} has been marked as completed.",
                'type' => 'info',
                'status' => 'unread',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Failed to complete booking.');
        }

        return back()->with('success', 'Booking marked as completed.');
    }
}
