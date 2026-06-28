@extends('layouts.app')
@section('title','Compare Vehicles — DriveEase')
@section('content')
<div class="bg-dark text-white py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-white-50 mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}" class="text-white-50 text-decoration-none">Vehicles</a></li>
                <li class="breadcrumb-item active text-white">Compare</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Compare Vehicles</h2>
    </div>
</div>

<div class="container py-5">
    @if(count($vehicles) < 2)
    <div class="text-center py-5">
        <i class="fas fa-columns text-muted mb-3" style="font-size:3rem"></i>
        <h5 class="text-muted">Select at least 2 vehicles to compare</h5>
        <a href="{{ route('vehicles.index') }}" class="btn btn-primary mt-2">Browse Vehicles</a>
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th class="bg-dark text-white" style="width:200px">Specification</th>
                    @foreach($vehicles as $v)
                    <th class="text-center bg-light">
                        <img src="{{ vehicle_image_url($v->primary_image) }}" class="rounded mb-2 d-block mx-auto"
                             width="140" height="95" style="object-fit:cover"
                             onerror="this.src='https://placehold.co/140x95/1e40af/fff?text={{ urlencode($v->vehicle_name) }}'">
                        <div class="fw-bold">{{ $v->vehicle_name }}</div>
                        <div class="text-muted small">{{ $v->brand }} {{ $v->year }}</div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                $specs = [
                    ['Category',      fn($v) => $v->category_name],
                    ['Price/Day',     fn($v) => '<strong class="text-primary">$'.number_format($v->price_per_day,2).'</strong>'],
                    ['Fuel Type',     fn($v) => ucfirst($v->fuel_type)],
                    ['Transmission',  fn($v) => ucfirst($v->transmission)],
                    ['Seating',       fn($v) => $v->seating_capacity.' persons'],
                    ['Color',         fn($v) => $v->color ?? 'N/A'],
                    ['Mileage',       fn($v) => $v->mileage ?? 'N/A'],
                    ['Engine',        fn($v) => $v->engine_cc ?? 'N/A'],
                    ['Rating',        fn($v) => '⭐ '.$v->average_rating.' ('.$v->review_count.' reviews)'],
                    ['Air Cond.',     fn($v) => $v->air_conditioning ? '<span class="text-success">✓ Yes</span>' : '<span class="text-danger">✗ No</span>'],
                    ['GPS',           fn($v) => $v->gps ? '<span class="text-success">✓ Yes</span>' : '<span class="text-danger">✗ No</span>'],
                    ['Bluetooth',     fn($v) => $v->bluetooth ? '<span class="text-success">✓ Yes</span>' : '<span class="text-danger">✗ No</span>'],
                    ['USB Charger',   fn($v) => $v->usb_charger ? '<span class="text-success">✓ Yes</span>' : '<span class="text-danger">✗ No</span>'],
                    ['Child Seat',    fn($v) => $v->child_seat ? '<span class="text-success">✓ Yes</span>' : '<span class="text-danger">✗ No</span>'],
                    ['Status',        fn($v) => '<span class="badge bg-'.($v->status === 'available'?'success':'secondary').'">'.ucfirst($v->status).'</span>'],
                    ['Total Rentals', fn($v) => $v->total_rentals],
                ];
                @endphp
                @foreach($specs as [$label, $fn])
                <tr>
                    <td class="fw-semibold text-muted small bg-light">{{ $label }}</td>
                    @foreach($vehicles as $v)
                    <td class="text-center small">{!! $fn($v) !!}</td>
                    @endforeach
                </tr>
                @endforeach
                <tr>
                    <td class="bg-light"></td>
                    @foreach($vehicles as $v)
                    <td class="text-center">
                        @if($v->status === 'available')
                        <a href="{{ route('vehicles.show', $v->slug) }}" class="btn btn-primary btn-sm px-3">Book Now</a>
                        @else
                        <span class="text-muted small">Unavailable</span>
                        @endif
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-3 text-center">
        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Vehicles
        </a>
    </div>
    @endif
</div>
@endsection
