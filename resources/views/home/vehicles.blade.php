@extends('layouts.app')
@section('title', 'Browse Premium Fleet — DriveEase')

@push('styles')
<style>
.filter-card {
    background: #ffffff;
    border-radius: var(--radius-xl, 1.25rem);
    padding: 1.75rem;
    position: sticky;
    top: 90px;
    border: 1px solid var(--gray-200, #e2e8f0);
    box-shadow: var(--shadow-card, 0 2px 10px rgba(15, 23, 42, 0.04));
}
.filter-title {
    font-size: 0.725rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--gray-500, #64748b);
}
.filter-radio-custom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.45rem 0.65rem;
    border-radius: var(--radius-md, 0.625rem);
    transition: all 0.2s ease;
    cursor: pointer;
}
.filter-radio-custom:hover {
    background: var(--gray-100, #f1f5f9);
}
.price-range-display {
    color: var(--primary, #2563eb);
    font-weight: 800;
    font-size: 1.05rem;
}
.form-range::-webkit-slider-thumb {
    background: var(--primary, #2563eb);
}
.hero-catalog {
    background: linear-gradient(135deg, #090d16 0%, #0f172a 100%);
    position: relative;
    overflow: hidden;
    padding: 2.75rem 0;
}
.hero-catalog::after {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(37, 99, 235, 0.15), transparent 60%);
    pointer-events: none;
}
</style>
@endpush

@section('content')
<div class="hero-catalog text-white">
    <div class="container position-relative" style="z-index: 1;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-white-50 mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Vehicles</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-2">
            <div>
                <h1 class="h2 fw-bold mb-1 text-white">Explore Our Fleet</h1>
                <p class="text-secondary small mb-0">Browse through high-performance sedans, luxury SUVs, and efficient city cruisers.</p>
            </div>
            <div class="badge-glass px-3 py-2 text-white small">
                <i class="fas fa-shield-alt text-primary me-1"></i> 100% Verified Clean Fleet
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        {{-- Filters Sidebar --}}
        <div class="col-lg-3">
            <div class="filter-card">
                <form method="GET" action="{{ route('vehicles.index') }}" id="filterForm">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-sliders text-primary"></i>
                            <h6 class="fw-bold mb-0 text-dark">Filters</h6>
                        </div>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-link btn-sm p-0 text-decoration-none text-muted small fw-semibold">Clear all</a>
                    </div>

                    {{-- Search --}}
                    <div class="mb-4">
                        <label class="filter-title mb-2 d-block">Search Keyword</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control form-control-sm border-start-0"
                                   placeholder="Brand, model, name..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="mb-4">
                        <label class="filter-title mb-2 d-block">Vehicle Category</label>
                        @foreach($categories as $cat)
                        <label class="filter-radio-custom" for="cat{{ $cat->id }}">
                            <div class="d-flex align-items-center gap-2">
                                <input class="form-check-input filter-input mt-0" type="radio" name="category"
                                       id="cat{{ $cat->id }}" value="{{ $cat->id }}"
                                       {{ request('category') == $cat->id ? 'checked' : '' }}>
                                <span class="small fw-medium text-dark">{{ $cat->category_name }}</span>
                            </div>
                            <span class="badge bg-light text-muted border px-2 py-1 small rounded-pill">{{ $cat->vehicles_count }}</span>
                        </label>
                        @endforeach
                    </div>

                    {{-- Fuel Type --}}
                    <div class="mb-4">
                        <label class="filter-title mb-2 d-block">Fuel Type</label>
                        @foreach(['petrol','diesel','electric','hybrid','cng'] as $fuel)
                        <label class="filter-radio-custom" for="fuel_{{ $fuel }}">
                            <div class="d-flex align-items-center gap-2">
                                <input class="form-check-input filter-input mt-0" type="radio" name="fuel_type"
                                       id="fuel_{{ $fuel }}" value="{{ $fuel }}"
                                       {{ request('fuel_type') == $fuel ? 'checked' : '' }}>
                                <span class="small fw-medium text-dark">{{ ucfirst($fuel) }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    {{-- Transmission --}}
                    <div class="mb-4">
                        <label class="filter-title mb-2 d-block">Transmission</label>
                        @foreach(['manual','automatic','semi-automatic'] as $trans)
                        <label class="filter-radio-custom" for="trans_{{ $trans }}">
                            <div class="d-flex align-items-center gap-2">
                                <input class="form-check-input filter-input mt-0" type="radio" name="transmission"
                                       id="trans_{{ $trans }}" value="{{ $trans }}"
                                       {{ request('transmission') == $trans ? 'checked' : '' }}>
                                <span class="small fw-medium text-dark">{{ ucfirst($trans) }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    {{-- Price Slider --}}
                    <div class="mb-4">
                        <label class="filter-title mb-2 d-block">Max Price / Day</label>
                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            <span class="text-muted small">TK 0</span>
                            <span class="price-range-display" id="priceDisplay">
                                TK {{ number_format(request('max_price', $maxPrice)) }}
                            </span>
                        </div>
                        <input type="range" class="form-range" name="max_price" id="priceRange"
                               min="0" max="{{ $maxPrice }}" step="50"
                               value="{{ request('max_price', $maxPrice) }}">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                </form>
            </div>
        </div>

        {{-- Results --}}
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 pb-2 border-bottom">
                <p class="mb-0 text-muted small">
                    Showing <strong class="text-dark">{{ $vehicles->firstItem() ?? 0 }}–{{ $vehicles->lastItem() ?? 0 }}</strong>
                    of <strong class="text-dark">{{ $vehicles->total() }}</strong> available vehicles
                </p>
                <div class="d-flex gap-2 align-items-center">
                    <a href="{{ route('vehicles.compare', request()->merge(['ids' => collect(request('compare_ids', []))])->query()) }}"
                       class="btn btn-outline-primary btn-sm rounded-pill px-3" id="compareBtn" style="display:none!important">
                        <i class="fas fa-columns me-1"></i>Compare (<span id="compareCount">0</span>)
                    </a>
                    <div class="d-flex align-items-center gap-2">
                        <label for="sortSelect" class="text-muted small mb-0 text-nowrap"><i class="fas fa-arrow-down-short-wide me-1"></i>Sort:</label>
                        <select name="sort" id="sortSelect" class="form-select form-select-sm rounded-pill" style="width:170px" onchange="this.form.submit()" form="filterForm">
                            <option value="popular"    {{ request('sort')=='popular'   ?'selected':'' }}>Most Popular</option>
                            <option value="price_low"  {{ request('sort')=='price_low' ?'selected':'' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort')=='price_high'?'selected':'' }}>Price: High to Low</option>
                            <option value="newest"     {{ request('sort')=='newest'    ?'selected':'' }}>Newest First</option>
                        </select>
                    </div>
                </div>
            </div>

            @if($vehicles->isEmpty())
            <div class="text-center py-5 bg-white rounded-4 border p-5">
                <div class="category-icon-wrap mx-auto mb-3" style="width:72px;height:72px;font-size:2rem">
                    <i class="fas fa-car-side"></i>
                </div>
                <h5 class="fw-bold text-dark">No vehicles matched your criteria</h5>
                <p class="text-muted small mb-4">Try clearing some filters or searching with different terms.</p>
                <a href="{{ route('vehicles.index') }}" class="btn btn-primary px-4">
                    <i class="fas fa-rotate-left me-1"></i> Reset Filters
                </a>
            </div>
            @else
            <div class="row g-4" id="vehicleGrid">
                @foreach($vehicles as $vehicle)
                <div class="col-sm-6 col-xl-4">
                    <div class="vehicle-card card h-100">
                        <div class="vehicle-card-img-wrap">
                            <img src="{{ vehicle_image_url($vehicle->primary_image) }}"
                                 alt="{{ $vehicle->vehicle_name }}" loading="lazy">
                            <div class="badge-status-top">
                                <span class="badge bg-success shadow-sm">
                                    <i class="fas fa-circle-check me-1"></i>Available
                                </span>
                            </div>

                            @auth @if(auth()->user()->isCustomer())
                            @php $saved = in_array((int) $vehicle->id, $wishlistIds ?? [], true); @endphp
                            <form action="{{ route('customer.wishlist.toggle') }}" method="POST" class="d-inline" data-wishlist>
                                @csrf
                                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                                <button type="submit" class="wishlist-btn-corner {{ $saved ? 'active' : '' }}"
                                        title="{{ $saved ? 'Remove from wishlist' : 'Save to wishlist' }}"
                                        aria-label="Toggle wishlist" aria-pressed="{{ $saved ? 'true' : 'false' }}">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </form>
                            @endif @endauth

                            <div class="compare-checkbox-pill">
                                <div class="form-check m-0 d-flex align-items-center">
                                    <input class="form-check-input compare-check mt-0 me-1" type="checkbox"
                                           value="{{ $vehicle->id }}" id="compare{{ $vehicle->id }}">
                                    <label class="form-check-label small" for="compare{{ $vehicle->id }}">Compare</label>
                                </div>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column p-4">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                                        {{ $vehicle->brand }}
                                    </span>
                                    <h5 class="fw-bold mb-0 text-dark">{{ $vehicle->vehicle_name }}</h5>
                                </div>
                                <span class="badge bg-light text-dark border small rounded-pill">
                                    {{ $vehicle->category?->category_name ?? $vehicle->category_name ?? 'Fleet' }}
                                </span>
                            </div>

                            <p class="text-muted small mb-3">{{ $vehicle->model }} &bull; {{ $vehicle->year }}</p>

                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="spec-pill"><i class="fas fa-users"></i> {{ $vehicle->seating_capacity }} seats</span>
                                <span class="spec-pill"><i class="fas fa-gas-pump"></i> {{ ucfirst($vehicle->fuel_type) }}</span>
                                <span class="spec-pill"><i class="fas fa-gear"></i> {{ ucfirst($vehicle->transmission) }}</span>
                            </div>

                            <div class="d-flex align-items-center gap-1 mb-3">
                                @for($i=1;$i<=5;$i++)
                                    <i class="fas fa-star {{ $i<=$vehicle->average_rating ? 'text-warning' : 'text-muted' }}" style="font-size: 11px;"></i>
                                @endfor
                                <span class="small fw-semibold ms-1">{{ number_format($vehicle->average_rating, 1) }}</span>
                                <span class="text-muted small">({{ $vehicle->review_count }})</span>
                            </div>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="price-figure">TK {{ number_format($vehicle->price_per_day) }}</span>
                                    <span class="text-muted small">/day</span>
                                </div>
                                <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="btn btn-outline-primary btn-sm px-3">
                                    Details <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $vehicles->links('pagination::bootstrap-5') }}
            </div>
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
        priceDisplay.textContent = 'TK ' + priceRange.value;
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
