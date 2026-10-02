@extends('layouts.app')

@section('title', 'Inventory Valuation Report')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-warehouse text-primary me-2"></i> Inventory Valuation Report</h4>
        <p class="text-muted small mb-0">Total stock on hand multiplied by moving average cost price for financial balance sheet</p>
    </div>
    <div class="mt-2 mt-md-0">
        <button class="btn btn-outline-secondary" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> Print Valuation
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm p-3 mb-4 bg-primary text-white">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="text-white-50 text-uppercase small fw-semibold">Total Warehouse Stock Valuation</span>
            <h2 class="fw-bold mb-0">₹{{ number_format($totalValuation, 2) }}</h2>
        </div>
        <div>
            <i class="fa-solid fa-boxes-stacked fa-3x text-white-50"></i>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Product / Chemical Item</th>
                        <th>Classification</th>
                        <th>Category</th>
                        <th class="text-end">Current Stock</th>
                        <th class="text-end">Unit Cost (₹)</th>
                        <th class="text-end">Stock Asset Value (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                        @php
                            $stock = $p->total_stock;
                            $val = $stock * (float)$p->purchase_cost;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $p->name }}</strong>
                                <div class="text-muted small">SKU: {{ $p->sku }}</div>
                            </td>
                            <td><span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $p->item_type)) }}</span></td>
                            <td>{{ $p->category?->name }}</td>
                            <td class="text-end fw-semibold">{{ number_format($stock, 2) }} {{ $p->unit?->code }}</td>
                            <td class="text-end">₹{{ number_format($p->purchase_cost, 2) }}</td>
                            <td class="text-end fw-bold text-success fs-6">₹{{ number_format($val, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="text-end fw-bold fs-6">Total Asset Valuation:</td>
                        <td class="text-end fw-bold fs-5 text-primary">₹{{ number_format($totalValuation, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
