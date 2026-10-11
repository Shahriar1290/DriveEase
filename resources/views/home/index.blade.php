@extends('layouts.app')
@section('title', 'DriveEase — Premium Vehicle Rental')

@push('styles')
<style>
.hero {
    background: radial-gradient(circle at 85% 25%, rgba(37, 99, 235, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 15% 75%, rgba(14, 165, 233, 0.2) 0%, transparent 45%),
                #0b0f19;
    min-height: 88vh;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
    padding: 4rem 0;
}
.hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
    background-size: 28px 28px;
    opacity: 0.6;
}
.hero-search-card {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    border-radius: var(--radius-xl);
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
}
.stat-box {
    border-right: 1px solid rgba(255, 255, 255, 0.1);
}
@media (max-width: 767px) {
    .stat-box { border-right: none; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 1rem; }
}
.testimonial-card {
    background: var(--surface-card);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
}
.testimonial-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-4px);
}
</style>
@endpush

@section('content')

{{-- Hero Section --}}
<section class="hero">
    <div class="container position-relative z-1">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 text-white">
                <div class="glass-pill mb-3 animate-fade-in">
                    <i class="fas fa-certificate text-primary"></i>
                    <span>Verified Premium Fleet &middot; Instant Confirmation</span>
                </div>
                <h1 class="display-3 fw-bold lh-sm mb-4">
                    Find Your Perfect <br>
                    <span class="gradient-text">Drive in Seconds</span>
                </h1>
                <p class="lead text-light opacity-75 mb-4" style="max-width: 540px; font-size: 1.15rem;">
                    Rent top-tier sedans, rugged SUVs, and luxury electric vehicles across Bangladesh with zero hidden fees and transparent daily rates.
                </p>
                <div class="d-flex gap-3 flex-wrap align-items-center">
                    <a href="{{ route('vehicles.index') }}" class="btn btn-primary btn-lg px-4 rounded-pill">
                        <i class="fas fa-compass me-2"></i>Explore Fleet
                    </a>
                    <a href="{{ route('about') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                        Learn More
                    </a>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="hero-search-card p-4 p-md-5">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="brand-icon-wrap" style="width: 32px; height: 32px; font-size: 0.9rem;">
                            <i class="fas fa-search"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Find & Book a Vehicle</h5>
                    </div>
                    <form action="{{ route('vehicles.index') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Vehicle Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }} ({{ $cat->available_count }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Pickup Date</label>
                                <input type="date" name="pickup_date" class="form-control" min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Return Date</label>
                                <input type="date" name="return_date" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Keyword / Model</label>
                            <input type="text" name="search" class="form-control" placeholder="e.g. Camry, RAV4, Tesla, BMW...">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-md">
                            <i class="fas fa-car-side me-2"></i>Check Availability
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats Ribbon --}}
<section class="py-4" style="background: #090d16; border-bottom: 1px solid rgba(255,255,255,0.08);">
    <div class="container">
        <div class="row g-3 text-center text-white align-items-center">
            <div class="col-6 col-md-3 stat-box">
                <div class="stat-number gradient-text" data-target="{{ $stats['vehicles'] }}">0</div>
                <div class="text-secondary small fw-semibold text-uppercase tracking-wider">Available Fleet</div>
            </div>
            <div class="col-6 col-md-3 stat-box">
                <div class="stat-number gradient-text" data-target="{{ $stats['customers'] }}">0</div>
                <div class="text-secondary small fw-semibold text-uppercase tracking-wider">Satisfied Clients</div>
            </div>
            <div class="col-6 col-md-3 stat-box">
                <div class="stat-number gradient-text" data-target="{{ $stats['bookings'] }}">0</div>
                <div class="text-secondary small fw-semibold text-uppercase tracking-wider">Trips Completed</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-number gradient-text" data-target="{{ $stats['cities'] }}">0</div>
                <div class="text-secondary small fw-semibold text-uppercase tracking-wider">Service Hubs</div>
            </div>
        </div>
    </div>
