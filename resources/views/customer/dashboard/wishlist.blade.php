@extends('layouts.customer')
@section('title', 'My Wishlist')

@section('content')
<div class="content-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="fw-bold mb-0">My Wishlist ({{ $wishlists->total() }})</h6>
        <a href="{{ route('vehicles.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-search me-1"></i>Browse More
        </a>
    </div>

    @if($wishlists->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-heart text-muted mb-3" style="font-size:3rem"></i>
        <h6 class="text-muted">Your wishlist is empty</h6>
        <p class="text-muted small">Save vehicles you're interested in to find them easily later.</p>
        <a href="{{ route('vehicles.index') }}" class="btn btn-primary">Browse Vehicles</a>
    </div>
    @else
    <div class="row g-4">
        @foreach($wishlists as $w)
        @php $veh = $w->vehicle; @endphp
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 rounded-3 overflow-hidden shadow-sm h-100">
                <div class="position-relative">
                    <img src="{{ vehicle_image_url($veh?->primary_image ?? $w->primary_image ?? null) }}" class="card-img-top"
                         style="height:160px;object-fit:cover">
                    <span class="position-absolute top-0 start-0 m-2 badge bg-{{ ($veh?->status ?? 'available') === 'available' ? 'success' : 'secondary' }}">
                        {{ ucfirst($veh?->status ?? 'available') }}
                    </span>
                    <form action="{{ route('customer.wishlist.toggle') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="vehicle_id" value="{{ $w->vehicle_id }}">
                        <button type="submit" class="position-absolute top-0 end-0 m-2 btn btn-sm btn-danger rounded-circle" style="width:30px;height:30px;padding:0" title="Remove">
                            <i class="fas fa-times" style="font-size:11px"></i>
                        </button>
                    </form>
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="fw-bold mb-1">{{ $veh?->vehicle_name ?? 'Vehicle' }}</h6>
                    <p class="text-muted small mb-2">{{ $veh?->brand }} {{ $veh?->model }} · {{ $veh?->year }}</p>
                    <div class="d-flex gap-2 text-muted small mb-3">
                        <span><i class="fas fa-users me-1"></i>{{ $veh?->seating_capacity }}</span>
                        <span><i class="fas fa-gas-pump me-1"></i>{{ ucfirst($veh?->fuel_type ?? '') }}</span>
                        <span><i class="fas fa-cog me-1"></i>{{ ucfirst($veh?->transmission ?? '') }}</span>
                    </div>
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-primary">TK {{ number_format($veh?->price_per_day ?? 0) }}</span>
                            <span class="text-muted small">/day</span>
                        </div>
                        <a href="{{ route('vehicles.show', $veh?->slug ?? '#') }}" class="btn btn-primary btn-sm">
                            View
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $wishlists->links() }}</div>
    @endif
</div>
@endsection
