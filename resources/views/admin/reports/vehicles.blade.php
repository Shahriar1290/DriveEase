@extends('layouts.admin')
@section('title','Vehicle Report')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}" class="text-decoration-none">Reports</a></li>
<li class="breadcrumb-item active">Vehicles</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Vehicle Rental Report</h5>
    <a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf me-1"></i>Export PDF</a>
</div>
<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr><th>Vehicle</th><th>Category</th><th>Price/Day</th><th>Total Rentals</th><th>Completed</th><th>Revenue</th><th>Avg Rating</th></tr>
            </thead>
            <tbody>
                @foreach($vehicles as $v)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ vehicle_image_url($v->primary_image) }}" class="rounded" width="44" height="33" style="object-fit:cover"
                                 onerror="this.src='https://placehold.co/44x33/1e40af/fff?text=V'">
                            <div>
                                <div class="fw-semibold small">{{ $v->vehicle_name }}</div>
                                <div class="text-muted" style="font-size:11px">{{ $v->registration_number }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="small">{{ $v->category_name }}</td>
                    <td class="small">TK {{ number_format($v->price_per_day,2) }}</td>
                    <td><span class="badge bg-primary-subtle text-primary">{{ $v->total_rentals }}</span></td>
                    <td>{{ $v->completed_bookings }}</td>
                    <td class="fw-semibold text-success">TK {{ number_format($v->total_revenue ?? 0,2) }}</td>
                    <td>
                        <div class="d-flex gap-1 align-items-center">
                            <i class="fas fa-star text-warning" style="font-size:11px"></i>
                            <span class="small">{{ $v->average_rating }}</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
