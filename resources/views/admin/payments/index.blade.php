@extends('layouts.admin')
@section('title','Payments')
@section('breadcrumb')<li class="breadcrumb-item active">Payments</li>@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Payment Management</h5>
    <div class="badge bg-success fs-6 px-3 py-2">Total Revenue: ${{ number_format($totalRevenue,2) }}</div>
</div>
<div class="table-card mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3"><input type="text" name="search" class="form-control" placeholder="Transaction# or booking#" value="{{ request('search') }}"></div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                @foreach(['pending','completed','failed','refunded'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="method" class="form-select">
                <option value="">All Methods</option>
                @foreach(['cash','card','mobile_banking','bank_transfer'] as $m)
                <option value="{{ $m }}" {{ request('method')==$m?'selected':'' }}>{{ ucwords(str_replace('_',' ',$m)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5 d-flex gap-2">
            <button class="btn btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-light border">Clear</a>
        </div>
    </form>
</div>
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>Transaction ID</th><th>Booking#</th><th>Customer</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                <tr>
                    <td class="small fw-semibold">{{ $p->transaction_id }}</td>
                    <td class="small">{{ $p->booking_number }}</td>
                    <td class="small">{{ $p->user_name }}</td>
                    <td class="fw-semibold text-success">${{ number_format($p->amount,2) }}</td>
                    <td class="small">{{ ucwords(str_replace('_',' ',$p->payment_method)) }}</td>
                    <td class="small text-muted">{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') : 'Pending' }}</td>
                    <td>{!! payment_status_badge($p->payment_status) !!}</td>
                    <td>
                        <a href="{{ route('admin.payments.show', $p->id) }}" class="btn btn-sm btn-light border"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $payments->links() }}</div>
</div>
@endsection
