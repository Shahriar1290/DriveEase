@extends('layouts.admin')
@section('title','Revenue Report')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}" class="text-decoration-none">Reports</a></li>
<li class="breadcrumb-item active">Revenue</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Revenue Report</h5>
    <a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-danger btn-sm">
        <i class="fas fa-file-pdf me-1"></i>Export PDF
    </a>
</div>

<form method="GET" class="table-card mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-semibold">From</label>
            <input type="date" name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">To</label>
            <input type="date" name="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary w-100">Generate Report</button>
        </div>
    </div>
</form>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="table-card text-center">
            <div class="h3 fw-bold text-success">${{ number_format($total,2) }}</div>
            <div class="text-muted">Total Revenue</div>
            <div class="text-muted small">{{ \Carbon\Carbon::parse($from)->format('M d') }} – {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card text-center">
            <div class="h3 fw-bold text-primary">{{ count($payments) }}</div>
            <div class="text-muted">Transactions</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card text-center">
            <div class="h3 fw-bold text-warning">${{ count($payments) > 0 ? number_format($total/count($payments),2) : '0.00' }}</div>
            <div class="text-muted">Avg. Transaction</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-7">
        <div class="table-card">
            <h6 class="fw-bold mb-3">Daily Revenue</h6>
            <canvas id="dailyChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-md-5">
        <div class="table-card">
            <h6 class="fw-bold mb-3">By Payment Method</h6>
            @foreach($byMethod as $method => $data)
            <div class="d-flex justify-content-between align-items-center mb-2 small">
                <span>{{ ucwords(str_replace('_',' ',$method)) }}</span>
                <div class="text-end">
                    <div class="fw-semibold">${{ number_format($data['total'],2) }}</div>
                    <div class="text-muted">{{ $data['count'] }} txns</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="table-card">
    <h6 class="fw-bold mb-3">Transaction Details</h6>
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead class="table-light"><tr><th>Date</th><th>Transaction ID</th><th>Customer</th><th>Booking#</th><th>Method</th><th>Amount</th></tr></thead>
            <tbody>
                @foreach(array_slice($payments, 0, 50) as $p)
                <tr>
                    <td class="small">{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</td>
                    <td class="small">{{ $p->transaction_id }}</td>
                    <td class="small">{{ $p->user_name }}</td>
                    <td class="small">{{ $p->booking_number }}</td>
                    <td class="small">{{ ucwords(str_replace('_',' ',$p->payment_method)) }}</td>
                    <td class="fw-semibold text-success">${{ number_format($p->amount,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: {
        labels: @json(array_keys($daily)),
        datasets: [{
            label: 'Revenue', data: @json(array_values($daily)),
            borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,.1)',
            fill: true, tension: 0.4, pointRadius: 3,
        }]
    },
    options: { responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
});
</script>
@endpush
