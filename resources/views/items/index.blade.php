@extends('layouts.app')

@section('title', 'Inventory')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Inventory</h3>
    <div>
        <button class="btn btn-success" id="saleBtn">Record Sale</button>
        <button class="btn btn-primary" id="addBtn">+ Add Item</button>
    </div>
</div>

{{-- Low-stock alert --}}
@if ($lowStockCount > 0)
    <div class="alert alert-warning">
        {{ $lowStockCount }} item(s) are at or below minimum stock.
    </div>
@endif

{{-- Validation errors --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Search --}}
<form action="/items" method="GET" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="text" name="search" value="{{ $search }}"
               class="form-control" placeholder="Search by name or category">
    </div>
    <div class="col-auto">
        <button class="btn btn-secondary">Search</button>
        <a href="/items" class="btn btn-outline-secondary">Clear</a>
    </div>
</form>

{{-- Items table --}}
<div class="card">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Supplier</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Min Stock</th>
                    <th>Status</th>
                    <th width="230">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category }}</td>
                        <td>
                            @if ($item->supplier)
                                {{ $item->supplier->name }}
                                @if ($item->supplier->company)
                                    <div class="text-muted small">{{ $item->supplier->company }}</div>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->min_stock }}</td>
                        <td>
                            @if ($item->isLowStock())
                                <span class="badge bg-danger">Low Stock</span>
                            @else
                                <span class="badge bg-success">OK</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info stockBtn"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-qty="{{ $item->quantity }}"
                                    data-supplier="{{ $item->supplier_id }}">
                                Stock
                            </button>
                            <button class="btn btn-sm btn-warning editBtn"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-category="{{ $item->category }}"
                                    data-price="{{ $item->unit_price }}"
                                    data-min="{{ $item->min_stock }}"
                                    data-supplier="{{ $item->supplier_id }}">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger deleteBtn"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add / Edit popup (same form for both) --}}
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="itemForm" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" id="category" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-select">
                        <option value="">-- No supplier --</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">
                                {{ $supplier->name }}@if ($supplier->company) ({{ $supplier->company }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3" id="quantityBox">
                    <label class="form-label">Opening Quantity *</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" value="0" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Unit Price *</label>
                    <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Minimum Stock Level *</label>
                    <input type="number" name="min_stock" id="min_stock" class="form-control" value="5" min="0" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Stock In / Stock Out popup --}}
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="stockForm" action="/stock" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="item_id" id="stock_item_id">

            <div class="modal-header">
                <h5 class="modal-title">Stock In / Stock Out</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Item</label>
                    <input type="text" id="stock_item_name" class="form-control" readonly>
                    <div class="text-muted small mt-1">Current stock: <span id="stock_current"></span></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Type *</label>
                    <select name="type" id="stock_type" class="form-select" required>
                        <option value="in">Stock In (purchase / receipt)</option>
                        <option value="out">Stock Out (removal)</option>
                    </select>
                </div>
                <div class="mb-3" id="stockSupplierBox">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" id="stock_supplier" class="form-select">
                        <option value="">-- No supplier --</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">
                                {{ $supplier->name }}@if ($supplier->company) ({{ $supplier->company }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity *</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date *</label>
                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Record Sale popup --}}
