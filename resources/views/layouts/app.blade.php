<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Inventory Tracker')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .navbar .nav-link.active { color: #fff; font-weight: 600; border-bottom: 2px solid #0d6efd; }
        .card { border: 0; box-shadow: 0 2px 8px rgba(0,0,0,.06); border-radius: 10px; }
        .card-header { background: #fff; border-bottom: 1px solid #eef0f3; border-radius: 10px 10px 0 0 !important; }
        .table thead.table-dark th { font-weight: 500; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="/dashboard">
            <i class="bi bi-box-seam"></i> Inventory Tracker
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <div class="navbar-nav me-auto">
                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="/dashboard">Dashboard</a>
                <a class="nav-link {{ request()->is('items*') ? 'active' : '' }}" href="/items">Inventory</a>
                <a class="nav-link {{ request()->is('suppliers*') ? 'active' : '' }}" href="/suppliers">Suppliers</a>
                <a class="nav-link {{ request()->is('reports*') ? 'active' : '' }}" href="/reports">Reports</a>
            </div>

            {{-- User menu --}}
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none"
                   data-bs-toggle="dropdown" aria-expanded="false" title="{{ auth()->user()->name }}">
                    <i class="bi bi-person-circle fs-3"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li class="px-3 py-2">
                        <div class="fw-semibold">{{ auth()->user()->name }}</div>
                        <div class="text-muted small">{{ auth()->user()->email }}</div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="/profile">
                            <i class="bi bi-person"></i> Profile
                        </a>
                    </li>
                    <li>
                        <form action="/logout" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>