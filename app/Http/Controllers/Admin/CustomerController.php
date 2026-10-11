<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->withCount('bookings');

        if ($s = $request->search) {
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }
        if ($request->status === 'active') {
            $query->where('is_active', true);
        }
        if ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(int $id)
    {
        $customer = User::where('id', $id)->where('role', 'customer')->firstOrFail();

        $bookings = $customer->bookings()
            ->with('vehicle:id,vehicle_name')
            ->latest()
            ->limit(10)
            ->get();

        $stats = [
            'total_bookings' => $customer->bookings()->count(),
            'completed_bookings' => $customer->bookings()->where('booking_status', 'completed')->count(),
            'total_spent' => $customer->bookings()->where('booking_status', 'completed')->sum('final_amount'),
        ];

        return view('admin.customers.show', compact('customer', 'bookings', 'stats'));
    }

    public function toggleStatus(int $id)
    {
        $customer = User::where('id', $id)->where('role', 'customer')->firstOrFail();

        $customer->update(['is_active' => !$customer->is_active]);

        $statusText = $customer->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Customer account {$statusText}.");
    }
}
