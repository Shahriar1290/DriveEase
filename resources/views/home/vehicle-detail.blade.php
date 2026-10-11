@extends('layouts.app')
@section('title', $vehicle->vehicle_name . ' — DriveEase')

@push('styles')
<style>
.gallery-thumb {
    width: 80px;
    height: 56px;
    object-fit: cover;
    border-radius: var(--radius-md, 0.625rem);
    cursor: pointer;
    opacity: 0.6;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    border: 2px solid transparent;
}
.gallery-thumb.active, .gallery-thumb:hover {
    opacity: 1;
    border-color: var(--primary, #2563eb);
    transform: translateY(-2px);
}
.main-img-wrap {
    height: 440px;
    width: 100%;
    border-radius: var(--radius-xl, 1.25rem);
    overflow: hidden;
    background: var(--dark-950, #090d16);
    position: relative;
    box-shadow: var(--shadow-lg, 0 12px 24px -4px rgba(15, 23, 42, 0.1));
}
.main-img {
    height: 100%;
    width: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.spec-item {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.85rem;
    border-radius: var(--radius-md, 0.625rem);
    background: #ffffff;
    border: 1px solid var(--gray-200, #e2e8f0);
    transition: all 0.2s ease;
}
.spec-item:hover {
    border-color: var(--primary-light, #3b82f6);
    background: var(--gray-50, #f8fafc);
}
.spec-icon {
    width: 42px;
    height: 42px;
    background: rgba(37, 99, 235, 0.1);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary, #2563eb);
    font-size: 1.1rem;
    flex-shrink: 0;
}
.feature-tag {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
    border-radius: var(--radius-pill, 9999px);
    padding: 0.35rem 0.85rem;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
}
.feature-tag.no {
    background: #fef2f2;
    color: #991b1b;
    border-color: #fecaca;
}
.booking-card {
    background: #ffffff;
    border-radius: var(--radius-xl, 1.25rem);
    padding: 1.75rem;
    box-shadow: var(--shadow-xl, 0 24px 48px -8px rgba(15, 23, 42, 0.14));
    border: 1px solid var(--gray-200, #e2e8f0);
    position: sticky;
    top: 90px;
}
.detail-hero-bar {
    background: linear-gradient(135deg, #090d16 0%, #0f172a 100%);
    padding: 1.25rem 0;
}
</style>
@endpush

@section('content')
<div class="detail-hero-bar text-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small text-white-50">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}" class="text-white-50 text-decoration-none">Vehicles</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">{{ $vehicle->vehicle_name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        {{-- Left: Gallery + Details --}}
        <div class="col-lg-8">
            {{-- Gallery --}}
            <div class="mb-4">
                <div class="main-img-wrap">
                    <img id="mainImg" src="{{ vehicle_image_url($images[0]->image ?? null) }}"
                         class="main-img" alt="{{ $vehicle->vehicle_name }}">
                </div>
                @if(count($images) > 1)
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    @foreach($images as $img)
                    <img src="{{ vehicle_image_url($img->image) }}" class="gallery-thumb {{ $loop->first ? 'active' : '' }}"
                         onclick="switchImg(this, '{{ vehicle_image_url($img->image) }}')" alt="Thumbnail">
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Info Header Card --}}
            <div class="card p-4 rounded-4 border-0 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary px-3 py-1 rounded-pill">{{ $vehicle->category?->category_name ?? $vehicle->category_name ?? 'Fleet' }}</span>
                            @if($vehicle->status === 'available')
                                <span class="badge bg-success px-3 py-1 rounded-pill"><i class="fas fa-circle-check me-1"></i>Available</span>
                            @else
                                <span class="badge bg-secondary px-3 py-1 rounded-pill">{{ ucfirst($vehicle->status) }}</span>
                            @endif
                        </div>
                        <h2 class="fw-bold mb-1 text-dark">{{ $vehicle->vehicle_name }}</h2>
                        <p class="text-muted mb-0 small">
                            <span class="fw-semibold text-dark">{{ $vehicle->brand }}</span> {{ $vehicle->model }} &bull; Model Year {{ $vehicle->year }}
                        </p>
                    </div>
                    <div class="text-lg-end">
                        <div class="h2 fw-bold text-primary mb-0">TK {{ number_format($vehicle->price_per_day) }}</div>
                        <small class="text-muted fw-semibold">per rental day</small>
                    </div>
                </div>

                {{-- Rating & Rentals Bar --}}
                <div class="d-flex align-items-center gap-2 pt-3 border-top flex-wrap">
                    <div class="d-flex gap-1 text-warning">
                        @for($i=1;$i<=5;$i++)
                            <i class="fas fa-star {{ $i<=$vehicle->average_rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                    </div>
                    <span class="fw-bold text-dark">{{ number_format($vehicle->average_rating, 1) }}</span>
                    <span class="text-muted small">({{ $reviewCount }} verified reviews)</span>
                    <span class="text-muted">&bull;</span>
                    <span class="text-muted small"><i class="fas fa-route text-primary me-1"></i>{{ $vehicle->total_rentals }} completed rentals</span>
                </div>
            </div>

            {{-- Description --}}
            @if($vehicle->description)
            <div class="card p-4 rounded-4 border-0 shadow-sm mb-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-circle-info text-primary me-2"></i>About This Vehicle</h5>
                <p class="text-muted mb-0 lh-lg">{{ $vehicle->description }}</p>
            </div>
            @endif

            {{-- Specs & Features --}}
            <div class="card p-4 rounded-4 border-0 shadow-sm mb-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-sliders text-primary me-2"></i>Technical Specifications</h5>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="spec-item">
                            <div class="spec-icon"><i class="fas fa-users"></i></div>
                            <div>
                                <div class="text-muted small">Seating Capacity</div>
                                <div class="fw-bold text-dark">{{ $vehicle->seating_capacity }} Persons</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="spec-item">
                            <div class="spec-icon"><i class="fas fa-gas-pump"></i></div>
                            <div>
                                <div class="text-muted small">Fuel Type</div>
                                <div class="fw-bold text-dark">{{ ucfirst($vehicle->fuel_type) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="spec-item">
                            <div class="spec-icon"><i class="fas fa-gear"></i></div>
                            <div>
                                <div class="text-muted small">Transmission</div>
                                <div class="fw-bold text-dark">{{ ucfirst($vehicle->transmission) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="spec-item">
                            <div class="spec-icon"><i class="fas fa-palette"></i></div>
                            <div>
                                <div class="text-muted small">Exterior Color</div>
                                <div class="fw-bold text-dark">{{ $vehicle->color ?? 'Standard' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="spec-item">
                            <div class="spec-icon"><i class="fas fa-gauge-high"></i></div>
                            <div>
                                <div class="text-muted small">Odometer / Mileage</div>
                                <div class="fw-bold text-dark">{{ $vehicle->mileage ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="spec-item">
                            <div class="spec-icon"><i class="fas fa-id-card"></i></div>
                            <div>
                                <div class="text-muted small">Registration Reg.</div>
                                <div class="fw-bold text-dark">{{ $vehicle->registration_number }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3 text-dark">Features & Equipment</h6>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="feature-tag {{ $vehicle->air_conditioning ? '' : 'no' }}">
                        <i class="fas fa-{{ $vehicle->air_conditioning ? 'check' : 'xmark' }} me-1"></i>Air Conditioning
                    </span>
                    <span class="feature-tag {{ $vehicle->gps ? '' : 'no' }}">
                        <i class="fas fa-{{ $vehicle->gps ? 'check' : 'xmark' }} me-1"></i>GPS Navigation
                    </span>
                    <span class="feature-tag {{ $vehicle->bluetooth ? '' : 'no' }}">
                        <i class="fab fa-{{ $vehicle->bluetooth ? 'bluetooth' : 'bluetooth' }} me-1"></i>Bluetooth
                    </span>
                    <span class="feature-tag {{ $vehicle->usb_charger ? '' : 'no' }}">
                        <i class="fas fa-{{ $vehicle->usb_charger ? 'bolt' : 'xmark' }} me-1"></i>USB Fast Charging
                    </span>
                    <span class="feature-tag {{ $vehicle->child_seat ? '' : 'no' }}">
                        <i class="fas fa-{{ $vehicle->child_seat ? 'check' : 'xmark' }} me-1"></i>Child Safety Seat
                    </span>
                </div>

                {{-- Pricing Breakdown --}}
                <div class="bg-light rounded-3 p-3">
                    <h6 class="fw-bold mb-2 text-dark small text-uppercase" style="letter-spacing: 0.05em;">Standard Pricing Overview</h6>
                    <div class="row g-2 text-center small">
                        <div class="col-4 border-end">
                            <div class="text-muted">Daily Rate</div>
                            <div class="fw-bold text-dark">TK {{ number_format($vehicle->price_per_day) }}</div>
                        </div>
                        <div class="col-4 border-end">
                            <div class="text-muted">Weekly (7d)</div>
                            <div class="fw-bold text-dark">TK {{ number_format($vehicle->price_per_day * 7) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted">Taxes & Fees</div>
                            <div class="fw-bold text-success">5% Included</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reviews --}}
            <div class="card p-4 rounded-4 border-0 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Customer Reviews ({{ $reviewCount }})</h5>
                        <small class="text-muted">Ratings from customers who booked this car</small>
                    </div>
                    @auth
                    @if(auth()->user()->isCustomer() && $userHasBookedVehicle)
                    <button class="btn btn-outline-primary btn-sm px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#reviewModal">
                        <i class="fas fa-pen me-1"></i> Write Review
                    </button>
                    @endif
                    @endauth
                </div>

                @forelse($reviews as $review)
                <div class="border-bottom pb-3 mb-3 last-no-border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ user_avatar_url($review->user_avatar, $review->user_name) }}" class="rounded-circle" width="38" height="38" style="object-fit:cover" alt="{{ $review->user_name }}">
                            <div>
                                <span class="fw-semibold text-dark d-block lh-1">{{ $review->user_name }}</span>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}</small>
                            </div>
                        </div>
                        <div class="d-flex gap-1 text-warning">
                            @for($i=1;$i<=5;$i++)
                                <i class="fas fa-star {{ $i<=$review->rating ? 'text-warning' : 'text-muted' }}" style="font-size:12px"></i>
                            @endfor
                        </div>
                    </div>
                    <p class="text-muted small mb-0 ps-5">{{ $review->review }}</p>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-comments text-muted mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted small mb-0">No reviews yet. Be the first to rent and rate this vehicle!</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Right: Booking Card --}}
        <div class="col-lg-4">
            <div class="booking-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark">Book Vehicle</h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small rounded-pill">Instant Booking</span>
                </div>

                @if($vehicle->status === 'available')
                @auth
                @if(auth()->user()->isCustomer())
                <form action="{{ route('customer.bookings.store') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Pickup Date <span class="text-danger">*</span></label>
                        <input type="date" name="pickup_date" id="pickupDate" class="form-control"
                               min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" id="returnDate" class="form-control"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>

                    <div id="costPreview" class="bg-light rounded-3 p-3 mb-3 d-none border">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Duration</span>
                            <span class="fw-bold text-dark" id="daysCount">-</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Rate</span>
                            <span class="fw-medium">TK {{ number_format($vehicle->price_per_day,2) }}/day</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-medium" id="subtotal">-</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Tax (5%)</span>
                            <span class="fw-medium" id="taxAmt">-</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span class="text-dark">Total Amount</span>
                            <span class="text-primary fs-6" id="totalAmt">-</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Pickup Location</label>
                        <div class="input-group">
                            <input type="text" name="pickup_location" id="pickup_location" class="form-control" placeholder="e.g. Airport Terminal 1, City Center">
                            <button type="button" class="btn btn-outline-secondary" id="useMyLocation"
                                    title="Use my current location">
                                <i class="fas fa-location-crosshairs text-primary"></i>
                            </button>
                        </div>
                        <small class="text-muted" id="locationStatus"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">Select payment method</option>
                            <option value="cash">Cash on Handover</option>
                            <option value="card">Credit / Debit Card</option>
                            <option value="mobile_banking">bKash / Nagad Mobile Banking</option>
                            <option value="bank_transfer">Direct Bank Transfer</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-dark">Special Requests (Optional)</label>
                        <textarea name="special_requests" class="form-control" rows="2" placeholder="Driver preference, luggage assistance, etc."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                        <i class="fas fa-calendar-check me-2"></i> Confirm & Book Now
                    </button>
                </form>
                @else
                <div class="alert alert-info small mb-0"><i class="fas fa-info-circle me-1"></i> Admin accounts cannot create bookings.</div>
                @endif
                @else
                <div class="text-center py-4 bg-light rounded-3 p-3">
                    <i class="fas fa-lock text-muted mb-2" style="font-size:2rem"></i>
                    <p class="text-muted small mb-3">Please sign in to reserve this vehicle</p>
                    <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">Sign In</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">Create Free Account</a>
                </div>
                @endauth
                @else
                <div class="alert alert-warning small mb-0"><i class="fas fa-circle-exclamation me-2"></i>This vehicle is currently unavailable for rent.</div>
                @endif

                <div class="border-top pt-3 mt-4">
                    <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                        <i class="fas fa-shield-check text-success"></i> Free cancellation up to 24h before pickup
                    </div>
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <i class="fas fa-headset text-primary"></i> 24/7 Roadside Assistance & Support
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- Related Vehicles --}}
    @if(count($relatedVehicles))
    <div class="mt-5">
        <h5 class="fw-bold mb-4">Similar Vehicles</h5>
        <div class="row g-3">
            @foreach($relatedVehicles as $v)
            <div class="col-sm-6 col-md-3">
                <a href="{{ route('vehicles.show', $v->slug) }}" class="text-decoration-none">
                    <div class="card border-0 rounded-3 overflow-hidden shadow-sm h-100">
                        <img src="{{ vehicle_image_url($v->primary_image) }}" class="card-img-top" style="height:140px;object-fit:cover">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-1 text-dark">{{ $v->vehicle_name }}</h6>
                            <span class="text-primary fw-bold">TK {{ number_format($v->price_per_day) }}/day</span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Review Modal --}}
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customer.reviews.store') }}" method="POST">
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rating</label>
                        <div class="d-flex gap-2" id="starRating">
                            @for($i=1;$i<=5;$i++)
                            <i class="fas fa-star text-muted fs-4 star-btn" data-val="{{ $i }}" style="cursor:pointer"></i>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Your Review</label>
                        <textarea name="review" class="form-control" rows="4" required minlength="10" placeholder="Share your experience..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchImg(thumb, src) {
    document.getElementById('mainImg').src = src;
    document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}

// Cost calculator
const pricePerDay = {{ $vehicle->price_per_day }};
function calcCost() {
    const p = document.getElementById('pickupDate').value;
    const r = document.getElementById('returnDate').value;
    if (!p || !r) return;
    const days = Math.ceil((new Date(r) - new Date(p)) / 86400000);
    if (days <= 0) return;
    const sub = days * pricePerDay;
    const tax = sub * 0.05;
    const total = sub + tax;
    document.getElementById('daysCount').textContent = days + ' day(s)';
    document.getElementById('subtotal').textContent = 'TK ' + sub.toFixed(2);
    document.getElementById('taxAmt').textContent = 'TK ' + tax.toFixed(2);
    document.getElementById('totalAmt').textContent = 'TK ' + total.toFixed(2);
    document.getElementById('costPreview').classList.remove('d-none');
}
document.getElementById('pickupDate')?.addEventListener('change', calcCost);
document.getElementById('returnDate')?.addEventListener('change', calcCost);

// Star rating
const stars = document.querySelectorAll('.star-btn');
stars.forEach(s => {
    s.addEventListener('click', () => {
        const val = +s.dataset.val;
        document.getElementById('ratingInput').value = val;
        stars.forEach((st, i) => st.classList.toggle('text-warning', i < val));
        stars.forEach((st, i) => st.classList.toggle('text-muted', i >= val));
    });
    s.addEventListener('mouseenter', () => {
        const val = +s.dataset.val;
        stars.forEach((st, i) => st.classList.toggle('text-warning', i < val));
    });
});
document.getElementById('starRating')?.addEventListener('mouseleave', () => {
    const val = +document.getElementById('ratingInput').value;
    stars.forEach((st, i) => {
        st.classList.toggle('text-warning', i < val);
        st.classList.toggle('text-muted', i >= val);
    });
});

document.getElementById('useMyLocation').addEventListener('click', function () {
    const status = document.getElementById('locationStatus');
    const input = document.getElementById('pickup_location');
    const btn = this;
    if (!navigator.geolocation) {
        status.textContent = 'Geolocation is not supported by your browser.';
        status.className = 'text-danger small';
        return;
    }
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    status.textContent = 'Detecting location...';
    status.className = 'text-muted small';
    navigator.geolocation.getCurrentPosition(
        function (pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    input.value = data.display_name || (lat + ', ' + lng);
                    status.textContent = 'Location detected!';
                    status.className = 'text-success small';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-location-crosshairs"></i>';
                })
                .catch(function () {
                    input.value = lat + ', ' + lng;
                    status.textContent = 'Coordinates set (address lookup failed).';
                    status.className = 'text-warning small';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-location-crosshairs"></i>';
                });
        },
        function (err) {
            status.textContent = err.code === 1 ? 'Location access denied.' : 'Unable to detect location.';
            status.className = 'text-danger small';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-location-crosshairs"></i>';
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
});
</script>
@endpush
