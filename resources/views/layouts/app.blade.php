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

        /* Modern Sidebar */
        #sidebar {
            width: 270px;
            height: 100vh;
            background-color: var(--gudi-sidebar-bg);
            color: #94a3b8;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 1040;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.06);
        }

        #sidebar .brand-header {
            padding: 1.25rem 1.4rem;
            background: linear-gradient(135deg, #07152d 0%, #002b54 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            flex-shrink: 0;
        }

        #sidebar .sidebar-menu-scroll {
            flex-grow: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.75rem 0.6rem 1.5rem 0.6rem;
        }

        #sidebar .sidebar-user-footer {
            flex-shrink: 0;
            padding: 0.85rem 1.1rem;
            background: #060b14;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
        }

        #sidebar .menu-category {
            font-size: 0.68rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: #64748b;
            padding: 0.85rem 0.85rem 0.35rem 0.85rem;
        }

        #sidebar .nav-link {
            color: #94a3b8;
            padding: 0.58rem 0.85rem;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 2px;
            transition: all 0.18s ease;
        }

        #sidebar .nav-link i {
            width: 22px;
            font-size: 0.98rem;
            margin-right: 9px;
            opacity: 0.85;
            transition: transform 0.18s ease;
        }

        #sidebar .nav-link:hover {
            color: #ffffff;
            background-color: var(--gudi-sidebar-hover);
        }

        #sidebar .nav-link:hover i {
            transform: scale(1.1);
        }

        #sidebar .nav-link.active {
            background: linear-gradient(135deg, #005a9c 0%, #0284c7 100%);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 90, 156, 0.35);
        }

        #sidebar .nav-link.active i {
            opacity: 1;
        }

        /* Main Viewport Column: Fixed 100vh with Fixed Header */
        #main-content {
            flex: 1;
            min-width: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background-color: var(--gudi-bg);
        }

        /* Fixed Top Header */
        .topbar {
            height: 64px;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--gudi-border);
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        /* Dedicated Content Scroller */
        .page-content-wrapper {
            flex-grow: 1;
            height: calc(100vh - 64px);
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        .page-content-wrapper > main {
            flex-grow: 1;
        }

        .footer-bar {
            flex-shrink: 0;
            padding-top: 1.5rem;
            padding-bottom: 0.5rem;
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
            <!-- Brand Header -->
            <div class="brand-header d-flex align-items-center justify-content-between">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none text-white">
                    <div class="rounded-3 bg-primary p-2 me-2.5 d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; background: linear-gradient(135deg, #005a9c 0%, #00a88f 100%) !important;">
                        <i class="fa-solid fa-flask-vial fa-lg text-white"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold tracking-wide" style="letter-spacing: 0.03em;">GUDI CHEMICALS</h6>
                        <small style="font-size: 0.68rem; color: #94a3b8; letter-spacing: 0.04em;">MANUFACTURING & GST ERP</small>
                    </div>
                </a>
                <button class="btn btn-sm text-white-50 d-lg-none" onclick="toggleSidebar()">
                    <i class="fa-solid fa-times fa-lg"></i>
                </button>
            </div>

            <!-- Scrollable Menu -->
            <div class="sidebar-menu-scroll">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>

                <div class="menu-category">POS & Sales Billing</div>
                <a href="{{ route('pos.index') }}" target="_blank" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cash-register text-success"></i> POS Fast Billing
                </a>
                <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice"></i> Sales Invoices
                </a>
                <a href="{{ route('returns.index') }}" class="nav-link {{ request()->routeIs('returns.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-arrow-rotate-left"></i> Sales Returns
                </a>

                <div class="menu-category">Chemical Manufacturing</div>
                <a href="{{ route('production.formulas.index') }}" class="nav-link {{ request()->routeIs('production.formulas.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-mortar-pestle"></i> Formulas (BOM)
                </a>
                <a href="{{ route('production.orders.index') }}" class="nav-link {{ request()->routeIs('production.orders.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-industry"></i> Production Batches
                </a>

                <div class="menu-category">Multi-Tier Inventory</div>
                <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-boxes-stacked"></i> Stock on Hand
                </a>
                <a href="{{ route('inventory.ledger') }}" class="nav-link {{ request()->routeIs('inventory.ledger') ? 'active' : '' }}">
                    <i class="fa-solid fa-list-check"></i> Stock Movement Ledger
                </a>
                <a href="{{ route('inventory.adjustments.index') }}" class="nav-link {{ request()->routeIs('inventory.adjustments.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sliders"></i> Stock Adjustments
                </a>

                <div class="menu-category">Purchasing & Vendor</div>
                <a href="{{ route('purchases.orders.index') }}" class="nav-link {{ request()->routeIs('purchases.orders.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cart-shopping"></i> Purchase Orders
                </a>
                <a href="{{ route('purchases.grn.index') }}" class="nav-link {{ request()->routeIs('purchases.grn.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck-ramp-box"></i> Goods Receipts (GRN)
                </a>
                <a href="{{ route('purchases.payments.index') }}" class="nav-link {{ request()->routeIs('purchases.payments.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-money-bill-transfer"></i> Vendor Payments
                </a>

                <div class="menu-category">Master Data</div>
                <a href="{{ route('masters.products.index') }}" class="nav-link {{ request()->routeIs('masters.products.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-box-archive"></i> Product Master
                </a>
                <a href="{{ route('masters.customers.index') }}" class="nav-link {{ request()->routeIs('masters.customers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> Customers
                </a>
                <a href="{{ route('masters.vendors.index') }}" class="nav-link {{ request()->routeIs('masters.vendors.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-handshake"></i> Vendors
                </a>
                <a href="{{ route('masters.categories.index') }}" class="nav-link {{ request()->routeIs('masters.categories.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i> Categories
                </a>
                <a href="{{ route('masters.units.index') }}" class="nav-link {{ request()->routeIs('masters.units.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-scale-balanced"></i> Units & Conversions
                </a>
                <a href="{{ route('promotions.index') }}" class="nav-link {{ request()->routeIs('promotions.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gift text-warning"></i> Promotions & Offers
                </a>

                <div class="menu-category">Finance & Intelligence</div>
                <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-receipt"></i> Expenses
                </a>
                <a href="{{ route('reports.sales') }}" class="nav-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> Sales Register
                </a>
                <a href="{{ route('reports.gst') }}" class="nav-link {{ request()->routeIs('reports.gst') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-shield text-info"></i> GSTR-1 Tax Summary
                </a>
                <a href="{{ route('reports.receivables') }}" class="nav-link {{ request()->routeIs('reports.receivables') ? 'active' : '' }}">
                    <i class="fa-solid fa-hand-holding-dollar text-success"></i> Customer Receivables
                </a>
                <a href="{{ route('reports.payables') }}" class="nav-link {{ request()->routeIs('reports.payables') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar text-danger"></i> Vendor Payables
                </a>
                <a href="{{ route('reports.collections') }}" class="nav-link {{ request()->routeIs('reports.collections') ? 'active' : '' }}">
                    <i class="fa-solid fa-wallet text-warning"></i> Daily Collections
                </a>
                <a href="{{ route('reports.inventory') }}" class="nav-link {{ request()->routeIs('reports.inventory') ? 'active' : '' }}">
                    <i class="fa-solid fa-warehouse"></i> Inventory Valuation
                </a>
                <a href="{{ route('reports.production') }}" class="nav-link {{ request()->routeIs('reports.production') ? 'active' : '' }}">
                    <i class="fa-solid fa-vial"></i> Production Yield & Cost
                </a>

                <div class="menu-category">System Administration</div>
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i> Company & Settings
                </a>
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-shield"></i> Users & Roles
                </a>
            </div>

            <!-- Footer User Profile Badge -->
            <div class="sidebar-user-footer">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 36px; height: 36px; font-weight: 700; background: linear-gradient(135deg, #005a9c 0%, #00a88f 100%);">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="mb-0 text-white text-truncate fw-semibold" style="font-size: 0.82rem;">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <small class="text-muted d-block text-truncate" style="font-size: 0.7rem;">{{ auth()->user()->role ?? 'Super Admin' }}</small>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-white-50 p-1" title="Sign out">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content Area with Fixed Header -->
        <div id="main-content">
            <!-- Fixed Top Navbar -->
            <header class="topbar">
                <div class="d-flex align-items-center">
                    <button class="btn btn-light btn-sm me-3 d-lg-none border" onclick="toggleSidebar()">
                        <i class="fa-solid fa-bars"></i>
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

            <!-- Scrollable Page Body (100vh - 64px) -->
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

                <!-- Footer Bar -->
                <footer class="footer-bar text-muted small d-flex justify-content-between align-items-center">
                    <span>&copy; {{ date('Y') }} <strong>Gudi Chemicals</strong>. All rights reserved.</span>
                    <span>Chemical Manufacturing, POS & GST ERP v1.0</span>
                </footer>
            </div>
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
    </script>
    @stack('scripts')
</body>
</html>
