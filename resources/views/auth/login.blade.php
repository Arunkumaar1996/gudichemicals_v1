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

        /* Enforce exactly 100% viewport height with zero unwanted vertical scroll on desktop */
        html, body {
            height: 100vh;
            max-height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
            background-color: #ffffff;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }

        /* Full Screen Split Container (Exactly 100vh) */
        .auth-split-wrapper {
            display: flex;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        /* -------------------------------------------------------------
         * LEFT PANEL: Enterprise Hero & Dynamic Animated Color Fill
         * ------------------------------------------------------------- */
        .auth-showcase-panel {
            flex: 1.15;
            background: linear-gradient(135deg, #040c1a 0%, #061830 20%, #00284d 45%, #053b5a 70%, #06494e 85%, #041424 100%);
            background-size: 350% 350%;
            animation: dynamicColorFillFlow 14s ease infinite;
            color: #ffffff;
            padding: 1.85rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Ambient Glowing Geometric Matrix Grid */
        .showcase-grid-matrix {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(rgba(56, 189, 248, 0.18) 1.5px, transparent 1.5px),
                radial-gradient(rgba(45, 212, 191, 0.14) 1.5px, transparent 1.5px);
            background-size: 32px 32px;
            background-position: 0 0, 16px 16px;
            opacity: 0.65;
            pointer-events: none;
            z-index: 1;
        }

        /* Color Fill Liquid Wave Orbs in Background */
        .showcase-orb-1 {
            position: absolute;
            top: -10%;
            right: -6%;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(0, 140, 255, 0.40) 0%, rgba(0, 90, 156, 0.12) 70%, transparent 80%);
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
            z-index: 1;
            animation: orbFloat 9s ease-in-out infinite alternate;
        }

        .showcase-orb-2 {
            position: absolute;
            bottom: -12%;
            left: -6%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.35) 0%, rgba(13, 148, 136, 0.12) 70%, transparent 80%);
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
            z-index: 1;
            animation: orbFloat 11s ease-in-out infinite alternate-reverse;
        }

        .showcase-content {
            position: relative;
            z-index: 2;
        }

        /* Top Brand Header in Showcase */
        .showcase-brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .showcase-brand-left {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        /* 3D Brand Logo with Animated Liquid Color Fill Inside */
        .brand-emblem-box {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #0284c7 0%, #005a9c 50%, #0d9488 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 18px rgba(0, 90, 156, 0.4),
                        inset 0 1px 1px rgba(255, 255, 255, 0.6);
            border: 1.5px solid rgba(255, 255, 255, 0.35);
            animation: logoBob 4s ease-in-out infinite;
        }

        /* Animated Chemical Liquid Fill Effect Inside Logo */
        .liquid-fill-level {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 65%;
            background: linear-gradient(180deg, rgba(45, 212, 191, 0.85) 0%, rgba(14, 165, 233, 0.9) 100%);
            border-top: 2px solid #ffffff;
            animation: liquidFillWave 3.5s ease-in-out infinite alternate;
            z-index: 1;
        }

        .brand-emblem-box i {
            position: relative;
            z-index: 2;
            color: #ffffff;
            font-size: 1.35rem;
            filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.35));
        }

        .brand-emblem-sparkle {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 14px;
            height: 14px;
            background: #f59e0b;
            color: #ffffff;
            border-radius: 50%;
            font-size: 0.50rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1.5px solid #050d1d;
            animation: pulseGlow 1.8s infinite;
            z-index: 3;
        }

        .brand-text-block h5 {
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            margin: 0;
            color: #ffffff;
            text-transform: uppercase;
        }

        .brand-text-block small {
            font-size: 0.64rem;
            color: #38bdf8;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
        }

        /* Top Right System Status Pill */
        .system-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.30rem 0.65rem;
            border-radius: 50rem;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(52, 211, 153, 0.28);
            font-size: 0.66rem;
            font-weight: 700;
            color: #6ee7b7;
            letter-spacing: 0.04em;
        }

        .system-status-pill .status-pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 8px #10b981;
            animation: blinkDot 1.6s infinite;
        }

        /* Hero Headline with Shimmering Color Fill */
        .showcase-headline {
            font-size: 1.55rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin-bottom: 0.45rem;
        }

        /* Animated Color Fill Gradient Text */
        .gradient-text-colorfill {
            background: linear-gradient(90deg, #38bdf8 0%, #34d399 25%, #fbbf24 50%, #818cf8 75%, #38bdf8 100%);
            background-size: 250% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: textColorFillSweep 5s linear infinite;
        }

        .showcase-subtitle {
            font-size: 0.78rem;
            color: #94a3b8;
            line-height: 1.45;
            max-width: 480px;
            margin-bottom: 0.95rem;
        }

        /* -------------------------------------------------------------
         * CENTERPIECE: Live Glass Chemical Reactor & Telemetry Console
         * ------------------------------------------------------------- */
        .telemetry-console-card {
            background: rgba(15, 23, 42, 0.62);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(56, 189, 248, 0.22);
            border-radius: 14px;
            padding: 0.95rem 1.15rem;
            box-shadow: 0 16px 36px -10px rgba(0, 0, 0, 0.45),
                        inset 0 1px 1px rgba(255, 255, 255, 0.12);
            margin-bottom: 0.85rem;
            position: relative;
            overflow: hidden;
        }

        .telemetry-console-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #38bdf8, #34d399, transparent);
            animation: scanlineSweep 4s ease-in-out infinite;
        }

        .telemetry-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding-bottom: 0.50rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.72rem;
        }

        .telemetry-header-title {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-weight: 700;
            color: #f1f5f9;
        }

        .telemetry-status-badge {
            background: rgba(14, 165, 233, 0.18);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.35);
            font-size: 0.62rem;
            font-weight: 700;
            padding: 0.18rem 0.50rem;
            border-radius: 50rem;
            display: inline-flex;
            align-items: center;
            gap: 0.30rem;
        }

        .telemetry-body {
            display: flex;
            align-items: center;
            gap: 1.15rem;
        }

        /* Animated Chemical Flask Graphic */
        .chemical-flask-display {
            width: 72px;
            height: 78px;
            flex-shrink: 0;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, transparent 70%);
            border-radius: 12px;
            border: 1px solid rgba(56, 189, 248, 0.18);
        }

        .flask-svg {
            width: 52px;
            height: 60px;
            filter: drop-shadow(0 0 10px rgba(45, 212, 191, 0.5));
        }

        .flask-liquid-path {
            animation: liquidFillWave 4s ease-in-out infinite alternate;
            fill: url(#flaskGrad);
        }

        .flask-bubble {
            animation: flaskBubbleRise 2.5s ease-in infinite;
            fill: #ffffff;
            opacity: 0.8;
        }

        .flask-bubble-2 {
            animation: flaskBubbleRise 3s ease-in infinite 0.7s;
            fill: #a7f3d0;
            opacity: 0.7;
        }

        /* Telemetry Metrics Grid */
        .telemetry-metrics-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.45rem 0.75rem;
        }

        .telemetry-metric-item {
            background: rgba(255, 255, 255, 0.035);
            border-radius: 8px;
            padding: 0.40rem 0.55rem;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .telemetry-metric-label {
            font-size: 0.60rem;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 0.10rem;
        }

        .telemetry-metric-value {
            font-size: 0.74rem;
            color: #f8fafc;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.30rem;
        }

        /* Telemetry Progress Bar */
        .telemetry-pipeline-bar {
            margin-top: 0.65rem;
            padding-top: 0.50rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.60rem;
            color: #64748b;
        }

        .pipeline-step {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            color: #94a3b8;
            font-weight: 600;
        }

        .pipeline-step.active {
            color: #38bdf8;
            font-weight: 700;
        }

        .pipeline-step.completed {
            color: #34d399;
        }

        /* -------------------------------------------------------------
         * FEATURED HERO: Turbo POS Desk Flagship Workstation Card
         * ------------------------------------------------------------- */
        .featured-pos-card {
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.22) 0%, rgba(13, 148, 136, 0.20) 45%, rgba(15, 23, 42, 0.72) 100%);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1.5px solid rgba(56, 189, 248, 0.45);
            border-radius: 12px;
            padding: 0.65rem 0.90rem;
            box-shadow: 0 10px 26px -6px rgba(2, 132, 199, 0.32),
                        inset 0 1px 1px rgba(255, 255, 255, 0.22);
            margin-bottom: 0.65rem;
            position: relative;
            overflow: hidden;
            transition: all 0.25s ease;
            animation: posCardGlow 4s ease-in-out infinite alternate;
        }

        .featured-pos-card:hover {
            transform: translateY(-2px);
            border-color: #38bdf8;
            box-shadow: 0 14px 32px -4px rgba(2, 132, 199, 0.48);
        }

        @keyframes posCardGlow {
            0% {
                border-color: rgba(56, 189, 248, 0.40);
                box-shadow: 0 10px 24px -6px rgba(2, 132, 199, 0.28);
            }
            100% {
                border-color: rgba(45, 212, 191, 0.65);
                box-shadow: 0 12px 28px -4px rgba(45, 212, 191, 0.38);
            }
        }

        .pos-hero-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.45rem;
        }

        .pos-hero-title-group {
            display: flex;
            align-items: center;
            gap: 0.60rem;
        }

        .pos-hero-icon-box {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #0284c7, #0d9488);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.95rem;
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.45);
            flex-shrink: 0;
            animation: pulseGlow 2.5s infinite;
        }

        .pos-hero-heading {
            font-size: 0.82rem;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.40rem;
            margin-bottom: 0.05rem;
        }

        .pos-core-badge {
            background: linear-gradient(90deg, #f59e0b, #ef4444);
            color: #ffffff;
            font-size: 0.55rem;
            font-weight: 800;
            padding: 0.10rem 0.38rem;
            border-radius: 50rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.35);
        }

        .pos-hero-sub {
            font-size: 0.64rem;
            color: #cbd5e1;
            margin: 0;
            line-height: 1.2;
        }

        .pos-speed-pill {
            background: rgba(16, 185, 129, 0.22);
            border: 1px solid rgba(52, 211, 153, 0.40);
            color: #6ee7b7;
            font-size: 0.62rem;
            font-weight: 700;
            padding: 0.18rem 0.50rem;
            border-radius: 50rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            white-space: nowrap;
        }

        .pos-chips-row {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .pos-mini-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 6px;
            padding: 0.14rem 0.40rem;
            font-size: 0.60rem;
            font-weight: 600;
            color: #e2e8f0;
        }

        .pos-mini-chip i {
            color: #38bdf8;
            font-size: 0.58rem;
        }

        /* 3-Column Companion Module Row */
        .features-grid-3col {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.45rem;
            margin-bottom: 0.65rem;
        }

        .feature-mini-card {
            background: rgba(255, 255, 255, 0.035);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 0.38rem 0.50rem;
            display: flex;
            align-items: center;
            gap: 0.45rem;
            transition: all 0.2s ease;
        }

        .feature-mini-card:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(56, 189, 248, 0.30);
            transform: translateY(-1px);
        }

        .feature-mini-icon {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            flex-shrink: 0;
        }

        .feature-mini-title {
            font-size: 0.66rem;
            font-weight: 700;
            color: #f1f5f9;
            line-height: 1.15;
            margin-bottom: 0.05rem;
        }

        .feature-mini-desc {
            font-size: 0.58rem;
            color: #94a3b8;
            line-height: 1.15;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Showcase Bottom Compliance Bar */
        .showcase-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.68rem;
            color: #64748b;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .compliance-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.55rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            font-size: 0.67rem;
            color: #cbd5e1;
            font-weight: 600;
        }

        /* -------------------------------------------------------------
         * RIGHT PANEL: Interactive Sign In Form (Scaled for 100vh fit)
         * ------------------------------------------------------------- */
        .auth-form-panel {
            flex: 0.88;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 2.5rem;
            position: relative;
            height: 100vh;
            overflow-y: auto;
        }

        .auth-form-container {
            width: 100%;
            max-width: 390px;
            animation: cardEntrance 0.65s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Mobile Brand Header */
        .mobile-brand-header {
            display: none;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Form Top Badge */
        .form-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 0.22rem 0.60rem;
            border-radius: 50rem;
            font-size: 0.68rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
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
            font-size: 1.48rem;
            font-weight: 800;
            color: #0b1324;
            letter-spacing: -0.02em;
            margin-bottom: 0.30rem;
        }

        .form-subtitle {
            font-size: 0.80rem;
            color: #64748b;
            margin-bottom: 1.45rem;
            line-height: 1.45;
        }

        /* Custom Form Inputs */
        .form-label-custom {
            font-size: 0.76rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.40rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            border: 1.5px solid #cbd5e1;
            border-radius: 9px;
            background: #ffffff;
            transition: all 0.2s ease-in-out;
            overflow: hidden;
            margin-bottom: 1.05rem;
        }

        .input-wrapper:focus-within {
            border-color: #005a9c;
            box-shadow: 0 0 0 3px rgba(0, 90, 156, 0.16);
        }

        .input-wrapper.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12);
        }

        .input-prefix-icon {
            width: 40px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 0.90rem;
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
            padding: 0.62rem 0.80rem;
            font-size: 0.85rem;
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
            padding: 0 0.80rem;
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
            margin-bottom: 1.25rem;
            font-size: 0.78rem;
        }

        .form-check-input {
            width: 1.05em;
            height: 1.05em;
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
            font-size: 0.76rem;
        }

        .support-link:hover {
            text-decoration: underline;
        }

        /* Submit Button with Dynamic Gradient Color-Fill Sweep */
        .btn-auth-submit {
            width: 100%;
            padding: 0.72rem 1.15rem;
            font-size: 0.88rem;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(135deg, #005a9c 0%, #003e6b 40%, #0d9488 75%, #0284c7 100%);
            background-size: 250% auto;
            border: none;
            border-radius: 9px;
            box-shadow: 0 6px 18px -3px rgba(0, 90, 156, 0.42);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            animation: buttonGlowFlow 6s ease infinite;
        }

        .btn-auth-submit:hover {
            background-position: right center;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px -3px rgba(0, 90, 156, 0.52);
            color: #ffffff;
        }

        .btn-auth-submit:active {
            transform: translateY(0);
        }

        /* Bottom Security & Help Footer */
        .auth-panel-footer {
            margin-top: 1.45rem;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 0.70rem;
            color: #94a3b8;
            line-height: 1.45;
        }

        .auth-panel-footer strong {
            color: #475569;
        }

        /* -------------------------------------------------------------
         * KEYFRAME ANIMATIONS: Dynamic Color Fill, Waves & Pulses
         * ------------------------------------------------------------- */
        @keyframes dynamicColorFillFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes textColorFillSweep {
            0% { background-position: 0% center; }
            100% { background-position: 250% center; }
        }

        @keyframes liquidFillWave {
            0% {
                height: 48%;
                filter: hue-rotate(0deg);
            }
            50% {
                height: 75%;
                filter: hue-rotate(25deg);
            }
            100% {
                height: 58%;
                filter: hue-rotate(0deg);
            }
        }

        @keyframes buttonGlowFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes logoBob {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        @keyframes orbFloat {
            from { transform: translateY(0) scale(1); }
            to { transform: translateY(20px) scale(1.06); }
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        @keyframes blinkDot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.35; }
        }

        @keyframes scanlineSweep {
            0% { left: -100%; }
            50%, 100% { left: 100%; }
        }

        @keyframes flaskBubbleRise {
            0% {
                transform: translateY(0) scale(0.6);
                opacity: 0;
            }
            30% {
                opacity: 0.9;
            }
            80% {
                transform: translateY(-28px) scale(1.1);
                opacity: 0.8;
            }
            100% {
                transform: translateY(-38px) scale(1.3);
                opacity: 0;
            }
        }

        /* Responsive Breakpoints */
        @media (max-width: 991px) {
            html, body {
                height: auto;
                max-height: none;
                overflow: auto;
            }
            .auth-split-wrapper {
                height: auto;
                flex-direction: column;
            }
            .auth-showcase-panel {
                display: none;
            }
            .auth-form-panel {
                flex: 1;
                height: 100vh;
                min-height: 100vh;
                background: #f8fafc;
                padding: 2rem 1.25rem;
            }
            .auth-form-container {
                background: #ffffff;
                padding: 2rem 1.5rem;
                border-radius: 16px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 14px 32px -8px rgba(15, 23, 42, 0.08);
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
             LEFT PANEL: Enterprise Showcase with Dynamic Color-Fill Animation
             ----------------------------------------------------------- -->
        <aside class="auth-showcase-panel">
            <div class="showcase-grid-matrix"></div>
            <div class="showcase-orb-1"></div>
            <div class="showcase-orb-2"></div>

            <!-- Top Brand Emblem with Chemical Liquid Fill & Live Status -->
            <div class="showcase-content showcase-brand">
                <div class="showcase-brand-left">
                    <div class="brand-emblem-box">
                        <div class="liquid-fill-level"></div>
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
                <div class="system-status-pill">
                    <span class="status-pulse-dot"></span>
                    <span>SYSTEM ONLINE</span>
                </div>
            </div>

            <!-- Center Headline, Glass Telemetry Console & 2x2 Matrix -->
            <div class="showcase-content my-auto py-2">
                <h1 class="showcase-headline">
                    Precision Chemical <span class="gradient-text-colorfill">Manufacturing &amp; POS</span> Workstation.
                </h1>
                <p class="showcase-subtitle">
                    Automated recipe compounding, barcode billing, multi-tier FIFO inventory, and automated GST compliance in real-time.
                </p>

                <!-- High-Tech Glass Telemetry Console -->
                <div class="telemetry-console-card">
                    <div class="telemetry-header">
                        <div class="telemetry-header-title">
                            <i class="fa-solid fa-atom text-info"></i>
                            <span>Reactor Line A-04 &bull; Batch #GC-2026-104</span>
                        </div>
                        <div class="telemetry-status-badge">
                            <span class="status-pulse-dot" style="width: 5px; height: 5px; background: #38bdf8; box-shadow: 0 0 6px #38bdf8;"></span>
                            <span>Compounding 92%</span>
                        </div>
                    </div>

                    <div class="telemetry-body">
                        <!-- Animated Chemical Flask Graphic with SVG & Rising Bubbles -->
                        <div class="chemical-flask-display">
                            <svg class="flask-svg" viewBox="0 0 64 74" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="flaskGrad" x1="0" y1="1" x2="0" y2="0">
                                        <stop offset="0%" stop-color="#0d9488" />
                                        <stop offset="50%" stop-color="#0284c7" />
                                        <stop offset="100%" stop-color="#38bdf8" />
                                    </linearGradient>
                                    <clipPath id="flaskOutlineClip">
                                        <path d="M26 6 H38 V26 L55 58 C57 62 54 66 49 66 H15 C10 66 7 62 9 58 L26 26 Z" />
                                    </clipPath>
                                </defs>
                                <!-- Flask Glass Outline -->
                                <path d="M26 6 H38 V26 L55 58 C57 62 54 66 49 66 H15 C10 66 7 62 9 58 L26 26 Z" 
                                      stroke="rgba(255, 255, 255, 0.45)" stroke-width="2.5" stroke-linejoin="round" fill="rgba(255, 255, 255, 0.04)" />
                                <!-- Lip of Flask -->
                                <rect x="23" y="3" width="18" height="4" rx="2" fill="rgba(255, 255, 255, 0.6)" />
                                <!-- Animated Liquid inside clip path -->
                                <g clip-path="url(#flaskOutlineClip)">
                                    <rect x="0" y="34" width="64" height="34" fill="url(#flaskGrad)" opacity="0.88">
                                        <animate attributeName="y" values="34; 28; 34" dur="4s" repeatCount="indefinite" />
                                    </rect>
                                    <!-- Wave sheen -->
                                    <ellipse cx="32" cy="34" rx="26" ry="4" fill="rgba(255, 255, 255, 0.5)">
                                        <animate attributeName="cy" values="34; 28; 34" dur="4s" repeatCount="indefinite" />
                                    </ellipse>
                                    <!-- Rising Micro-Bubbles -->
                                    <circle cx="28" cy="56" r="2.2" class="flask-bubble" />
                                    <circle cx="36" cy="50" r="1.8" class="flask-bubble-2" />
                                    <circle cx="22" cy="44" r="1.5" class="flask-bubble" />
                                </g>
                            </svg>
                        </div>

                        <!-- Live Telemetry Stats -->
                        <div class="telemetry-metrics-grid">
                            <div class="telemetry-metric-item">
                                <div class="telemetry-metric-label">Active Formulation</div>
                                <div class="telemetry-metric-value text-info">
                                    <i class="fa-solid fa-flask-vial fa-xs"></i> Solvent HD-90
                                </div>
                            </div>
                            <div class="telemetry-metric-item">
                                <div class="telemetry-metric-label">Batch Target Yield</div>
                                <div class="telemetry-metric-value text-success">
                                    <i class="fa-solid fa-gauge-high fa-xs"></i> 1,500 L (92%)
                                </div>
                            </div>
                            <div class="telemetry-metric-item">
                                <div class="telemetry-metric-label">Quality QC Status</div>
                                <div class="telemetry-metric-value text-warning">
                                    <i class="fa-solid fa-square-check fa-xs"></i> 7.2 pH &bull; Passed
                                </div>
                            </div>
                            <div class="telemetry-metric-item">
                                <div class="telemetry-metric-label">Live POS Stream</div>
                                <div class="telemetry-metric-value text-light">
                                    <i class="fa-solid fa-receipt fa-xs text-primary"></i> &#8377;1,84,650
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Process Pipeline Ribbon -->
                    <div class="telemetry-pipeline-bar">
                        <span class="pipeline-step completed">
                            <i class="fa-solid fa-circle-check"></i> BOM Scaled
                        </span>
                        <span class="text-white-50">&bull;</span>
                        <span class="pipeline-step active">
                            <i class="fa-solid fa-spinner fa-spin"></i> Compounding
                        </span>
                        <span class="text-white-50">&bull;</span>
                        <span class="pipeline-step completed">
                            <i class="fa-solid fa-circle-check"></i> Lab QC Pass
                        </span>
                        <span class="text-white-50">&bull;</span>
                        <span class="pipeline-step">
                            <i class="fa-regular fa-circle"></i> POS Retail Ready
                        </span>
                    </div>
                </div>

                <!-- FEATURED FLAGSHIP CARD: Turbo POS Desk -->
                <div class="featured-pos-card">
                    <div class="pos-hero-top">
                        <div class="pos-hero-title-group">
                            <div class="pos-hero-icon-box">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <div>
                                <div class="pos-hero-heading">
                                    <span>Turbo POS Desk</span>
                                    <span class="pos-core-badge">
                                        <i class="fa-solid fa-star fa-2xs"></i> Core Engine
                                    </span>
                                </div>
                                <p class="pos-hero-sub">High-Speed Retail Billing, Barcode Checkout &amp; Counter Terminal</p>
                            </div>
                        </div>
                        <div class="pos-speed-pill">
                            <i class="fa-solid fa-bolt-lightning"></i>
                            <span>&lt;0.2s Instant Billing</span>
                        </div>
                    </div>
                    <div class="pos-chips-row">
                        <span class="pos-mini-chip">
                            <i class="fa-solid fa-barcode"></i> Laser Barcode Fast-Scan
                        </span>
                        <span class="pos-mini-chip">
                            <i class="fa-solid fa-money-bill-wave"></i> Split Cash / UPI / Card
                        </span>
                        <span class="pos-mini-chip">
                            <i class="fa-solid fa-receipt"></i> 80mm Thermal Print
                        </span>
                        <span class="pos-mini-chip">
                            <i class="fa-solid fa-boxes-packing"></i> FIFO Stock Auto-Sync
                        </span>
                    </div>
                </div>

                <!-- 3 Companion Modules Compact Row -->
                <div class="features-grid-3col">
                    <div class="feature-mini-card">
                        <div class="feature-mini-icon" style="background: rgba(13, 148, 136, 0.22); color: #2dd4bf;">
                            <i class="fa-solid fa-flask"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="feature-mini-title">Compounding BOM</div>
                            <p class="feature-mini-desc">Formulations &amp; Lab QC</p>
                        </div>
                    </div>

                    <div class="feature-mini-card">
                        <div class="feature-mini-icon" style="background: rgba(245, 158, 11, 0.22); color: #fbbf24;">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="feature-mini-title">FIFO Batch Ledger</div>
                            <p class="feature-mini-desc">Lot tracking &amp; alerts</p>
                        </div>
                    </div>

                    <div class="feature-mini-card">
                        <div class="feature-mini-icon" style="background: rgba(168, 85, 247, 0.22); color: #c084fc;">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="feature-mini-title">GST Reports</div>
                            <p class="feature-mini-desc">GSTR-1, P&amp;L &amp; PDF</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Compliance Badges -->
            <div class="showcase-content showcase-footer">
                <div class="d-flex align-items-center gap-2">
                    <span class="compliance-pill">
                        <i class="fa-solid fa-shield-halved text-success"></i> 256-Bit TLS
                    </span>
                    <span class="compliance-pill">
                        <i class="fa-solid fa-check-double text-info"></i> GST Ready
                    </span>
                    <span class="compliance-pill">
                        <i class="fa-solid fa-circle-check text-warning"></i> Audit Logged
                    </span>
                </div>
                <span>&copy; {{ date('Y') }} Gudi Chemicals Pvt. Ltd.</span>
            </div>
        </aside>

        <!-- -----------------------------------------------------------
             RIGHT PANEL: Sign In Form (Scaled for 100vh Viewport Height)
             ----------------------------------------------------------- -->
        <main class="auth-form-panel">
            <div class="auth-form-container">
                <!-- Mobile Brand Header (Visible only on small screens) -->
                <div class="mobile-brand-header">
                    <div class="brand-emblem-box" style="width: 38px; height: 38px; font-size: 1.15rem;">
                        <div class="liquid-fill-level"></div>
                        <i class="fa-solid fa-flask-vial"></i>
                    </div>
                    <div class="brand-text-block">
                        <h6 class="fw-bold mb-0 text-dark">GUDI CHEMICALS</h6>
                        <small class="text-primary fw-semibold" style="font-size: 0.62rem;">CHEMICAL ERP WORKSTATION</small>
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
                    <div class="alert alert-danger py-2 px-3 small mb-3 d-flex align-items-center gap-2 border-0 shadow-sm" style="background-color: #fef2f2; color: #991b1b; border-radius: 8px;">
                        <i class="fa-solid fa-circle-exclamation text-danger fs-6"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success py-2 px-3 small mb-3 d-flex align-items-center gap-2 border-0 shadow-sm" style="background-color: #f0fdf4; color: #166534; border-radius: 8px;">
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

                    <!-- Submit Button with Dynamic Gradient Color-Fill Sweep -->
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
