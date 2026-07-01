@extends('layouts.admin')
@section('title','Payment Details')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}" class="text-decoration-none">Payments</a></li>
<li class="breadcrumb-item active">{{ $payment->transaction_id }}</li>
@endsection
@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
<div class="table-card">
    <h5 class="fw-bold mb-4">Payment Details</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-6"><div class="text-muted small">Transaction ID</div><div class="fw-semibold">{{ $payment->transaction_id }}</div></div>
        <div class="col-md-6"><div class="text-muted small">Receipt Number</div><div class="fw-semibold">{{ $payment->receipt_number }}</div></div>
        <div class="col-md-6"><div class="text-muted small">Booking Number</div><div class="fw-semibold">{{ $payment->booking_number }}</div></div>
        <div class="col-md-6"><div class="text-muted small">Customer</div><div class="fw-semibold">{{ $payment->user_name }}</div></div>
        <div class="col-md-6"><div class="text-muted small">Amount</div><div class="fw-bold text-success fs-5">${{ number_format($payment->amount,2) }}</div></div>
        <div class="col-md-6"><div class="text-muted small">Method</div><div class="fw-semibold">{{ ucwords(str_replace('_',' ',$payment->payment_method)) }}</div></div>
        <div class="col-md-6"><div class="text-muted small">Payment Date</div><div class="fw-semibold">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y H:i') : 'N/A' }}</div></div>
        <div class="col-md-6"><div class="text-muted small">Status</div>{!! payment_status_badge($payment->payment_status) !!}</div>
    </div>
    <form action="{{ route('admin.payments.status', $payment->id) }}" method="POST" class="border-top pt-3">
        @csrf @method('PUT')
        <div class="row g-2 align-items-end">
            <div class="col"><select name="payment_status" class="form-select">
                @foreach(['pending','completed','failed','refunded'] as $ps)
                <option value="{{ $ps }}" {{ $payment->payment_status==$ps?'selected':'' }}>{{ ucfirst($ps) }}</option>
                @endforeach
            </select></div>
            <div class="col-auto"><button class="btn btn-primary">Update Status</button></div>
        </div>
    </form>
    <a href="{{ route('admin.bookings.show', $payment->booking_id) }}" class="btn btn-light border mt-3 w-100">View Booking Details</a>
</div>
</div></div>
@endsection
