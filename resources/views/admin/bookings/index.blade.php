@extends('layouts.admin')
@section('title', 'Bookings')
@section('breadcrumb')
<li class="breadcrumb-item active">Bookings</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Booking Management</h5>
</div>

<div class="table-card mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Booking# or customer..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                @foreach(['pending','approved','active','completed','cancelled','rejected'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-light border">Clear</a>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>Booking#</th><th>Customer</th><th>Vehicle</th><th>Dates</th><th>Days</th><th>Amount</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                <tr>
                    <td class="fw-semibold small text-primary">{{ $b->booking_number }}</td>
                    <td>
                        <div class="fw-semibold small">{{ $b->user_name }}</div>
                        <div class="text-muted" style="font-size:11px">{{ $b->user_email }}</div>
                    </td>
                    <td class="small">{{ $b->vehicle_name }}</td>
                    <td class="small">
                        <div>{{ \Carbon\Carbon::parse($b->pickup_date)->format('M d') }}</div>
                        <div class="text-muted">→ {{ \Carbon\Carbon::parse($b->return_date)->format('M d, Y') }}</div>
                    </td>
                    <td class="text-center">{{ $b->total_days }}</td>
                    <td class="fw-semibold">TK {{ number_format($b->final_amount,0) }}</td>
                    <td>{!! booking_status_badge($b->booking_status) !!}</td>
                    <td>
                        <a href="{{ route('admin.bookings.show', $b->id) }}" class="btn btn-sm btn-light border">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $bookings->links() }}</div>
</div>
@endsection
