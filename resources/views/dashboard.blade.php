@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
    .hero {
        background: linear-gradient(120deg, #0d6efd 0%, #6610f2 100%);
        color: #fff;
        border-radius: 14px;
        padding: 2rem;
    }
    .hero .btn-light { font-weight: 500; }
    .stat-card { transition: transform .15s ease, box-shadow .15s ease; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,.1); }
    .stat-icon {
        width: 52px; height: 52px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }
</style>

{{-- Hero section --}}
<div class="hero mb-4">
    <div class="row align-items-center g-3">
        <div class="col-lg-7">
            <div class="opacity-75" id="todayDate"></div>
            <h2 class="fw-bold mb-1"><span id="greeting">Welcome</span>, {{ auth()->user()->name }}</h2>
            <p class="mb-0 opacity-75">Here is what is happening with your inventory today.</p>
        </div>
        <div class="col-lg-5 text-lg-end">
            <a href="/items" class="btn btn-light me-1 mb-1"><i class="bi bi-cart-check"></i> Record Sale</a>
            <a href="/items" class="btn btn-outline-light me-1 mb-1"><i class="bi bi-plus-lg"></i> Add Item</a>
            <a href="/reports" class="btn btn-outline-light mb-1"><i class="bi bi-bar-chart"></i> Reports</a>
        </div>
    </div>
</div>

{{-- Low-stock alert --}}
@if ($lowStockCount > 0)
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
        <div><i class="bi bi-exclamation-triangle-fill"></i>
            {{ $lowStockCount }} item(s) are at or below minimum stock.
        </div>
        <a href="/reports" class="btn btn-sm btn-warning">View</a>
    </div>
@endif

{{-- Summary cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <a href="/items" class="text-decoration-none text-dark">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-box-seam"></i></div>
                    <div>
                        <div class="text-muted small">Total Items</div>
                        <div class="fs-3 fw-bold">{{ $totalItems }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-lg-3">
        <a href="/suppliers" class="text-decoration-none text-dark">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-truck"></i></div>
                    <div>
                        <div class="text-muted small">Suppliers</div>
                        <div class="fs-3 fw-bold">{{ $totalSuppliers }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-lg-3">
        <a href="/reports" class="text-decoration-none text-dark">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon {{ $lowStockCount > 0 ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary' }}">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Low Stock Items</div>
                        <div class="fs-3 fw-bold">{{ $lowStockCount }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-lg-3">
        <a href="/reports" class="text-decoration-none text-dark">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-cash-stack"></i></div>
                    <div>
                        <div class="text-muted small">Sales This Month</div>
                        <div class="fs-3 fw-bold">{{ number_format($monthlySales, 2) }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-3">

    {{-- Recent stock movements --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header fw-bold"><i class="bi bi-arrow-left-right"></i> Recent Stock Movements</div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Supplier</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTransactions as $t)
                            <tr>
                                <td>{{ $t->date }}</td>
                                <td>{{ $t->item->name }}</td>
                                <td>
                                    @if ($t->type === 'in')
                                        <span class="badge bg-success">In</span>
                                    @else
                                        <span class="badge bg-danger">Out</span>
                                    @endif
                                </td>
                                <td>{{ $t->quantity }}</td>
                                <td>{{ $t->supplier->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No stock movements yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent sales --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header fw-bold"><i class="bi bi-receipt"></i> Recent Sales</div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Items</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentSales as $sale)
                            <tr>
                                <td>{{ $sale->sale_date }}</td>
                                <td>
                                    @foreach ($sale->items as $si)
                                        {{ $si->item->name }} x{{ $si->quantity }}@if (!$loop->last), @endif
                                    @endforeach
                                </td>
                                <td class="text-end fw-semibold">{{ number_format($sale->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No sales yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    // Greeting and date use the visitor's own clock, so they are always correct locally
    const now  = new Date();
    const hour = now.getHours();
    document.getElementById('greeting').innerText =
        hour < 12 ? 'Good morning' : (hour < 17 ? 'Good afternoon' : 'Good evening');
    document.getElementById('todayDate').innerText =
        now.toLocaleDateString(undefined, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
</script>
@endpush