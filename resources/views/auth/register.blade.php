@extends('layouts.app')
@section('title', 'Register — DriveEase')
@section('content')
<div class="min-vh-100 d-flex align-items-center py-5" style="background:linear-gradient(135deg,#0f172a,#1e3a5f)">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center mb-4">
                    <a href="{{ route('home') }}" class="text-white text-decoration-none fw-bold fs-3">
                        <i class="fas fa-car-side text-primary me-2"></i><span class="text-primary">Drive</span>Ease
                    </a>
                    <p class="text-white-50 mt-1">Create your account and start renting today.</p>
                </div>
                <div class="card border-0 rounded-4 shadow-xl p-4">
                    @if($errors->any())
                    <div class="alert alert-danger rounded-3 small">
                        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                    </div>
                    @endif
                    <h5 class="fw-bold mb-4">Create Account</h5>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       placeholder="John Doe" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       placeholder="you@example.com" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Phone Number</label>
                                <input type="tel" name="phone" class="form-control"
                                       placeholder="+880 17XX XXXXXX" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Min. 8 characters" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label small" for="terms">
                                        I agree to the <a href="{{ route('terms') }}" class="text-primary">Terms & Conditions</a>
                                        and <a href="{{ route('privacy') }}" class="text-primary">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Create Account</button>
                            </div>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <span class="text-muted small">Already have an account? </span>
                        <a href="{{ route('login') }}" class="text-primary small fw-semibold">Sign In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
