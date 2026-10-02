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
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --gudi-primary: #005a9c;
            --gudi-primary-dark: #003e6b;
            --gudi-secondary: #00a88f;
            --gudi-accent: #f59e0b;
            --gudi-bg: #f8fafc;
            --gudi-sidebar-bg: #0f172a;
            --gudi-sidebar-hover: #1e293b;
            --gudi-border: #e2e8f0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--gudi-bg);
            color: #334155;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: 260px;
            background-color: var(--gudi-sidebar-bg);
            min-height: 100vh;
            color: #94a3b8;
            transition: all 0.25s ease-in-out;
            z-index: 1000;
        }

        #sidebar .brand-header {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, #003e6b 0%, #005a9c 100%);
            color: #ffffff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        #sidebar .nav-link {
            color: #94a3b8;
            padding: 0.65rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            border-radius: 6px;
            margin: 2px 10px;
            transition: all 0.2s;
        }

        #sidebar .nav-link i {
            width: 24px;
            font-size: 1.05rem;
            margin-right: 10px;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            color: #ffffff;
            background-color: var(--gudi-sidebar-hover);
        }

        #sidebar .nav-link.active {
            background-color: var(--gudi-primary);
            color: #ffffff;
        }

        #sidebar .menu-category {
            font-size: 0.72rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: #64748b;
            padding: 1rem 1.5rem 0.35rem 1.5rem;
        }

        /* Main Content */
        #main-content {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid var(--gudi-border);
            padding: 0.75rem 1.5rem;
        }

        .btn-gudi-primary {
            background-color: var(--gudi-primary);
            border-color: var(--gudi-primary);
            color: #ffffff;
        }
        .btn-gudi-primary:hover {
            background-color: var(--gudi-primary-dark);
            border-color: var(--gudi-primary-dark);
            color: #ffffff;
        }

        .card-stat {
            border: 1px solid var(--gudi-border);
            border-radius: 10px;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .card-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07);
        }

        /* Responsive Mobile Drawer */
        @media (max-width: 991.98px) {
            #sidebar {
                position: fixed;
                left: -260px;
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
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }
            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex">

    <!-- Mobile Backdrop -->
    <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <nav id="sidebar" class="d-flex flex-column flex-shrink-0">
        <div class="brand-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-flask-vial fa-xl me-2 text-warning"></i>
                <div>
                    <h6 class="mb-0 fw-bold tracking-wide">GUDI CHEMICALS</h6>
                    <small style="font-size: 0.7rem; opacity: 0.85;">Manufacturing & GST ERP</small>
                </div>
            </div>
            <button class="btn btn-sm text-white d-lg-none" onclick="toggleSidebar()">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <div class="flex-grow-1 overflow-auto py-2">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>

            <div class="menu-category">POS & Billing</div>
            <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                <i class="fa-solid fa-cash-register text-success"></i> POS Fast Billing
            </a>
            <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice"></i> Sales Invoices
            </a>
            <a href="{{ route('returns.index') }}" class="nav-link {{ request()->routeIs('returns.*') ? 'active' : '' }}">
                <i class="fa-solid fa-arrow-rotate-left"></i> Sales Returns
            </a>

            <div class="menu-category">Manufacturing</div>
            <a href="{{ route('production.formulas.index') }}" class="nav-link {{ request()->routeIs('production.formulas.*') ? 'active' : '' }}">
                <i class="fa-solid fa-mortar-pestle"></i> Formulas (BOM)
            </a>
            <a href="{{ route('production.orders.index') }}" class="nav-link {{ request()->routeIs('production.orders.*') ? 'active' : '' }}">
                <i class="fa-solid fa-industry"></i> Production Batches
            </a>

            <div class="menu-category">Inventory</div>
            <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked"></i> Stock on Hand
            </a>
            <a href="{{ route('inventory.ledger') }}" class="nav-link {{ request()->routeIs('inventory.ledger') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check"></i> Stock Movement Ledger
            </a>
            <a href="{{ route('inventory.adjustments.index') }}" class="nav-link {{ request()->routeIs('inventory.adjustments.*') ? 'active' : '' }}">
                <i class="fa-solid fa-sliders"></i> Stock Adjustments
            </a>

            <div class="menu-category">Purchasing</div>
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

            <div class="menu-category">Finance & Reports</div>
            <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i> Expenses
            </a>
            <a href="{{ route('reports.sales') }}" class="nav-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> Sales & GST Reports
            </a>
            <a href="{{ route('reports.inventory') }}" class="nav-link {{ request()->routeIs('reports.inventory') ? 'active' : '' }}">
                <i class="fa-solid fa-warehouse"></i> Inventory Valuation
            </a>
            <a href="{{ route('reports.production') }}" class="nav-link {{ request()->routeIs('reports.production') ? 'active' : '' }}">
                <i class="fa-solid fa-vial"></i> Production Yield & Cost
            </a>

            <div class="menu-category">System</div>
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> Company & Settings
            </a>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-shield"></i> Users & Roles
            </a>
        </div>

        <div class="p-3 border-top border-secondary border-opacity-25" style="background: #090d16;">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-weight: 600;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="mb-0 text-white text-truncate fw-semibold" style="font-size: 0.85rem;">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <small class="text-muted d-block text-truncate" style="font-size: 0.72rem;">{{ auth()->user()->role ?? 'Super Admin' }}</small>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div id="main-content">
        <!-- Top Navbar -->
        <header class="topbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary btn-sm me-3 d-lg-none" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="d-none d-md-flex align-items-center text-muted small">
                    <i class="fa-solid fa-shield-halved text-success me-1"></i> GSTIN: <strong>{{ \App\Models\CompanySetting::current()->gstin ?? '27AAACG1234D1Z5' }}</strong>
                    <span class="mx-2">|</span>
                    <i class="fa-regular fa-calendar me-1"></i> FY: <strong>{{ \App\Models\CompanySetting::current()->fy_code ?? '2026-27' }}</strong>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('pos.index') }}" class="btn btn-success btn-sm px-3 fw-semibold">
                    <i class="fa-solid fa-bolt me-1"></i> New Bill / POS
                </a>

                <div class="dropdown">
                    <button class="btn btn-light btn-sm border dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-user me-1 text-primary"></i>
                        <span>{{ auth()->user()->name ?? 'Admin' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fa-solid fa-id-badge me-2"></i> Profile & Security</a></li>
                        <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="fa-solid fa-sliders me-2"></i> ERP Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Body -->
        <main class="p-4 flex-grow-1">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-check fa-lg me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="fa-solid fa-triangle-exclamation fa-lg me-2"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
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

        <!-- Footer -->
        <footer class="bg-white border-top py-2 px-4 text-muted small d-flex justify-content-between align-items-center">
            <span>&copy; {{ date('Y') }} <strong>Gudi Chemicals</strong>. All rights reserved.</span>
            <span>Chemical Manufacturing & GST ERP v1.0</span>
        </footer>
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
