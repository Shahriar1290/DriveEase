{{-- resources/views/home/contact.blade.php --}}
@extends('layouts.app')
@section('title','Contact Us — DriveEase')
@section('content')
<div class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold mb-2">Contact Us</h1>
        <p class="text-white-50 mb-0">We'd love to hear from you. Send us a message!</p>
    </div>
</div>
<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-4">
            <h5 class="fw-bold mb-4">Get In Touch</h5>
            @foreach([
                ['fas fa-map-marker-alt','Address','123 Main Street, Dhaka, Bangladesh'],
                ['fas fa-phone','Phone','+880 1700-000000'],
                ['fas fa-envelope','Email','info@driveease.com'],
                ['fas fa-clock','Hours','Mon–Sat: 8AM–8PM'],
            ] as $i)
            <div class="d-flex gap-3 mb-4">
                <div class="rounded-2 bg-primary-subtle d-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:44px">
                    <i class="{{ $i[0] }} text-primary"></i>
                </div>
                <div><div class="fw-semibold">{{ $i[1] }}</div><div class="text-muted small">{{ $i[2] }}</div></div>
            </div>
            @endforeach
        </div>
        <div class="col-lg-8">
            <div class="bg-white rounded-4 shadow-sm p-4">
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <h5 class="fw-bold mb-4">Send a Message</h5>
                <form method="POST" action="{{ route('contact.submit') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Phone</label>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" required value="{{ old('subject') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