<div class="modal fade" id="saleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="saleForm" action="/sales" method="POST" class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Record Sale</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3" style="max-width: 220px;">
                    <label class="form-label">Sale Date *</label>
                    <input type="date" name="sale_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th width="110">Quantity</th>
                            <th width="130">Selling Price</th>
                            <th width="110">Subtotal</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody id="saleRows"></tbody>
                </table>

                <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn">+ Add another item</button>

                <div class="text-end fs-5 mt-3">
                    Total: <strong id="saleTotal">0.00</strong>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Save Sale</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete confirmation popup --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="deleteForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')

            <div class="modal-header">
                <h5 class="modal-title">Delete Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to delete <strong id="deleteName"></strong>?
                <div class="text-muted small mt-2">
                    If this item has stock or sales history, it will be marked inactive instead.
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const itemModal   = new bootstrap.Modal(document.getElementById('itemModal'));
    const stockModal  = new bootstrap.Modal(document.getElementById('stockModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form        = document.getElementById('itemForm');
    const stockForm   = document.getElementById('stockForm');
    const quantityBox = document.getElementById('quantityBox');
    const quantity    = document.getElementById('quantity');
    const stockType   = document.getElementById('stock_type');
    const stockSupplierBox = document.getElementById('stockSupplierBox');

    // Add: open an empty form (quantity is shown)
    document.getElementById('addBtn').addEventListener('click', function () {
        form.reset();
        form.action = '/items';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').innerText = 'Add Item';
        quantityBox.style.display = 'block';
        quantity.disabled = false;
        itemModal.show();
    });

    // Edit: same form with existing data (quantity is hidden, supplier can be changed)
    document.querySelectorAll('.editBtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            form.action = '/items/' + btn.dataset.id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('modalTitle').innerText = 'Edit Item';
            document.getElementById('name').value        = btn.dataset.name;
            document.getElementById('category').value    = btn.dataset.category;
            document.getElementById('supplier_id').value = btn.dataset.supplier;
            document.getElementById('unit_price').value  = btn.dataset.price;
            document.getElementById('min_stock').value   = btn.dataset.min;
            quantityBox.style.display = 'none';
            quantity.disabled = true;
            itemModal.show();
        });
    });

    // Stock: open the stock popup for the clicked item
    document.querySelectorAll('.stockBtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            stockForm.reset();
            document.getElementById('stock_item_id').value   = btn.dataset.id;
            document.getElementById('stock_item_name').value = btn.dataset.name;
            document.getElementById('stock_current').innerText = btn.dataset.qty;
            document.getElementById('stock_supplier').value  = btn.dataset.supplier;
            stockSupplierBox.style.display = 'block';
            stockModal.show();
        });
    });

    // Supplier is only needed for Stock In
    stockType.addEventListener('change', function () {
        stockSupplierBox.style.display = (stockType.value === 'in') ? 'block' : 'none';
    });

    // Delete: ask for confirmation
    document.querySelectorAll('.deleteBtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('deleteForm').action = '/items/' + btn.dataset.id;
            document.getElementById('deleteName').innerText = btn.dataset.name;
            deleteModal.show();
        });
    });

    // ---------- Record Sale ----------
    const saleItems = @json($saleItems);   // [{id, name, quantity, unit_price}, ...]
    const saleModal = new bootstrap.Modal(document.getElementById('saleModal'));
    const saleRows  = document.getElementById('saleRows');
    let rowIndex = 0;

    function addSaleRow() {
        let options = '<option value="">-- Select item --</option>';
        saleItems.forEach(function (it) {
            options += '<option value="' + it.id + '" data-price="' + it.unit_price +
                       '" data-qty="' + it.quantity + '">' + it.name + ' (stock: ' + it.quantity + ')</option>';
        });

        const i = rowIndex++;
        const tr = document.createElement('tr');
        tr.innerHTML =
            '<td><select name="items[' + i + '][item_id]" class="form-select form-select-sm itemSelect" required>' + options + '</select></td>' +
            '<td><input type="number" name="items[' + i + '][quantity]" class="form-control form-control-sm qtyInput" min="1" value="1" required></td>' +
            '<td><input type="number" step="0.01" name="items[' + i + '][selling_price]" class="form-control form-control-sm priceInput" min="0" required></td>' +
            '<td class="subtotal">0.00</td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger removeRow">&times;</button></td>';
        saleRows.appendChild(tr);
    }

    function updateTotals() {
        let total = 0;
        saleRows.querySelectorAll('tr').forEach(function (tr) {
            const qty   = parseFloat(tr.querySelector('.qtyInput').value) || 0;
            const price = parseFloat(tr.querySelector('.priceInput').value) || 0;
            const sub   = qty * price;
            tr.querySelector('.subtotal').innerText = sub.toFixed(2);
            total += sub;
        });
        document.getElementById('saleTotal').innerText = total.toFixed(2);
    }

    // Open the popup with one empty row
    document.getElementById('saleBtn').addEventListener('click', function () {
        saleRows.innerHTML = '';
        rowIndex = 0;
        addSaleRow();
        updateTotals();
        saleModal.show();
    });

    document.getElementById('addRowBtn').addEventListener('click', function () {
        addSaleRow();
    });

    // Item chosen -> fill the default price and limit quantity to available stock
    saleRows.addEventListener('change', function (e) {
        if (e.target.classList.contains('itemSelect')) {
            const tr  = e.target.closest('tr');
            const opt = e.target.selectedOptions[0];
            tr.querySelector('.priceInput').value = opt.dataset.price || '';
            tr.querySelector('.qtyInput').max     = opt.dataset.qty || '';
            updateTotals();
        }
    });

    saleRows.addEventListener('input', updateTotals);

    saleRows.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            if (saleRows.querySelectorAll('tr').length > 1) {
                e.target.closest('tr').remove();
                updateTotals();
            }
        }
    });
</script>
@endpush