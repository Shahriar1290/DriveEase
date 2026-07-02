@extends('layouts.admin')
@section('title','Reports')
@section('breadcrumb')<li class="breadcrumb-item active">Reports</li>@endsection
@section('content')
<h5 class="fw-bold mb-4">Reports & Analytics</h5>
<div class="row g-4">
    @foreach([
        ['title'=>'Revenue Report','icon'=>'fas fa-dollar-sign','color'=>'success','desc'=>'Monthly and daily revenue breakdown with payment method analysis.','route'=>'admin.reports.revenue'],
        ['title'=>'Vehicle Report','icon'=>'fas fa-car','color'=>'primary','desc'=>'Most rented vehicles, utilization rates, and revenue per vehicle.','route'=>'admin.reports.vehicles'],
        ['title'=>'Customer Report','icon'=>'fas fa-users','color'=>'purple','desc'=>'Top customers, spending analysis, and activity trends.','route'=>'admin.reports.customers'],
        ['title'=>'Maintenance Report','icon'=>'fas fa-wrench','color'=>'warning','desc'=>'Maintenance costs, service history, and upcoming schedules.','route'=>'admin.reports.maintenance'],
    ] as $r)
    <div class="col-md-6">
        <a href="{{ route($r['route']) }}" class="text-decoration-none">
            <div class="table-card h-100 hover-shadow" style="transition:transform .2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:54px;height:54px;background:var(--bs-{{ $r['color'] }}-bg,#f0fdf4)">
                        <i class="{{ $r['icon'] }} text-{{ $r['color'] }} fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">{{ $r['title'] }}</h6>
                        <p class="text-muted small mb-0">{{ $r['desc'] }}</p>
                    </div>
                    <i class="fas fa-arrow-right text-muted ms-auto mt-1"></i>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endsection
