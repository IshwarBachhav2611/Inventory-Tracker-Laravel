<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width: 420px; margin-top: 80px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h4 class="mb-3 text-center">Inventory Tracker</h4>
            <h6 class="mb-3 text-center text-muted">Login</h6>

            <form action="/login" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary w-100">Login</button>
            </form>

            <p class="text-center mt-3 mb-0">
                No account? <a href="/register">Register</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>