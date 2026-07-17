@extends('layouts.customer')
@section('title', 'My Bookings')

@section('content')
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="fw-bold mb-0">My Bookings ({{ $bookings->total() }})</h6>
        <a href="{{ route('vehicles.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>New Booking
        </a>
    </div>

    @if($bookings->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-calendar-times text-muted mb-3" style="font-size:3rem"></i>
        <h6 class="text-muted">No bookings yet</h6>
        <a href="{{ route('vehicles.index') }}" class="btn btn-primary mt-2">Browse Vehicles</a>
    </div>
    @else
    @foreach($bookings as $b)
    <div class="border rounded-3 p-3 mb-3">
        <div class="d-flex gap-3 align-items-start flex-wrap">
            <img src="{{ vehicle_image_url($b->primary_image) }}" class="rounded-2 flex-shrink-0"
                 width="100" height="70" style="object-fit:cover"
                 onerror="this.src='https://placehold.co/100x70/1e40af/fff?text=V'">
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="fw-bold mb-0">{{ $b->vehicle_name }}</h6>
                    {!! booking_status_badge($b->booking_status) !!}
                </div>
                <p class="text-muted small mb-1">
                    <i class="fas fa-hashtag me-1"></i>{{ $b->booking_number }}
                </p>
                <div class="d-flex gap-3 text-muted small flex-wrap">
                    <span><i class="fas fa-calendar-alt me-1 text-primary"></i>{{ \Carbon\Carbon::parse($b->pickup_date)->format('M d, Y') }} → {{ \Carbon\Carbon::parse($b->return_date)->format('M d, Y') }}</span>
                    <span><i class="fas fa-clock me-1 text-primary"></i>{{ $b->total_days }} days</span>
                    <span><i class="fas fa-money-bill me-1 text-primary"></i>TK {{ number_format($b->final_amount, 2) }}</span>
                    @if($b->payment_method)
                    <span><i class="fas fa-credit-card me-1 text-primary"></i>{{ ucwords(str_replace('_',' ',$b->payment_method)) }}</span>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-column gap-2 ms-auto">
                <a href="{{ route('customer.bookings.show', $b->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye me-1"></i>Details
                </a>
                @if(in_array($b->booking_status, ['approved','completed']))
                <a href="{{ route('customer.bookings.invoice', $b->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-file-invoice me-1"></i>Invoice
                </a>
                @endif
                @if(in_array($b->booking_status, ['pending','approved']))
                <form method="POST" action="{{ route('customer.bookings.cancel', $b->id) }}"
                      onsubmit="return confirm('Cancel this booking?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
    <div class="mt-3">{{ $bookings->links() }}</div>
    @endif
</div>
@endsection
