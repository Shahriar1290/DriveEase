@extends('layouts.app')
@section('title','FAQ — DriveEase')
@section('content')
<div class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold mb-2">Frequently Asked Questions</h1>
        <p class="text-white-50">Everything you need to know about renting with DriveEase</p>
    </div>
</div>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @php $faqs = [
                ['What documents do I need to rent a vehicle?','You need a valid driving license, a national ID or passport, and a credit/debit card for the security deposit.'],
                ['How do I make a booking?','Browse our vehicle listings, select your preferred vehicle, choose your pickup and return dates, and confirm your booking online.'],
                ['Can I cancel my booking?','Yes, you can cancel a pending or approved booking from your dashboard. Cancellations made 24+ hours before pickup receive a full refund.'],
                ['What is included in the rental price?','The rental price includes basic insurance, unlimited mileage, and 24/7 roadside assistance. Additional features may apply.'],
                ['What happens if the vehicle breaks down?','Contact our 24/7 support team immediately. We will arrange a replacement vehicle or roadside assistance as quickly as possible.'],
                ['Can I extend my rental period?','Yes, contact us at least 24 hours before your scheduled return date. Extensions are subject to vehicle availability.'],
                ['Is fuel included in the price?','No, vehicles are provided with a full tank and should be returned full. Fuel costs are the renter\'s responsibility.'],
                ['What is the minimum age to rent?','You must be at least 21 years old with a valid driving license held for at least 1 year.'],
            ]; @endphp
            <div class="accordion" id="faqAccordion">
                @foreach($faqs as $i => $faq)
                <div class="accordion-item border mb-2 rounded-3 overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }} fw-semibold" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">
                            {{ $faq[0] }}
                        </button>
                    </h2>
                    <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">{{ $faq[1] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-5 p-4 bg-light rounded-3">
                <h5 class="fw-bold mb-2">Still have questions?</h5>
                <p class="text-muted mb-3">Our support team is available 24/7 to help you.</p>
                <a href="{{ route('contact') }}" class="btn btn-primary">Contact Us</a>
            </div>
        </div>
    </div>
</div>
@endsection
