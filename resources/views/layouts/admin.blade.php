<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#090d16">
    <title>🚗 @yield('title', 'Admin') — DriveEase</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @stack('styles')
</head>
<body class="admin-body">
<a href="#main-content" class="skip-link">Skip to content</a>

{{-- Sidebar --}}
<nav class="sidebar" id="sidebar" aria-label="Admin navigation">
    <div class="sidebar-logo">
        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center">
            <span class="brand-icon-wrap me-2" style="width:34px;height:34px"><i class="fas fa-car-side"></i></span>
            <span><span class="brand-accent">Drive</span>Ease <small class="text-secondary fw-medium" style="font-size:.75rem">Admin</small></span>
        </a>
    </div>

    <div class="py-2">
        <div class="sidebar-section">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-gauge-high"></i> Dashboard
        </a>

        <div class="sidebar-section">Fleet</div>
        <a href="{{ route('admin.vehicles.index') }}" class="sidebar-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
            <i class="fas fa-car"></i> Vehicles
        </a>
        <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i> Categories
        </a>
        <a href="{{ route('admin.maintenance.index') }}" class="sidebar-link {{ request()->routeIs('admin.maintenance.*') ? 'active' : '' }}">
            <i class="fas fa-wrench"></i> Maintenance
        </a>

        <div class="sidebar-section">Rentals</div>
        <a href="{{ route('admin.bookings.index') }}" class="sidebar-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i> Bookings
            @if($sidebarPendingBookings) <span class="badge rounded-pill bg-warning text-dark">{{ $sidebarPendingBookings }}</span> @endif
        </a>
        <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="fas fa-credit-card"></i> Payments
        </a>

        <div class="sidebar-section">People</div>
        <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Customers
        </a>
        <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <i class="fas fa-star"></i> Reviews
            @if($sidebarPendingReviews) <span class="badge rounded-pill bg-warning text-dark">{{ $sidebarPendingReviews }}</span> @endif
        </a>

        <div class="sidebar-section">Analytics</div>
        <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-column"></i> Reports
        </a>
    </div>

    <div class="sidebar-footer">
        <a href="{{ route('home') }}" class="sidebar-link" target="_blank" rel="noopener">
            <i class="fas fa-arrow-up-right-from-square"></i> View Site
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-100 text-start border-0 bg-transparent" style="width:calc(100% - 1.5rem) !important">
                <i class="fas fa-arrow-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>
</nav>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

{{-- Main Content --}}
<div class="main-content">
    {{-- Top Navbar --}}
    <header class="top-navbar d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3 min-w-0">
            <button class="btn btn-light btn-sm nav-icon-btn d-lg-none" id="sidebarToggle" aria-label="Open menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
            <div class="min-w-0">
                <p class="page-title text-truncate">@yield('title', 'Dashboard')</p>
                <nav aria-label="breadcrumb" class="d-none d-md-block">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary btn-sm d-none d-md-inline-flex">
                <i class="fas fa-plus"></i> Add Vehicle
            </a>
            {{-- Notifications --}}
            <div class="dropdown">
                <button class="btn btn-light btn-sm nav-icon-btn position-relative" data-bs-toggle="dropdown" aria-expanded="false"
                        aria-label="Notifications{{ $navUnreadCount ? ' ('.$navUnreadCount.' unread)' : '' }}">
                    <i class="fas fa-bell"></i>
                    @if($navUnreadCount) <span class="nav-badge" style="box-shadow:0 0 0 2px #fff">{{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}</span> @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end notif-menu">
                    <span class="dropdown-header px-2">Notifications</span>
                    @forelse($navNotifications as $n)
                        <a href="{{ $n->link ?? '#' }}" class="dropdown-item notif-item py-2 {{ $n->status === 'unread' ? 'unread' : '' }}">
                            <span class="d-block">
                                <span class="d-block fw-semibold small text-dark">{{ $n->title }}</span>
                                <span class="d-block text-muted" style="font-size:12px">{{ Str::limit($n->message, 70) }}</span>
                                <span class="d-block text-muted" style="font-size:11px">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</span>
                            </span>
                        </a>
                    @empty
                        <div class="text-center text-muted small py-4">
                            <i class="far fa-bell-slash d-block mb-2 fs-4"></i>You're all caught up
                        </div>
                    @endforelse
                </div>
            </div>
            {{-- User --}}
            <div class="dropdown">
                <button class="btn btn-light btn-sm d-flex align-items-center gap-2 rounded-pill ps-1 pe-3" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ user_avatar_url(auth()->user()->avatar, auth()->user()->name) }}" class="rounded-circle" width="28" height="28" alt="">
                    <span class="d-none d-md-inline small">{{ Str::words(auth()->user()->name, 1, '') }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width:220px">
                    <li class="dropdown-user-head">
                        <div class="fw-semibold small text-dark">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size:12px">{{ auth()->user()->email }}</div>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('home') }}"><i class="fas fa-globe me-2 text-primary"></i>View Site</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf <button class="dropdown-item text-danger"><i class="fas fa-arrow-right-from-bracket me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <main class="p-3 p-md-4" id="main-content">
        @yield('content')
    </main>

    <footer class="admin-footer d-flex justify-content-between flex-wrap gap-2">
        <span>&copy; {{ date('Y') }} DriveEase Admin</span>
        <span>Signed in as {{ auth()->user()->email }}</span>
    </footer>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.1/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
// Mobile / tablet sidebar
(() => {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const toggle = document.getElementById('sidebarToggle');
    const setOpen = open => {
        sidebar.classList.toggle('show', open);
        backdrop.classList.toggle('show', open);
        toggle?.setAttribute('aria-expanded', open);
    };
    toggle?.addEventListener('click', () => setOpen(!sidebar.classList.contains('show')));
    backdrop.addEventListener('click', () => setOpen(false));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') setOpen(false); });
    sidebar.querySelector('.sidebar-link.active')?.scrollIntoView({ block: 'nearest' });
})();

// Chart.js defaults to match the design system
if (window.Chart) {
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = '#f1f5f9';
    Chart.defaults.plugins.tooltip.backgroundColor = '#0f172a';
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
}

// Auto-init DataTables
document.querySelectorAll('.datatable').forEach(el => {
    $(el).DataTable({ responsive: true, pageLength: 15, order: [] });
});

// Confirm delete helper
function confirmDelete(formId, msg) {
    Swal.fire({ title: 'Are you sure?', text: msg || 'This cannot be undone.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#64748b', confirmButtonText: 'Yes, delete'
    }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
}

// Flash messages
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
@if(session('success'))
Toast.fire({ icon: 'success', title: @js(session('success')) });
@endif
@if(session('error'))
Toast.fire({ icon: 'error', title: @js(session('error')), timer: 5000 });
@endif
</script>
@stack('scripts')
</body>
</html>
