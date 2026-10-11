<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0b0f19">
    <title>🚗 @yield('title', 'My Account') — DriveEase</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @stack('styles')
</head>
<body>
@include('partials.navbar')

<main id="main-content" class="site-main">
    <div class="container customer-shell">
        <div class="row g-4">
            {{-- Sidebar --}}
            <div class="col-lg-3">
                <aside class="customer-sidebar">
                    <div class="profile-head">
                        <img src="{{ user_avatar_url(auth()->user()->avatar, auth()->user()->name) }}" class="rounded-circle mb-3" width="76" height="76" style="object-fit:cover" alt="">
                        <h6 class="fw-bold mb-0 text-dark">{{ auth()->user()->name }}</h6>
                        <small class="text-muted text-break">{{ auth()->user()->email }}</small>
                    </div>
                    <nav class="d-flex flex-column gap-1" aria-label="Account">
                        <a href="{{ route('customer.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-gauge-high"></i> Dashboard
                        </a>
                        <a href="{{ route('customer.bookings.index') }}" class="sidebar-nav-link {{ request()->routeIs('customer.bookings.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check"></i> My Bookings
                        </a>
                        <a href="{{ route('customer.wishlist.index') }}" class="sidebar-nav-link {{ request()->routeIs('customer.wishlist.*') ? 'active' : '' }}">
                            <i class="fas fa-heart"></i> Wishlist
                        </a>
                        <a href="{{ route('customer.profile') }}" class="sidebar-nav-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}">
                            <i class="fas fa-user-pen"></i> Profile
                        </a>
                        <hr class="my-2 text-body-tertiary">
                        <a href="{{ route('vehicles.index') }}" class="sidebar-nav-link">
                            <i class="fas fa-car"></i> Browse Vehicles
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="sidebar-nav-link w-100 text-start border-0 bg-transparent text-danger">
                                <i class="fas fa-arrow-right-from-bracket"></i> Logout
                            </button>
                        </form>
                    </nav>
                </aside>
            </div>

            {{-- Main Content --}}
            <div class="col-lg-9">
                @yield('content')
            </div>
        </div>
    </div>
</main>

@include('partials.footer')
@include('partials.scripts')
@stack('scripts')
</body>
</html>
