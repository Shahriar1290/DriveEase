@extends('layouts.admin')
@section('title','Maintenance Report')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}" class="text-decoration-none">Reports</a></li>
<li class="breadcrumb-item active">Maintenance</li>
@endsection
@section('content')
<h5 class="fw-bold mb-4">Maintenance Cost Report</h5>
<form method="GET" class="table-card mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-4"><label class="form-label small fw-semibold">From</label><input type="date" name="from" class="form-control" value="{{ $from }}"></div>
        <div class="col-md-4"><label class="form-label small fw-semibold">To</label><input type="date" name="to" class="form-control" value="{{ $to }}"></div>
        <div class="col-md-4"><button class="btn btn-primary w-100 mt-4">Generate</button></div>
    </div>
</form>
<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="table-card text-center"><div class="h3 fw-bold text-danger">${{ number_format($totalCost,2) }}</div><div class="text-muted">Total Maintenance Cost</div></div></div>
    <div class="col-md-4"><div class="table-card text-center"><div class="h3 fw-bold text-primary">{{ count($records) }}</div><div class="text-muted">Total Records</div></div></div>
    <div class="col-md-4"><div class="table-card text-center"><div class="h3 fw-bold text-warning">${{ count($records) > 0 ? number_format($totalCost/count($records),2) : '0.00' }}</div><div class="text-muted">Avg Cost/Record</div></div></div>
</div>
<div class="row g-4 mb-4">
    <div class="col-md-5">
        <div class="table-card">
            <h6 class="fw-bold mb-3">By Type</h6>
            @foreach($byType as $type => $data)
            <div class="d-flex justify-content-between mb-2 small">
                <span>{{ $type }}</span>
                <div class="text-end"><div class="fw-semibold">${{ number_format($data['cost'],2) }}</div><div class="text-muted">{{ $data['count'] }} records</div></div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="col-md-7">
        <div class="table-card">
            <h6 class="fw-bold mb-3">Records</h6>
            <div class="table-responsive" style="max-height:300px;overflow-y:auto">
                <table class="table table-sm">
                    <thead class="table-light sticky-top"><tr><th>Vehicle</th><th>Type</th><th>Date</th><th>Cost</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($records as $r)
                        <tr>
                            <td class="small">{{ $r->vehicle_name }}</td>
                            <td class="small">{{ $r->maintenance_type }}</td>
                            <td class="small">{{ \Carbon\Carbon::parse($r->maintenance_date)->format('M d, Y') }}</td>
                            <td class="small fw-semibold">${{ number_format($r->cost,2) }}</td>
                            <td>{!! maintenance_status_badge($r->status) !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
