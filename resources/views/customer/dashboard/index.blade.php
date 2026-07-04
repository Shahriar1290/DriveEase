@extends('layouts.customer')
@section('title', 'My Dashboard')

@section('content')
<div class="content-card mb-4">
    <h5 class="fw-bold mb-1">Welcome back, {{ auth()->user()->name }}! 👋</h5>
    <p class="text-muted small mb-0">Here's a summary of your rental activity.</p>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['label'=>'Total Bookings',    'value'=>$stats['total_bookings'],      'icon'=>'fas fa-calendar',       'color'=>'primary'],
        ['label'=>'Active Rentals',    'value'=>$stats['active_bookings'],     'icon'=>'fas fa-car-side',        'color'=>'success'],
        ['label'=>'Completed',         'value'=>$stats['completed_bookings'],  'icon'=>'fas fa-flag-checkered',  'color'=>'info'],
        ['label'=>'Pending',           'value'=>$stats['pending_bookings'],    'icon'=>'fas fa-clock',           'color'=>'warning'],
        ['label'=>'Wishlist',          'value'=>$stats['wishlist_count'],      'icon'=>'fas fa-heart',           'color'=>'danger'],
        ['label'=>'Notifications',     'value'=>$stats['unread_notifications'],'icon'=>'fas fa-bell',            'color'=>'secondary'],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="col-6 col-md-4">
        <div class="content-card text-center py-3">
            <i class="{{ $card['icon'] }} text-{{ $card['color'] }} mb-2" style="font-size:1.5rem"></i>
            <div class="h4 fw-bold mb-0">{{ $card['value'] }}</div>
            <div class="text-muted small">{{ $card['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Bookings --}}
<div class="content-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Recent Bookings</h6>
        <a href="{{ route('customer.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    @forelse($recentBookings as $b)
    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
        <img src="{{ vehicle_image_url($b->primary_image) }}" class="rounded" width="60" height="45" style="object-fit:cover"
             onerror="this.src='https://placehold.co/60x45/1e40af/fff?text=V'">
        <div class="flex-grow-1 overflow-hidden">
            <div class="fw-semibold small text-truncate">{{ $b->vehicle_name }}</div>
            <div class="text-muted" style="font-size:11px">
                {{ \Carbon\Carbon::parse($b->pickup_date)->format('M d') }} → {{ \Carbon\Carbon::parse($b->return_date)->format('M d, Y') }}
                · {{ $b->total_days }} days
            </div>
        </div>
        <div class="text-end">
            <div class="fw-semibold small">${{ number_format($b->final_amount, 0) }}</div>
            {!! booking_status_badge($b->booking_status) !!}
        </div>
        <a href="{{ route('customer.bookings.show', $b->id) }}" class="btn btn-sm btn-light border">
            <i class="fas fa-eye"></i>
        </a>
    </div>
    @empty
    <div class="text-center py-4">
        <i class="fas fa-calendar-times text-muted mb-2" style="font-size:2rem"></i>
        <p class="text-muted mb-2">No bookings yet</p>
        <a href="{{ route('vehicles.index') }}" class="btn btn-primary btn-sm">Browse Vehicles</a>
    </div>
    @endforelse
</div>

{{-- Notifications --}}
<div class="content-card">
    <h6 class="fw-bold mb-3">Recent Notifications</h6>
    @forelse($notifications as $n)
    <div class="d-flex gap-3 mb-3 pb-2 border-bottom {{ $n->status === 'unread' ? 'bg-light rounded p-2' : '' }}">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0
                    bg-{{ $n->type }}-subtle"
             style="width:36px;height:36px">
            <i class="fas fa-{{ $n->type === 'success' ? 'check' : ($n->type === 'danger' ? 'times' : 'info') }}
                       text-{{ $n->type }}" style="font-size:12px"></i>
        </div>
        <div>
            <div class="fw-semibold small">{{ $n->title }}</div>
            <div class="text-muted small">{{ $n->message }}</div>
            <div class="text-muted" style="font-size:11px">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</div>
        </div>
        @if($n->status === 'unread')
            <span class="badge bg-primary ms-auto align-self-start" style="font-size:9px">New</span>
        @endif
    </div>
    @empty
    <p class="text-muted text-center py-2 small">No notifications</p>
    @endforelse
</div>
@endsection