</section>

{{-- Categories Section --}}
<section class="py-5 my-3">
    <div class="container">
        <div class="text-center mb-5">
            <span class="glass-pill text-primary mb-2">Fleet Categories</span>
            <h2 class="fw-bold mt-2">Tailored for Every Journey</h2>
            <p class="text-muted" style="max-width: 520px; margin: 0 auto;">Select from our wide selection of certified vehicles ready for business, family, or weekend getaways.</p>
        </div>
        <div class="row g-4">
            @foreach($categories as $cat)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('vehicles.index', ['category' => $cat->id]) }}" class="category-card text-decoration-none">
                    <div class="category-icon-wrap">
                        <i class="{{ $cat->icon ?? 'fas fa-car' }}"></i>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">{{ $cat->category_name }}</h6>
                    <small class="text-muted fw-medium">{{ $cat->available_count }} Available</small>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Vehicles Section --}}
<section class="py-5" style="background-color: #f1f5f9;">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5 gap-3">
            <div>
                <span class="glass-pill text-primary mb-2">Handpicked Fleet</span>
                <h2 class="fw-bold mb-0 mt-2">Featured Premium Vehicles</h2>
            </div>
            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-primary px-4 rounded-pill">
                View Entire Fleet <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        <div class="row g-4">
            @foreach($featuredVehicles->take(8) as $vehicle)
            <div class="col-sm-6 col-lg-3">
                <div class="vehicle-card">
                    <div class="vehicle-card-img-wrap">
                        <img src="{{ vehicle_image_url($vehicle->primary_image) }}"
                             alt="{{ $vehicle->vehicle_name }}" loading="lazy">
                        <div class="position-absolute top-0 start-0 p-3">
                            <span class="badge-glass">
                                <i class="fas fa-circle text-success me-1" style="font-size: 7px;"></i>Available
                            </span>
                        </div>
                        @auth
                        @if(auth()->user()->isCustomer())
                        <form action="{{ route('customer.wishlist.toggle') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                            <button type="submit" class="wishlist-btn" title="Add to Wishlist">
                                <i class="fas fa-heart"></i>
                            </button>
                        </form>
                        @endif
                        @endauth
                    </div>
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">{{ $vehicle->vehicle_name }}</h6>
                                <small class="text-muted">{{ $vehicle->brand }} &middot; {{ $vehicle->year }}</small>
                            </div>
                            <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill small">
                                {{ $vehicle->category?->category_name ?? $vehicle->category_name ?? 'Vehicle' }}
                            </span>
                        </div>

                        {{-- Specs pills --}}
                        <div class="d-flex gap-2 my-3 flex-wrap">
                            <span class="spec-pill"><i class="fas fa-user-group"></i>{{ $vehicle->seating_capacity }} Seats</span>
                            <span class="spec-pill"><i class="fas fa-gas-pump"></i>{{ ucfirst($vehicle->fuel_type) }}</span>
                            <span class="spec-pill"><i class="fas fa-gears"></i>{{ ucfirst($vehicle->transmission) }}</span>
                        </div>

                        <div class="d-flex align-items-center gap-1 mb-3">
                            @for($i=1;$i<=5;$i++)
                                <i class="fas fa-star {{ $i <= $vehicle->average_rating ? 'text-warning' : 'text-muted' }}" style="font-size:11px"></i>
                            @endfor
                            <small class="text-muted ms-1">({{ $vehicle->review_count }})</small>
                        </div>

                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <div>
                                <span class="h5 fw-bold text-dark mb-0">TK {{ number_format($vehicle->price_per_day) }}</span>
                                <span class="text-muted small">/day</span>
                            </div>
                            <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="btn btn-primary btn-sm px-3 rounded-pill">
                                Rent Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Why Choose Us (Bento-style feature cards) --}}
