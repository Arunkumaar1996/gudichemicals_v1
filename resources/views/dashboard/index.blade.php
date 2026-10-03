@extends('layouts.app')

@section('title', 'Operations Dashboard')

@section('content')
<style>
    /* Dashboard Modern Aesthetics & Card Styles */
    .dashboard-hero-banner {
        background: linear-gradient(135deg, #091326 0%, #112240 50%, #004b87 100%);
        border-radius: 12px;
        color: #ffffff;
        padding: 1.25rem 1.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .dashboard-hero-banner::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.18) 0%, rgba(0,0,0,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .dashboard-hero-banner::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: 20%;
        width: 280px;
        height: 280px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.14) 0%, rgba(0,0,0,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* KPI Primary Stat Cards */
    .stat-metric-card {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        padding: 1.1rem 1.25rem;
        position: relative;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .stat-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    }
    .stat-metric-card.card-sales { border-top: 3.5px solid #10b981; }
    .stat-metric-card.card-receivables { border-top: 3.5px solid #0284c7; }
    .stat-metric-card.card-payables { border-top: 3.5px solid #f59e0b; }
    .stat-metric-card.card-batches { border-top: 3.5px solid #8b5cf6; }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }
    .icon-sales { background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.25) 100%); color: #059669; }
    .icon-receivables { background: linear-gradient(135deg, rgba(2, 132, 199, 0.15) 0%, rgba(56, 189, 248, 0.25) 100%); color: #0284c7; }
    .icon-payables { background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.25) 100%); color: #d97706; }
    .icon-batches { background: linear-gradient(135deg, rgba(139, 92, 246, 0.15) 0%, rgba(124, 58, 237, 0.25) 100%); color: #7c3aed; }

    /* Quick Launchpad Action Tiles */
    .quick-launch-tile {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        padding: 0.75rem 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: #334155;
        transition: all 0.18s ease-in-out;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }
    .quick-launch-tile:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        color: #005a9c;
    }
    .quick-tile-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        color: #ffffff;
        flex-shrink: 0;
    }

    /* Pulse dot */
    .live-pulse-dot {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.35);
        animation: pulseAnimation 1.8s infinite;
    }
    @keyframes pulseAnimation {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* Uniform Default Height Dashboard Tables */
    .dashboard-table-container {
        height: 310px;
        min-height: 310px;
        max-height: 310px;
        overflow-y: auto;
        overflow-x: auto;
        position: relative;
    }
    .dashboard-table-container thead th {
        position: sticky;
        top: 0;
        background-color: #f8fafc;
        z-index: 2;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        border-bottom: 2px solid #e2e8f0;
    }
    .dashboard-table-container table {
        margin-bottom: 0;
    }
    .dashboard-table-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .dashboard-table-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .dashboard-table-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .dashboard-table-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<!-- Modern Hero Banner Section -->
<div class="dashboard-hero-banner mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                <span class="badge bg-success bg-opacity-25 text-white border border-success border-opacity-25 py-1 px-2.5 d-flex align-items-center">
                    <span class="live-pulse-dot me-1.5"></span> Live Operations Hub
                </span>
                <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-10 py-1 px-2">
                    <i class="fa-regular fa-calendar-check me-1 text-info"></i> {{ date('l, d F Y') }}
                </span>
                <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-10 py-1 px-2">
                    <i class="fa-solid fa-industry me-1 text-warning"></i> FY: {{ \App\Models\CompanySetting::current()->fy_code ?? '2026-27' }}
                </span>
            </div>
            <h4 class="fw-bold mb-1 text-white">Gudi Chemicals — Operations Dashboard</h4>
            <p class="text-white-50 small mb-0">Real-time production, stock, POS sales, and GST overview</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('pos.index') }}" target="_blank" class="btn btn-success fw-bold shadow-sm d-flex align-items-center py-2 px-3" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border: none;">
                <i class="fa-solid fa-bolt me-1.5 text-warning"></i> Open POS Billing Desk
            </a>
            @can('production.create')
            <a href="{{ route('production.orders.create') }}" class="btn btn-light fw-bold shadow-sm d-flex align-items-center py-2 px-3 text-primary border-0" style="background: #ffffff;">
                <i class="fa-solid fa-plus me-1.5 text-primary"></i> New Production Batch
            </a>
            @endcan
        </div>
    </div>
</div>

<!-- Primary Stats Metric Cards (Enhanced with Vibrant Icons & Accent Borders) -->
<div class="row g-3 mb-4">
    <!-- Today's Gross Sales -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-metric-card card-sales h-100">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Today's Gross Sales</span>
                    <h3 class="fw-bold my-1 text-dark" style="font-family: var(--pos-mono, inherit); font-size: 1.45rem;">
                        ₹{{ number_format($todayGrossSales, 2) }}
                    </h3>
                    <div class="d-flex align-items-center text-success small fw-semibold" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-file-invoice me-1"></i>
                        <span>{{ $todayInvoicesCount }} Invoices Posted</span>
                    </div>
                </div>
                <div class="stat-icon-wrapper icon-sales shadow-sm">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.7rem;">
                <span>Taxable: ₹{{ number_format($todayTaxableSales, 2) }}</span>
                <a href="{{ route('invoices.index') }}" class="text-decoration-none text-success fw-semibold">View Sales &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Receivables (Customers) -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-metric-card card-receivables h-100">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Receivables (Customers)</span>
                    <h3 class="fw-bold my-1 text-dark" style="font-family: var(--pos-mono, inherit); font-size: 1.45rem;">
                        ₹{{ number_format($totalCustomerReceivables, 2) }}
                    </h3>
                    <div class="d-flex align-items-center text-info small fw-semibold" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-users me-1"></i>
                        <span>Outstanding Customer Credit</span>
                    </div>
                </div>
                <div class="stat-icon-wrapper icon-receivables shadow-sm">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
            <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.7rem;">
                <span>Ledger Balance</span>
                <a href="{{ route('masters.customers.index') }}" class="text-decoration-none text-info fw-semibold">Customers &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Payables (Vendors) -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-metric-card card-payables h-100">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Payables (Vendors)</span>
                    <h3 class="fw-bold my-1 text-dark" style="font-family: var(--pos-mono, inherit); font-size: 1.45rem;">
                        ₹{{ number_format($totalVendorPayables, 2) }}
                    </h3>
                    <div class="d-flex align-items-center text-warning small fw-semibold" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-truck-field me-1"></i>
                        <span>Outstanding Vendor Bills</span>
                    </div>
                </div>
                <div class="stat-icon-wrapper icon-payables shadow-sm">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
            </div>
            <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.7rem;">
                <span>{{ $pendingPoCount }} Open Purchase Orders</span>
                <a href="{{ route('purchases.payments.index') }}" class="text-decoration-none text-warning fw-semibold">Payments &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Active Production Batches -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-metric-card card-batches h-100">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Active Batches</span>
                    <h3 class="fw-bold my-1 text-dark" style="font-family: var(--pos-mono, inherit); font-size: 1.45rem;">
                        {{ $pendingBatchesCount }}
                    </h3>
                    <div class="d-flex align-items-center text-purple small fw-semibold" style="font-size: 0.72rem; color: #7c3aed;">
                        <i class="fa-solid fa-industry me-1"></i>
                        <span>In Compounding / QC</span>
                    </div>
                </div>
                <div class="stat-icon-wrapper icon-batches shadow-sm">
                    <i class="fa-solid fa-flask-vial"></i>
                </div>
            </div>
            <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.7rem;">
                <span>Batch Processing</span>
                @can('production.view')
                <a href="{{ route('production.orders.index') }}" class="text-decoration-none fw-semibold" style="color: #7c3aed;">Batch Orders &rarr;</a>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Quick ERP Action Tiles Launchpad (Vibrant Icons & Shortcuts) -->
<div class="row g-2.5 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('pos.index') }}" target="_blank" class="quick-launch-tile">
            <div class="quick-tile-icon shadow-sm" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div class="overflow-hidden">
                <div class="fw-bold text-truncate" style="font-size: 0.78rem;">POS Billing</div>
                <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Fast GST Desk</small>
            </div>
        </a>
    </div>

    @can('production.view')
    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('production.formulas.index') }}" class="quick-launch-tile">
            <div class="quick-tile-icon shadow-sm" style="background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);">
                <i class="fa-solid fa-vial"></i>
            </div>
            <div class="overflow-hidden">
                <div class="fw-bold text-truncate" style="font-size: 0.78rem;">Formulas (BOM)</div>
                <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Chemical Recipes</small>
            </div>
        </a>
    </div>
    @endcan

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('inventory.index') }}" class="quick-launch-tile">
            <div class="quick-tile-icon shadow-sm" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="overflow-hidden">
                <div class="fw-bold text-truncate" style="font-size: 0.78rem;">Inventory</div>
                <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Stock on Hand</small>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('purchases.grn.index') }}" class="quick-launch-tile">
            <div class="quick-tile-icon shadow-sm" style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <div class="overflow-hidden">
                <div class="fw-bold text-truncate" style="font-size: 0.78rem;">Inward (GRN)</div>
                <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Goods Receipts</small>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('invoices.index') }}" class="quick-launch-tile">
            <div class="quick-tile-icon shadow-sm" style="background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div class="overflow-hidden">
                <div class="fw-bold text-truncate" style="font-size: 0.78rem;">Sales Invoices</div>
                <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">B2B & Retail Bills</small>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('reports.sales') }}" class="quick-launch-tile">
            <div class="quick-tile-icon shadow-sm" style="background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%);">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div class="overflow-hidden">
                <div class="fw-bold text-truncate" style="font-size: 0.78rem;">GST Reports</div>
                <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">GSTR-1 & PDF</small>
            </div>
        </a>
    </div>
