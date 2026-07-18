@extends('layouts.app')
@section('title', $vehicle->vehicle_name . ' — DriveEase')

@push('styles')
<style>
.gallery-thumb { width:70px;height:55px;object-fit:cover;border-radius:.375rem;cursor:pointer;opacity:.6;transition:all .2s;border:2px solid transparent; }
.gallery-thumb.active,.gallery-thumb:hover { opacity:1;border-color:#0ea5e9; }
.main-img { height:420px;object-fit:cover;border-radius:.75rem;width:100%; }
.spec-item { display:flex;align-items:center;gap:.75rem;padding:.75rem 0;border-bottom:1px solid #f3f4f6; }
.spec-item:last-child { border-bottom:none; }
.spec-icon { width:36px;height:36px;background:#eff6ff;border-radius:.5rem;display:flex;align-items:center;justify-content:center;color:#0ea5e9;flex-shrink:0; }
.feature-tag { background:#f0fdf4;color:#16a34a;border-radius:.375rem;padding:.25rem .6rem;font-size:.78rem;font-weight:500; }
.feature-tag.no { background:#fef2f2;color:#dc2626; }
.booking-card { background:#fff;border-radius:.75rem;padding:1.5rem;box-shadow:0 4px 24px rgba(0,0,0,.1);position:sticky;top:80px; }
.review-star { color:#f59e0b; }
</style>
@endpush

@section('content')
<div class="bg-dark text-white py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}" class="text-white-50 text-decoration-none">Vehicles</a></li>
                <li class="breadcrumb-item active text-white">{{ $vehicle->vehicle_name }}</li>
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
                <img id="mainImg" src="{{ vehicle_image_url($images[0]->image ?? null) }}"
                     class="main-img shadow-sm" alt="{{ $vehicle->vehicle_name }}"
                     onerror="this.src='https://placehold.co/800x420/1e40af/fff?text={{ urlencode($vehicle->vehicle_name) }}'">
                @if(count($images) > 1)
                <div class="d-flex gap-2 mt-2 flex-wrap">
                    @foreach($images as $img)
                    <img src="{{ vehicle_image_url($img->image) }}" class="gallery-thumb {{ $loop->first ? 'active' : '' }}"
                         onclick="switchImg(this, '{{ vehicle_image_url($img->image) }}')"
                         onerror="this.src='https://placehold.co/70x55/1e40af/fff?text=img'">
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Info Header --}}
            <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary">{{ $vehicle->category_name }}</span>
                        <span class="badge bg-success">{{ ucfirst($vehicle->status) }}</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $vehicle->vehicle_name }}</h2>
                    <p class="text-muted mb-0">{{ $vehicle->brand }} {{ $vehicle->model }} · {{ $vehicle->year }}</p>
                </div>
                <div class="text-end">
                    <div class="h3 fw-bold text-primary mb-0">TK {{ number_format($vehicle->price_per_day) }}</div>
                    <small class="text-muted">per day</small>
                </div>
            </div>

            {{-- Rating --}}
            <div class="d-flex align-items-center gap-2 mb-4">
                <div class="d-flex gap-1">
                    @for($i=1;$i<=5;$i++)
                        <i class="fas fa-star {{ $i<=$vehicle->average_rating?'text-warning':'text-muted' }}"></i>
                    @endfor
                </div>
                <span class="fw-semibold">{{ $vehicle->average_rating }}</span>
                <span class="text-muted">({{ $reviewCount }} reviews)</span>
                <span class="text-muted">·</span>
                <span class="text-muted">{{ $vehicle->total_rentals }} rentals</span>
            </div>

            {{-- Description --}}
            @if($vehicle->description)
            <div class="mb-4">
                <h5 class="fw-bold mb-2">About This Vehicle</h5>
                <p class="text-muted">{{ $vehicle->description }}</p>
            </div>
            @endif

            {{-- Specs --}}
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-3">Specifications</h5>
                    <div class="spec-item"><div class="spec-icon"><i class="fas fa-users"></i></div><div><div class="text-muted small">Seating</div><div class="fw-semibold">{{ $vehicle->seating_capacity }} Persons</div></div></div>
                    <div class="spec-item"><div class="spec-icon"><i class="fas fa-gas-pump"></i></div><div><div class="text-muted small">Fuel Type</div><div class="fw-semibold">{{ ucfirst($vehicle->fuel_type) }}</div></div></div>
                    <div class="spec-item"><div class="spec-icon"><i class="fas fa-cog"></i></div><div><div class="text-muted small">Transmission</div><div class="fw-semibold">{{ ucfirst($vehicle->transmission) }}</div></div></div>
                    <div class="spec-item"><div class="spec-icon"><i class="fas fa-palette"></i></div><div><div class="text-muted small">Color</div><div class="fw-semibold">{{ $vehicle->color ?? 'N/A' }}</div></div></div>
                    <div class="spec-item"><div class="spec-icon"><i class="fas fa-tachometer-alt"></i></div><div><div class="text-muted small">Mileage</div><div class="fw-semibold">{{ $vehicle->mileage ?? 'N/A' }}</div></div></div>
                    <div class="spec-item"><div class="spec-icon"><i class="fas fa-id-badge"></i></div><div><div class="text-muted small">Registration</div><div class="fw-semibold">{{ $vehicle->registration_number }}</div></div></div>
                </div>
                <div class="col-md-6">
                    <h5 class="fw-bold mb-3">Features & Amenities</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="feature-tag {{ $vehicle->air_conditioning?'':'no' }}">
                            <i class="fas fa-{{ $vehicle->air_conditioning?'check':'times' }} me-1"></i>Air Conditioning
                        </span>
                        <span class="feature-tag {{ $vehicle->gps?'':'no' }}">
                            <i class="fas fa-{{ $vehicle->gps?'check':'times' }} me-1"></i>GPS Navigation
                        </span>
                        <span class="feature-tag {{ $vehicle->bluetooth?'':'no' }}">
                            <i class="fab fa-{{ $vehicle->bluetooth?'check':'times' }} me-1"></i>Bluetooth
                        </span>
                        <span class="feature-tag {{ $vehicle->usb_charger?'':'no' }}">
                            <i class="fas fa-{{ $vehicle->usb_charger?'check':'times' }} me-1"></i>USB Charger
                        </span>
                        <span class="feature-tag {{ $vehicle->child_seat?'':'no' }}">
                            <i class="fas fa-{{ $vehicle->child_seat?'check':'times' }} me-1"></i>Child Seat
                        </span>
                    </div>

                    <div class="mt-4">
                        <h6 class="fw-semibold mb-2 text-muted small text-uppercase">Pricing Breakdown</h6>
                        <table class="table table-sm table-borderless">
                            <tr><td class="text-muted">Daily Rate</td><td class="fw-bold">TK {{ number_format($vehicle->price_per_day,2) }}</td></tr>
                            <tr><td class="text-muted">Weekly (x7)</td><td class="fw-bold">TK {{ number_format($vehicle->price_per_day*7,2) }}</td></tr>
                            <tr><td class="text-muted">Monthly (x30)</td><td class="fw-bold">TK {{ number_format($vehicle->price_per_day*30,2) }}</td></tr>
                            <tr><td class="text-muted">Tax (5%)</td><td class="fw-bold">Included</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Reviews --}}
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Customer Reviews ({{ $reviewCount }})</h5>
                    @auth
                    @if(auth()->user()->isCustomer() && $userHasBookedVehicle)
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reviewModal">
                        <i class="fas fa-star me-1"></i>Write Review
                    </button>
                    @endif
                    @endauth
                </div>

                @forelse($reviews as $review)
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ user_avatar_url($review->user_avatar, $review->user_name) }}" class="rounded-circle" width="36" height="36" style="object-fit:cover">
                            <span class="fw-semibold">{{ $review->user_name }}</span>
                        </div>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}</small>
                    </div>
                    <div class="d-flex gap-1 mb-1">
                        @for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i<=$review->rating?'text-warning':'text-muted' }}" style="font-size:12px"></i>@endfor
                    </div>
                    <p class="text-muted small mb-0">{{ $review->review }}</p>
                </div>
                @empty
                <p class="text-muted">No reviews yet. Be the first to review!</p>
                @endforelse
            </div>
        </div>

        {{-- Right: Booking Card --}}
        <div class="col-lg-4">
            <div class="booking-card">
                <h5 class="fw-bold mb-4">Book This Vehicle</h5>

                @if($vehicle->status === 'available')
                @auth
                @if(auth()->user()->isCustomer())
                <form action="{{ route('customer.bookings.store') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pickup Date <span class="text-danger">*</span></label>
                        <input type="date" name="pickup_date" id="pickupDate" class="form-control"
                               min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" id="returnDate" class="form-control"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>

                    <div id="costPreview" class="bg-light rounded p-3 mb-3 d-none">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Duration</span>
                            <span class="fw-semibold" id="daysCount">-</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Rate</span>
                            <span>TK {{ number_format($vehicle->price_per_day,2) }}/day</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Subtotal</span>
                            <span id="subtotal">-</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Tax (5%)</span>
                            <span id="taxAmt">-</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span class="text-primary" id="totalAmt">-</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pickup Location</label>
                        <div class="input-group">
                            <input type="text" name="pickup_location" id="pickup_location" class="form-control" placeholder="e.g. Airport Terminal 1">
                            <button type="button" class="btn btn-outline-primary" id="useMyLocation"
                                    title="Use my current location">
                                <i class="fas fa-location-crosshairs"></i>
                            </button>
                        </div>
                        <small class="text-muted" id="locationStatus"></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">Select method</option>
                            <option value="cash">Cash</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="mobile_banking">Mobile Banking</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Special Requests</label>
                        <textarea name="special_requests" class="form-control" rows="2" placeholder="Any special requirements..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="fas fa-calendar-check me-2"></i>Book Now
                    </button>
                </form>
                @else
                <div class="alert alert-info">Admin accounts cannot make bookings.</div>
                @endif
                @else
                <div class="text-center py-3">
                    <i class="fas fa-lock text-muted mb-2" style="font-size:2rem"></i>
                    <p class="text-muted mb-3">Please login to book this vehicle</p>
                    <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">Create Account</a>
                </div>
                @endauth
                @else
                <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>This vehicle is currently unavailable.</div>
                @endif

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                        <i class="fas fa-shield-alt text-success"></i>Free cancellation up to 24h before pickup
                    </div>
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <i class="fas fa-headset text-primary"></i>24/7 customer support
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
                        <img src="{{ vehicle_image_url($v->primary_image) }}" class="card-img-top" style="height:140px;object-fit:cover"
                             onerror="this.src='https://placehold.co/300x140/1e40af/fff?text={{ urlencode($v->vehicle_name) }}'">
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
    document.getElementById('subtotal').textContent = '$' + sub.toFixed(2);
    document.getElementById('taxAmt').textContent = '$' + tax.toFixed(2);
    document.getElementById('totalAmt').textContent = '$' + total.toFixed(2);
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
