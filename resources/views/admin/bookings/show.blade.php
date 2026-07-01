@extends('layouts.admin')
@section('title', 'Booking #'.$booking->booking_number)
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}" class="text-decoration-none">Bookings</a></li>
<li class="breadcrumb-item active">{{ $booking->booking_number }}</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="table-card mb-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h5 class="fw-bold mb-1">{{ $booking->booking_number }}</h5>
                    <p class="text-muted mb-0 small">Created {{ \Carbon\Carbon::parse($booking->created_at)->format('M d, Y H:i') }}</p>
                </div>
                <div>{!! booking_status_badge($booking->booking_status) !!}</div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="bg-light rounded p-3">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-user text-primary me-2"></i>Customer</h6>
                        <p class="mb-1 fw-semibold">{{ $booking->user_name }}</p>
                        <p class="mb-1 text-muted small">{{ $booking->user_email }}</p>
                        <p class="mb-0 text-muted small">{{ $booking->user_phone ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light rounded p-3">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-car text-primary me-2"></i>Vehicle</h6>
                        <div class="d-flex gap-2">
                            <img src="{{ vehicle_image_url($booking->primary_image) }}" class="rounded" width="60" height="45" style="object-fit:cover"
                                 onerror="this.src='https://placehold.co/60x45/1e40af/fff?text=V'">
                            <div>
                                <p class="mb-0 fw-semibold">{{ $booking->vehicle_name }}</p>
                                <p class="mb-0 text-muted small">{{ $booking->registration_number }}</p>
                                <p class="mb-0 text-muted small">${{ $booking->price_per_day }}/day</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light rounded p-3">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-calendar text-primary me-2"></i>Rental Period</h6>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Pickup</span>
                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($booking->pickup_date)->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Return</span>
                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($booking->return_date)->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Duration</span>
                            <span class="fw-semibold">{{ $booking->total_days }} days</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light rounded p-3">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-dollar-sign text-primary me-2"></i>Payment</h6>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Subtotal</span><span>${{ number_format($booking->total_cost,2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Tax (5%)</span><span>${{ number_format($booking->tax,2) }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span><span class="text-primary">${{ number_format($booking->final_amount,2) }}</span>
                        </div>
                        @if($payment)
                        <div class="mt-2 d-flex justify-content-between small">
                            <span class="text-muted">Method</span>
                            <span>{{ ucwords(str_replace('_',' ',$payment->payment_method)) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Payment Status</span>
                            {!! payment_status_badge($payment->payment_status) !!}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($booking->special_requests)
            <div class="mt-3 p-3 bg-light rounded">
                <h6 class="fw-semibold mb-2">Special Requests</h6>
                <p class="mb-0 text-muted small">{{ $booking->special_requests }}</p>
            </div>
            @endif

            @if($booking->admin_notes)
            <div class="mt-3 p-3 bg-warning-subtle rounded">
                <h6 class="fw-semibold mb-2">Admin Notes</h6>
                <p class="mb-0 text-muted small">{{ $booking->admin_notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="table-card mb-4">
            <h6 class="fw-bold mb-3">Actions</h6>

            @if($booking->booking_status === 'pending')
            <form action="{{ route('admin.bookings.approve', $booking->id) }}" method="POST" class="mb-2">
                @csrf
                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this booking?')">
                    <i class="fas fa-check me-2"></i>Approve Booking
                </button>
            </form>

            <button class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="fas fa-times me-2"></i>Reject Booking
            </button>
            @endif

            @if($booking->booking_status === 'approved')
            <form action="{{ route('admin.bookings.complete', $booking->id) }}" method="POST" class="mb-2">
                @csrf
                <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Mark as completed?')">
                    <i class="fas fa-flag-checkered me-2"></i>Mark Completed
                </button>
            </form>
            @endif

            <a href="{{ route('admin.bookings.index') }}" class="btn btn-light border w-100">
                <i class="fas fa-arrow-left me-2"></i>Back to Bookings
            </a>
        </div>

        @if($payment)
        <div class="table-card">
            <h6 class="fw-bold mb-3">Payment Details</h6>
            <div class="small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Transaction ID</span>
                    <span class="fw-semibold text-truncate ms-2" style="max-width:150px">{{ $payment->transaction_id }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Receipt#</span>
                    <span>{{ $payment->receipt_number }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Method</span>
                    <span>{{ ucwords(str_replace('_',' ',$payment->payment_method)) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Status</span>
                    {!! payment_status_badge($payment->payment_status) !!}
                </div>
            </div>
            <form action="{{ route('admin.payments.status', $payment->id) }}" method="POST" class="mt-3">
                @csrf @method('PUT')
                <div class="d-flex gap-2">
                    <select name="payment_status" class="form-select form-select-sm">
                        @foreach(['pending','completed','failed','refunded'] as $ps)
                        <option value="{{ $ps }}" {{ $payment->payment_status==$ps?'selected':'' }}>{{ ucfirst($ps) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm px-3">Update</button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea name="admin_notes" class="form-control" rows="4" required placeholder="Explain why this booking is being rejected..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
