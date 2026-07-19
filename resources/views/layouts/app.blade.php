<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🚗 @yield('title', 'Vehicle Rental Management System')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="{{ route('home') }}">
            <i class="fas fa-car-side text-primary me-2"></i>
            <span class="text-primary">Drive</span>Ease
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('vehicles.index') }}">Vehicles</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('faq') }}">FAQ</a></li>
            </ul>

            {{-- Search Bar --}}
            <form class="d-flex me-3 position-relative" id="navSearchForm">
                <input class="form-control form-control-sm bg-dark text-light border-secondary" type="search"
                       id="navSearch" placeholder="Search vehicles..." autocomplete="off" style="width:220px">
                <div id="searchDropdown" class="position-absolute bg-white rounded shadow-lg d-none"
                     style="top:100%;left:0;right:0;z-index:9999;max-height:300px;overflow-y:auto"></div>
            </form>

            <ul class="navbar-nav align-items-center gap-1">
                @guest
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Register</a>
                    </li>
                @else
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="btn btn-warning btn-sm" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Admin
                            </a>
                        </li>
                    @else
                        {{-- Wishlist --}}
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('customer.wishlist.index') }}">
                                <i class="fas fa-heart"></i>
                            </a>
                        </li>
                        {{-- Notifications --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                @if($navUnreadCount > 0)
                                    <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size:9px">{{ $navUnreadCount }}</span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow" style="width:320px;max-height:400px;overflow-y:auto">
                                <h6 class="dropdown-header">Notifications</h6>
                                @foreach($navNotifications as $notif)
                                    <a href="{{ $notif->link ?? '#' }}" class="dropdown-item py-2 border-bottom {{ $notif->status==='unread' ? 'bg-light' : '' }}">
                                        <div class="d-flex gap-2">
                                            <i class="fas fa-circle text-{{ $notif->type }} mt-1" style="font-size:8px"></i>
                                            <div>
                                                <div class="fw-semibold small">{{ $notif->title }}</div>
                                                <div class="text-muted" style="font-size:12px">{{ Str::limit($notif->message, 60) }}</div>
                                                <div class="text-muted" style="font-size:11px">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                                <a href="{{ route('customer.dashboard') }}" class="dropdown-item text-center text-primary small py-2">View all</a>
                            </div>
                        </li>
                    @endif

                    {{-- User Menu --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            <img src="{{ user_avatar_url(auth()->user()->avatar, auth()->user()->name) }}" class="rounded-circle" width="28" height="28" style="object-fit:cover">
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            @if(auth()->user()->isCustomer())
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.bookings.index') }}"><i class="fas fa-calendar me-2 text-primary"></i>My Bookings</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="fas fa-user me-2 text-primary"></i>Profile</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-0 rounded-0 border-0" role="alert">
        <div class="container"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0 border-0" role="alert">
        <div class="container"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    </div>
@endif

@yield('content')

{{-- Footer --}}
<footer class="bg-dark text-light pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="mb-3">
                    <a class="text-white text-decoration-none fw-bold fs-4" href="{{ route('home') }}">
                        <i class="fas fa-car-side text-primary me-2"></i>
                        <span class="text-primary">Drive</span>Ease
                    </a>
                </div>
                <p class="text-light small">Your trusted vehicle rental partner. We offer a wide range of vehicles for every need and budget.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-light fs-5"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-light fs-5"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-light fs-5"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-light fs-5"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <h6 class="fw-semibold mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-1"><a href="{{ route('home') }}" class="text-light text-decoration-none small hover-white">Home</a></li>
                    <li class="mb-1"><a href="{{ route('vehicles.index') }}" class="text-light text-decoration-none small">Vehicles</a></li>
                    <li class="mb-1"><a href="{{ route('about') }}" class="text-light text-decoration-none small">About Us</a></li>
                    <li class="mb-1"><a href="{{ route('contact') }}" class="text-light text-decoration-none small">Contact</a></li>
                    <li class="mb-1"><a href="{{ route('faq') }}" class="text-light text-decoration-none small">FAQ</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-4">
                <h6 class="fw-semibold mb-3">Legal</h6>
                <ul class="list-unstyled">
                    <li class="mb-1"><a href="{{ route('privacy') }}" class="text-light text-decoration-none small">Privacy Policy</a></li>
                    <li class="mb-1"><a href="{{ route('terms') }}" class="text-light text-decoration-none small">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4">
                <h6 class="fw-semibold mb-3">Newsletter</h6>
                <p class="text-light small">Subscribe to get the latest deals and offers.</p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="email" name="email" class="form-control form-control-sm bg-secondary border-0 text-light" placeholder="Your email">
                    <button class="btn btn-primary btn-sm px-3">Subscribe</button>
                </form>
                <div class="mt-3 text-light small">
                    <p class="mb-1"><i class="fas fa-map-marker-alt me-2 text-primary"></i>123 Main Street, Dhaka, Bangladesh</p>
                    <p class="mb-1"><i class="fas fa-phone me-2 text-primary"></i>+880 1700-000000</p>
                    <p><i class="fas fa-envelope me-2 text-primary"></i>info@driveease.com</p>
                </div>
            </div>
        </div>
        <hr class="border-secondary mt-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <p class="text-light small mb-0">&copy; {{ date('Y') }} DriveEase. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.1/sweetalert2.all.min.js"></script>
<script>
// Live search
let searchTimeout;
const searchInput = document.getElementById('navSearch');
const searchDropdown = document.getElementById('searchDropdown');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 2) { searchDropdown.classList.add('d-none'); return; }
        searchTimeout = setTimeout(() => {
            fetch(`/vehicles/search?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.length) { searchDropdown.classList.add('d-none'); return; }
                    searchDropdown.innerHTML = data.map(v => `
                        <a href="${v.url}" class="d-flex align-items-center gap-2 p-2 text-decoration-none text-dark border-bottom hover-bg">
                            <img src="${v.image}" width="40" height="30" class="rounded object-fit-cover" onerror="this.src='/images/vehicle-default.jpg'">
                            <div>
                                <div class="fw-semibold small">${v.name}</div>
                                <div class="text-muted" style="font-size:11px">${v.brand} · TK $${v.price}/day</div>
                            </div>
                        </a>`).join('');
                    searchDropdown.classList.remove('d-none');
                });
        }, 300);
    });
    document.addEventListener('click', e => {
        if (!e.target.closest('#navSearchForm')) searchDropdown.classList.add('d-none');
    });
}

// SweetAlert confirm on delete
document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function(e) {
        e.preventDefault();
        const form = document.querySelector(this.dataset.form || '#deleteForm');
        Swal.fire({
            title: 'Are you sure?',
            text: this.dataset.confirm || 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!'
        }).then(result => { if (result.isConfirmed) form.submit(); });
    });
});

@if(session('success'))
Swal.fire({ toast:true, position:'top-end', icon:'success', title:'{{ session('success') }}', showConfirmButton:false, timer:3000 });
@endif
</script>
@stack('scripts')
</body>
</html>
