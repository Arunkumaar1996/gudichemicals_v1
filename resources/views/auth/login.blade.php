<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Workstation Sign In — Gudi Chemicals ERP</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --gudi-primary: #005a9c;
            --gudi-primary-dark: #003e6b;
            --gudi-primary-light: #e0f2fe;
            --gudi-secondary: #0d9488;
            --gudi-accent: #f59e0b;
            --gudi-card-bg: rgba(255, 255, 255, 0.95);
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
            background: linear-gradient(135deg, #060d1a 0%, #0b172a 40%, #002b4d 100%);
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Ambient Geometric Chemical Matrix Background */
        .ambient-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 0;
            background-image: 
                radial-gradient(rgba(0, 163, 255, 0.12) 1.5px, transparent 1.5px),
                radial-gradient(rgba(13, 148, 136, 0.08) 1.5px, transparent 1.5px);
            background-size: 36px 36px;
            background-position: 0 0, 18px 18px;
            opacity: 0.9;
        }

        /* Glowing Ambient Spheres */
        .ambient-orb-1 {
            position: fixed;
            top: -15%;
            right: 15%;
            width: 520px;
            height: 520px;
            background: radial-gradient(circle, rgba(0, 90, 156, 0.35) 0%, rgba(0, 90, 156, 0) 70%);
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
            z-index: 0;
            animation: orbFloat 10s ease-in-out infinite alternate;
        }

        .ambient-orb-2 {
            position: fixed;
            bottom: -15%;
            left: 10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(13, 148, 136, 0.28) 0%, rgba(13, 148, 136, 0) 70%);
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
            z-index: 0;
            animation: orbFloat 12s ease-in-out infinite alternate-reverse;
        }

        .ambient-orb-3 {
            position: fixed;
            top: 40%;
            left: -10%;
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.15) 0%, rgba(245, 158, 11, 0) 70%);
            border-radius: 50%;
            filter: blur(70px);
            pointer-events: none;
            z-index: 0;
            animation: orbFloat 14s ease-in-out infinite alternate;
        }

        /* Container Layout */
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 460px;
            padding: 1.5rem 1rem;
            margin: auto;
        }

        /* Main Login Card */
        .login-glass-card {
            background: var(--gudi-card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 22px;
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.55),
                        0 18px 36px -18px rgba(0, 0, 0, 0.4),
                        inset 0 1px 0 rgba(255, 255, 255, 0.8);
            overflow: hidden;
            animation: cardEntrance 0.75s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Card Header with Brand Aesthetics */
        .login-card-header {
            padding: 2.25rem 2rem 1.5rem 2rem;
            text-align: center;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
            position: relative;
        }

        /* 3D-Styled Animated Brand Logo */
        .brand-logo-container {
            position: relative;
            width: 82px;
            height: 82px;
            margin: 0 auto 1.15rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo-box {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #005a9c 0%, #003e6b 45%, #0d9488 100%);
            color: #ffffff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.1rem;
            box-shadow: 0 12px 24px -6px rgba(0, 90, 156, 0.45),
                        inset 0 1px 1px rgba(255, 255, 255, 0.6);
            border: 2px solid rgba(255, 255, 255, 0.4);
            position: relative;
            z-index: 2;
            animation: logoBob 4s ease-in-out infinite;
        }

        .brand-logo-box i {
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.25));
        }

        .brand-flask-bubble {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            font-size: 0.68rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
            animation: pulseWarning 2s infinite;
            z-index: 3;
        }

        .brand-halo-ring {
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border-radius: 26px;
            border: 2px dashed rgba(0, 90, 156, 0.35);
            animation: rotateHalo 22s linear infinite;
            z-index: 1;
        }

        .brand-title {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            color: #0b1324;
            margin-bottom: 0.25rem;
        }

        .brand-subtitle {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: #0d9488;
            margin: 0;
        }

        /* Card Body */
        .login-card-body {
            padding: 2rem;
        }

        /* Custom Input Groups */
        .form-label {
            font-size: 0.80rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.45rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: stretch;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #ffffff;
            transition: all 0.2s ease-in-out;
            overflow: hidden;
        }

        .input-group-custom:focus-within {
            border-color: #005a9c;
            box-shadow: 0 0 0 3.5px rgba(0, 90, 156, 0.16);
            background: #ffffff;
        }

        .input-group-custom.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
        }

        .input-group-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            color: #64748b;
            font-size: 0.95rem;
            background: #f8fafc;
            border-right: 1px solid #e2e8f0;
            transition: color 0.2s ease;
        }

        .input-group-custom:focus-within .input-group-icon {
            color: #005a9c;
            background: #f0f7ff;
        }

        .form-control-custom {
            border: none;
            padding: 0.68rem 0.85rem;
            font-size: 0.88rem;
            font-weight: 500;
            color: #0f172a;
            width: 100%;
            outline: none;
            background: transparent;
        }

        .form-control-custom::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        /* Toggle Password View Eye Button */
        .btn-toggle-password {
            background: transparent;
            border: none;
            color: #64748b;
            padding: 0 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            outline: none;
        }

        .btn-toggle-password:hover {
            color: #005a9c;
            background: #f8fafc;
        }

        /* Checkbox */
        .form-check-input {
            width: 1.1em;
            height: 1.1em;
            border-color: #cbd5e1;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #005a9c;
            border-color: #005a9c;
        }

        .form-check-label {
            font-size: 0.80rem;
            color: #475569;
            cursor: pointer;
            user-select: none;
            font-weight: 500;
        }

        /* Submit Button */
        .btn-signin-primary {
            width: 100%;
            padding: 0.78rem 1.25rem;
            font-size: 0.90rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            color: #ffffff;
            background: linear-gradient(135deg, #005a9c 0%, #003e6b 50%, #0d9488 100%);
            border: none;
            border-radius: 10px;
            box-shadow: 0 6px 18px -2px rgba(0, 90, 156, 0.42);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-signin-primary:hover {
            background: linear-gradient(135deg, #006ebf 0%, #004c85 50%, #0f766e 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 24px -2px rgba(0, 90, 156, 0.52);
            color: #ffffff;
        }

        .btn-signin-primary:active {
            transform: translateY(0);
        }

        /* Security Trust Footer within card */
        .login-trust-footer {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.72rem;
            color: #64748b;
            text-align: center;
        }

        /* Page Outside Footer */
        .login-outer-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.74rem;
            color: #94a3b8;
        }

        .login-outer-footer a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 600;
        }

        .login-outer-footer a:hover {
            text-decoration: underline;
        }

        /* Keyframe Animations */
        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(28px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes logoBob {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        @keyframes rotateHalo {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes orbFloat {
            from { transform: translateY(0) scale(1); }
            to { transform: translateY(30px) scale(1.08); }
        }

        @keyframes pulseWarning {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        @media (max-width: 576px) {
            .login-card-header {
                padding: 1.75rem 1.25rem 1.25rem 1.25rem;
            }
            .login-card-body {
                padding: 1.5rem 1.25rem;
            }
            .brand-logo-container {
                width: 72px;
                height: 72px;
            }
            .brand-logo-box {
                width: 62px;
                height: 62px;
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="ambient-grid"></div>
    <div class="ambient-orb-1"></div>
    <div class="ambient-orb-2"></div>
    <div class="ambient-orb-3"></div>

    <div class="login-wrapper">
        <div class="login-glass-card">
            <!-- Header with Attractive Animated Logo -->
            <div class="login-card-header">
                <div class="brand-logo-container">
                    <div class="brand-halo-ring"></div>
                    <div class="brand-logo-box">
                        <i class="fa-solid fa-flask-vial"></i>
                        <div class="brand-flask-bubble" title="Industrial System Active">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                    </div>
                </div>
                <h3 class="brand-title">GUDI CHEMICALS</h3>
                <p class="brand-subtitle">
                    <i class="fa-solid fa-industry me-1"></i> Chemical Manufacturing &bull; POS &bull; GST ERP
                </p>
            </div>

            <!-- Card Body / Sign In Form -->
            <div class="login-card-body">
                @if(session('error'))
                    <div class="alert alert-danger py-2 px-3 small mb-3 d-flex align-items-center gap-2 border-0 shadow-sm" style="background-color: #fef2f2; color: #991b1b; border-radius: 10px;">
                        <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success py-2 px-3 small mb-3 d-flex align-items-center gap-2 border-0 shadow-sm" style="background-color: #f0fdf4; color: #166534; border-radius: 10px;">
                        <i class="fa-solid fa-circle-check text-success fs-5"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" autocomplete="on">
                    @csrf

                    <!-- Workstation Email Input -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <span><i class="fa-solid fa-user-shield text-primary me-1"></i> Workstation Email</span>
                        </label>
                        <div class="input-group-custom @error('email') is-invalid @enderror">
                            <span class="input-group-icon">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" 
                                   class="form-control-custom" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   placeholder="staff@gudichemicals.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1 fw-semibold">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password Input with Show/Hide Eye Toggle -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <span><i class="fa-solid fa-lock text-primary me-1"></i> Security Password</span>
                        </label>
                        <div class="input-group-custom @error('password') is-invalid @enderror">
                            <span class="input-group-icon">
                                <i class="fa-solid fa-key"></i>
                            </span>
                            <input type="password" 
                                   class="form-control-custom" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   placeholder="Enter workstation password">
                            <button type="button" 
                                    class="btn-toggle-password" 
                                    id="togglePasswordBtn" 
                                    onclick="togglePasswordVisibility()" 
                                    title="Show/Hide Password" 
                                    aria-label="Toggle password visibility">
                                <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1 fw-semibold">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Workstation Session -->
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember" checked>
                            <label class="form-check-label" for="remember">
                                Keep workstation session active
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-signin-primary">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span>Authenticate &amp; Launch ERP</span>
                    </button>
                </form>

                <!-- Security Assurance Footer (Authorized Only) -->
                <div class="login-trust-footer">
                    <i class="fa-solid fa-shield-halved text-success fs-6"></i>
                    <span>256-Bit SSL Encrypted &bull; Audit Trail Logged &bull; GST Ready</span>
                </div>
            </div>
        </div>

        <!-- Outer Footer -->
        <div class="login-outer-footer">
            <div>&copy; {{ date('Y') }} <strong>Gudi Chemicals</strong>. All rights reserved.</div>
            <div class="mt-1">
                Authorized Personnel Only &bull; Need help? Contact <a href="mailto:admin@gudichemicals.com">admin@gudichemicals.com</a>
            </div>
        </div>
    </div>

    <!-- Interactive Show/Hide Password Script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            
            if (!passwordInput || !toggleIcon) return;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                toggleIcon.setAttribute('title', 'Hide Password');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                toggleIcon.setAttribute('title', 'Show Password');
            }
        }
    </script>
</body>
</html>
