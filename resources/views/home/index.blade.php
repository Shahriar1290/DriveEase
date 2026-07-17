@extends('layouts.app')
@section('title', 'DriveEase — Premium Vehicle Rental')

@push('styles')
<style>
.hero { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0ea5e9 100%);
    min-height: 90vh; display: flex; align-items: center; position: relative; overflow: hidden; }
.hero::before { content:''; position:absolute; inset:0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="15" fill="rgba(255,255,255,.03)"/><circle cx="80" cy="80" r="25" fill="rgba(255,255,255,.02)"/></svg>');
    background-size: cover; }
.hero-search-card { background: rgba(255,255,255,.95); backdrop-filter: blur(10px); border-radius: 1rem; }
.category-card { border: none; border-radius: .75rem; overflow: hidden; transition: all .3s; cursor: pointer; }
.category-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,.15) !important; }
.category-icon-wrap { width: 64px; height: 64px; background: linear-gradient(135deg, #0ea5e9, #0284c7);
    border-radius: 1rem; display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; color: white; margin: 0 auto 1rem; }
.vehicle-card { border: none; border-radius: .75rem; overflow: hidden; transition: all .3s; }
.vehicle-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,.12) !important; }
.vehicle-card .card-img-top { height: 200px; object-fit: cover; }
.vehicle-badge { position: absolute; top: .75rem; left: .75rem; }
.wishlist-btn { position: absolute; top: .75rem; right: .75rem; background: rgba(255,255,255,.9);
    border: none; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center;
    justify-content: center; cursor: pointer; transition: all .2s; }
.wishlist-btn:hover { background: white; transform: scale(1.1); }
.stat-num { font-size: 2.5rem; font-weight: 800; color: #0ea5e9; }
.testimonial-card { border: none; border-radius: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,.07); }
.why-icon { width: 60px; height: 60px; border-radius: 1rem; background: linear-gradient(135deg, #0ea5e9, #0284c7);
    display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem; }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="hero">
    <div class="container position-relative z-1">
        <div class="row align-items-center">
            <div class="col-lg-7 text-white mb-5 mb-lg-0">
                <span class="badge bg-primary mb-3 px-3 py-2 rounded-pill">🚗 Premium Vehicle Rental</span>
                <h1 class="display-4 fw-bold lh-sm mb-4">
                    Find Your Perfect <br><span class="text-primary">Ride Today</span>
                </h1>
                <p class="lead text-white-50 mb-4">Choose from 50+ premium vehicles. No hidden fees, flexible pickup, and 24/7 support.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('vehicles.index') }}" class="btn btn-primary btn-lg px-4 rounded-pill">
                        <i class="fas fa-search me-2"></i>Browse Vehicles
                    </a>
                    <a href="{{ route('about') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill">Learn More</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-search-card p-4 shadow-xl">
                    <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-search text-primary me-2"></i>Quick Search</h5>
                    <form action="{{ route('vehicles.index') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Vehicle Type</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }} ({{ $cat->available_count }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-muted">Pickup Date</label>
                                <input type="date" name="pickup_date" class="form-control" min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-muted">Return Date</label>
                                <input type="date" name="return_date" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Brand, model, or name...">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="fas fa-search me-2"></i>Search Vehicles
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="py-4 bg-dark">
    <div class="container">
        <div class="row g-3 text-center text-white">
            <div class="col-6 col-md-3">
                <div class="stat-num" data-target="{{ $stats['vehicles'] }}">0</div>
                <div class="text-light small">Total Vehicles</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-num" data-target="{{ $stats['customers'] }}">0</div>
                <div class="text-light small">Happy Customers</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-num" data-target="{{ $stats['bookings'] }}">0</div>
                <div class="text-light small">Completed Rentals</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-num" data-target="{{ $stats['cities'] }}">0</div>
                <div class="text-light small">Cities Served</div>
            </div>
        </div>
    </div>
</section>

