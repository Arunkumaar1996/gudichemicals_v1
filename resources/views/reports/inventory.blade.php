@extends('layouts.app')

@section('title', 'Inventory Valuation & Stock Balance Report')

@push('styles')
<style>
    @media print {
        #sidebar, .topbar, .btn-print, .no-print, footer {
            display: none !important;
        }
        .page-content-wrapper {
            padding: 0 !important;
            overflow: visible !important;
            height: auto !important;
        }
        #main-content, #app-layout, body, html {
            height: auto !important;
            overflow: visible !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 no-print">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-warehouse text-primary me-2"></i> Inventory Valuation Report</h4>
        <p class="text-muted small mb-0">Total chemical stock-on-hand multiplied by purchase/manufactured cost for balance sheet assets.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('reports.inventory', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-outline-danger">
            <i class="fa-solid fa-file-pdf me-1.5"></i> Download PDF
        </a>
        <a href="{{ route('reports.inventory', ['export' => 'csv']) }}" class="btn btn-outline-success">
            <i class="fa-solid fa-file-excel me-1.5"></i> Export Inventory CSV
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="fa-solid fa-print me-1.5"></i> Print
        </button>
    </div>
</div>

<!-- Total Valuation Banner -->
<div class="card border-0 shadow-sm p-4 mb-4 bg-primary text-white" style="background: linear-gradient(135deg, #005a9c 0%, #002f54 100%) !important;">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="text-white-50 text-uppercase small fw-bold tracking-wider">Total Warehouse Inventory Asset Valuation</span>
            <h1 class="fw-bold mb-0 mt-1">₹{{ number_format($totalValuation, 2) }}</h1>
            <small class="text-white-50 mt-1 d-block"><i class="fa-solid fa-check-double me-1"></i> Based on moving weighted average costs across all chemical items</small>
        </div>
        <div class="d-none d-md-block opacity-50">
            <i class="fa-solid fa-boxes-stacked fa-4x text-white"></i>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-flask-vial text-primary me-2"></i> Product Valuation Breakdown</h6>
        <span class="badge bg-light text-muted border">{{ count($products) }} chemical items</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">SKU & Product Name</th>
                        <th>Classification</th>
                        <th>Category</th>
                        <th class="text-end">Current Stock</th>
                        <th class="text-end">Unit Cost</th>
                        <th class="text-end">Total Valuation</th>
                        <th class="text-center pe-3">Stock Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                        @php
                            $stock = $p->total_stock;
                            $val = $stock * (float)$p->purchase_cost;
                            $isLow = $stock <= $p->reorder_level;
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <strong>{{ $p->name }}</strong>
                                <div class="text-muted small">{{ $p->sku }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $p->item_type)) }}</span>
                            </td>
                            <td>{{ $p->category?->name ?: '—' }}</td>
                            <td class="text-end fw-bold {{ $stock <= 0 ? 'text-danger' : 'text-dark' }}">
                                {{ number_format($stock, 2) }} {{ $p->unit?->code }}
                            </td>
                            <td class="text-end text-muted">₹{{ number_format($p->purchase_cost, 2) }}</td>
                            <td class="text-end fw-bold text-success">₹{{ number_format($val, 2) }}</td>
                            <td class="text-center pe-3">
                                @if($isLow)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Reorder
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        Optimal
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
