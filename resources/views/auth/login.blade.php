{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')
@section('title', 'Login — DriveEase')
@section('content')
<div class="min-vh-100 d-flex align-items-center py-5" style="background:linear-gradient(135deg,#0f172a,#1e3a5f)">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <a href="{{ route('home') }}" class="text-white text-decoration-none fw-bold fs-3">
                        <i class="fas fa-car-side text-primary me-2"></i><span class="text-primary">Drive</span>Ease
                    </a>
                    <p class="text-white-50 mt-1">Welcome back! Sign in to continue.</p>
                </div>
                <div class="card border-0 rounded-4 shadow-xl p-4">
                    @if($errors->any())
                    <div class="alert alert-danger rounded-3 small">
                        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                    @endif
                    <h5 class="fw-bold mb-4">Sign In</h5>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                                <button type="button" class="btn btn-light border border-start-0" onclick="togglePwd(this)"><i class="fas fa-eye text-muted"></i></button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label small" for="remember">Remember me</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Sign In</button>
                    </form>
                    <div class="text-center mt-3">
                        <span class="text-muted small">Don't have an account? </span>
                        <a href="{{ route('register') }}" class="text-primary small fw-semibold">Create Account</a>
                    </div>
                    <hr class="my-3">
                    <div class="text-center">
                        <small class="text-muted">Demo: <code>admin@vrms.com</code> / <code>password</code></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function togglePwd(btn) {
    const input = btn.previousElementSibling;
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.querySelector('i').classList.toggle('fa-eye');
    btn.querySelector('i').classList.toggle('fa-eye-slash');
}
</script>
@endpush
