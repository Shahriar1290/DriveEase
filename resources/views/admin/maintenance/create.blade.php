@extends('layouts.admin')
@section('title', isset($maintenance) ? 'Edit Maintenance' : 'Add Maintenance')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.maintenance.index') }}" class="text-decoration-none">Maintenance</a></li>
<li class="breadcrumb-item active">{{ isset($maintenance) ? 'Edit' : 'Add' }}</li>
@endsection
@section('content')
<div class="row justify-content-center"><div class="col-lg-8">
<div class="table-card">
    <h5 class="fw-bold mb-4">{{ isset($maintenance) ? 'Edit Maintenance Record' : 'Add Maintenance Record' }}</h5>
    @if($errors->any())
    <div class="alert alert-danger small">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
    @endif
    <form method="POST" action="{{ isset($maintenance) ? route('admin.maintenance.update',$maintenance->id) : route('admin.maintenance.store') }}">
        @csrf @if(isset($maintenance)) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Vehicle <span class="text-danger">*</span></label>
                <select name="vehicle_id" class="form-select" required>
                    <option value="">Select vehicle</option>
                    @foreach($vehicles as $v)
                    <option value="{{ $v->id }}" {{ old('vehicle_id', $maintenance->vehicle_id ?? '') == $v->id ? 'selected' : '' }}>
                        {{ $v->vehicle_name }} ({{ $v->registration_number }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Maintenance Type <span class="text-danger">*</span></label>
                <select name="maintenance_type" class="form-select" required>
                    @foreach(['Oil Change','Tire Rotation','Brake Service','Engine Tune-up','Battery Replacement','AC Service','Transmission Service','Full Service','Other'] as $t)
                    <option value="{{ $t }}" {{ old('maintenance_type', $maintenance->maintenance_type ?? '') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Maintenance Date <span class="text-danger">*</span></label>
                <input type="date" name="maintenance_date" class="form-control" required
                       value="{{ old('maintenance_date', isset($maintenance) ? substr($maintenance->maintenance_date, 0, 10) : '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Next Maintenance Date</label>
                <input type="date" name="next_maintenance_date" class="form-control"
                       value="{{ old('next_maintenance_date', isset($maintenance) ? substr($maintenance->next_maintenance_date ?? '', 0, 10) : '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Cost ($) <span class="text-danger">*</span></label>
                <input type="number" name="cost" class="form-control" step="0.01" min="0" required
                       value="{{ old('cost', $maintenance->cost ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Mechanic Name</label>
                <input type="text" name="mechanic_name" class="form-control"
                       value="{{ old('mechanic_name', $maintenance->mechanic_name ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Service Center</label>
                <input type="text" name="service_center" class="form-control"
                       value="{{ old('service_center', $maintenance->service_center ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    @foreach(['scheduled','in_progress','completed'] as $s)
                    <option value="{{ $s }}" {{ old('status', $maintenance->status ?? 'scheduled') == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small">Description <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="3" required>{{ old('description', $maintenance->description ?? '') }}</textarea>
            </div>
            <div class="col-12 d-flex gap-3">
                <button type="submit" class="btn btn-primary px-5"><i class="fas fa-save me-1"></i>Save Record</button>
                <a href="{{ route('admin.maintenance.index') }}" class="btn btn-light border px-4">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div></div>
@endsection
