<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Error') — Gudi Chemicals ERP</title>

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
            --gudi-bg: #f8fafc;
            --gudi-border: #e2e8f0;
            --gudi-text-main: #0f172a;
            --gudi-text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
            background: linear-gradient(135deg, #f0f4f9 0%, #e2e8f0 50%, #eef2f6 100%);
            color: var(--gudi-text-main);
            -webkit-font-smoothing: antialiased;
        }

        /* Ambient Background Grid Pattern */
        .ambient-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 0;
            background-image: 
                radial-gradient(rgba(0, 90, 156, 0.08) 1.5px, transparent 1.5px),
                radial-gradient(rgba(13, 148, 136, 0.06) 1.5px, transparent 1.5px);
            background-size: 32px 32px;
            background-position: 0 0, 16px 16px;
            opacity: 0.85;
        }

        /* Ambient Glow Spheres */
        .glow-sphere-1 {
            position: fixed;
            top: -10%;
            right: 10%;
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, rgba(0, 90, 156, 0.12) 0%, rgba(0, 90, 156, 0) 70%);
            border-radius: 50%;
            filter: blur(40px);
            pointer-events: none;
            z-index: 0;
            animation: pulseAura 8s ease-in-out infinite alternate;
        }

        .glow-sphere-2 {
            position: fixed;
            bottom: -10%;
            left: 5%;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.08) 0%, rgba(220, 38, 38, 0) 70%);
            border-radius: 50%;
            filter: blur(50px);
            pointer-events: none;
            z-index: 0;
            animation: pulseAura 10s ease-in-out infinite alternate-reverse;
        }

        /* Main Error Wrapper */
        .error-page-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem 1rem;
        }

        /* Top Header */
        .error-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
            padding-bottom: 1rem;
        }

        .brand-logo-badge {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #005a9c 0%, #003e6b 100%);
            color: #ffffff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 4px 10px rgba(0, 90, 156, 0.25);
        }

        .brand-text h6 {
            margin: 0;
            font-weight: 800;
            font-size: 0.95rem;
            letter-spacing: 0.03em;
            color: #0b1324;
        }

        .brand-text small {
            font-size: 0.68rem;
            color: #64748b;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Center Error Card */
        .error-card-wrapper {
            max-width: 720px;
            margin: auto;
            width: 100%;
            padding: 1rem 0;
        }

        .error-card {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.12),
                        0 8px 16px -6px rgba(15, 23, 42, 0.06),
                        inset 0 1px 0 rgba(255, 255, 255, 0.9);
            padding: 2.75rem 2.25rem;
            position: relative;
            overflow: hidden;
            text-align: center;
            animation: cardEntrance 0.65s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Watermark Large Code in background */
        .watermark-code {
            position: absolute;
            top: 2%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11.5rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.04em;
            opacity: 0.04;
            user-select: none;
            pointer-events: none;
            font-family: 'Plus Jakarta Sans', monospace;
            z-index: 0;
        }

        /* Hero Animated Icon Container */
        .icon-hero-wrapper {
            position: relative;
            z-index: 1;
            width: 110px;
            height: 110px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-hero-box {
            width: 90px;
            height: 90px;
            border-radius: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            position: relative;
            z-index: 2;
            box-shadow: 0 14px 28px -6px rgba(0, 0, 0, 0.15);
            animation: floatBob 4s ease-in-out infinite;
        }

        /* Animated Halo Ring */
        .icon-halo-ring {
            position: absolute;
            top: -6px;
            left: -6px;
            right: -6px;
            bottom: -6px;
            border-radius: 32px;
            border: 2px dashed rgba(0, 90, 156, 0.25);
            animation: rotateDashed 20s linear infinite;
            z-index: 1;
        }

        .icon-halo-pulse {
            position: absolute;
            width: 115px;
            height: 115px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 90, 156, 0.15) 0%, rgba(0, 90, 156, 0) 70%);
            animation: pulseGlow 2.5s ease-in-out infinite;
            z-index: 0;
        }

        /* Status Badge Pill */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 0.85rem;
            border-radius: 50rem;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .status-badge .indicator-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            animation: blinkDot 1.8s infinite ease-in-out;
        }

        /* Error Heading & Description */
        .error-title {
            font-size: 1.55rem;
            font-weight: 800;
            color: #0b1324;
            margin-bottom: 0.65rem;
            letter-spacing: -0.01em;
            position: relative;
            z-index: 1;
        }

        .error-desc {
            font-size: 0.90rem;
            color: #475569;
            line-height: 1.6;
            max-width: 540px;
            margin: 0 auto 1.5rem auto;
            position: relative;
            z-index: 1;
        }

        /* Context Information Box */
        .context-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.9rem 1.25rem;
            margin: 0 auto 1.75rem auto;
            max-width: 560px;
            font-size: 0.80rem;
            text-align: left;
            position: relative;
            z-index: 1;
        }

        /* Action Buttons */
        .action-button-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .btn-action-primary {
            background: linear-gradient(135deg, #005a9c 0%, #003e6b 100%);
            color: #ffffff;
            border: none;
            font-weight: 600;
            font-size: 0.84rem;
            padding: 0.65rem 1.35rem;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 90, 156, 0.25);
            transition: all 0.2s ease-in-out;
        }

        .btn-action-primary:hover {
            background: linear-gradient(135deg, #006ebf 0%, #004c85 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 90, 156, 0.35);
        }

        .btn-action-secondary {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
            font-weight: 600;
            font-size: 0.84rem;
            padding: 0.65rem 1.2rem;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
        }

        .btn-action-secondary:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
            transform: translateY(-1px);
        }

        /* Quick Module Links */
        .quick-links-panel {
            padding-top: 1.25rem;
            border-top: 1px solid #f1f5f9;
            position: relative;
            z-index: 1;
        }

        .quick-links-title {
            font-size: 0.70rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            font-weight: 700;
            margin-bottom: 0.65rem;
        }

        .quick-links-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.65rem;
        }

        .quick-link-pill {
            font-size: 0.74rem;
            color: #475569;
            background: #f1f5f9;
            padding: 0.3rem 0.65rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .quick-link-pill:hover {
            background: #e2e8f0;
            color: #005a9c;
        }

        /* Footer */
        .error-footer {
            text-align: center;
            padding-top: 1rem;
            font-size: 0.72rem;
            color: #94a3b8;
        }

        .error-footer a {
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
        }

        .error-footer a:hover {
            color: #005a9c;
            text-decoration: underline;
        }

        /* --- Keyframe Animations --- */
        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes floatBob {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-9px);
            }
        }

        @keyframes pulseAura {
            from {
                transform: scale(0.9);
                opacity: 0.5;
            }
            to {
                transform: scale(1.15);
                opacity: 0.85;
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.22);
                opacity: 0.85;
            }
        }

        @keyframes rotateDashed {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes blinkDot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Status Specific Animation Modifiers */
        .anim-spin-slow {
            animation: rotateDashed 12s linear infinite;
        }

        .anim-swing {
            animation: swingPendulum 3s ease-in-out infinite;
        }

        @keyframes swingPendulum {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(15deg); }
            75% { transform: rotate(-15deg); }
        }

        .anim-shimmer {
            animation: shimmerEffect 2.5s infinite;
        }

        @keyframes shimmerEffect {
            0% { filter: drop-shadow(0 0 2px rgba(220, 38, 38, 0.4)); }
            50% { filter: drop-shadow(0 0 16px rgba(220, 38, 38, 0.75)); }
            100% { filter: drop-shadow(0 0 2px rgba(220, 38, 38, 0.4)); }
        }

        @media (max-width: 576px) {
            .error-card {
                padding: 2rem 1.25rem;
                border-radius: 16px;
            }
            .watermark-code {
                font-size: 8rem;
            }
            .error-title {
                font-size: 1.35rem;
            }
            .action-button-group {
                flex-direction: column;
                width: 100%;
            }
            .btn-action-primary, .btn-action-secondary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    @yield('extra_styles')
</head>
<body>
    <div class="ambient-background"></div>
    <div class="glow-sphere-1"></div>
    <div class="glow-sphere-2"></div>

    <div class="error-page-container">
        <!-- Topbar / Logo -->
        <header class="error-topbar">
            <a href="{{ url('/') }}" class="brand-logo-badge">
                <div class="brand-icon">
                    <i class="fa-solid fa-flask-vial"></i>
                </div>
                <div class="brand-text">
                    <h6>GUDI CHEMICALS</h6>
                    <small>MANUFACTURING &amp; GST ERP</small>
                </div>
            </a>
            <div class="d-none d-sm-flex align-items-center gap-2">
                <span class="badge bg-light text-secondary border px-2.5 py-1.5" style="font-size: 0.70rem;">
                    <i class="fa-solid fa-server text-success me-1"></i> System Online
                </span>
                @if(auth()->check())
                    <span class="badge bg-light text-dark border px-2.5 py-1.5" style="font-size: 0.70rem;">
                        <i class="fa-solid fa-user-check text-primary me-1"></i> {{ auth()->user()->name }}
                    </span>
                @endif
            </div>
        </header>

        <!-- Main Card Section -->
        <main class="error-card-wrapper">
            <div class="error-card">
                <!-- Large Background Watermark -->
                <div class="watermark-code">@yield('code', '403')</div>

                <!-- Animated Icon Box -->
                <div class="icon-hero-wrapper">
                    <div class="icon-halo-ring"></div>
                    <div class="icon-halo-pulse"></div>
                    @yield('icon_hero')
                </div>

                <!-- Status Badge -->
                @yield('badge')

                <!-- Error Title & Description -->
                <h1 class="error-title">@yield('error_heading')</h1>
                <p class="error-desc">@yield('error_message')</p>

                <!-- Contextual Details Box (Optional) -->
                @yield('context_box')

                <!-- Action Button Group -->
                <div class="action-button-group">
                    @yield('actions')
                </div>

                <!-- Quick Module Navigation Shortcuts -->
                <div class="quick-links-panel">
                    <div class="quick-links-title">Quick ERP Shortcuts</div>
                    <div class="quick-links-list">
                        <a href="{{ route('dashboard') }}" class="quick-link-pill">
                            <i class="fa-solid fa-chart-line text-primary"></i> Dashboard
                        </a>
                        <a href="{{ route('pos.index') }}" class="quick-link-pill">
                            <i class="fa-solid fa-bolt text-success"></i> POS Billing
                        </a>
                        <a href="{{ route('inventory.index') }}" class="quick-link-pill">
                            <i class="fa-solid fa-boxes-stacked text-warning"></i> Inventory
                        </a>
                        <a href="{{ route('guide.index') }}" class="quick-link-pill">
                            <i class="fa-solid fa-book-open-reader text-info"></i> User Manual
                        </a>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="error-footer">
            <div>
                &copy; {{ date('Y') }} <strong>Gudi Chemicals</strong> &bull; Industrial Solutions &amp; Chemical ERP
            </div>
            <div class="mt-1">
                Need administrative assistance? Contact ERP Support: 
                <a href="mailto:admin@gudichemicals.com">admin@gudichemicals.com</a> &bull; Hotline: +91 98765 43210
            </div>
        </footer>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('extra_scripts')
</body>
</html>
