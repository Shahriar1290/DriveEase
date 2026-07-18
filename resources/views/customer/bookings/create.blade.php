@extends('layouts.customer')
@section('title', 'Book '.$vehicle->vehicle_name)

@section('content')
<div class="content-card mb-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="btn btn-light border btn-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h6 class="fw-bold mb-0">Book: {{ $vehicle->vehicle_name }}</h6>
    </div>

    {{-- Vehicle Preview --}}
    <div class="d-flex gap-3 mb-4 p-3 bg-light rounded-3">
        <img src="{{ vehicle_image_url($vehicle->primary_image) }}" class="rounded-2 flex-shrink-0"
             width="120" height="80" style="object-fit:cover"
             onerror="this.src='https://placehold.co/120x80/1e40af/fff?text=V'">
        <div>
            <h6 class="fw-bold mb-1">{{ $vehicle->vehicle_name }}</h6>
            <p class="text-muted small mb-1">{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->year }}</p>
            <p class="text-muted small mb-0">
                <i class="fas fa-users me-1"></i>{{ $vehicle->seating_capacity }} ·
                <i class="fas fa-gas-pump ms-2 me-1"></i>{{ ucfirst($vehicle->fuel_type) }} ·
                <i class="fas fa-cog ms-2 me-1"></i>{{ ucfirst($vehicle->transmission) }}
            </p>
        </div>
        <div class="ms-auto text-end">
            <div class="h5 fw-bold text-primary">TK {{ number_format($vehicle->price_per_day,2) }}</div>
            <small class="text-muted">per day</small>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger small">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <form action="{{ route('customer.bookings.store') }}" method="POST">
        @csrf
        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Pickup Date <span class="text-danger">*</span></label>
                <input type="date" name="pickup_date" id="pickupDate" class="form-control"
                       min="{{ date('Y-m-d') }}" value="{{ old('pickup_date') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Return Date <span class="text-danger">*</span></label>
                <input type="date" name="return_date" id="returnDate" class="form-control"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('return_date') }}" required>
            </div>

            {{-- Cost Preview --}}
            <div class="col-12">
                <div id="costPreview" class="border rounded-3 p-3 bg-primary-subtle d-none">
                    <h6 class="fw-semibold mb-3">Booking Summary</h6>
                    <div class="row g-2 small">
                        <div class="col-6"><span class="text-muted">Duration</span><br><strong id="daysCount"></strong></div>
                        <div class="col-6"><span class="text-muted">Daily Rate</span><br><strong>TK {{ number_format($vehicle->price_per_day,2) }}</strong></div>
                        <div class="col-6"><span class="text-muted">Subtotal</span><br><strong id="subtotal"></strong></div>
                        <div class="col-6"><span class="text-muted">Tax (5%)</span><br><strong id="taxAmt"></strong></div>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span><span class="text-primary fs-6" id="totalAmt"></span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small">Pickup Location</label>
                <div class="input-group">
                    <input type="text" name="pickup_location" id="pickup_location" class="form-control"
                           placeholder="e.g. Dhaka Airport" value="{{ old('pickup_location') }}">
                    <button type="button" class="btn btn-outline-primary" id="useMyLocation"
                            title="Use my current location">
                        <i class="fas fa-location-crosshairs"></i>
                    </button>
                </div>
                <small class="text-muted" id="locationStatus"></small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Return Location</label>
                <input type="text" name="return_location" class="form-control"
                       placeholder="e.g. Same as pickup" value="{{ old('return_location') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Payment Method <span class="text-danger">*</span></label>
                <select name="payment_method" class="form-select" required>
                    <option value="">Select method...</option>
                    <option value="cash"           {{ old('payment_method')=='cash'?'selected':'' }}>Cash</option>
                    <option value="card"           {{ old('payment_method')=='card'?'selected':'' }}>Credit / Debit Card</option>
                    <option value="mobile_banking" {{ old('payment_method')=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                    <option value="bank_transfer"  {{ old('payment_method')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small">Special Requests</label>
                <textarea name="special_requests" class="form-control" rows="3"
                          placeholder="Any special requirements or notes...">{{ old('special_requests') }}</textarea>
            </div>
            <div class="col-12">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                    <label class="form-check-label small" for="agreeTerms">
                        I agree to the <a href="{{ route('terms') }}" class="text-primary" target="_blank">Terms & Conditions</a>
                        and <a href="{{ route('privacy') }}" class="text-primary" target="_blank">Privacy Policy</a>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold">
                    <i class="fas fa-calendar-check me-2"></i>Confirm Booking
                </button>
                <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="btn btn-light border ms-2">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
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
    // Set return date min
    document.getElementById('returnDate').min = new Date(new Date(p).getTime() + 86400000).toISOString().split('T')[0];
}
document.getElementById('pickupDate').addEventListener('change', calcCost);
document.getElementById('returnDate').addEventListener('change', calcCost);

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
