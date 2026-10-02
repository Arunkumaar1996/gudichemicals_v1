<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Gudi Chemicals ERP</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #003e6b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #003e6b 0%, #005a9c 100%);
            color: #ffffff;
            padding: 2.25rem 2rem 2rem 2rem;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="login-card mx-auto">
        <div class="login-header">
            <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                <i class="fa-solid fa-flask-vial fa-2x text-warning"></i>
            </div>
            <h4 class="fw-bold mb-1 tracking-wide">GUDI CHEMICALS</h4>
            <p class="mb-0 text-white-50 small">Chemical Manufacturing, Inventory & GST ERP</p>
        </div>

        <div class="p-4 p-md-4">
            @if(session('error'))
                <div class="alert alert-danger py-2 px-3 small mb-3">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success py-2 px-3 small mb-3">
                    <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold text-secondary">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-regular fa-envelope"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', 'admin@gudichemicals.com') }}" required autofocus placeholder="admin@gudichemicals.com">
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="Admin@12345" required placeholder="Enter password">
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" checked>
                        <label class="form-check-label small text-muted" for="remember">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm" style="background-color: #005a9c; border-color: #005a9c;">
                    <i class="fa-solid fa-right-to-bracket me-1"></i> Sign In to ERP
                </button>
            </form>

            <div class="mt-4 pt-3 border-top text-center">
                <small class="text-muted d-block mb-2">Demo Credentials Quick-Fill:</small>
                <div class="btn-group btn-group-sm w-100">
                    <button type="button" class="btn btn-outline-secondary" onclick="fillCreds('admin@gudichemicals.com', 'Admin@12345')">Admin</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="fillCreds('cashier@gudichemicals.com', 'Cashier@12345')">Cashier</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function fillCreds(email, pass) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = pass;
    }
</script>
</body>
</html>
