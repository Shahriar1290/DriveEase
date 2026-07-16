@extends('layouts.admin')
@section('title','Customer Report')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}" class="text-decoration-none">Reports</a></li>
<li class="breadcrumb-item active">Customers</li>
@endsection
@section('content')
<h5 class="fw-bold mb-4">Customer Activity Report</h5>
<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr><th>#</th><th>Customer</th><th>Phone</th><th>Total Bookings</th><th>Total Spent</th><th>Joined</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($customers as $i => $c)
                <tr>
                    <td class="text-muted small">{{ $i+1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ user_avatar_url($c->avatar, $c->name) }}" class="rounded-circle" width="36" height="36" style="object-fit:cover">
                            <div>
                                <div class="fw-semibold small">{{ $c->name }}</div>
                                <div class="text-muted" style="font-size:11px">{{ $c->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="small">{{ $c->phone ?? 'N/A' }}</td>
                    <td><span class="badge bg-primary-subtle text-primary">{{ $c->bookings_count }}</span></td>
                    <td class="fw-semibold text-success">TK {{ number_format($c->total_spent ?? 0, 2) }}</td>
                    <td class="small text-muted">{{ \Carbon\Carbon::parse($c->created_at)->format('M d, Y') }}</td>
                    <td>
                        @if($c->is_active)<span class="badge bg-success">Active</span>
                        @else<span class="badge bg-secondary">Inactive</span>@endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
