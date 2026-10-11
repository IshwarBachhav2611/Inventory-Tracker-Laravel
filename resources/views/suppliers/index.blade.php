@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Suppliers</h3>
    <button class="btn btn-primary" id="addBtn">+ Add Supplier</button>
</div>

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

{{-- Suppliers table --}}
<div class="card">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Company / Organization</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->company }}</td>
                        <td>{{ $supplier->phone }}</td>
                        <td>{{ $supplier->email }}</td>
                        <td>{{ $supplier->address }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning editBtn"
                                    data-id="{{ $supplier->id }}"
                                    data-name="{{ $supplier->name }}"
                                    data-company="{{ $supplier->company }}"
                                    data-phone="{{ $supplier->phone }}"
                                    data-email="{{ $supplier->email }}"
                                    data-address="{{ $supplier->address }}">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger deleteBtn"
                                    data-id="{{ $supplier->id }}"
                                    data-name="{{ $supplier->name }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No suppliers yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add / Edit popup (same form for both) --}}
<div class="modal fade" id="supplierModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="supplierForm" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Company / Organization Name</label>
                    <input type="text" name="company" id="company" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
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
                <h5 class="modal-title">Delete Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to delete <strong id="deleteName"></strong>?
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
    const supplierModal = new bootstrap.Modal(document.getElementById('supplierModal'));
    const deleteModal   = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form          = document.getElementById('supplierForm');

    // Add: open an empty form
    document.getElementById('addBtn').addEventListener('click', function () {
        form.reset();
        form.action = '/suppliers';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').innerText = 'Add Supplier';
        supplierModal.show();
    });

    // Edit: open the same form with existing data
    document.querySelectorAll('.editBtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            form.action = '/suppliers/' + btn.dataset.id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('modalTitle').innerText = 'Edit Supplier';
            document.getElementById('name').value    = btn.dataset.name;
            document.getElementById('company').value = btn.dataset.company;
            document.getElementById('phone').value   = btn.dataset.phone;
            document.getElementById('email').value   = btn.dataset.email;
            document.getElementById('address').value = btn.dataset.address;
            supplierModal.show();
        });
    });

    // Delete: ask for confirmation
    document.querySelectorAll('.deleteBtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('deleteForm').action = '/suppliers/' + btn.dataset.id;
            document.getElementById('deleteName').innerText = btn.dataset.name;
            deleteModal.show();
        });
    });
</script>
@endpush