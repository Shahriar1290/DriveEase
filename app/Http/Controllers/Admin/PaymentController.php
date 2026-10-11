<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['booking.user:id,name', 'booking.vehicle:id,vehicle_name']);

        if ($s = $request->search) {
            $query->where(function ($q) use ($s) {
                $q->where('transaction_id', 'like', "%{$s}%")
                    ->orWhereHas('booking', fn($bq) => $bq->where('booking_number', 'like', "%{$s}%"));
            });
        }
        if ($status = $request->status) {
            $query->where('payment_status', $status);
        }
        if ($method = $request->method) {
            $query->where('payment_method', $method);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        $totalRevenue = Payment::where('payment_status', 'completed')->sum('amount');

        return view('admin.payments.index', compact('payments', 'totalRevenue'));
    }

    public function show(int $id)
    {
        $payment = Payment::with(['booking.user:id,name', 'booking:id,booking_number,user_id'])
            ->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['payment_status' => 'required|in:pending,completed,failed,refunded']);

        $payment = Payment::findOrFail($id);

        $data = ['payment_status' => $request->payment_status];
        if ($request->payment_status === 'completed') {
            $data['payment_date'] = now();
        }

        $payment->update($data);

        return back()->with('success', 'Payment status updated.');
    }
}