</div>

<!-- Interactive Charts Row -->
<div class="row g-3 mb-4">
    <!-- 14-Day Sales Revenue & Invoices Trend -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-chart-area text-primary me-2"></i> 14-Day Sales Revenue & Invoices Trend
                    </h6>
                    <small class="text-muted">Daily billed turnover (₹) and invoice transaction volume</small>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold px-2 py-1">
                    <i class="fa-regular fa-clock me-1"></i> Last 14 Days
                </span>
            </div>
            <div class="card-body">
                <div style="height: 280px; position: relative;">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Category Distribution -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-chart-pie text-success me-2"></i> Category Revenue
                    </h6>
                    <small class="text-muted">Turnover mix by chemical category</small>
                </div>
                <span class="badge bg-light text-muted border px-2 py-1">All Time</span>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div style="height: 240px; position: relative;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Collections Breakdown & Low Stock Alert -->
<div class="row g-3 mb-4">
    <!-- Today's Payment Collections -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-wallet text-primary me-2"></i> Today's Payment Collections</h6>
                <span class="badge bg-light text-dark border px-2 py-1">{{ date('d M Y') }}</span>
            </div>
            <div class="card-body">
                <div class="row g-2 text-center mb-3">
                    <div class="col-6">
                        <div class="p-2 border rounded bg-white shadow-2xs" style="border-left: 3px solid #10b981 !important;">
                            <div class="d-flex align-items-center justify-content-center gap-1.5 text-muted small mb-0.5">
                                <i class="fa-solid fa-money-bill-wave text-success"></i>
                                <span class="fw-semibold">Cash</span>
                            </div>
                            <span class="fw-bold text-success fs-6" style="font-family: var(--pos-mono);">₹{{ number_format($todayPayments['cash'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-white shadow-2xs" style="border-left: 3px solid #0284c7 !important;">
                            <div class="d-flex align-items-center justify-content-center gap-1.5 text-muted small mb-0.5">
                                <i class="fa-solid fa-qrcode text-primary"></i>
                                <span class="fw-semibold">UPI / QR</span>
                            </div>
                            <span class="fw-bold text-primary fs-6" style="font-family: var(--pos-mono);">₹{{ number_format($todayPayments['upi'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-white shadow-2xs" style="border-left: 3px solid #06b6d4 !important;">
                            <div class="d-flex align-items-center justify-content-center gap-1.5 text-muted small mb-0.5">
                                <i class="fa-solid fa-credit-card text-info"></i>
                                <span class="fw-semibold">Card</span>
                            </div>
                            <span class="fw-bold text-info fs-6" style="font-family: var(--pos-mono);">₹{{ number_format($todayPayments['card'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-white shadow-2xs" style="border-left: 3px solid #475569 !important;">
                            <div class="d-flex align-items-center justify-content-center gap-1.5 text-muted small mb-0.5">
                                <i class="fa-solid fa-building-columns text-dark"></i>
                                <span class="fw-semibold">Bank Transfer</span>
                            </div>
                            <span class="fw-bold text-dark fs-6" style="font-family: var(--pos-mono);">₹{{ number_format($todayPayments['bank_transfer'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-primary bg-primary-subtle border-0 py-2 px-3 small d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-semibold text-primary"><i class="fa-solid fa-calculator me-1.5"></i> Total Collected Today:</span>
                    <strong class="fs-6 text-primary" style="font-family: var(--pos-mono);">₹{{ number_format(array_sum($todayPayments), 2) }}</strong>
                </div>

                <!-- 30-Day Collections Mode Donut -->
                <div style="height: 140px; position: relative;">
                    <canvas id="paymentsModeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Raw Chemical & Finished Goods Low Stock Alerts -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-triangle-exclamation text-danger me-1.5"></i> Stock Reorder Level Alerts
                    </h6>
                    @if($lowStockTotalCount > 0)
                        <span class="badge bg-danger rounded-pill px-2 py-1"><i class="fa-solid fa-bell me-1"></i> {{ $lowStockTotalCount }} Items</span>
                    @else
                        <span class="badge bg-success rounded-pill px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> Optimal</span>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('inventory.index', ['filter' => 'low_stock']) }}" class="btn btn-sm btn-outline-danger d-flex align-items-center">
                        <i class="fa-solid fa-bell me-1"></i> Filter Low Stock
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-secondary">All Stock</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive dashboard-table-container">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Item / SKU</th>
                                <th>Type</th>
                                <th class="text-end">Current Stock</th>
                                <th class="text-end">Reorder Level</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $prod)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $prod->name }}</div>
                                        <div class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $prod->sku }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fa-solid fa-flask text-secondary me-0.5"></i>
                                            {{ ucwords(str_replace('_', ' ', $prod->item_type)) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-danger">{{ number_format($prod->total_stock, 2) }} {{ $prod->unit?->code }}</td>
                                    <td class="text-end text-muted">{{ number_format($prod->reorder_level, 2) }} {{ $prod->unit?->code }}</td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold">
                                            <i class="fa-solid fa-arrow-down me-1"></i> Low Stock
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('inventory.index', ['search' => $prod->sku]) }}" class="btn btn-xs btn-outline-primary py-0.5 px-2" title="Adjust stock or reorder threshold">
                                            <i class="fa-solid fa-pen-to-square me-0.5"></i> Set Qty
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr style="height: 240px;">
                                    <td colspan="6" class="text-center text-muted align-middle py-4">
                                        <i class="fa-regular fa-circle-check text-success fa-2x d-block mb-1"></i>
                                        All raw chemicals and finished products are safely above reorder thresholds.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($lowStockTotalCount > count($lowStockProducts))
                <div class="card-footer bg-light py-2 text-center small">
                    <a href="{{ route('inventory.index', ['filter' => 'low_stock']) }}" class="text-decoration-none fw-semibold text-danger">
                        View all {{ $lowStockTotalCount }} low stock items in Inventory Manager &rarr;
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Invoices & Batches Row -->
<div class="row g-3">
    <!-- Recent Invoices -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm" style="border-radius: 10px;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-receipt text-primary me-2"></i> Recent Invoices</h6>
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold">View All &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive dashboard-table-container">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices as $inv)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $inv->invoice_number }}</strong>
                                        <div class="text-muted" style="font-size: 0.70rem;">{{ $inv->invoice_date->format('d M Y') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 160px;">{{ $inv->customer?->name }}</div>
                                    </td>
                                    <td class="fw-bold" style="font-family: var(--pos-mono);">₹{{ number_format($inv->grand_total, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $inv->payment_status === 'paid' ? 'bg-success-subtle text-success border border-success-subtle' : ($inv->payment_status === 'partially_paid' ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle') }}">
                                            {{ ucfirst($inv->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-primary py-0.5 px-2">
                                            <i class="fa-solid fa-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr style="height: 240px;"><td colspan="5" class="text-center text-muted align-middle py-3">No invoices posted yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Production Batches -->
    @can('production.view')
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm" style="border-radius: 10px;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-industry text-success me-2"></i> Recent Production Batches</h6>
                <a href="{{ route('production.orders.index') }}" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold text-success">View All &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive dashboard-table-container">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Batch #</th>
                                <th>Chemical Product</th>
                                <th>Planned Qty</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBatches as $batch)
                                <tr>
                                    <td>
                                        <strong class="text-dark">{{ $batch->batch_number }}</strong>
                                        <div class="text-muted" style="font-size: 0.70rem;">{{ $batch->order_date->format('d M Y') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 170px;">{{ $batch->outputProduct?->name }}</div>
                                    </td>
                                    <td class="fw-semibold">{{ number_format($batch->planned_qty, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $batch->status === 'completed' ? 'bg-success-subtle text-success border border-success-subtle' : ($batch->status === 'in_progress' ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle') }}">
                                             {{ ucfirst(str_replace('_', ' ', $batch->status)) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('production.orders.show', $batch->id) }}" class="btn btn-sm btn-outline-primary py-0.5 px-2">
                                            <i class="fa-solid fa-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr style="height: 240px;"><td colspan="5" class="text-center text-muted align-middle py-3">No production orders initiated yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection

@push('scripts')
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Sales Revenue & Invoices Combo Trend Chart
    const trendCtx = document.getElementById('salesTrendChart');
    if (trendCtx) {
        const trendLabels = @json($chartLabels);
        const trendRevenues = @json($chartRevenues);
        const trendCounts = @json($chartCounts);

        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        type: 'line',
                        label: 'Invoices Count',
                        data: trendCounts,
                        borderColor: '#0284c7',
                        backgroundColor: '#0284c7',
                        yAxisID: 'yCount',
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        borderWidth: 2,
                    },
                    {
                        type: 'bar',
                        label: 'Gross Sales (₹)',
                        data: trendRevenues,
                        backgroundColor: 'rgba(0, 90, 156, 0.78)',
                        hoverBackgroundColor: 'rgba(0, 90, 156, 0.95)',
                        borderColor: '#005a9c',
                        borderRadius: 5,
                        yAxisID: 'yRevenue',
                        borderWidth: 1,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    },
                    yRevenue: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            callback: function(value) {
                                return '₹' + Number(value).toLocaleString('en-IN');
                            },
                            font: { size: 10 }
                        },
                        title: {
                            display: true,
                            text: 'Revenue (₹)',
                            font: { size: 10, weight: 'bold' }
                        }
                    },
                    yCount: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            stepSize: 1,
                            font: { size: 10 }
                        },
                        title: {
                            display: true,
                            text: 'Invoice Count',
                            font: { size: 10, weight: 'bold' }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { size: 10 }, usePointStyle: true, boxWidth: 8 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.yAxisID === 'yRevenue') {
                                    return ' Gross Sales: ₹' + Number(context.raw).toLocaleString('en-IN', {minimumFractionDigits: 2});
                                }
                                return ' Invoices: ' + context.raw;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Product Category Revenue Distribution (Donut Chart with Vibrant Palette)
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        const catData = @json($categorySales);
        const catLabels = Object.keys(catData);
        const catValues = Object.values(catData);

        const vibrantPalette = [
            '#0284c7', // Sky Blue
            '#10b981', // Emerald
            '#8b5cf6', // Violet
            '#f59e0b', // Amber
            '#ec4899', // Pink
            '#06b6d4', // Cyan
            '#64748b'  // Slate
        ];

        if (catLabels.length === 0) {
            catLabels.push('No Sales Recorded');
            catValues.push(1);
        }

        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catValues,
                    backgroundColor: vibrantPalette.slice(0, catLabels.length),
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 10 }, boxWidth: 10, usePointStyle: true }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ₹' + Number(context.raw).toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Payment Methods Distribution (Mini Donut)
    const paymentsCtx = document.getElementById('paymentsModeChart');
    if (paymentsCtx) {
        const pmData = @json($paymentMethodsSplit);
        const pmLabels = Object.keys(pmData).map(k => k.replace('_', ' ').toUpperCase());
        const pmValues = Object.values(pmData);

        const pmColors = {
            'CASH': '#10b981',
            'UPI': '#0284c7',
            'CARD': '#06b6d4',
            'BANK TRANSFER': '#475569',
            'CREDIT': '#f59e0b'
        };
        const bgColors = pmLabels.map(l => pmColors[l] || '#94a3b8');

        if (pmLabels.length === 0) {
            pmLabels.push('No collections yet');
            pmValues.push(1);
            bgColors.push('#e2e8f0');
        }

        new Chart(paymentsCtx, {
            type: 'doughnut',
            data: {
                labels: pmLabels,
                datasets: [{
                    data: pmValues,
                    backgroundColor: bgColors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { font: { size: 9 }, boxWidth: 8, usePointStyle: true }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ₹' + Number(context.raw).toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
