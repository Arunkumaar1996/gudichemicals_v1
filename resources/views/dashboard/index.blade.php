@extends('layouts.app')

@section('title', 'Operations Dashboard')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Gudi Chemicals — Operations Dashboard</h4>
        <p class="text-muted small mb-0">Real-time production, stock, POS sales, and GST overview</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2">
        <a href="{{ route('pos.index') }}" class="btn btn-success fw-semibold shadow-sm">
            <i class="fa-solid fa-bolt me-1"></i> Open POS Billing Desk
        </a>
        <a href="{{ route('production.orders.create') }}" class="btn btn-primary fw-semibold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> New Production Batch
        </a>
    </div>
</div>

<!-- Primary Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-stat border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Today's Gross Sales</span>
                    <h3 class="fw-bold my-1 text-dark">₹{{ number_format($todayGrossSales, 2) }}</h3>
                    <small class="text-success"><i class="fa-solid fa-file-invoice"></i> {{ $todayInvoicesCount }} Invoices Posted</small>
                </div>
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                    <i class="fa-solid fa-chart-line fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-stat border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Receivables (Customers)</span>
                    <h3 class="fw-bold my-1 text-dark">₹{{ number_format($totalCustomerReceivables, 2) }}</h3>
                    <small class="text-muted"><i class="fa-solid fa-users"></i> Outstanding Credit</small>
                </div>
                <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                    <i class="fa-solid fa-hand-holding-dollar fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-stat border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Payables (Vendors)</span>
                    <h3 class="fw-bold my-1 text-dark">₹{{ number_format($totalVendorPayables, 2) }}</h3>
                    <small class="text-muted"><i class="fa-solid fa-truck"></i> Outstanding Invoices</small>
                </div>
                <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                    <i class="fa-solid fa-credit-card fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-stat border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Active Batches</span>
                    <h3 class="fw-bold my-1 text-dark">{{ $pendingBatchesCount }}</h3>
                    <small class="text-secondary"><i class="fa-solid fa-industry"></i> In Compounding / QC</small>
                </div>
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                    <i class="fa-solid fa-flask-vial fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Charts Row -->
<div class="row g-3 mb-4">
    <!-- 14-Day Sales Revenue & Invoices Trend -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-chart-area text-primary me-2"></i> 14-Day Sales Revenue & Invoices Trend
                    </h6>
                    <small class="text-muted">Daily billed turnover (₹) and invoice transaction volume</small>
                </div>
                <span class="badge bg-light text-primary border border-primary-subtle fw-semibold">Last 14 Days</span>
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
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-chart-pie text-success me-2"></i> Category Revenue
                    </h6>
                    <small class="text-muted">Turnover mix by chemical category</small>
                </div>
                <span class="badge bg-light text-muted border">All Time</span>
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
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-wallet text-primary me-2"></i> Today's Payment Collections</h6>
                <span class="badge bg-light text-dark border">{{ date('d M Y') }}</span>
            </div>
            <div class="card-body">
                <div class="row g-2 text-center mb-3">
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Cash</small>
                            <span class="fw-bold text-success">₹{{ number_format($todayPayments['cash'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">UPI</small>
                            <span class="fw-bold text-primary">₹{{ number_format($todayPayments['upi'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Card</small>
                            <span class="fw-bold text-info">₹{{ number_format($todayPayments['card'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Bank Transfer</small>
                            <span class="fw-bold text-dark">₹{{ number_format($todayPayments['bank_transfer'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-secondary py-2 px-3 small d-flex justify-content-between align-items-center mb-3">
                    <span>Total Collected Today:</span>
                    <strong class="fs-6 text-primary">₹{{ number_format(array_sum($todayPayments), 2) }}</strong>
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
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-triangle-exclamation text-danger me-1"></i> Stock Reorder Level Alerts
                    </h6>
                    @if($lowStockTotalCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $lowStockTotalCount }} Items</span>
                    @else
                        <span class="badge bg-success rounded-pill">Optimal</span>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('inventory.index', ['filter' => 'low_stock']) }}" class="btn btn-sm btn-outline-danger">
                        <i class="fa-solid fa-bell me-1"></i> Filter Low Stock
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-secondary">All Stock</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
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
                                        <strong>{{ $prod->name }}</strong>
                                        <div class="text-muted small font-monospace">{{ $prod->sku }}</div>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $prod->item_type)) }}</span></td>
                                    <td class="text-end fw-bold text-danger">{{ number_format($prod->total_stock, 2) }} {{ $prod->unit?->code }}</td>
                                    <td class="text-end">{{ number_format($prod->reorder_level, 2) }} {{ $prod->unit?->code }}</td>
                                    <td>
                                        <span class="badge bg-danger">
                                            <i class="fa-solid fa-arrow-down me-1"></i> Low Stock
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('inventory.index', ['search' => $prod->sku]) }}" class="btn btn-xs btn-outline-primary py-0 px-2" title="Adjust stock or reorder threshold">
                                            <i class="fa-solid fa-pen-to-square"></i> Set Qty
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fa-regular fa-circle-check text-success fa-2x d-block mb-1"></i>
                                        All raw chemicals and finished products are above reorder thresholds.
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
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-receipt text-primary me-2"></i> Recent Invoices</h6>
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
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
                                        <strong>{{ $inv->invoice_number }}</strong>
                                        <div class="text-muted small">{{ $inv->invoice_date->format('d M Y') }}</div>
                                    </td>
                                    <td>{{ $inv->customer?->name }}</td>
                                    <td class="fw-bold">₹{{ number_format($inv->grand_total, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $inv->payment_status === 'paid' ? 'bg-success' : ($inv->payment_status === 'partially_paid' ? 'bg-warning' : 'bg-danger') }}">
                                            {{ ucfirst($inv->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2">
                                            <i class="fa-solid fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No invoices posted yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Production Batches -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-industry text-success me-2"></i> Recent Production Batches</h6>
                <a href="{{ route('production.orders.index') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
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
                                        <strong>{{ $batch->batch_number }}</strong>
                                        <div class="text-muted small">{{ $batch->order_date->format('d M Y') }}</div>
                                    </td>
                                    <td>{{ $batch->outputProduct?->name }}</td>
                                    <td>{{ number_format($batch->planned_qty, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $batch->status === 'completed' ? 'bg-success' : ($batch->status === 'in_progress' ? 'bg-info' : 'bg-secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $batch->status)) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('production.orders.show', $batch->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2">
                                            <i class="fa-solid fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No production orders initiated yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        borderWidth: 2,
                    },
                    {
                        type: 'bar',
                        label: 'Gross Sales (₹)',
                        data: trendRevenues,
                        backgroundColor: 'rgba(0, 90, 156, 0.75)',
                        hoverBackgroundColor: 'rgba(0, 90, 156, 0.95)',
                        borderColor: '#005a9c',
                        borderRadius: 4,
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
                        ticks: { font: { size: 11 } }
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
                            font: { size: 11, weight: 'bold' }
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
                            font: { size: 11, weight: 'bold' }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.yAxisID === 'yRevenue') {
                                    return 'Sales: ₹' + Number(context.parsed.y).toLocaleString('en-IN', {minimumFractionDigits: 2});
                                }
                                return 'Invoices: ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Product Category Revenue Doughnut Chart
    const catCtx = document.getElementById('categoryChart');
    if (catCtx) {
        const catData = @json($categorySales);
        const catLabels = Object.keys(catData);
        const catValues = Object.values(catData);

        const defaultLabels = catLabels.length > 0 ? catLabels : ['General Chemicals'];
        const defaultValues = catValues.length > 0 ? catValues : [1];

        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: defaultLabels,
                datasets: [{
                    data: defaultValues,
                    backgroundColor: [
                        '#005a9c', '#10b981', '#f59e0b', '#8b5cf6',
                        '#ec4899', '#06b6d4', '#64748b'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ₹' + Number(context.parsed).toLocaleString('en-IN', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }

    // 3. Payment Methods Split Mini Doughnut Chart
    const payCtx = document.getElementById('paymentsModeChart');
    if (payCtx) {
        const payData = @json($paymentMethodsSplit);
        const payLabels = Object.keys(payData).map(k => k.replace('_', ' ').toUpperCase());
        const payValues = Object.values(payData);

        const defaultLabels = payLabels.length > 0 ? payLabels : ['Cash', 'UPI'];
        const defaultValues = payValues.length > 0 ? payValues : [100, 0];

        new Chart(payCtx, {
            type: 'doughnut',
            data: {
                labels: defaultLabels,
                datasets: [{
                    data: defaultValues,
                    backgroundColor: ['#10b981', '#005a9c', '#06b6d4', '#475569'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ₹' + Number(context.parsed).toLocaleString('en-IN', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }
});
</script>
@endpush
