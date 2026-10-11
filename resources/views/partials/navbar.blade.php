{{-- resources/views/partials/navbar.blade.php — shared by the public site and customer area --}}
<a href="#main-content" class="skip-link">Skip to content</a>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top py-3" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold fs-4 text-white me-4" href="{{ route('home') }}">
            <span class="brand-icon-wrap me-2"><i class="fas fa-car-side"></i></span>
            <span><span class="brand-accent">Drive</span>Ease</span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">Vehicles</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('faq') ? 'active' : '' }}" href="{{ route('faq') }}">FAQ</a></li>
            </ul>

            {{-- Live search --}}
            <form class="nav-search d-flex me-lg-3 position-relative my-2 my-lg-0" id="navSearchForm" role="search"
                  action="{{ route('vehicles.index') }}" method="GET">
                <div class="input-group input-group-sm">
                    <span class="input-group-text ps-3 pe-2 border-end-0 rounded-start-pill">
                        <i class="fas fa-search" style="font-size: 0.8rem;"></i>
                    </span>
                    <input class="form-control form-control-sm border-start-0 rounded-end-pill px-2" type="search" name="search"
                           id="navSearch" placeholder="Search vehicles..." autocomplete="off" aria-label="Search vehicles">
                </div>
                <div id="searchDropdown" class="search-dropdown position-absolute d-none"></div>
            </form>

            <ul class="navbar-nav align-items-lg-center gap-1 flex-row flex-wrap">
                @guest
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm rounded-pill px-3" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm rounded-pill px-3" href="{{ route('register') }}">Get Started</a>
                    </li>
                @else
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="btn btn-warning btn-sm rounded-pill px-3" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-gauge-high"></i>Admin Panel
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link nav-icon-btn position-relative {{ request()->routeIs('customer.wishlist.*') ? 'active' : '' }}"
                               href="{{ route('customer.wishlist.index') }}" aria-label="Wishlist" title="Wishlist">
                                <i class="fas fa-heart"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-icon-btn position-relative" href="#" data-bs-toggle="dropdown" aria-expanded="false"
                               aria-label="Notifications{{ $navUnreadCount ? ' ('.$navUnreadCount.' unread)' : '' }}" title="Notifications">
                                <i class="fas fa-bell"></i>
                                @if($navUnreadCount > 0)
                                    <span class="nav-badge">{{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}</span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notif-menu">
                                <div class="d-flex justify-content-between align-items-center px-2 pt-1 pb-2">
                                    <span class="dropdown-header p-0">Notifications</span>
                                    @if($navUnreadCount > 0)
                                        <span class="status-pill status-primary">{{ $navUnreadCount }} new</span>
                                    @endif
                                </div>
                                @forelse($navNotifications as $notif)
                                    @php $icon = ['success' => 'check', 'danger' => 'xmark', 'warning' => 'exclamation'][$notif->type] ?? 'info'; @endphp
                                    <a href="{{ $notif->link ?? '#' }}" class="dropdown-item notif-item py-2 {{ $notif->status === 'unread' ? 'unread' : '' }}">
                                        <span class="notif-dot bg-{{ $notif->type }}-subtle text-{{ $notif->type }}"><i class="fas fa-{{ $icon }}"></i></span>
                                        <span class="d-block">
                                            <span class="d-block fw-semibold small text-dark">{{ $notif->title }}</span>
                                            <span class="d-block text-muted" style="font-size:12px">{{ Str::limit($notif->message, 70) }}</span>
                                            <span class="d-block text-muted" style="font-size:11px">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</span>
                                        </span>
                                    </a>
                                @empty
                                    <div class="text-center text-muted small py-4">
                                        <i class="far fa-bell-slash d-block mb-2 fs-4"></i>You're all caught up
                                    </div>
                                @endforelse
                                <div class="border-top mt-1 pt-1">
                                    <a href="{{ route('customer.dashboard') }}" class="dropdown-item text-center text-primary small fw-semibold">View all activity</a>
                                </div>
                            </div>
                        </li>
                    @endif

                    {{-- User menu --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ user_avatar_url(auth()->user()->avatar, auth()->user()->name) }}" class="rounded-circle" width="30" height="30" style="object-fit:cover" alt="">
                            <span class="d-none d-xl-inline">{{ Str::words(auth()->user()->name, 1, '') }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width:230px">
                            <li class="dropdown-user-head">
                                <div class="fw-semibold small text-dark">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size:12px">{{ auth()->user()->email }}</div>
                            </li>
                            @if(auth()->user()->isCustomer())
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="fas fa-gauge-high me-2 text-primary"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.bookings.index') }}"><i class="fas fa-calendar-check me-2 text-primary"></i>My Bookings</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.wishlist.index') }}"><i class="fas fa-heart me-2 text-primary"></i>Wishlist</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="fas fa-user-pen me-2 text-primary"></i>Profile</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-gauge-high me-2 text-primary"></i>Admin Dashboard</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger"><i class="fas fa-arrow-right-from-bracket me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
