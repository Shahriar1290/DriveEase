<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🚗 @yield('title', 'Admin') — DriveEase</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; --sidebar-bg: #1a1f2e; --sidebar-hover: #2a3142; --primary: #0ea5e9; }
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; }
        .sidebar {
            width: var(--sidebar-width); background: var(--sidebar-bg);
            height: 100vh; position: fixed; left: 0; top: 0; z-index: 1040;
            overflow-y: auto; transition: all .3s; scrollbar-width: thin; scrollbar-color: #374151 transparent;
        }
        .sidebar-logo { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,.08); }
        .sidebar-logo a { color: #fff; text-decoration: none; font-size: 1.25rem; font-weight: 700; }
        .sidebar-section { padding: .5rem 1rem .25rem; font-size: .65rem; font-weight: 600;
            color: rgba(255,255,255,.3); letter-spacing: .08em; text-transform: uppercase; }
        .sidebar-link {
            display: flex; align-items: center; gap: .75rem; padding: .6rem 1.25rem;
            color: rgba(255,255,255,.65); text-decoration: none; font-size: .875rem;
            border-radius: .375rem; margin: .1rem .75rem; transition: all .2s;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: var(--sidebar-hover); color: #fff;
        }
        .sidebar-link.active { background: var(--primary); color: #fff; }
        .sidebar-link i { width: 18px; text-align: center; }
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .top-navbar { background: #fff; border-bottom: 1px solid #e5e7eb; padding: .75rem 1.5rem;
            position: sticky; top: 0; z-index: 1030; }
        .stat-card { border: none; border-radius: .75rem; overflow: hidden; transition: transform .2s; }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-icon { width: 52px; height: 52px; display: flex; align-items: center; justify-content: center;
            border-radius: .5rem; font-size: 1.25rem; }
        .table-card { background: #fff; border-radius: .75rem; padding: 1.5rem; border: none; box-shadow: 0 1px 3px rgba(0,0,0,.07); }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<nav class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="{{ route('admin.dashboard') }}">
            <i class="fas fa-car-side text-primary me-2"></i>
            <span class="text-primary">Drive</span>Ease Admin
        </a>
    </div>

    <div class="py-2">
        <div class="sidebar-section">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
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
            @if($sidebarPendingBookings) <span class="badge bg-warning ms-auto">{{ $sidebarPendingBookings }}</span> @endif
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
            @if($sidebarPendingReviews) <span class="badge bg-warning ms-auto">{{ $sidebarPendingReviews }}</span> @endif
        </a>

        <div class="sidebar-section">Analytics</div>
        <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Reports
        </a>

        <div class="sidebar-section mt-2">Account</div>
        <a href="{{ route('home') }}" class="sidebar-link">
            <i class="fas fa-globe"></i> View Site
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-100 text-start border-0 bg-transparent">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</nav>

{{-- Main Content --}}
<div class="main-content">
    {{-- Top Navbar --}}
    <div class="top-navbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            {{-- Notifications --}}
            <div class="dropdown">
                <button class="btn btn-light btn-sm position-relative" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    @if($navUnreadCount) <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size:9px">{{ $navUnreadCount }}</span> @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow" style="width:300px">
                    <h6 class="dropdown-header">Notifications</h6>
                    @foreach($navNotifications as $n)
                        <a href="{{ $n->link ?? '#' }}" class="dropdown-item py-2 border-bottom small {{ $n->status==='unread'?'bg-light':'' }}">
                            <div class="fw-semibold">{{ $n->title }}</div>
                            <div class="text-muted">{{ Str::limit($n->message, 50) }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
            {{-- User --}}
            <div class="dropdown">
                <button class="btn btn-light btn-sm d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <img src="{{ user_avatar_url(auth()->user()->avatar, auth()->user()->name) }}" class="rounded-circle" width="24" height="24">
                    <span class="d-none d-md-inline small">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="{{ route('home') }}"><i class="fas fa-globe me-2"></i>View Site</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf <button class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3 mb-0" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <main class="p-4">
        @yield('content')
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.1/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('show');
});
// Auto-init DataTables
document.querySelectorAll('.datatable').forEach(el => {
    $(el).DataTable({ responsive: true, pageLength: 15, order: [] });
});
// Confirm delete helper
function confirmDelete(formId, msg) {
    Swal.fire({ title:'Are you sure?', text: msg || 'This cannot be undone.', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#d33', confirmButtonText:'Yes, delete!'
    }).then(r => { if(r.isConfirmed) document.getElementById(formId).submit(); });
}
</script>
@stack('scripts')
</body>
</html>
