@extends('layouts.app')

@section('title', 'Goods Receipt Note ' . $receipt->grn_number)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-truck-ramp-box text-primary me-2"></i> Goods Receipt Note: {{ $receipt->grn_number }}</h4>
        <span class="badge bg-success me-2">Inventory Posted</span>
        <span class="text-muted small">Date: {{ $receipt->receipt_date->format('d M Y') }}</span>
    </div>
    <div class="mt-2 mt-md-0 d-flex gap-2">
        @if($receipt->purchaseOrder)
            <a href="{{ route('purchases.orders.show', $receipt->purchase_order_id) }}" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-file-contract me-1"></i> View PO ({{ $receipt->purchaseOrder->po_number }})
            </a>
        @endif
        <a href="{{ route('purchases.grn.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to GRNs
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <h6 class="fw-bold text-muted small text-uppercase mb-2">Vendor / Supplier</h6>
            <h5 class="fw-bold text-dark mb-1">{{ $receipt->vendor?->company_name ?: $receipt->vendor?->name }}</h5>
            <div class="small">
                <strong>Challan #:</strong> {{ $receipt->supplier_challan_no ?: 'N/A' }} |
                <strong>Invoice #:</strong> {{ $receipt->supplier_invoice_no ?: 'N/A' }}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <h6 class="fw-bold text-muted small text-uppercase mb-2">Receiving Warehouse & Staff</h6>
            <h5 class="fw-bold text-dark mb-1">{{ $receipt->warehouse?->name }}</h5>
            <div class="small text-muted">
                Received by: <strong>{{ $receipt->receiver?->name ?: 'Storekeeper' }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold mb-0">Received Inventory Lots</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Chemical Item</th>
                        <th>Internal Batch #</th>
                        <th>Supplier Lot #</th>
                        <th class="text-end">Received Qty</th>
                        <th class="text-end">Unit Cost</th>
                        <th class="text-end">GST %</th>
                        <th class="text-end">Total Valuation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipt->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product?->name }}</strong>
                                <div class="text-muted small">SKU: {{ $item->product?->sku }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $item->batch_number ?: 'N/A' }}</span></td>
                            <td>{{ $item->supplier_lot_number ?: '-' }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($item->received_quantity, 2) }} {{ $item->product?->unit?->code }}</td>
                            <td class="text-end">₹{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="text-end">{{ number_format($item->tax_rate, 0) }}%</td>
                            <td class="text-end fw-bold">₹{{ number_format($item->total_cost, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
