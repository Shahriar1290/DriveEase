@extends('layouts.admin')
@section('title', 'Vehicles')
@section('breadcrumb')
<li class="breadcrumb-item active">Vehicles</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Vehicle Management</h5>
    <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add Vehicle
    </a>
</div>

{{-- Filters --}}
<div class="table-card mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search name, brand, reg..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="category_id" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id?'selected':'' }}>{{ $cat->category_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                @foreach(['available','rented','maintenance','inactive'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-light border">Clear</a>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Vehicle</th><th>Category</th><th>Reg. No.</th>
                    <th>Fuel / Trans.</th><th>Price/Day</th><th>Status</th><th>Rentals</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $v)
                <tr>
                    <td class="text-muted small">{{ $v->id }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ vehicle_image_url($v->primary_image) }}" class="rounded" width="50" height="38" style="object-fit:cover"
                                 onerror="this.src='https://placehold.co/50x38/1e40af/fff?text=V'">
                            <div>
                                <div class="fw-semibold">{{ $v->vehicle_name }}</div>
                                <div class="text-muted small">{{ $v->brand }} {{ $v->model }} {{ $v->year }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $v->category_name }}</span></td>
                    <td class="small">{{ $v->registration_number }}</td>
                    <td class="small">{{ ucfirst($v->fuel_type) }} / {{ ucfirst($v->transmission) }}</td>
                    <td class="fw-semibold text-primary">TK {{ number_format($v->price_per_day,2) }}</td>
                    <td>
                        @php
                        $sc = ['available'=>'success','rented'=>'primary','maintenance'=>'warning','inactive'=>'secondary'];
                        @endphp
                        <span class="badge bg-{{ $sc[$v->status] ?? 'secondary' }}">{{ ucfirst($v->status) }}</span>
                    </td>
                    <td>{{ $v->total_rentals }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.vehicles.show', $v->id) }}" class="btn btn-sm btn-light border" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.vehicles.edit', $v->id) }}" class="btn btn-sm btn-light border" title="Edit"><i class="fas fa-edit"></i></a>
                            <form id="del{{ $v->id }}" method="POST" action="{{ route('admin.vehicles.destroy', $v->id) }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-light border text-danger" title="Delete"
                                        onclick="confirmDelete('del{{ $v->id }}','Delete this vehicle?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">No vehicles found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $vehicles->links() }}</div>
</div>
@endsection
