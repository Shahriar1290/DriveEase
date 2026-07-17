@extends('layouts.customer')
@section('title', 'Booking #'.$booking->booking_number)

@section('content')
<div class="content-card mb-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h6 class="fw-bold mb-1">Booking Details</h6>
            <p class="text-muted small mb-0">#{{ $booking->booking_number }} · {{ \Carbon\Carbon::parse($booking->created_at)->format('M d, Y H:i') }}</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            {!! booking_status_badge($booking->booking_status) !!}
            @if(in_array($booking->booking_status, ['approved','completed']))
            <a href="{{ route('customer.bookings.invoice', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-file-invoice me-1"></i>Invoice
            </a>
            @endif
        </div>
    </div>

    {{-- Vehicle --}}
    <div class="bg-light rounded-3 p-3 mb-4">
        <h6 class="fw-semibold mb-3 small text-muted text-uppercase">Vehicle</h6>
        <div class="d-flex gap-3">
            <img src="{{ vehicle_image_url($vehicle->primary_image ?? null) }}" class="rounded-2 flex-shrink-0"
                 width="120" height="80" style="object-fit:cover"
                 onerror="this.src='https://placehold.co/120x80/1e40af/fff?text=V'">
            <div>
                <h6 class="fw-bold mb-1">{{ $vehicle->vehicle_name }}</h6>
                <p class="text-muted small mb-1">{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->year }}</p>
                <p class="text-muted small mb-0">
                    <i class="fas fa-users me-1"></i>{{ $vehicle->seating_capacity }} seats ·
                    <i class="fas fa-gas-pump ms-2 me-1"></i>{{ ucfirst($vehicle->fuel_type) }} ·
                    <i class="fas fa-cog ms-2 me-1"></i>{{ ucfirst($vehicle->transmission) }}
                </p>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Dates --}}
        <div class="col-md-6">
            <div class="bg-light rounded-3 p-3 h-100">
                <h6 class="fw-semibold mb-3 small text-muted text-uppercase">Rental Period</h6>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Pickup Date</span>
                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($booking->pickup_date)->format('D, M d Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Return Date</span>
                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($booking->return_date)->format('D, M d Y') }}</span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Duration</span>
                    <span class="fw-bold text-primary">{{ $booking->total_days }} days</span>
                </div>
                @if($booking->pickup_location)
                <hr class="my-2">
                <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $booking->pickup_location }}</div>
                @endif
            </div>
        </div>

        {{-- Cost --}}
        <div class="col-md-6">
            <div class="bg-light rounded-3 p-3 h-100">
                <h6 class="fw-semibold mb-3 small text-muted text-uppercase">Payment Summary</h6>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">TK {{ number_format($booking->price_per_day,2) }} × {{ $booking->total_days }} days</span>
                    <span>TK {{ number_format($booking->total_cost,2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Discount</span>
                    <span class="text-success">-TK {{ number_format($booking->discount,2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Tax (5%)</span>
                    <span>TK {{ number_format($booking->tax,2) }}</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total</span>
                    <span class="text-primary fs-6">TK {{ number_format($booking->final_amount,2) }}</span>
                </div>
                @if($payment)
                <hr class="my-2">
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Method</span>
                    <span>{{ ucwords(str_replace('_',' ',$payment->payment_method)) }}</span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Payment</span>
                    {!! payment_status_badge($payment->payment_status) !!}
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($booking->special_requests)
    <div class="mt-3 p-3 bg-info-subtle rounded-3">
        <h6 class="fw-semibold small mb-1 text-muted">Special Requests</h6>
        <p class="mb-0 small">{{ $booking->special_requests }}</p>
    </div>
    @endif

    @if($booking->admin_notes && in_array($booking->booking_status, ['rejected']))
    <div class="mt-3 p-3 bg-danger-subtle rounded-3">
        <h6 class="fw-semibold small mb-1 text-danger">Rejection Reason</h6>
        <p class="mb-0 small">{{ $booking->admin_notes }}</p>
    </div>
    @endif

    <div class="d-flex gap-2 mt-4">
        <a href="{{ route('customer.bookings.index') }}" class="btn btn-light border">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
        @if(in_array($booking->booking_status, ['pending','approved']))
        <form method="POST" action="{{ route('customer.bookings.cancel', $booking->id) }}"
              onsubmit="return confirm('Are you sure you want to cancel this booking?')">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-times me-1"></i>Cancel Booking
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