{{-- Categories --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill mb-2">Categories</span>
            <h2 class="fw-bold">Browse by Vehicle Type</h2>
            <p class="text-muted">Find the perfect vehicle category for your journey</p>
        </div>
        <div class="row g-4">
            @foreach($categories as $cat)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('vehicles.index', ['category' => $cat->id]) }}" class="text-decoration-none">
                    <div class="category-card card shadow-sm text-center p-4">
                        <div class="category-icon-wrap">
                            <i class="{{ $cat->icon ?? 'fas fa-car' }}"></i>
                        </div>
                        <h6 class="fw-semibold mb-1 text-dark">{{ $cat->category_name }}</h6>
                        <small class="text-muted">{{ $cat->available_count }} Available</small>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Vehicles --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill mb-2">Featured</span>
                <h2 class="fw-bold mb-0">Most Popular Vehicles</h2>
            </div>
            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-primary">View All</a>
        </div>
        <div class="row g-4">
            @foreach($featuredVehicles->take(8) as $vehicle)
            <div class="col-sm-6 col-lg-3">
                <div class="vehicle-card card shadow-sm h-100">
                    <div class="position-relative">
                        <img src="{{ vehicle_image_url($vehicle->primary_image) }}"
                             class="card-img-top" alt="{{ $vehicle->vehicle_name }}">
                        <span class="vehicle-badge badge bg-success">Available</span>
                        @auth
                        @if(auth()->user()->isCustomer())
                        <form action="{{ route('customer.wishlist.toggle') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                            <button type="submit" class="wishlist-btn"><i class="fas fa-heart text-muted"></i></button>
                        </form>
                        @endif
                        @endauth
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0 lh-sm">{{ $vehicle->vehicle_name }}</h6>
                            <span class="badge bg-light text-dark border">{{ $vehicle->category_name }}</span>
                        </div>
                        <div class="d-flex gap-2 text-muted small mb-3 flex-wrap">
                            <span><i class="fas fa-users me-1"></i>{{ $vehicle->seating_capacity }}</span>
                            <span><i class="fas fa-gas-pump me-1"></i>{{ ucfirst($vehicle->fuel_type) }}</span>
                            <span><i class="fas fa-cog me-1"></i>{{ ucfirst($vehicle->transmission) }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1 mb-3">
                            @for($i=1;$i<=5;$i++)
                                <i class="fas fa-star {{ $i <= $vehicle->average_rating ? 'text-warning' : 'text-muted' }}" style="font-size:12px"></i>
                            @endfor
                            <small class="text-muted">({{ $vehicle->review_count }})</small>
                        </div>
                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <div>
                                <span class="h5 fw-bold text-primary mb-0">TK {{ number_format($vehicle->price_per_day) }}</span>
                                <span class="text-muted small">/day</span>
                            </div>
                            <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="btn btn-primary btn-sm px-3">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Why Choose Us --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill mb-2">Why Us</span>
            <h2 class="fw-bold">Why Choose DriveEase?</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['icon'=>'fas fa-shield-alt','title'=>'Fully Insured','text'=>'All our vehicles come with comprehensive insurance coverage for your peace of mind.'],
                ['icon'=>'fas fa-money-bill','title'=>'Best Prices','text'=>'We offer competitive rates with no hidden charges. What you see is what you pay.'],
                ['icon'=>'fas fa-headset','title'=>'24/7 Support','text'=>'Our dedicated team is available round the clock to assist you with any queries.'],
                ['icon'=>'fas fa-car','title'=>'Wide Selection','text'=>'From economy to luxury, we have vehicles for every budget and occasion.'],
                ['icon'=>'fas fa-map-marker-alt','title'=>'Multiple Locations','text'=>'Convenient pickup and drop-off locations across 25 cities.'],
                ['icon'=>'fas fa-mobile-alt','title'=>'Easy Booking','text'=>'Book online in minutes from any device. Quick, simple, and hassle-free.'],
            ] as $item)
            <div class="col-md-4">
                <div class="d-flex gap-3 align-items-start">
                    <div class="why-icon flex-shrink-0"><i class="{{ $item['icon'] }}"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">{{ $item['title'] }}</h6>
                        <p class="text-muted small mb-0">{{ $item['text'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Testimonials --}}
@if($testimonials->count())
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill mb-2">Reviews</span>
            <h2 class="fw-bold">What Our Customers Say</h2>
        </div>
        <div class="row g-4">
            @foreach($testimonials->take(3) as $review)
            <div class="col-md-4">
                <div class="testimonial-card card p-4 h-100">
                    <div class="d-flex gap-1 mb-3">
                        @for($i=1;$i<=5;$i++)
                            <i class="fas fa-star {{ $i<=$review->rating?'text-warning':'text-muted' }}"></i>
                        @endfor
                    </div>
                    <p class="text-muted mb-4">"{{ $review->review }}"</p>
                    <div class="d-flex align-items-center gap-2 mt-auto">
                        <img src="{{ user_avatar_url($review->user_avatar, $review->user_name) }}" class="rounded-circle" width="40" height="40" style="object-fit:cover">
                        <div>
                            <div class="fw-semibold small">{{ $review->user_name }}</div>
                            <div class="text-muted" style="font-size:11px">Verified Renter</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="py-5" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
    <div class="container text-center text-white">
        <h2 class="fw-bold mb-3">Ready to Hit the Road?</h2>
        <p class="lead mb-4 text-white-75">Book your vehicle today and enjoy the freedom of the open road.</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="{{ route('vehicles.index') }}" class="btn btn-light btn-lg px-5 fw-semibold">Browse Vehicles</a>
            @guest
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-5">Sign Up Free</a>
            @endguest
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
// Animated counters
const counters = document.querySelectorAll('.stat-num[data-target]');
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el = entry.target, target = +el.dataset.target;
        let count = 0;
        const step = Math.ceil(target / 50);
        const timer = setInterval(() => {
            count = Math.min(count + step, target);
            el.textContent = count.toLocaleString();
            if (count >= target) clearInterval(timer);
        }, 30);
        observer.unobserve(el);
    });
}, { threshold: 0.5 });
counters.forEach(c => observer.observe(c));
</script>
@endpush
