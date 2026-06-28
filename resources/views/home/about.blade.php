{{-- resources/views/home/about.blade.php --}}
@extends('layouts.app')
@section('title','About Us — DriveEase')
@section('content')
<div class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold mb-3">About DriveEase</h1>
        <p class="lead text-white-50 mb-0">Your trusted vehicle rental partner since 2015</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill mb-3">Our Story</span>
                <h2 class="fw-bold mb-4">Driving Excellence Since 2015</h2>
                <p class="text-muted mb-3">DriveEase was founded with a simple mission: to make quality vehicle rental accessible, affordable, and hassle-free for everyone. We started with a small fleet of 10 vehicles and have grown to over 50 premium vehicles across 25 cities.</p>
                <p class="text-muted mb-4">Our team of dedicated professionals works around the clock to ensure every rental experience exceeds expectations. From our easy online booking system to our 24/7 customer support, we put you first.</p>
                <div class="row g-3">
                    @foreach([['50+','Premium Vehicles'],['10K+','Happy Customers'],['25','Cities Served'],['9','Years of Service']] as $s)
                    <div class="col-6">
                        <div class="bg-light rounded-3 p-3 text-center">
                            <div class="h3 fw-bold text-primary mb-0">{{ $s[0] }}</div>
                            <div class="text-muted small">{{ $s[1] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-primary rounded-4 p-5 text-white text-center" style="background:linear-gradient(135deg,#0ea5e9,#0284c7)!important">
                    <i class="fas fa-car-side mb-3" style="font-size:5rem;opacity:.3"></i>
                    <h3 class="fw-bold">Our Mission</h3>
                    <p class="opacity-75">To provide reliable, affordable, and premium vehicle rental services that empower people to travel freely and confidently.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5"><h2 class="fw-bold">Our Core Values</h2></div>
        <div class="row g-4">
            @foreach([
                ['fas fa-shield-alt','Safety First','All vehicles undergo rigorous safety checks before every rental.'],
                ['fas fa-handshake','Transparency','No hidden fees. Honest pricing, always.'],
                ['fas fa-heart','Customer Care','Your satisfaction is our highest priority.'],
                ['fas fa-leaf','Sustainability','Growing our electric fleet for a greener future.'],
            ] as $v)
            <div class="col-md-3 text-center">
                <div class="bg-white rounded-3 p-4 shadow-sm h-100">
                    <div class="rounded-circle bg-primary-subtle d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px">
                        <i class="{{ $v[0] }} text-primary fs-4"></i>
                    </div>
                    <h6 class="fw-bold mb-2">{{ $v[1] }}</h6>
                    <p class="text-muted small mb-0">{{ $v[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
