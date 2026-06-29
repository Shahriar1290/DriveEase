@extends('layouts.admin')
@section('title', $vehicle->vehicle_name)
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.vehicles.index') }}" class="text-decoration-none">Vehicles</a></li>
<li class="breadcrumb-item active">{{ $vehicle->vehicle_name }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $vehicle->vehicle_name }}</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-edit me-1"></i>Edit
        </a>
        <form id="dvDel" method="POST" action="{{ route('admin.vehicles.destroy', $vehicle->id) }}">
            @csrf @method('DELETE')
            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('dvDel','Delete this vehicle and all its data?')">
                <i class="fas fa-trash me-1"></i>Delete
            </button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Images --}}
        <div class="table-card mb-4">
            <div class="row g-2">
                @forelse($images as $img)
                <div class="col-4 col-md-3">
                    <img src="{{ vehicle_image_url($img->image) }}" class="rounded w-100" style="height:90px;object-fit:cover"
                         onerror="this.src='https://placehold.co/200x90/1e40af/fff?text=V'">
                    @if($img->is_primary)<span class="badge bg-primary" style="font-size:9px">Primary</span>@endif
                </div>
                @empty
                <p class="text-muted small">No images uploaded.</p>
                @endforelse
            </div>
        </div>

        {{-- Specs --}}
        <div class="table-card mb-4">
            <h6 class="fw-bold mb-3">Specifications</h6>
            <div class="row g-2">
                @foreach([
                    ['Category',     $vehicle->category_name],
                    ['Brand',        $vehicle->brand],
                    ['Model',        $vehicle->model],
                    ['Year',         $vehicle->year],
                    ['Reg. No.',     $vehicle->registration_number],
                    ['Fuel Type',    ucfirst($vehicle->fuel_type)],
                    ['Transmission', ucfirst($vehicle->transmission)],
                    ['Seating',      $vehicle->seating_capacity.' persons'],
                    ['Color',        $vehicle->color ?? 'N/A'],
                    ['Mileage',      $vehicle->mileage ?? 'N/A'],
                    ['Engine',       $vehicle->engine_cc ?? 'N/A'],
                    ['Price/Day',    '$'.number_format($vehicle->price_per_day,2)],
                    ['Status',       ucfirst($vehicle->status)],
                    ['Avg Rating',   $vehicle->average_rating.' ⭐'],
                    ['Total Rentals',$vehicle->total_rentals],
                ] as [$k,$v])
                <div class="col-md-4 col-6">
                    <div class="bg-light rounded p-2">
                        <div class="text-muted" style="font-size:10px;text-transform:uppercase;letter-spacing:.04em">{{ $k }}</div>
                        <div class="fw-semibold small">{{ $v }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Features --}}
        <div class="table-card mb-4">
            <h6 class="fw-bold mb-3">Features</h6>
            <div class="d-flex flex-wrap gap-2">
                @foreach([
                    [$vehicle->air_conditioning,'Air Conditioning'],
                    [$vehicle->gps,'GPS'],
                    [$vehicle->bluetooth,'Bluetooth'],
                    [$vehicle->usb_charger,'USB Charger'],
                    [$vehicle->child_seat,'Child Seat'],
                ] as [$has,$label])
                <span class="badge {{ $has ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2">
                    <i class="fas fa-{{ $has ? 'check' : 'times' }} me-1"></i>{{ $label }}
                </span>
                @endforeach
            </div>
        </div>

        {{-- Recent Bookings --}}
        <div class="table-card">
            <h6 class="fw-bold mb-3">Recent Bookings ({{ count($bookings) }})</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light"><tr><th>Booking#</th><th>Customer</th><th>Dates</th><th>Amount</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($bookings as $b)
                        <tr>
                            <td class="small">{{ $b->booking_number }}</td>
                            <td class="small">{{ $b->user_name }}</td>
                            <td class="small">{{ \Carbon\Carbon::parse($b->pickup_date)->format('M d') }} – {{ \Carbon\Carbon::parse($b->return_date)->format('M d, Y') }}</td>
                            <td class="small">${{ number_format($b->final_amount,0) }}</td>
                            <td>{!! booking_status_badge($b->booking_status) !!}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted small">No bookings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Description --}}
        @if($vehicle->description)
        <div class="table-card mb-4">
            <h6 class="fw-bold mb-2">Description</h6>
            <p class="text-muted small mb-0">{{ $vehicle->description }}</p>
        </div>
        @endif

        {{-- Maintenance --}}
        <div class="table-card mb-4">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="fw-bold mb-0">Maintenance</h6>
                <a href="{{ route('admin.maintenance.create') }}" class="btn btn-sm btn-outline-primary">Add</a>
            </div>
            @forelse(array_slice($maintenanceRecords, 0, 4) as $m)
            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom small">
                <div>
                    <div class="fw-semibold">{{ $m->maintenance_type }}</div>
                    <div class="text-muted">{{ \Carbon\Carbon::parse($m->maintenance_date)->format('M d, Y') }}</div>
                </div>
                <div class="text-end">
                    <div>${{ number_format($m->cost,0) }}</div>
                    {!! maintenance_status_badge($m->status) !!}
                </div>
            </div>
            @empty
            <p class="text-muted small">No maintenance records.</p>
            @endforelse
        </div>

        {{-- Reviews --}}
        <div class="table-card">
            <h6 class="fw-bold mb-3">Reviews ({{ $allReviewCount }})</h6>
            @forelse($reviews as $r)
            <div class="mb-3 pb-2 border-bottom">
                <div class="d-flex justify-content-between">
                    <span class="fw-semibold small">{{ $r->user_name }}</span>
                    <div>@for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i<=$r->rating?'text-warning':'text-muted' }}" style="font-size:10px"></i>@endfor</div>
                </div>
                <p class="text-muted small mb-0">{{ Str::limit($r->review, 80) }}</p>
            </div>
            @empty
            <p class="text-muted small">No reviews yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