<section class="py-5 my-4">
    <div class="container">
        <div class="text-center mb-5">
            <span class="glass-pill text-primary mb-2">Our Standards</span>
            <h2 class="fw-bold mt-2">Why Discerning Drivers Choose Us</h2>
            <p class="text-muted" style="max-width: 520px; margin: 0 auto;">Experience top-flight automotive care, complete transparency, and seamless digital booking.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['icon'=>'fas fa-shield-halved','title'=>'Comprehensive Coverage','text'=>'Every vehicle is covered with full commercial insurance and zero hidden deductibles.'],
                ['icon'=>'fas fa-hand-holding-dollar','title'=>'Guaranteed Best Rates','text'=>'Upfront transparent pricing with transparent security deposits and instant digital receipts.'],
                ['icon'=>'fas fa-headset','title'=>'Dedicated Concierge','text'=>'Round-the-clock roadside assistance and concierge support anywhere across our active zones.'],
                ['icon'=>'fas fa-car-rear','title'=>'Pristine Multi-point Inspection','text'=>'Every vehicle is thoroughly sanitized and undergoes 40-point maintenance before dispatch.'],
                ['icon'=>'fas fa-map-location-dot','title'=>'Flexible Hub Pickup','text'=>'Convenient airport drop-offs and city hubs for effortless pick and return.'],
                ['icon'=>'fas fa-mobile-screen','title'=>'100% Digital Rental Process','text'=>'Verify credentials and confirm your booking in under 2 minutes directly from your phone.'],
            ] as $item)
            <div class="col-md-6 col-lg-4">
                <div class="feature-box">
                    <div class="feature-icon-wrap">
                        <i class="{{ $item['icon'] }}"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark">{{ $item['title'] }}</h5>
                    <p class="text-muted small mb-0 lh-base">{{ $item['text'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Customer Testimonials --}}
@if(isset($testimonials) && $testimonials->isNotEmpty())
<section class="py-5" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="glass-pill text-primary mb-2">Client Testimonials</span>
            <h2 class="fw-bold mt-2">Trusted by Over 10,000+ Drivers</h2>
        </div>
        <div class="row g-4">
            @foreach($testimonials->take(3) as $review)
            <div class="col-md-4">
                <div class="testimonial-card p-4 h-100 d-flex flex-column">
                    <div class="mb-3">
                        @for($i=1;$i<=5;$i++)
                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}" style="font-size:12px"></i>
                        @endfor
                    </div>
                    <p class="text-secondary small fst-italic mb-4 flex-grow-1">"{{ $review->review ?? $review->comment }}"</p>
                    <div class="d-flex align-items-center gap-3 border-top pt-3">
                        <div class="brand-icon-wrap" style="width: 38px; height: 38px; font-size: 0.85rem;">
                            {{ strtoupper(substr($review->user?->name ?? 'C', 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold small text-dark">{{ $review->user?->name ?? 'Verified Renter' }}</div>
                            <small class="text-muted">{{ $review->vehicle?->vehicle_name ?? 'DriveEase Fleet' }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Call to Action Banner --}}
<section class="py-5" style="background: linear-gradient(135deg, #090d16 0%, #1e293b 100%);">
    <div class="container text-center text-white py-4">
        <h2 class="fw-bold display-5 mb-3">Ready to Hit the Road?</h2>
        <p class="lead text-light opacity-75 mb-4" style="max-width: 580px; margin: 0 auto;">
            Book your next ride in minutes with flexible cancellation and seamless pickup.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('vehicles.index') }}" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg">
                <i class="fas fa-car-side me-2"></i>Browse All Vehicles
            </a>
            @guest
            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                Create Free Account
            </a>
            @endguest
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
// Animated Counter
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const el = entry.target;
            const target = parseInt(el.dataset.target || 0);
            let current = 0;
            const step = Math.ceil(target / 40);
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    el.textContent = target.toLocaleString() + '+';
                    clearInterval(timer);
                } else {
                    el.textContent = current.toLocaleString();
                }
            }, 30);
            observer.unobserve(el);
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('.stat-number').forEach(el => observer.observe(el));
</script>
@endpush
