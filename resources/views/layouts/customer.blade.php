<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My Account') — DriveEase</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; }
        .customer-sidebar { background: #fff; border-radius: .75rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,.07); }
        .sidebar-nav-link { display: flex; align-items: center; gap: .75rem; padding: .6rem .75rem;
            color: #6b7280; text-decoration: none; border-radius: .5rem; font-size: .875rem; transition: all .2s; }
        .sidebar-nav-link:hover, .sidebar-nav-link.active { background: #eff6ff; color: #0ea5e9; }
        .sidebar-nav-link i { width: 18px; text-align: center; }
        .content-card { background: #fff; border-radius: .75rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,.07); }
    </style>
    @stack('styles')
</head>
<body>
@include('partials.navbar')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-0 rounded-0" role="alert">
        <div class="container"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
        <div class="container"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    </div>
@endif

<div class="container py-5">
    <div class="row g-4">
        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="customer-sidebar">
                <div class="text-center mb-4">
                    <img src="{{ user_avatar_url(auth()->user()->avatar, auth()->user()->name) }}" class="rounded-circle mb-2" width="80" height="80" style="object-fit:cover">
                    <h6 class="fw-semibold mb-0">{{ auth()->user()->name }}</h6>
                    <small class="text-muted">{{ auth()->user()->email }}</small>
                </div>
                <nav class="d-flex flex-column gap-1">
                    <a href="{{ route('customer.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="{{ route('customer.bookings.index') }}" class="sidebar-nav-link {{ request()->routeIs('customer.bookings.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i> My Bookings
                    </a>
                    <a href="{{ route('customer.wishlist.index') }}" class="sidebar-nav-link {{ request()->routeIs('customer.wishlist.*') ? 'active' : '' }}">
                        <i class="fas fa-heart"></i> Wishlist
                    </a>
                    <a href="{{ route('customer.profile') }}" class="sidebar-nav-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-edit"></i> Profile
                    </a>
                    <hr class="my-2">
                    <a href="{{ route('vehicles.index') }}" class="sidebar-nav-link">
                        <i class="fas fa-car"></i> Browse Vehicles
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="sidebar-nav-link w-100 text-start border-0 bg-transparent text-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="col-lg-9">
            @yield('content')
        </div>
    </div>
</div>

@include('partials.footer')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.1/sweetalert2.all.min.js"></script>
@stack('scripts')
</body>
</html>
