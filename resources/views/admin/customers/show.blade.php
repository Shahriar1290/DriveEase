@extends('layouts.admin')
@section('title', $customer->name)
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}" class="text-decoration-none">Customers</a></li>
<li class="breadcrumb-item active">{{ $customer->name }}</li>
@endsection
@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="table-card text-center">
            <img src="{{ user_avatar_url($customer->avatar, $customer->name) }}" class="rounded-circle mb-3" width="90" height="90" style="object-fit:cover">
            <h5 class="fw-bold mb-1">{{ $customer->name }}</h5>
            <p class="text-muted small mb-3">{{ $customer->email }}</p>
            <div class="row g-2 text-center mb-3">
                <div class="col-4"><div class="fw-bold fs-5 text-primary">{{ $stats['total_bookings'] }}</div><div class="text-muted small">Bookings</div></div>
                <div class="col-4"><div class="fw-bold fs-5 text-success">{{ $stats['completed_bookings'] }}</div><div class="text-muted small">Completed</div></div>
                <div class="col-4"><div class="fw-bold fs-5 text-warning">TK {{ number_format($stats['total_spent'],0) }}</div><div class="text-muted small">Spent</div></div>
            </div>
            <div class="text-start small">
                <div class="mb-1"><i class="fas fa-phone text-muted me-2"></i>{{ $customer->phone ?? 'N/A' }}</div>
                <div class="mb-1"><i class="fas fa-map-marker-alt text-muted me-2"></i>{{ $customer->address ?? 'N/A' }}</div>
                <div><i class="fas fa-calendar text-muted me-2"></i>Joined {{ \Carbon\Carbon::parse($customer->created_at)->format('M d, Y') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="table-card">
            <h6 class="fw-bold mb-3">Booking History</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light"><tr><th>Booking#</th><th>Vehicle</th><th>Dates</th><th>Amount</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse(array_slice($bookings, 0, 10) as $b)
                        <tr>
                            <td class="small fw-semibold">{{ $b->booking_number }}</td>
                            <td class="small">{{ $b->vehicle_name }}</td>
                            <td class="small">{{ \Carbon\Carbon::parse($b->pickup_date)->format('M d') }} → {{ \Carbon\Carbon::parse($b->return_date)->format('M d, Y') }}</td>
                            <td class="small">TK {{ number_format($b->final_amount,0) }}</td>
                            <td>{!! booking_status_badge($b->booking_status) !!}</td>
                        </tr>
                        @empty<tr><td colspan="5" class="text-center text-muted">No bookings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
