@extends('layouts.app')
@section('title', 'Forgot Password — DriveEase')
@section('content')
<div class="min-vh-100 d-flex align-items-center py-5" style="background:linear-gradient(135deg,#0f172a,#1e3a5f)">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <a href="{{ route('home') }}" class="text-white text-decoration-none fw-bold fs-3">
                        <i class="fas fa-car-side text-primary me-2"></i><span class="text-primary">Drive</span>Ease
                    </a>
                    <p class="text-white-50 mt-1">Enter your email to reset your password.</p>
                </div>
                <div class="card border-0 rounded-4 shadow-xl p-4">
                    @if(session('status'))
                    <div class="alert alert-success rounded-3 small">{{ session('status') }}</div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-danger rounded-3 small">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                    <div class="alert alert-danger rounded-3 small">
                        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                    @endif
                    <h5 class="fw-bold mb-4">Reset Password</h5>
                    <form method="POST" action="{{ route('password.request') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Send Reset Link</button>
                    </form>
                    <div class="text-center mt-3">
                        <span class="text-muted small">Remember your password? </span>
                        <a href="{{ route('login') }}" class="text-primary small fw-semibold">Sign In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
