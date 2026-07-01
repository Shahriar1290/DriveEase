{{-- resources/views/admin/customers/index.blade.php --}}
@extends('layouts.admin')
@section('title','Customers')
@section('breadcrumb')<li class="breadcrumb-item active">Customers</li>@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Customer Management</h5>
</div>
<div class="table-card mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="Name or email..." value="{{ request('search') }}"></div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-light border">Clear</a>
        </div>
    </form>
</div>
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>Customer</th><th>Phone</th><th>Bookings</th><th>Joined</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ user_avatar_url($c->avatar, $c->name) }}" class="rounded-circle" width="38" height="38" style="object-fit:cover">
                            <div>
                                <div class="fw-semibold">{{ $c->name }}</div>
                                <div class="text-muted small">{{ $c->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="small">{{ $c->phone ?? 'N/A' }}</td>
                    <td><span class="badge bg-primary-subtle text-primary">{{ $c->bookings_count }}</span></td>
                    <td class="small text-muted">{{ \Carbon\Carbon::parse($c->created_at)->format('M d, Y') }}</td>
                    <td>
                        @if($c->is_active)<span class="badge bg-success">Active</span>
                        @else<span class="badge bg-secondary">Inactive</span>@endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.customers.show', $c->id) }}" class="btn btn-sm btn-light border"><i class="fas fa-eye"></i></a>
                            <form method="POST" action="{{ route('admin.customers.toggle', $c->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light border {{ $c->is_active?'text-warning':'text-success' }}" title="{{ $c->is_active?'Deactivate':'Activate' }}">
                                    <i class="fas fa-{{ $c->is_active?'ban':'check' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $customers->links() }}</div>
</div>
@endsection
