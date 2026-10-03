<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — Gudi Chemicals ERP</title>

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
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
            background-color: #ffffff;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }

        /* Full Screen Split Container */
        .auth-split-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
            overflow-x: hidden;
        }

        /* -------------------------------------------------------------
         * LEFT PANEL: Enterprise Hero & Visual Showcase
         * ------------------------------------------------------------- */
        .auth-showcase-panel {
            flex: 1.15;
            background: linear-gradient(145deg, #061124 0%, #0b1a36 45%, #003660 100%);
            color: #ffffff;
            padding: 3.5rem 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Ambient Glowing Geometric Matrix */
        .showcase-grid-matrix {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(rgba(56, 189, 248, 0.15) 1.5px, transparent 1.5px),
                radial-gradient(rgba(13, 148, 136, 0.1) 1.5px, transparent 1.5px);
            background-size: 32px 32px;
            background-position: 0 0, 16px 16px;
            opacity: 0.65;
            pointer-events: none;
            z-index: 1;
        }

        .showcase-orb-1 {
            position: absolute;
            top: -10%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 90, 156, 0.45) 0%, rgba(0, 90, 156, 0) 70%);
            border-radius: 50%;
            filter: blur(70px);
            pointer-events: none;
            z-index: 1;
            animation: orbFloat 10s ease-in-out infinite alternate;
        }

        .showcase-orb-2 {
            position: absolute;
            bottom: -15%;
            left: -10%;
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, rgba(13, 148, 136, 0.35) 0%, rgba(13, 148, 136, 0) 70%);
            border-radius: 50%;
            filter: blur(70px);
            pointer-events: none;
            z-index: 1;
            animation: orbFloat 12s ease-in-out infinite alternate-reverse;
        }

        .showcase-content {
            position: relative;
            z-index: 2;
        }

        /* Top Brand Badge in Showcase */
        .showcase-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .brand-emblem-box {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #0284c7 0%, #005a9c 60%, #0d9488 100%);
            color: #ffffff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.45rem;
            box-shadow: 0 8px 20px rgba(0, 90, 156, 0.35),
                        inset 0 1px 1px rgba(255, 255, 255, 0.6);
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            position: relative;
            animation: logoBob 4s ease-in-out infinite;
        }

        .brand-emblem-sparkle {
            position: absolute;
            top: -3px;
            right: -3px;
            width: 16px;
            height: 16px;
            background: #f59e0b;
            color: #ffffff;
            border-radius: 50%;
            font-size: 0.55rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1.5px solid #061124;
            animation: pulseGlow 2s infinite;
        }

        .brand-text-block h5 {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            margin: 0;
            color: #ffffff;
        }

        .brand-text-block small {
            font-size: 0.70rem;
            color: #38bdf8;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-weight: 700;
        }

        /* Showcase Headline & Subtitle */
        .showcase-headline {
            font-size: 2.15rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin-bottom: 1rem;
        }

        .showcase-headline span.gradient-text {
            background: linear-gradient(135deg, #38bdf8 0%, #2dd4bf 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .showcase-subtitle {
            font-size: 0.92rem;
            color: #94a3b8;
            line-height: 1.6;
            max-width: 480px;
            margin-bottom: 2rem;
        }

        /* Showcase Feature Cards */
        .showcase-features-stack {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            max-width: 500px;
            margin-bottom: 2.5rem;
        }

        .feature-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.9rem 1.15rem;
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            transition: all 0.25s ease;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(56, 189, 248, 0.3);
            transform: translateX(4px);
        }

        .feature-icon-pill {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .feature-title {
            font-size: 0.86rem;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 0.15rem;
        }

        .feature-desc {
            font-size: 0.74rem;
            color: #94a3b8;
            margin: 0;
            line-height: 1.35;
        }

        /* Showcase Bottom Footer */
        .showcase-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.74rem;
            color: #64748b;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .compliance-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3rem 0.65rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            font-size: 0.70rem;
            color: #cbd5e1;
            font-weight: 600;
        }

        /* -------------------------------------------------------------
         * RIGHT PANEL: Interactive Sign In Form
         * ------------------------------------------------------------- */
        .auth-form-panel {
            flex: 0.85;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
            position: relative;
        }

        .auth-form-container {
            width: 100%;
            max-width: 420px;
            animation: cardEntrance 0.65s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Mobile Brand Header */
        .mobile-brand-header {
            display: none;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Form Top Section */
        .form-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 0.25rem 0.65rem;
            border-radius: 50rem;
            font-size: 0.72rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .form-header-badge .live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #22c55e;
            box-shadow: 0 0 6px #22c55e;
            display: inline-block;
            animation: blinkDot 1.8s infinite;
        }

        .form-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0b1324;
            letter-spacing: -0.02em;
            margin-bottom: 0.4rem;
        }

        .form-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 1.85rem;
            line-height: 1.5;
        }

        /* Custom Form Inputs */
        .form-label-custom {
            font-size: 0.80rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.45rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            background: #ffffff;
            transition: all 0.2s ease-in-out;
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .input-wrapper:focus-within {
            border-color: #005a9c;
            box-shadow: 0 0 0 3.5px rgba(0, 90, 156, 0.15);
        }

        .input-wrapper.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12);
        }

        .input-prefix-icon {
            width: 44px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 0.95rem;
            background: #f8fafc;
            border-right: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .input-wrapper:focus-within .input-prefix-icon {
            color: #005a9c;
            background: #f0f7ff;
        }

        .input-field {
            flex: 1;
            border: none;
            outline: none;
            padding: 0.72rem 0.85rem;
            font-size: 0.88rem;
            font-weight: 500;
            color: #0f172a;
            background: transparent;
            font-family: inherit;
        }

        .input-field::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        /* Show/Hide Password Eye Button */
        .btn-toggle-eye {
            border: none;
            background: transparent;
            color: #94a3b8;
            padding: 0 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: color 0.2s ease;
            outline: none;
        }

        .btn-toggle-eye:hover {
            color: #005a9c;
        }

        /* Checkbox & Help Link */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-size: 0.80rem;
        }

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
            color: #475569;
            cursor: pointer;
            user-select: none;
            font-weight: 500;
        }

        .support-link {
            color: #005a9c;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.78rem;
        }

        .support-link:hover {
            text-decoration: underline;
        }

        /* Submit Button */
        .btn-auth-submit {
            width: 100%;
            padding: 0.80rem 1.25rem;
            font-size: 0.90rem;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(135deg, #005a9c 0%, #003e6b 50%, #0d9488 100%);
            border: none;
            border-radius: 10px;
            box-shadow: 0 8px 20px -4px rgba(0, 90, 156, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
        }

        .btn-auth-submit:hover {
            background: linear-gradient(135deg, #0284c7 0%, #004c85 50%, #0f766e 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 26px -4px rgba(0, 90, 156, 0.55);
            color: #ffffff;
        }

        .btn-auth-submit:active {
            transform: translateY(0);
        }

        /* Bottom Security & Help Footer */
        .auth-panel-footer {
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 0.74rem;
            color: #94a3b8;
            line-height: 1.5;
        }

        .auth-panel-footer strong {
            color: #475569;
        }

        /* Keyframes */
        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes logoBob {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        @keyframes orbFloat {
            from { transform: translateY(0) scale(1); }
            to { transform: translateY(25px) scale(1.06); }
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.25); }
        }

        @keyframes blinkDot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.35; }
        }

        /* Responsive Breakpoints */
        @media (max-width: 991px) {
            .auth-showcase-panel {
                display: none;
            }
            .auth-form-panel {
                flex: 1;
                background: #f8fafc;
                padding: 2.5rem 1.5rem;
            }
            .auth-form-container {
                background: #ffffff;
                padding: 2.25rem 1.75rem;
                border-radius: 18px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.1);
            }
            .mobile-brand-header {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <div class="auth-split-wrapper">
        <!-- -----------------------------------------------------------
             LEFT PANEL: Enterprise Showcase & Visual Branding (Desktop)
             ----------------------------------------------------------- -->
        <aside class="auth-showcase-panel">
            <div class="showcase-grid-matrix"></div>
            <div class="showcase-orb-1"></div>
            <div class="showcase-orb-2"></div>

            <!-- Top Brand Emblem -->
            <div class="showcase-content showcase-brand">
                <div class="brand-emblem-box">
                    <i class="fa-solid fa-flask-vial"></i>
                    <div class="brand-emblem-sparkle">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                </div>
                <div class="brand-text-block">
                    <h5>GUDI CHEMICALS</h5>
                    <small>MANUFACTURING, POS &amp; GST ERP</small>
                </div>
            </div>

            <!-- Center Headline & Feature Cards -->
            <div class="showcase-content my-auto py-4">
                <h1 class="showcase-headline">
                    Intelligent Chemical <span class="gradient-text">Manufacturing &amp; POS</span> Workstation.
                </h1>
                <p class="showcase-subtitle">
                    Automated recipe compounding, barcode billing, multi-tier FIFO inventory, and automated GST compliance in real-time.
                </p>

                <div class="showcase-features-stack">
                    <div class="feature-card">
                        <div class="feature-icon-pill" style="background: rgba(13, 148, 136, 0.18); color: #2dd4bf;">
                            <i class="fa-solid fa-flask"></i>
                        </div>
                        <div>
                            <div class="feature-title">Batch Compounding &amp; Lab QC</div>
                            <p class="feature-desc">Automated BOM formulation scaling, loss tracking, and test pass/fail recording.</p>
                        </div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon-pill" style="background: rgba(2, 132, 199, 0.18); color: #38bdf8;">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div>
                            <div class="feature-title">High-Speed POS &amp; Billing Desk</div>
                            <p class="feature-desc">Sub-second barcode scans, multi-payment tenders, and thermal 80mm invoice receipts.</p>
                        </div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon-pill" style="background: rgba(245, 158, 11, 0.18); color: #fbbf24;">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <div class="feature-title">Multi-Tier Inventory &amp; GSTR-1</div>
                            <p class="feature-desc">Batch/Lot FIFO tracking, real-time low stock alerts, and one-click PDF tax reports.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Compliance Badges -->
            <div class="showcase-content showcase-footer">
                <div class="d-flex align-items-center gap-2">
                    <span class="compliance-pill">
                        <i class="fa-solid fa-shield-halved text-success"></i> 256-Bit SSL
                    </span>
                    <span class="compliance-pill">
                        <i class="fa-solid fa-check-double text-info"></i> GST Ready
                    </span>
                    <span class="compliance-pill">
                        <i class="fa-solid fa-circle-check text-warning"></i> Audit Logged
                    </span>
                </div>
                <span>&copy; {{ date('Y') }} Gudi Chemicals</span>
            </div>
        </aside>

        <!-- -----------------------------------------------------------
             RIGHT PANEL: Sign In Form & Workstation Authentication
             ----------------------------------------------------------- -->
        <main class="auth-form-panel">
            <div class="auth-form-container">
                <!-- Mobile Brand Header (Visible only on small screens) -->
                <div class="mobile-brand-header">
                    <div class="brand-emblem-box" style="width: 42px; height: 42px; font-size: 1.25rem;">
                        <i class="fa-solid fa-flask-vial"></i>
                    </div>
                    <div class="brand-text-block">
                        <h6 class="fw-bold mb-0 text-dark">GUDI CHEMICALS</h6>
                        <small class="text-primary fw-semibold" style="font-size: 0.65rem;">CHEMICAL ERP WORKSTATION</small>
                    </div>
                </div>

                <!-- Form Title Block -->
                <div class="form-header-badge">
                    <span class="live-dot"></span>
                    <span>ERP Workstation Portal</span>
                </div>

                <h2 class="form-title">Workstation Sign In</h2>
                <p class="form-subtitle">Enter your designated staff email and security password to access your terminal.</p>

                <!-- Feedback Alerts -->
                @if(session('error'))
                    <div class="alert alert-danger py-2 px-3 small mb-3 d-flex align-items-center gap-2 border-0 shadow-sm" style="background-color: #fef2f2; color: #991b1b; border-radius: 9px;">
                        <i class="fa-solid fa-circle-exclamation text-danger fs-6"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success py-2 px-3 small mb-3 d-flex align-items-center gap-2 border-0 shadow-sm" style="background-color: #f0fdf4; color: #166534; border-radius: 9px;">
                        <i class="fa-solid fa-circle-check text-success fs-6"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                <!-- Authentication Form -->
                <form method="POST" action="{{ route('login') }}" autocomplete="on">
                    @csrf

                    <!-- Workstation Email -->
                    <div>
                        <label for="email" class="form-label-custom">
                            <i class="fa-regular fa-envelope text-primary"></i>
                            <span>Staff Email Address</span>
                        </label>
                        <div class="input-wrapper @error('email') is-invalid @enderror">
                            <div class="input-prefix-icon">
                                <i class="fa-solid fa-at"></i>
                            </div>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="input-field" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   placeholder="e.g. staff@gudichemicals.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-n2 mb-2 fw-semibold">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Security Password with Show/Hide Eye Toggle -->
                    <div>
                        <label for="password" class="form-label-custom">
                            <i class="fa-solid fa-lock text-primary"></i>
                            <span>Security Password</span>
                        </label>
                        <div class="input-wrapper @error('password') is-invalid @enderror">
                            <div class="input-prefix-icon">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="input-field" 
                                   required 
                                   placeholder="Enter your security password">
                            <button type="button" 
                                    class="btn-toggle-eye" 
                                    id="togglePasswordBtn" 
                                    onclick="togglePasswordVisibility()" 
                                    title="Show/Hide Password"
                                    aria-label="Toggle password view">
                                <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-n2 mb-2 fw-semibold">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Workstation & Help Link -->
                    <div class="remember-row">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember" checked>
                            <label class="form-check-label" for="remember">
                                Keep session active
                            </label>
                        </div>
                        <a href="mailto:admin@gudichemicals.com?subject=ERP%20Password%20Assistance" class="support-link">
                            Need help?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-auth-submit">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span>Sign In to Workstation</span>
                    </button>
                </form>

                <!-- Footer Support Note -->
                <div class="auth-panel-footer">
                    <div><strong>Gudi Chemicals Industrial ERP</strong> &bull; Version 1.2</div>
                    <div class="mt-1">
                        For role permission changes or account reset, contact 
                        <a href="mailto:admin@gudichemicals.com" class="text-decoration-none text-primary fw-semibold">
                            admin@gudichemicals.com
                        </a>
                    </div>
                </div>
            </div>
        </main>
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
