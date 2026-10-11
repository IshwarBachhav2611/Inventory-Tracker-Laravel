@extends('layouts.app')

@section('title', 'Profile')

@section('content')

<h3 class="mb-3">My Profile</h3>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body py-4">
                <i class="bi bi-person-circle text-primary" style="font-size: 4.5rem;"></i>
                <h5 class="mt-2 mb-0">{{ auth()->user()->name }}</h5>
                <div class="text-muted">{{ auth()->user()->email }}</div>
                <div class="text-muted small mt-2">
                    Member since {{ auth()->user()->created_at->format('d M Y') }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header fw-bold">Edit Details</div>
            <div class="card-body">
                <form action="/profile" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', auth()->user()->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', auth()->user()->email) }}" required>
                    </div>

                    <hr>
                    <div class="text-muted small mb-2">Leave the password fields empty to keep your current password.</div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <button class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection