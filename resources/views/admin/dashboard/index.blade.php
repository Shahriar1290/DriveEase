@extends('layouts.admin')
@section('title', 'Dashboard')
@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['label'=>'Total Vehicles',   'value'=>$stats['total_vehicles'],     'icon'=>'fas fa-car',          'color'=>'#0ea5e9','bg'=>'#eff6ff'],
        ['label'=>'Available',        'value'=>$stats['available_vehicles'], 'icon'=>'fas fa-check-circle', 'color'=>'#22c55e','bg'=>'#f0fdf4'],
        ['label'=>'Total Customers',  'value'=>$stats['total_customers'],    'icon'=>'fas fa-users',         'color'=>'#8b5cf6','bg'=>'#f5f3ff'],
        ['label'=>'Total Bookings',   'value'=>$stats['total_bookings'],     'icon'=>'fas fa-calendar',      'color'=>'#f59e0b','bg'=>'#fffbeb'],
        ['label'=>'Pending Bookings', 'value'=>$stats['pending_bookings'],   'icon'=>'fas fa-clock',         'color'=>'#ef4444','bg'=>'#fef2f2'],
        ['label'=>'Active Rentals',   'value'=>$stats['active_bookings'],    'icon'=>'fas fa-car-side',      'color'=>'#06b6d4','bg'=>'#ecfeff'],
        ['label'=>'Completed',        'value'=>$stats['completed_bookings'], 'icon'=>'fas fa-flag-checkered','color'=>'#10b981','bg'=>'#ecfdf5'],
        ['label'=>'Monthly Revenue',  'value'=>'TK '.number_format($stats['monthly_revenue'],0),'icon'=>'fas fa-taka-sign','color'=>'#6366f1','bg'=>'#eef2ff'],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card card p-3 h-100" style="box-shadow:0 1px 3px rgba(0,0,0,.07)">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:{{ $card['bg'] }};color:{{ $card['color'] }}">
                    <i class="{{ $card['icon'] }}"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0">{{ $card['value'] }}</div>
                    <div class="text-muted small">{{ $card['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    {{-- Revenue Chart --}}
    <div class="col-lg-8">
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0">Monthly Revenue (Last 12 Months)</h6>
                <span class="badge bg-primary">Total: TK {{ number_format($stats['total_revenue'],0) }}</span>
            </div>
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>

    {{-- Booking Status Pie --}}
    <div class="col-lg-4">
        <div class="table-card h-100">
            <h6 class="fw-bold mb-4">Booking Status</h6>
            <canvas id="bookingChart" height="200"></canvas>
            <div class="mt-3">
                @php
                $statusColors = ['pending'=>'#f59e0b','approved'=>'#22c55e','completed'=>'#0ea5e9','cancelled'=>'#9ca3af','rejected'=>'#ef4444','active'=>'#8b5cf6'];
                @endphp
                @foreach($bookingStats as $status => $count)
                <div class="d-flex justify-content-between align-items-center mb-1 small">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:10px;height:10px;border-radius:50%;background:{{ $statusColors[$status] ?? '#9ca3af' }}"></div>
                        <span>{{ ucfirst($status) }}</span>
                    </div>
                    <span class="fw-semibold">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Recent Bookings --}}
    <div class="col-lg-8">
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Recent Bookings</h6>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>Booking#</th><th>Customer</th><th>Vehicle</th><th>Amount</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $b)
                        <tr>
                            <td class="fw-semibold small">{{ $b->booking_number }}</td>
                            <td class="small">{{ $b->user_name }}</td>
                            <td class="small">{{ $b->vehicle_name }}</td>
                            <td class="small fw-semibold">TK {{ number_format($b->final_amount,0) }}</td>
                            <td>{!! booking_status_badge($b->booking_status) !!}</td>
                            <td><a href="{{ route('admin.bookings.show', $b->id) }}" class="btn btn-xs btn-light border btn-sm py-0 px-2"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top Vehicles --}}
    <div class="col-lg-4">
        <div class="table-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Top Vehicles</h6>
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            @foreach($topVehicles as $v)
            <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                <img src="{{ vehicle_image_url($v->primary_image) }}" class="rounded" width="48" height="40" style="object-fit:cover"
                     onerror="this.src='https://placehold.co/48x40/1e40af/fff?text=V'">
                <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-semibold small text-truncate">{{ $v->vehicle_name }}</div>
                    <div class="text-muted" style="font-size:11px">{{ $v->total_rentals }} rentals · TK {{ number_format($v->price_per_day) }}/day</div>
                </div>
                <span class="badge bg-primary-subtle text-primary">{{ $v->total_rentals }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Recent Customers --}}
    <div class="col-md-6">
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">New Customers</h6>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            @foreach($recentCustomers as $c)
            <div class="d-flex align-items-center gap-3 mb-3">
                <img src="{{ user_avatar_url($c->avatar, $c->name) }}" class="rounded-circle" width="38" height="38" style="object-fit:cover">
                <div class="flex-grow-1">
                    <div class="fw-semibold small">{{ $c->name }}</div>
                    <div class="text-muted" style="font-size:11px">{{ $c->email }}</div>
                </div>
                <div class="text-muted" style="font-size:11px">{{ \Carbon\Carbon::parse($c->created_at)->diffForHumans() }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Upcoming Maintenance --}}
    <div class="col-md-6">
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Upcoming Maintenance</h6>
                <a href="{{ route('admin.maintenance.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            @forelse($upcomingMaintenance as $m)
            <div class="d-flex align-items-start gap-3 mb-3 pb-2 border-bottom">
                <div class="rounded bg-warning-subtle d-flex align-items-center justify-content-center flex-shrink-0" style="width:38px;height:38px">
                    <i class="fas fa-wrench text-warning"></i>
                </div>
                <div>
                    <div class="fw-semibold small">{{ $m->vehicle_name }}</div>
                    <div class="text-muted" style="font-size:11px">{{ $m->maintenance_type }} · {{ \Carbon\Carbon::parse($m->maintenance_date)->format('M d, Y') }}</div>
                    <div class="text-muted" style="font-size:11px">Est. cost: TK {{ number_format($m->cost,0) }}</div>
                </div>
            </div>
            @empty
            <p class="text-muted small text-center py-2">No upcoming maintenance</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chartLabels = @json(array_keys($chartData));
const chartValues = @json(array_values($chartData));

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Revenue (TK)',
            data: chartValues,
            backgroundColor: 'rgba(14,165,233,.15)',
            borderColor: '#0ea5e9',
            borderWidth: 2,
            borderRadius: 6,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
    }
});

const bStats = @json($bookingStats);
new Chart(document.getElementById('bookingChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(bStats).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
        datasets: [{
            data: Object.values(bStats),
            backgroundColor: ['#f59e0b','#22c55e','#0ea5e9','#9ca3af','#ef4444','#8b5cf6'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
