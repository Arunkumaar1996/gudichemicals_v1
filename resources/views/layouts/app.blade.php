<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Gudi Chemicals ERP</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
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
            --gudi-sidebar-bg: #0b1324;
            --gudi-sidebar-card: #152238;
            --gudi-sidebar-hover: #1e293b;
            --gudi-border: #e2e8f0;
            --gudi-text-main: #1e293b;
            --gudi-text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
            background-color: var(--gudi-bg);
            color: var(--gudi-text-main);
            -webkit-font-smoothing: antialiased;
        }

        /* App Viewport Container: Exactly 100vh */
        #app-layout {
            display: flex;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        /* Modern Sidebar (Desktop & Collapsed) */
        #sidebar {
            width: 260px;
            height: 100vh;
            background-color: var(--gudi-sidebar-bg);
            color: #94a3b8;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 1040;
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.07);
            position: relative;
        }

        #sidebar .brand-header {
            height: 64px;
            padding: 0 1.25rem;
            background: linear-gradient(135deg, #07152d 0%, #002b54 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
            transition: padding 0.25s ease;
        }

        #sidebar .brand-logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: linear-gradient(135deg, #005a9c 0%, #00a88f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0, 90, 156, 0.4);
            color: #ffffff;
        }

        #sidebar .brand-text {
            margin-left: 10px;
            overflow: hidden;
            white-space: nowrap;
            transition: opacity 0.2s ease, width 0.25s ease;
        }

        #sidebar .sidebar-menu-scroll {
            flex-grow: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.85rem 0.65rem 1.5rem 0.65rem;
        }

        #sidebar .sidebar-menu-scroll::-webkit-scrollbar {
            width: 4px;
        }
        #sidebar .sidebar-menu-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 4px;
        }

        #sidebar .sidebar-user-footer {
            flex-shrink: 0;
            height: 56px;
            padding: 0 1rem;
            background: #060b14;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        #sidebar .menu-category {
            font-size: 0.68rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.09em;
            color: #475569;
            padding: 1rem 0.85rem 0.35rem 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Nav link & Submenu Accordion Items */
        #sidebar .nav-link,
        #sidebar .menu-toggle {
            color: #94a3b8;
            padding: 0.6rem 0.85rem;
            font-size: 0.86rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            border-radius: 9px;
            margin-bottom: 3px;
            transition: all 0.18s ease;
            text-decoration: none;
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            position: relative;
        }

        #sidebar .nav-link i,
        #sidebar .menu-toggle .menu-icon i {
            width: 20px;
            font-size: 0.98rem;
            margin-right: 10px;
            opacity: 0.9;
            transition: transform 0.2s ease, opacity 0.2s ease;
            text-align: center;
        }

        #sidebar .nav-link:hover,
        #sidebar .menu-toggle:hover {
            color: #ffffff;
            background-color: var(--gudi-sidebar-hover);
        }

        #sidebar .nav-link:hover i,
        #sidebar .menu-toggle:hover .menu-icon i {
            transform: scale(1.1);
            opacity: 1;
        }

        #sidebar .nav-link.active,
        #sidebar .menu-toggle.active {
            background: linear-gradient(135deg, rgba(0, 90, 156, 0.35) 0%, rgba(2, 132, 199, 0.25) 100%);
            color: #38bdf8;
            font-weight: 600;
            border-left: 3px solid #38bdf8;
        }

        #sidebar .nav-link.active i,
        #sidebar .menu-toggle.active .menu-icon i {
            opacity: 1;
            color: #38bdf8;
        }

        /* Rotating Chevron Indicator */
        #sidebar .menu-arrow {
            margin-left: auto;
            font-size: 0.72rem;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0.6;
        }

        #sidebar .menu-toggle[aria-expanded="true"] .menu-arrow {
            transform: rotate(90deg);
            opacity: 1;
            color: #38bdf8;
        }

        /* Submenu container (Accordion) */
        #sidebar .menu-sub {
            padding-left: 0.5rem;
            margin-bottom: 4px;
            position: relative;
            transition: all 0.25s ease-out;
        }

        #sidebar .menu-sub::before {
            content: '';
            position: absolute;
            left: 21px;
            top: 4px;
            bottom: 6px;
            width: 1px;
            background: rgba(255, 255, 255, 0.08);
        }

        #sidebar .menu-sub-link {
            color: #94a3b8;
            padding: 0.45rem 0.75rem 0.45rem 1.85rem;
            font-size: 0.82rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            border-radius: 7px;
            text-decoration: none;
            margin-bottom: 2px;
            position: relative;
            transition: all 0.18s ease;
        }

        #sidebar .menu-sub-link .bullet-dot {
            position: absolute;
            left: 11px;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background-color: #475569;
            transition: all 0.18s ease;
        }

        #sidebar .menu-sub-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
            padding-left: 2rem;
        }

        #sidebar .menu-sub-link:hover .bullet-dot {
            background-color: #38bdf8;
            box-shadow: 0 0 8px #38bdf8;
            transform: scale(1.3);
        }

        #sidebar .menu-sub-link.active {
            color: #38bdf8;
            background: rgba(2, 132, 199, 0.16);
            font-weight: 600;
        }

        #sidebar .menu-sub-link.active .bullet-dot {
            background-color: #38bdf8;
            box-shadow: 0 0 8px #38bdf8;
            transform: scale(1.3);
        }

        /* -------------------------------------------------------------
         * COLLAPSED SIDEBAR (MINI ICON-ONLY MODE - Modern Admin Look)
         * ------------------------------------------------------------- */
        #app-layout.sidebar-collapsed #sidebar {
            width: 74px;
        }

        #app-layout.sidebar-collapsed #sidebar .brand-header {
            padding: 0;
            justify-content: center;
        }

        #app-layout.sidebar-collapsed #sidebar .brand-text {
            display: none !important;
            width: 0;
            opacity: 0;
        }

        #app-layout.sidebar-collapsed #sidebar .menu-category {
            height: 1px;
            margin: 0.6rem 0.6rem;
            padding: 0;
            background: rgba(255, 255, 255, 0.08);
            font-size: 0;
            color: transparent;
            overflow: hidden;
        }

        #app-layout.sidebar-collapsed #sidebar .nav-link,
        #app-layout.sidebar-collapsed #sidebar .menu-toggle {
            padding: 0.68rem 0;
            justify-content: center;
            border-radius: 9px;
            border-left: none !important;
        }

        #app-layout.sidebar-collapsed #sidebar .nav-link i,
        #app-layout.sidebar-collapsed #sidebar .menu-toggle .menu-icon i {
            margin-right: 0;
            font-size: 1.15rem;
        }

        #app-layout.sidebar-collapsed #sidebar .menu-title,
        #app-layout.sidebar-collapsed #sidebar .menu-badge,
        #app-layout.sidebar-collapsed #sidebar .menu-arrow {
            display: none !important;
        }

        /* Floating Flyout Submenu in Collapsed Mode */
        #app-layout.sidebar-collapsed #sidebar .menu-item {
            position: relative;
        }

        #app-layout.sidebar-collapsed #sidebar .menu-sub {
            display: none !important;
            position: absolute;
            left: 74px;
            top: 0;
            width: 225px;
            background: #0b1324;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.65), 0 5px 10px -2px rgba(0, 0, 0, 0.4);
            padding: 0.5rem;
            z-index: 1060;
            animation: flyoutFadeIn 0.18s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        #app-layout.sidebar-collapsed #sidebar .menu-sub::before {
            display: none;
        }

        #app-layout.sidebar-collapsed #sidebar .menu-item:hover > .menu-sub {
            display: block !important;
        }

        #app-layout.sidebar-collapsed #sidebar .menu-sub .flyout-header {
            display: block !important;
            padding: 0.4rem 0.75rem 0.5rem 0.75rem;
            font-size: 0.72rem;
            font-weight: 700;
            color: #38bdf8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 0.35rem;
        }

        #app-layout.sidebar-collapsed #sidebar .sidebar-user-footer {
            padding: 0;
            justify-content: center;
        }

        #app-layout.sidebar-collapsed #sidebar .sidebar-user-footer .user-info-text,
        #app-layout.sidebar-collapsed #sidebar .sidebar-user-footer .btn-logout-text {
            display: none !important;
        }

        @keyframes flyoutFadeIn {
            from {
                opacity: 0;
                transform: translateX(-6px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Main Viewport Column: Fixed 100vh with Fixed Header & Fixed Footer */
        #main-content {
            flex: 1;
            min-width: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background-color: var(--gudi-bg);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Fixed Top Header */
        .topbar {
            height: 64px;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--gudi-border);
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        /* Dedicated Content Scroller: Exactly between Topbar and Fixed Footer */
        .page-content-wrapper {
            flex: 1;
            height: calc(100vh - 64px - 42px);
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        .page-content-wrapper > main {
            flex-grow: 1;
        }

        /* Fixed Bottom Footer Bar */
        .footer-bar {
            height: 42px;
            flex-shrink: 0;
            background: #ffffff;
            border-top: 1px solid var(--gudi-border);
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            font-size: 0.8rem;
            color: var(--gudi-text-muted);
            z-index: 1010;
            box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.02);
        }

        .pulse-indicator {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #16a34a;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.7);
            animation: pulseGreen 2s infinite;
        }

        @keyframes pulseGreen {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 5px rgba(22, 163, 74, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(22, 163, 74, 0);
            }
        }

        /* Modern Form Inputs & Controls */
        .form-control, .form-select {
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            padding: 0.56rem 0.85rem;
            font-size: 0.88rem;
            color: #1e293b;
            background-color: #ffffff;
            transition: all 0.18s ease-in-out;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02);
        }

        .form-control:focus, .form-select:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.16);
            outline: none;
            background-color: #ffffff;
        }

        .form-control::placeholder {
            color: #94a3b8;
            font-size: 0.84rem;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.35rem;
            letter-spacing: 0.01em;
        }

        .input-group-text {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 9px;
            color: #64748b;
            font-size: 0.88rem;
        }

        /* Modern Cards */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.03), 0 1px 2px -1px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem 1.35rem;
            border-top-left-radius: 14px !important;
            border-top-right-radius: 14px !important;
        }

        .card-stat {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .card-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.06);
            border-color: #cbd5e1;
        }

        /* Modern Buttons */
        .btn {
            border-radius: 9px;
            font-weight: 600;
            padding: 0.5rem 1rem;
            font-size: 0.88rem;
            transition: all 0.18s ease-in-out;
        }

        .btn-gudi-primary {
            background: linear-gradient(135deg, #005a9c 0%, #004273 100%);
            color: #ffffff;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 90, 156, 0.25);
        }

        .btn-gudi-primary:hover {
            background: linear-gradient(135deg, #004d85 0%, #00365e 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 90, 156, 0.32);
        }

        .btn-outline-primary {
            border-color: #005a9c;
            color: #005a9c;
        }

        .btn-outline-primary:hover {
            background-color: #005a9c;
            color: #ffffff;
        }

        /* Modern Tables */
        .table {
            color: #334155;
            vertical-align: middle;
            font-size: 0.88rem;
        }

        .table > :not(caption) > * > * {
            padding: 0.85rem 1rem;
        }

        .table thead th {
            background-color: #f8fafc;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
        }

        .table tbody tr {
            transition: background-color 0.15s ease;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Custom Scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        #sidebar .sidebar-menu-scroll::-webkit-scrollbar-thumb {
            background: #1e293b;
        }
        #sidebar .sidebar-menu-scroll::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }

        /* Mobile Responsive Drawer */
        @media (max-width: 991.98px) {
            #sidebar {
                position: fixed;
                left: -270px;
                top: 0;
                bottom: 0;
            }
            #sidebar.show {
                left: 0;
            }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(4px);
                z-index: 1035;
            }
            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div id="app-layout">
        <!-- Mobile Backdrop -->
        <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <nav id="sidebar">
            <!-- Brand Header (Icon + Text in expanded, Icon only in collapsed) -->
            <div class="brand-header">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none text-white overflow-hidden">
                    <div class="brand-logo-icon">
                        <i class="fa-solid fa-flask-vial fa-lg"></i>
                    </div>
                    <div class="brand-text">
                        <h6 class="mb-0 fw-bold tracking-wide" style="letter-spacing: 0.03em;">GUDI CHEMICALS</h6>
                        <small style="font-size: 0.68rem; color: #94a3b8; letter-spacing: 0.04em;">MANUFACTURING & GST ERP</small>
                    </div>
                </a>
                <button class="btn btn-sm text-white-50 d-lg-none" onclick="toggleSidebar()">
                    <i class="fa-solid fa-times fa-lg"></i>
                </button>
            </div>

            <!-- Scrollable Navigation Menu -->
            <div class="sidebar-menu-scroll">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span class="menu-title">Dashboard</span>
                </a>

                <!-- POS & Sales Billing (Collapsible Submenu) -->
                @php
                    $isSalesActive = request()->routeIs('pos.*') || request()->routeIs('invoices.*') || request()->routeIs('returns.*') || request()->routeIs('promotions.*');
                @endphp
                <div class="menu-item {{ $isSalesActive ? 'active-parent' : '' }}">
                    <button class="menu-toggle {{ $isSalesActive ? 'active' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#menuSales" aria-expanded="{{ $isSalesActive ? 'true' : 'false' }}">
                        <span class="menu-icon"><i class="fa-solid fa-cash-register text-success"></i></span>
                        <span class="menu-title">POS & Sales Billing</span>
                        <span class="menu-badge badge bg-success-subtle text-success ms-auto me-2">POS</span>
                        <i class="fa-solid fa-chevron-right menu-arrow"></i>
                    </button>
                    <div class="menu-sub collapse {{ $isSalesActive ? 'show' : '' }}" id="menuSales">
                        <div class="flyout-header d-none">POS & Sales Billing</div>
                        <a href="{{ route('pos.index') }}" target="_blank" class="menu-sub-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">POS Fast Billing Desk</span>
                            <span class="badge bg-danger ms-auto fs-xs" style="font-size: 0.65rem;">FAST</span>
                        </a>
                        <a href="{{ route('invoices.index') }}" class="menu-sub-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Sales Invoices</span>
                        </a>
                        <a href="{{ route('returns.index') }}" class="menu-sub-link {{ request()->routeIs('returns.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Sales Returns / Credit</span>
                        </a>
                        <a href="{{ route('reports.collections') }}" class="menu-sub-link {{ request()->routeIs('reports.collections') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Payment Collections</span>
                        </a>
                        <a href="{{ route('promotions.index') }}" class="menu-sub-link {{ request()->routeIs('promotions.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Promotions & Offers</span>
                        </a>
                    </div>
                </div>

                <!-- Chemical Manufacturing (Collapsible Submenu) -->
                @php
                    $isMfgActive = request()->routeIs('production.*');
                @endphp
                <div class="menu-item {{ $isMfgActive ? 'active-parent' : '' }}">
                    <button class="menu-toggle {{ $isMfgActive ? 'active' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#menuMfg" aria-expanded="{{ $isMfgActive ? 'true' : 'false' }}">
                        <span class="menu-icon"><i class="fa-solid fa-industry text-info"></i></span>
                        <span class="menu-title">Chemical Production</span>
                        <i class="fa-solid fa-chevron-right menu-arrow"></i>
                    </button>
                    <div class="menu-sub collapse {{ $isMfgActive ? 'show' : '' }}" id="menuMfg">
                        <div class="flyout-header d-none">Chemical Production</div>
                        <a href="{{ route('production.formulas.index') }}" class="menu-sub-link {{ request()->routeIs('production.formulas.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Formulas (BOM)</span>
                        </a>
                        <a href="{{ route('production.orders.index') }}" class="menu-sub-link {{ request()->routeIs('production.orders.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Production Batches</span>
                        </a>
                        <a href="{{ route('reports.production') }}" class="menu-sub-link {{ request()->routeIs('reports.production') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Yield & Costing</span>
                        </a>
                    </div>
                </div>

                <!-- Multi-Tier Inventory (Collapsible Submenu) -->
                @php
                    $isInvActive = request()->routeIs('inventory.*');
                @endphp
                <div class="menu-item {{ $isInvActive ? 'active-parent' : '' }}">
                    <button class="menu-toggle {{ $isInvActive ? 'active' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#menuInv" aria-expanded="{{ $isInvActive ? 'true' : 'false' }}">
                        <span class="menu-icon"><i class="fa-solid fa-boxes-stacked text-warning"></i></span>
                        <span class="menu-title">Multi-Tier Inventory</span>
                        <i class="fa-solid fa-chevron-right menu-arrow"></i>
                    </button>
                    <div class="menu-sub collapse {{ $isInvActive ? 'show' : '' }}" id="menuInv">
                        <div class="flyout-header d-none">Multi-Tier Inventory</div>
                        <a href="{{ route('inventory.index') }}" class="menu-sub-link {{ request()->routeIs('inventory.index') && !request()->has('filter') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Stock on Hand</span>
                        </a>
                        <a href="{{ route('inventory.index', ['filter' => 'low_stock']) }}" class="menu-sub-link {{ request()->get('filter') === 'low_stock' ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text text-danger fw-semibold">Low Stock Alerts</span>
                        </a>
                        <a href="{{ route('inventory.ledger') }}" class="menu-sub-link {{ request()->routeIs('inventory.ledger') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Stock Movement Ledger</span>
                        </a>
                        <a href="{{ route('inventory.adjustments.index') }}" class="menu-sub-link {{ request()->routeIs('inventory.adjustments.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Stock Adjustments</span>
                        </a>
                        <a href="{{ route('inventory.opening_stock') }}" class="menu-sub-link {{ request()->routeIs('inventory.opening_stock') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Opening Stock</span>
                        </a>
                    </div>
                </div>

                <!-- Purchases & Suppliers (Collapsible Submenu) -->
                @php
                    $isPurActive = request()->routeIs('purchases.*');
                @endphp
                <div class="menu-item {{ $isPurActive ? 'active-parent' : '' }}">
                    <button class="menu-toggle {{ $isPurActive ? 'active' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#menuPurchases" aria-expanded="{{ $isPurActive ? 'true' : 'false' }}">
                        <span class="menu-icon"><i class="fa-solid fa-cart-shopping text-primary"></i></span>
                        <span class="menu-title">Purchasing & Vendor</span>
                        <i class="fa-solid fa-chevron-right menu-arrow"></i>
                    </button>
                    <div class="menu-sub collapse {{ $isPurActive ? 'show' : '' }}" id="menuPurchases">
                        <div class="flyout-header d-none">Purchasing & Vendor</div>
                        <a href="{{ route('purchases.orders.index') }}" class="menu-sub-link {{ request()->routeIs('purchases.orders.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Purchase Orders</span>
                        </a>
                        <a href="{{ route('purchases.grn.index') }}" class="menu-sub-link {{ request()->routeIs('purchases.grn.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Goods Receipts (GRN)</span>
                        </a>
                        <a href="{{ route('purchases.payments.index') }}" class="menu-sub-link {{ request()->routeIs('purchases.payments.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Vendor Payments</span>
                        </a>
                    </div>
                </div>

                <!-- Master Data (Collapsible Submenu) -->
                @php
                    $isMastersActive = request()->routeIs('masters.*');
                @endphp
                <div class="menu-item {{ $isMastersActive ? 'active-parent' : '' }}">
                    <button class="menu-toggle {{ $isMastersActive ? 'active' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#menuMasters" aria-expanded="{{ $isMastersActive ? 'true' : 'false' }}">
                        <span class="menu-icon"><i class="fa-solid fa-database text-secondary"></i></span>
                        <span class="menu-title">Master Data</span>
                        <i class="fa-solid fa-chevron-right menu-arrow"></i>
                    </button>
                    <div class="menu-sub collapse {{ $isMastersActive ? 'show' : '' }}" id="menuMasters">
                        <div class="flyout-header d-none">Master Data</div>
                        <a href="{{ route('masters.products.index') }}" class="menu-sub-link {{ request()->routeIs('masters.products.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Products & Chemicals</span>
                        </a>
                        <a href="{{ route('masters.customers.index') }}" class="menu-sub-link {{ request()->routeIs('masters.customers.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Customer Directory</span>
                        </a>
                        <a href="{{ route('masters.vendors.index') }}" class="menu-sub-link {{ request()->routeIs('masters.vendors.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Vendors & Suppliers</span>
                        </a>
                        <a href="{{ route('masters.categories.index') }}" class="menu-sub-link {{ request()->routeIs('masters.categories.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Product Categories</span>
                        </a>
                        <a href="{{ route('masters.units.index') }}" class="menu-sub-link {{ request()->routeIs('masters.units.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Units & Conversions</span>
                        </a>
                    </div>
                </div>

                <!-- Finance & GST Reports (Collapsible Submenu) -->
                @php
                    $isReportsActive = request()->routeIs('reports.*') || request()->routeIs('expenses.*');
                @endphp
                <div class="menu-item {{ $isReportsActive ? 'active-parent' : '' }}">
                    <button class="menu-toggle {{ $isReportsActive ? 'active' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#menuReports" aria-expanded="{{ $isReportsActive ? 'true' : 'false' }}">
                        <span class="menu-icon"><i class="fa-solid fa-chart-pie text-success"></i></span>
                        <span class="menu-title">Finance & Reports</span>
                        <i class="fa-solid fa-chevron-right menu-arrow"></i>
                    </button>
                    <div class="menu-sub collapse {{ $isReportsActive ? 'show' : '' }}" id="menuReports">
                        <div class="flyout-header d-none">Finance & Reports</div>
                        <a href="{{ route('reports.sales') }}" class="menu-sub-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Sales Register</span>
                        </a>
                        <a href="{{ route('reports.gst') }}" class="menu-sub-link {{ request()->routeIs('reports.gst') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">GSTR-1 Tax Summary</span>
                        </a>
                        <a href="{{ route('reports.receivables') }}" class="menu-sub-link {{ request()->routeIs('reports.receivables') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Customer Receivables</span>
                        </a>
                        <a href="{{ route('reports.payables') }}" class="menu-sub-link {{ request()->routeIs('reports.payables') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Vendor Payables</span>
                        </a>
                        <a href="{{ route('reports.collections') }}" class="menu-sub-link {{ request()->routeIs('reports.collections') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Daily Collections</span>
                        </a>
                        <a href="{{ route('reports.inventory') }}" class="menu-sub-link {{ request()->routeIs('reports.inventory') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Inventory Valuation</span>
                        </a>
                        <a href="{{ route('expenses.index') }}" class="menu-sub-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Operating Expenses</span>
                        </a>
                    </div>
                </div>

                <!-- System Administration (Collapsible Submenu) -->
                @php
                    $isSettingsActive = request()->routeIs('settings.*') || request()->routeIs('users.*');
                @endphp
                <div class="menu-item {{ $isSettingsActive ? 'active-parent' : '' }}">
                    <button class="menu-toggle {{ $isSettingsActive ? 'active' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#menuSettings" aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}">
                        <span class="menu-icon"><i class="fa-solid fa-gear text-secondary"></i></span>
                        <span class="menu-title">Settings & Users</span>
                        <i class="fa-solid fa-chevron-right menu-arrow"></i>
                    </button>
                    <div class="menu-sub collapse {{ $isSettingsActive ? 'show' : '' }}" id="menuSettings">
                        <div class="flyout-header d-none">Settings & Users</div>
                        <a href="{{ route('settings.index') }}" class="menu-sub-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Company Settings</span>
                        </a>
                        <a href="{{ route('users.index') }}" class="menu-sub-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <span class="bullet-dot"></span>
                            <span class="sub-text">Users & Permissions</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer User Profile Badge -->
            <div class="sidebar-user-footer">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 34px; height: 34px; font-weight: 700; background: linear-gradient(135deg, #005a9c 0%, #00a88f 100%); font-size: 0.85rem;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="user-info-text overflow-hidden">
                            <p class="mb-0 text-white text-truncate fw-semibold" style="font-size: 0.82rem;">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <small class="text-muted d-block text-truncate" style="font-size: 0.7rem;">{{ auth()->user()->role ?? 'Super Admin' }}</small>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline btn-logout-text">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-white-50 p-1" title="Sign out">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content Area with Fixed Header & Fixed Bottom Footer -->
        <div id="main-content">
            <!-- Fixed Top Navbar -->
            <header class="topbar">
                <div class="d-flex align-items-center">
                    <!-- Mobile Drawer Toggle -->
                    <button class="btn btn-light btn-sm me-3 d-lg-none border" onclick="toggleSidebar()">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <!-- Desktop Mini Sidebar Toggle Button -->
                    <button type="button" id="btnToggleSidebarMini" class="btn btn-light btn-sm border me-3 d-none d-lg-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 8px;" title="Toggle Collapsed Mini Sidebar">
                        <i class="fa-solid fa-bars-staggered text-primary"></i>
                    </button>
                    <div class="d-none d-md-flex align-items-center text-muted small">
                        <span class="badge bg-light text-dark border px-2 py-1 me-2">
                            <i class="fa-solid fa-building me-1 text-primary"></i> Gudi Chemicals
                        </span>
                        <i class="fa-solid fa-shield-halved text-success me-1"></i> GSTIN: <strong>{{ \App\Models\CompanySetting::current()->gstin ?? '27AAACG1234D1Z5' }}</strong>
                        <span class="mx-2 text-secondary">|</span>
                        <i class="fa-regular fa-calendar-check me-1 text-primary"></i> FY: <strong>{{ \App\Models\CompanySetting::current()->fy_code ?? '2026-27' }}</strong>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('pos.index') }}" target="_blank" class="btn btn-success btn-sm px-3 fw-bold shadow-sm d-flex align-items-center">
                        <i class="fa-solid fa-bolt me-1.5"></i> Fast Billing / POS
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-light btn-sm border dropdown-toggle d-flex align-items-center py-1.5" type="button" data-bs-toggle="dropdown">
                            <i class="fa-regular fa-user-circle me-1.5 text-primary fs-6"></i>
                            <span class="fw-semibold">{{ auth()->user()->name ?? 'Admin' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold">{{ auth()->user()->name ?? 'Administrator' }}</div>
                                <div class="text-muted small">{{ auth()->user()->email ?? 'admin@gudichemicals.com' }}</div>
                            </li>
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="fa-solid fa-id-badge me-2 text-muted"></i> Profile & Security</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('settings.index') }}"><i class="fa-solid fa-sliders me-2 text-muted"></i> ERP Settings</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Sign Out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Scrollable Page Body (100vh - 64px - 42px) -->
            <div class="page-content-wrapper">
                <main>
                    <!-- Flash Message Alerts -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm border-0 mb-4" role="alert">
                            <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
                            <div class="fw-medium">{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm border-0 mb-4" role="alert">
                            <i class="fa-solid fa-triangle-exclamation fa-lg me-2 text-danger"></i>
                            <div class="fw-medium">{{ session('error') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                            <strong><i class="fa-solid fa-circle-xmark me-1"></i> Please correct the following errors:</strong>
                            <ul class="mb-0 mt-1 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>

            <!-- Fixed Bottom Footer Bar (Fixed to bottom of content area) -->
            <footer class="footer-bar">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="d-flex align-items-center">
                        <span>&copy; {{ date('Y') }} <strong class="text-dark">Gudi Chemicals</strong>. All rights reserved.</span>
                        <span class="mx-2 text-muted d-none d-md-inline">|</span>
                        <span class="text-muted d-none d-md-inline">Chemical Manufacturing, POS &amp; GST ERP</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small d-flex align-items-center">
                            <span class="pulse-indicator me-1.5"></span> System Online
                        </span>
                        <span class="badge bg-light text-secondary border px-2 py-1 d-none d-sm-inline">v1.2 Production</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarBackdrop').classList.toggle('show');
        }

        // Setup AJAX CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Desktop Collapsed Sidebar Toggle with LocalStorage persistence
        document.addEventListener('DOMContentLoaded', function () {
            const appLayout = document.getElementById('app-layout');
            const btnToggle = document.getElementById('btnToggleSidebarMini');

            // Restore user's collapse preference
            if (localStorage.getItem('gudi_sidebar_collapsed') === 'true') {
                appLayout.classList.add('sidebar-collapsed');
            }

            if (btnToggle) {
                btnToggle.addEventListener('click', function () {
                    appLayout.classList.toggle('sidebar-collapsed');
                    const isCollapsed = appLayout.classList.contains('sidebar-collapsed');
                    localStorage.setItem('gudi_sidebar_collapsed', isCollapsed ? 'true' : 'false');
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
