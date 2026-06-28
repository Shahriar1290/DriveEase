@extends('layouts.app')
@section('title', 'Browse Vehicles — DriveEase')

@push('styles')
<style>
.filter-card { background:#fff; border-radius:.75rem; padding:1.5rem; position:sticky; top:80px; }
.filter-title { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#9ca3af; }
.vehicle-grid-card { border:none; border-radius:.75rem; overflow:hidden; transition:all .25s; box-shadow:0 1px 4px rgba(0,0,0,.07); }
.vehicle-grid-card:hover { transform:translateY(-4px); box-shadow:0 12px 32px rgba(0,0,0,.12); }
.vehicle-grid-card .card-img-top { height:190px; object-fit:cover; }
.compare-checkbox { position:absolute; bottom:.75rem; left:.75rem; background:rgba(255,255,255,.95); border-radius:.375rem; padding:.2rem .4rem; }
.price-range-display { color:#0ea5e9; font-weight:700; font-size:.95rem; }
</style>
@endpush

@section('content')
<div class="bg-dark text-white py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-white-50 mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white">Vehicles</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Browse Vehicles</h2>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        {{-- Filters --}}
        <div class="col-lg-3">
            <div class="filter-card shadow-sm">
                <form method="GET" action="{{ route('vehicles.index') }}" id="filterForm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Filters</h6>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-link btn-sm p-0 text-muted">Clear all</a>
                    </div>

                    <div class="mb-4">
                        <p class="filter-title mb-2">Search</p>
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Brand, model..." value="{{ request('search') }}">
                    </div>

                    <div class="mb-4">
                        <p class="filter-title mb-2">Category</p>
                        @foreach($categories as $cat)
                        <div class="form-check mb-1">
                            <input class="form-check-input filter-input" type="radio" name="category"
                                   id="cat{{ $cat->id }}" value="{{ $cat->id }}"
                                   {{ request('category') == $cat->id ? 'checked' : '' }}>
                            <label class="form-check-label small" for="cat{{ $cat->id }}">
                                {{ $cat->category_name }}
                                <span class="text-muted">({{ $cat->vehicles_count }})</span>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-4">
                        <p class="filter-title mb-2">Fuel Type</p>
                        @foreach(['petrol','diesel','electric','hybrid','cng'] as $fuel)
                        <div class="form-check mb-1">
                            <input class="form-check-input filter-input" type="radio" name="fuel_type"
                                   id="fuel_{{ $fuel }}" value="{{ $fuel }}"
                                   {{ request('fuel_type') == $fuel ? 'checked' : '' }}>
                            <label class="form-check-label small" for="fuel_{{ $fuel }}">{{ ucfirst($fuel) }}</label>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-4">
                        <p class="filter-title mb-2">Transmission</p>
                        @foreach(['manual','automatic','semi-automatic'] as $trans)
                        <div class="form-check mb-1">
                            <input class="form-check-input filter-input" type="radio" name="transmission"
                                   id="trans_{{ $trans }}" value="{{ $trans }}"
                                   {{ request('transmission') == $trans ? 'checked' : '' }}>
                            <label class="form-check-label small" for="trans_{{ $trans }}">{{ ucfirst($trans) }}</label>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-4">
                        <p class="filter-title mb-2">Price Per Day</p>
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">$0</small>
                            <span class="price-range-display small" id="priceDisplay">
                                ${{ request('max_price', $maxPrice) }}
                            </span>
                        </div>
                        <input type="range" class="form-range" name="max_price" id="priceRange"
                               min="0" max="{{ $maxPrice }}" step="10"
                               value="{{ request('max_price', $maxPrice) }}">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-sm">Apply Filters</button>
                </form>
            </div>
        </div>

        {{-- Results --}}
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <p class="mb-0 text-muted">
                    Showing <strong>{{ $vehicles->firstItem() }}–{{ $vehicles->lastItem() }}</strong>
                    of <strong>{{ $vehicles->total() }}</strong> vehicles
                </p>
                <div class="d-flex gap-2 align-items-center">
                    <a href="{{ route('vehicles.compare', request()->merge(['ids' => collect(request('compare_ids', []))])->query()) }}"
                       class="btn btn-outline-primary btn-sm" id="compareBtn" style="display:none!important">
                        <i class="fas fa-columns me-1"></i>Compare (<span id="compareCount">0</span>)
                    </a>
                    <select name="sort" class="form-select form-select-sm" style="width:180px" onchange="this.form.submit()" form="filterForm">
                        <option value="popular"    {{ request('sort')=='popular'   ?'selected':'' }}>Most Popular</option>
                        <option value="price_low"  {{ request('sort')=='price_low' ?'selected':'' }}>Price: Low → High</option>
                        <option value="price_high" {{ request('sort')=='price_high'?'selected':'' }}>Price: High → Low</option>
                        <option value="newest"     {{ request('sort')=='newest'    ?'selected':'' }}>Newest First</option>
                    </select>
                </div>
            </div>

            @if($vehicles->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-car text-muted" style="font-size:3rem"></i>
                <h5 class="mt-3 text-muted">No vehicles found</h5>
                <p class="text-muted">Try adjusting your filters</p>
                <a href="{{ route('vehicles.index') }}" class="btn btn-primary">Clear Filters</a>
            </div>
            @else
            <div class="row g-4" id="vehicleGrid">
                @foreach($vehicles as $vehicle)
                <div class="col-sm-6 col-xl-4">
                    <div class="vehicle-grid-card card h-100">
                        <div class="position-relative">
                            <img src="{{ vehicle_image_url($vehicle->primary_image) }}"
                                 class="card-img-top" alt="{{ $vehicle->vehicle_name }}"
                                 onerror="this.src='https://placehold.co/400x190/1e40af/fff?text={{ urlencode($vehicle->vehicle_name) }}'">
                            <span class="position-absolute top-0 start-0 m-2 badge bg-success">Available</span>
                            @auth @if(auth()->user()->isCustomer())
                            <form action="{{ route('customer.wishlist.toggle') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                                <button type="submit" style="position:absolute;top:.5rem;right:.5rem;background:rgba(255,255,255,.9);border:none;width:32px;height:32px;border-radius:50%;cursor:pointer">
                                    <i class="fas fa-heart text-muted"></i>
                                </button>
                            </form>
                            @endif @endauth
                            <div class="compare-checkbox">
                                <input class="form-check-input compare-check" type="checkbox"
                                       value="{{ $vehicle->id }}" id="compare{{ $vehicle->id }}">
                                <label class="form-check-label small ms-1" for="compare{{ $vehicle->id }}">Compare</label>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="fw-bold mb-0 lh-sm">{{ $vehicle->vehicle_name }}</h6>
                                <span class="badge bg-light text-dark border small">{{ $vehicle->category_name }}</span>
                            </div>
                            <p class="text-muted small mb-2">{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->year }}</p>
                            <div class="d-flex gap-3 text-muted small mb-2">
                                <span><i class="fas fa-users me-1"></i>{{ $vehicle->seating_capacity }}</span>
                                <span><i class="fas fa-gas-pump me-1"></i>{{ ucfirst($vehicle->fuel_type) }}</span>
                                <span><i class="fas fa-cog me-1"></i>{{ ucfirst($vehicle->transmission) }}</span>
                            </div>
                            <div class="d-flex gap-1 mb-3">
                                @for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i<=$vehicle->average_rating?'text-warning':'text-muted' }}" style="font-size:11px"></i>@endfor
                                <small class="text-muted">({{ $vehicle->review_count }})</small>
                            </div>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-primary fs-5">${{ number_format($vehicle->price_per_day) }}</span>
                                    <span class="text-muted small">/day</span>
                                </div>
                                <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="btn btn-primary btn-sm">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-4">{{ $vehicles->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Price range
const priceRange = document.getElementById('priceRange');
const priceDisplay = document.getElementById('priceDisplay');
if (priceRange) {
    priceRange.addEventListener('input', () => {
        priceDisplay.textContent = '$' + priceRange.value;
    });
}

// Auto-submit filters
document.querySelectorAll('.filter-input').forEach(el => {
    el.addEventListener('change', () => document.getElementById('filterForm').submit());
});

// Compare
let compareIds = [];
const compareBtn = document.getElementById('compareBtn');
const compareCount = document.getElementById('compareCount');
document.querySelectorAll('.compare-check').forEach(cb => {
    cb.addEventListener('change', function() {
        if (this.checked) {
            if (compareIds.length >= 4) { this.checked = false; alert('Max 4 vehicles'); return; }
            compareIds.push(this.value);
        } else {
            compareIds = compareIds.filter(id => id !== this.value);
        }
        compareCount.textContent = compareIds.length;
        compareBtn.style.display = compareIds.length >= 2 ? 'inline-flex' : 'none';
        compareBtn.href = '/vehicles/compare?' + compareIds.map(id => `ids[]=${id}`).join('&');
    });
});
</script>
@endpush
