@extends('layouts.admin')
@section('title','Maintenance')
@section('breadcrumb')<li class="breadcrumb-item active">Maintenance</li>@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Maintenance Records</h5>
    <a href="{{ route('admin.maintenance.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Record</a>
</div>
<div class="table-card mb-3">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="Search vehicle..." value="{{ request('search') }}"></div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="scheduled" {{ request('status')=='scheduled'?'selected':'' }}>Scheduled</option>
                <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('admin.maintenance.index') }}" class="btn btn-light border">Clear</a>
        </div>
    </form>
</div>
<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted small">Total maintenance cost: <strong>${{ number_format($totalCost,2) }}</strong></span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>Vehicle</th><th>Type</th><th>Date</th><th>Next Service</th><th>Cost</th><th>Mechanic</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                <tr>
                    <td class="fw-semibold small">{{ $r->vehicle_name }}</td>
                    <td class="small">{{ $r->maintenance_type }}</td>
                    <td class="small">{{ \Carbon\Carbon::parse($r->maintenance_date)->format('M d, Y') }}</td>
                    <td class="small text-muted">{{ $r->next_maintenance_date ? \Carbon\Carbon::parse($r->next_maintenance_date)->format('M d, Y') : 'N/A' }}</td>
                    <td class="fw-semibold">${{ number_format($r->cost,2) }}</td>
                    <td class="small">{{ $r->mechanic_name ?? 'N/A' }}</td>
                    <td>{!! maintenance_status_badge($r->status) !!}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.maintenance.edit', $r->id) }}" class="btn btn-sm btn-light border"><i class="fas fa-edit"></i></a>
                            <form id="delm{{ $r->id }}" method="POST" action="{{ route('admin.maintenance.destroy', $r->id) }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-light border text-danger" onclick="confirmDelete('delm{{ $r->id }}')"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No maintenance records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $records->links() }}</div>
</div>
@endsection
