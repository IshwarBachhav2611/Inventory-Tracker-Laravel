@extends('layouts.app')

@section('title', 'Reports')

@section('content')

<style>
    .chart-box { position: relative; height: 260px; }
</style>

<h3 class="mb-3">Reports</h3>

{{-- Validation errors --}}
@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

{{-- Date filter --}}
<form action="/reports" method="GET" class="row g-2 align-items-end mb-4">
    <div class="col-auto">
        <label class="form-label mb-1">From</label>
        <input type="date" name="from" value="{{ $from }}" class="form-control">
    </div>
    <div class="col-auto">
        <label class="form-label mb-1">To</label>
        <input type="date" name="to" value="{{ $to }}" class="form-control">
    </div>
    <div class="col-auto">
        <button class="btn btn-primary">Show Report</button>
        <a href="/reports" class="btn btn-outline-secondary">This Month</a>
    </div>
</form>

<p class="text-muted">Showing sales and stock movement from <strong>{{ $from }}</strong> to <strong>{{ $to }}</strong>.</p>

{{-- ================= SUMMARY ================= --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body">
                <div>Total Sales</div>
                <div class="fs-3 fw-bold">{{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body">
                <div>Number of Sales</div>
                <div class="fs-3 fw-bold">{{ $salesCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-bg-success">
            <div class="card-body">
                <div>Stock In (units)</div>
                <div class="fs-3 fw-bold">{{ $stockIn }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-bg-danger">
            <div class="card-body">
                <div>Stock Out (units)</div>
                <div class="fs-3 fw-bold">{{ $stockOut }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ================= CHARTS ================= --}}
<div class="row g-3 mb-5">

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-bold">Sales by Item</div>
            <div class="card-body">
                @if (count($itemValues) > 0)
                    <div class="chart-box"><canvas id="itemChart"></canvas></div>
                @else
                    <p class="text-muted text-center my-5">No sales in this period.</p>
                @endif
            </div>
            <div class="card-footer text-muted small">Taller bar = more money earned from that item.</div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-bold">Stock In vs Stock Out</div>
            <div class="card-body">
                @if ($stockIn + $stockOut > 0)
                    <div class="chart-box"><canvas id="stockChart"></canvas></div>
                @else
                    <p class="text-muted text-center my-5">No stock movements in this period.</p>
                @endif
            </div>
            <div class="card-footer text-muted small">Green = units added. Red = units removed.</div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-bold">Stock Value by Category</div>
            <div class="card-body">
                @if (count($categoryValues) > 0)
                    <div class="chart-box"><canvas id="categoryChart"></canvas></div>
                @else
                    <p class="text-muted text-center my-5">No items yet.</p>
                @endif
            </div>
            <div class="card-footer text-muted small">Bigger slice = more money tied up in that category.</div>
        </div>
    </div>

</div>

{{-- ================= SALES DETAILS ================= --}}
<h5>Sales Details</h5>

<div class="card mb-5">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Sale #</th>
                    <th>Item</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    @foreach ($sale->items as $si)
                        <tr>
                            <td>{{ $sale->sale_date }}</td>
                            <td>#{{ $sale->id }}</td>
                            <td>{{ $si->item->name }}</td>
                            <td class="text-end">{{ $si->quantity }}</td>
                            <td class="text-end">{{ number_format($si->selling_price, 2) }}</td>
                            <td class="text-end">{{ number_format($si->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No sales in this period.</td>
                    </tr>
                @endforelse
            </tbody>
            @if ($sales->count() > 0)
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="5" class="text-end">Total</td>
                        <td class="text-end">{{ number_format($totalRevenue, 2) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- ================= CURRENT INVENTORY ================= --}}
<h5>Current Inventory and Stock Valuation</h5>

<div class="card mb-5">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th class="text-end">Quantity</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category ?: '-' }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No items.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="4" class="text-end">Total Stock Value</td>
                    <td class="text-end">{{ number_format($totalValue, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- ================= LOW STOCK ================= --}}
<h5>Low-Stock Items</h5>

<div class="card mb-5">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Item</th>
                    <th>Supplier</th>
                    <th class="text-end">Quantity</th>
                    <th class="text-end">Min Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lowStockItems as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->supplier->name ?? '-' }}</td>
                        <td class="text-end text-danger fw-bold">{{ $item->quantity }}</td>
                        <td class="text-end">{{ $item->min_stock }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">All items are above minimum stock.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ================= STOCK MOVEMENT DETAILS ================= --}}
<h5>Stock Movement Details</h5>

<div class="card mb-5">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Type</th>
                    <th class="text-end">Qty</th>
                    <th>Supplier</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $t)
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
                        <td class="text-end">{{ $t->quantity }}</td>
                        <td>{{ $t->supplier->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No stock movements in this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    const colors = ['#0d6efd', '#20c997', '#f59f00', '#6f42c1', '#dc3545', '#0dcaf0', '#fd7e14', '#6c757d'];

    // Sales by item (simple bar chart)
    const itemCanvas = document.getElementById('itemChart');
    if (itemCanvas) {
        new Chart(itemCanvas, {
            type: 'bar',
            data: {
                labels: @json($itemLabels),
                datasets: [{
                    label: 'Sales',
                    data: @json($itemValues),
                    backgroundColor: colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Stock in vs stock out (pie with two slices)
    const stockCanvas = document.getElementById('stockChart');
    if (stockCanvas) {
        new Chart(stockCanvas, {
            type: 'pie',
            data: {
                labels: ['Stock In', 'Stock Out'],
                datasets: [{
                    data: [{{ $stockIn }}, {{ $stockOut }}],
                    backgroundColor: ['#198754', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Stock value by category (doughnut)
    const categoryCanvas = document.getElementById('categoryChart');
    if (categoryCanvas) {
        new Chart(categoryCanvas, {
            type: 'doughnut',
            data: {
                labels: @json($categoryLabels),
                datasets: [{
                    data: @json($categoryValues),
                    backgroundColor: colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
</script>
@endpush