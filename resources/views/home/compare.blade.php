@extends('layouts.app')
@section('title', 'Compare Vehicles — DriveEase')

@push('styles')
<style>
.hero-compare {
    background: linear-gradient(135deg, #090d16 0%, #0f172a 100%);
    position: relative;
    padding: 2.75rem 0;
}
.compare-table {
    border-collapse: separate;
    border-spacing: 0;
    border: 1px solid var(--gray-200, #e2e8f0);
    border-radius: var(--radius-xl, 1.25rem);
    overflow: hidden;
    background: #ffffff;
    box-shadow: var(--shadow-md, 0 4px 12px -2px rgba(15, 23, 42, 0.08));
}
.compare-table th, .compare-table td {
    padding: 1rem 1.25rem;
    border-color: var(--gray-200, #e2e8f0);
}
.compare-spec-col {
    background: var(--gray-50, #f8fafc);
    font-weight: 700;
    color: var(--dark-800, #1e293b);
    width: 220px;
    font-size: 0.85rem;
}
.vehicle-compare-header {
    background: #ffffff;
    vertical-align: bottom;
}
.vehicle-compare-header img {
    border-radius: var(--radius-md, 0.625rem);
    object-fit: cover;
    box-shadow: var(--shadow-sm);
    transition: transform 0.2s ease;
}
.vehicle-compare-header:hover img {
    transform: scale(1.03);
}
</style>
@endpush

@section('content')
<div class="hero-compare text-white">
    <div class="container position-relative" style="z-index: 1;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-white-50 mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}" class="text-white-50 text-decoration-none">Vehicles</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Compare</li>
            </ol>
        </nav>
        <h1 class="h2 fw-bold mb-1 text-white">Side-by-Side Fleet Comparison</h1>
        <p class="text-secondary small mb-0">Evaluate technical specifications, pricing, passenger space, and features before reserving.</p>
    </div>
</div>

<div class="container py-5">
    @if(count($vehicles) < 2)
    <div class="text-center py-5 bg-white rounded-4 border p-5 shadow-sm">
        <div class="category-icon-wrap mx-auto mb-3" style="width:72px;height:72px;font-size:2rem">
            <i class="fas fa-columns"></i>
        </div>
        <h5 class="fw-bold text-dark">Select At Least 2 Vehicles</h5>
        <p class="text-muted small mb-4">You need at least two vehicles in your comparison pool to view a side-by-side analysis.</p>
        <a href="{{ route('vehicles.index') }}" class="btn btn-primary px-4">
            <i class="fas fa-car-side me-1"></i> Browse Vehicles & Select
        </a>
    </div>
    @else
    <div class="table-responsive shadow-sm rounded-4">
        <table class="table compare-table align-middle mb-0">
            <thead>
                <tr>
                    <th class="compare-spec-col">Fleet Specifications</th>
                    @foreach($vehicles as $v)
                    <th class="text-center vehicle-compare-header p-4">
                        <img src="{{ vehicle_image_url($v->primary_image) }}" class="mb-3 d-block mx-auto"
                             width="160" height="110" alt="{{ $v->vehicle_name }}">
                        <h6 class="fw-bold text-dark mb-1">{{ $v->vehicle_name }}</h6>
                        <span class="badge bg-light text-dark border small rounded-pill">{{ $v->brand }} {{ $v->year }}</span>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                $specs = [
                    ['Category',      fn($v) => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill">'.($v->category?->category_name ?? $v->category_name ?? 'N/A').'</span>'],
                    ['Daily Rate',    fn($v) => '<span class="fw-bold text-primary fs-6">TK '.number_format($v->price_per_day).'</span> <small class="text-muted">/day</small>'],
                    ['Fuel Type',     fn($v) => '<i class="fas fa-gas-pump text-muted me-1"></i> '.ucfirst($v->fuel_type)],
                    ['Transmission',  fn($v) => '<i class="fas fa-gear text-muted me-1"></i> '.ucfirst($v->transmission)],
                    ['Seating Space', fn($v) => '<i class="fas fa-users text-muted me-1"></i> '.$v->seating_capacity.' Adults'],
                    ['Exterior Color',fn($v) => $v->color ?? 'Standard'],
                    ['Odometer',      fn($v) => $v->mileage ?? 'N/A'],
                    ['Engine Disp.',  fn($v) => $v->engine_cc ? $v->engine_cc.' cc' : 'Standard'],
                    ['Customer Score',fn($v) => '<i class="fas fa-star text-warning"></i> <strong class="text-dark">'.$v->average_rating.'</strong> <span class="text-muted small">('.$v->review_count.' reviews)</span>'],
                    ['Air Conditioning', fn($v) => $v->air_conditioning ? '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fas fa-check me-1"></i>Included</span>' : '<span class="text-muted small"><i class="fas fa-xmark me-1"></i>None</span>'],
                    ['GPS Navigation',   fn($v) => $v->gps ? '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fas fa-check me-1"></i>Included</span>' : '<span class="text-muted small"><i class="fas fa-xmark me-1"></i>None</span>'],
                    ['Bluetooth Audio', fn($v) => $v->bluetooth ? '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fas fa-check me-1"></i>Included</span>' : '<span class="text-muted small"><i class="fas fa-xmark me-1"></i>None</span>'],
                    ['USB Chargers',     fn($v) => $v->usb_charger ? '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fas fa-check me-1"></i>Included</span>' : '<span class="text-muted small"><i class="fas fa-xmark me-1"></i>None</span>'],
                    ['Child Seat Option', fn($v) => $v->child_seat ? '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fas fa-check me-1"></i>Available</span>' : '<span class="text-muted small"><i class="fas fa-xmark me-1"></i>None</span>'],
                    ['Fleet Status',    fn($v) => '<span class="badge bg-'.($v->status === 'available'?'success':'secondary').' rounded-pill">'.ucfirst($v->status).'</span>'],
                    ['Total Rentals',   fn($v) => '<span class="fw-semibold text-dark">'.$v->total_rentals.'</span> completed'],
                ];
                @endphp
                @foreach($specs as [$label, $fn])
                <tr>
                    <td class="compare-spec-col">{{ $label }}</td>
                    @foreach($vehicles as $v)
                    <td class="text-center small py-3">{!! $fn($v) !!}</td>
                    @endforeach
                </tr>
                @endforeach
                <tr class="bg-light">
                    <td class="compare-spec-col">Reservation Action</td>
                    @foreach($vehicles as $v)
                    <td class="text-center p-3">
                        @if($v->status === 'available')
                        <a href="{{ route('vehicles.show', $v->slug) }}" class="btn btn-primary btn-sm px-4 shadow-sm">
                            <i class="fas fa-calendar-check me-1"></i> Book Now
                        </a>
                        @else
                        <span class="badge bg-light text-muted border">Unavailable</span>
                        @endif
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 text-center">
        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary px-4">
            <i class="fas fa-arrow-left me-2"></i>Back to Fleet Catalog
        </a>
    </div>
    @endif
</div>
@endsection

