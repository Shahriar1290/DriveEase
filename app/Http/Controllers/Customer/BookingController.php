<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(Request $request, string $slug)
    {
        $vehicle = Vehicle::with('firstImage')
            ->where('slug', $slug)
            ->firstOrFail();

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

        DB::beginTransaction();
        try {
            $vehicle = Vehicle::lockForUpdate()->find($request->vehicle_id);
            if (!$vehicle || $vehicle->status !== 'available') {
                DB::rollBack();
                return back()->with('error', 'Vehicle not found or no longer available.')->withInput();
            }

            $conflict = Booking::where('vehicle_id', $request->vehicle_id)
                ->whereIn('booking_status', ['approved', 'active'])
                ->where(function ($q) use ($request) {
                    $q->where('pickup_date', '<', $request->return_date)
                      ->where('return_date', '>', $request->pickup_date);
                })
                ->exists();

            if ($conflict) {
                DB::rollBack();
                return back()->with('error', 'This vehicle is already booked for the selected dates.')->withInput();
            }

            $pickup = \Carbon\Carbon::parse($request->pickup_date);
            $return = \Carbon\Carbon::parse($request->return_date);
            $days   = max(1, (int) $pickup->diffInDays($return));
            $total  = $days * $vehicle->price_per_day;
            $tax    = $total * 0.05;
            $final  = $total + $tax;

            $bookingNumber = 'BK-' . strtoupper(Str::random(8));
            $booking = Booking::create([
                'booking_number' => $bookingNumber,
                'user_id' => auth()->id(),
                'vehicle_id' => $vehicle->id,
                'pickup_date' => $request->pickup_date,
                'return_date' => $request->return_date,
                'total_days' => $days,
                'price_per_day' => $vehicle->price_per_day,
                'total_cost' => $total,
                'discount' => 0,
                'tax' => $tax,
                'final_amount' => $final,
                'pickup_location' => $request->pickup_location,
                'return_location' => $request->return_location,
                'special_requests' => $request->special_requests,
                'booking_status' => 'pending',
            ]);

            Payment::create([
                'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
                'booking_id' => $booking->id,
                'amount' => $final,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'receipt_number' => 'RCP-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            ]);

            Notification::create([
                'user_id' => auth()->id(),
                'title' => 'Booking Submitted',
                'message' => "Your booking #{$bookingNumber} has been submitted and is awaiting approval.",
                'type' => 'info',
                'link' => route('customer.bookings.show', $booking->id),
                'status' => 'unread',
            ]);

            $adminIds = User::where('role', 'admin')->pluck('id');
            if ($adminIds->isNotEmpty()) {
                Notification::insert($adminIds->map(fn($adminId) => [
                    'user_id' => $adminId,
                    'title' => 'New Booking',
                    'message' => "New booking #{$bookingNumber} requires your approval.",
                    'type' => 'warning',
                    'link' => route('admin.bookings.show', $booking->id),
                    'status' => 'unread',
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray());
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Something went wrong while creating your booking. Please try again.')->withInput();
        }

        return redirect()->route('customer.bookings.index')->with('success', 'Booking submitted successfully! Awaiting admin approval.');
    }

    public function index(Request $request)
    {
        $bookings = Booking::with(['vehicle:id,vehicle_name,slug', 'vehicle.firstImage', 'payment:id,payment_method,payment_status'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.bookings.index', compact('bookings'));
    }

    public function show(int $bookingId)
    {
        $booking = $this->authorizedBooking($bookingId);

        $booking->load([
            'vehicle:id,vehicle_name,slug',
            'vehicle.firstImage',
            'payment',
        ]);

        return view('customer.bookings.show', ['booking' => $booking, 'vehicle' => $booking->vehicle, 'payment' => $booking->payment]);
    }

    public function cancel(int $bookingId)
    {
        $booking = $this->authorizedBooking($bookingId, requireOwnerOnly: true);

        if (!in_array($booking->booking_status, ['pending', 'approved'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $wasApproved = $booking->booking_status === 'approved';

        DB::beginTransaction();
        try {
            $booking->update(['booking_status' => 'cancelled']);

            if ($wasApproved) {
                $today = now()->toDateString();
                $hasOtherActive = Booking::where('vehicle_id', $booking->vehicle_id)
                    ->where('id', '!=', $booking->id)
                    ->whereIn('booking_status', ['active', 'approved'])
                    ->where('pickup_date', '<=', $today)
                    ->where('return_date', '>=', $today)
                    ->exists();

                if (!$hasOtherActive && $booking->vehicle->status === 'rented') {
                    $booking->vehicle->update(['status' => 'available']);
                }
            }

            Notification::create([
                'user_id' => auth()->id(),
                'title' => 'Booking Cancelled',
                'message' => "Your booking #{$booking->booking_number} has been cancelled.",
                'type' => 'warning',
                'status' => 'unread',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Failed to cancel booking.');
        }

        return back()->with('success', 'Booking cancelled successfully.');
    }

    public function invoice(int $bookingId)
    {
        $booking = $this->authorizedBooking($bookingId);

        $booking->load(['vehicle', 'payment', 'user']);

        return view('customer.bookings.invoice', [
            'booking' => $booking,
            'user'    => $booking->user,
            'vehicle' => $booking->vehicle,
            'payment' => $booking->payment,
        ]);
    }

    private function authorizedBooking(int $bookingId, bool $requireOwnerOnly = false)
    {
        $booking = Booking::findOrFail($bookingId);

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
