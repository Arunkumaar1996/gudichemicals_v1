@extends('layouts.app')

@section('title', 'Purchase Order ' . $order->po_number)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-file-contract text-primary me-2"></i> Purchase Order: {{ $order->po_number }}</h4>
        <span class="badge {{ $order->status === 'received' ? 'bg-success' : ($order->status === 'approved' ? 'bg-primary' : 'bg-secondary') }} me-2">
            Status: {{ ucfirst($order->status) }}
        </span>
        <span class="text-muted small">Date: {{ $order->order_date->format('d M Y') }}</span>
    </div>
    <div class="mt-2 mt-md-0 d-flex gap-2">
        @if($order->status === 'draft')
            <form method="POST" action="{{ route('purchases.orders.approve', $order->id) }}">
                @csrf
                <button type="submit" class="btn btn-success btn-sm fw-semibold">
                    <i class="fa-solid fa-check me-1"></i> Approve PO
                </button>
            </form>
        @endif

        @if($order->status !== 'received' && $order->status !== 'cancelled')
            <a href="{{ route('purchases.grn.create', ['po_id' => $order->id]) }}" class="btn btn-primary btn-sm fw-semibold">
                <i class="fa-solid fa-truck-ramp-box me-1"></i> Receive Inward Goods (GRN)
            </a>
        @endif

        <a href="{{ route('purchases.orders.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Orders
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <h6 class="fw-bold text-muted small text-uppercase mb-2">Vendor Information</h6>
            <h5 class="fw-bold text-dark mb-1">{{ $order->vendor?->company_name ?: $order->vendor?->name }}</h5>
            <p class="text-muted small mb-1">{{ $order->vendor?->address ?: 'No address specified' }}</p>
            <div class="small">
                <strong>GSTIN:</strong> {{ $order->vendor?->gstin ?: 'Unregistered' }} |
                <strong>State:</strong> {{ $order->vendor?->state_name }} ({{ $order->vendor?->state_code }})
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <h6 class="fw-bold text-muted small text-uppercase mb-2">Delivery & Destination</h6>
            <h5 class="fw-bold text-dark mb-1">{{ $order->warehouse?->name }}</h5>
            <p class="text-muted small mb-1">{{ $order->warehouse?->location }}</p>
            <div class="small">
                <strong>Expected Date:</strong> {{ $order->expected_date ? $order->expected_date->format('d M Y') : 'Immediate' }}
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold mb-0">Order Line Items</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Item Description</th>
                        <th class="text-end">Ordered Qty</th>
                        <th class="text-end">Received Qty</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">GST %</th>
                        <th class="text-end">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product?->name }}</strong>
                                <div class="text-muted small">SKU: {{ $item->product?->sku }}</div>
                            </td>
                            <td class="text-end fw-semibold">{{ number_format($item->quantity, 2) }} {{ $item->unit?->code }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($item->received_quantity, 2) }} {{ $item->unit?->code }}</td>
                            <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end">{{ number_format($item->tax_rate, 0) }}%</td>
                            <td class="text-end fw-bold">₹{{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="text-end fw-semibold">Subtotal:</td>
                        <td class="text-end fw-bold">₹{{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end fw-semibold">GST Tax Total:</td>
                        <td class="text-end fw-bold">₹{{ number_format($order->tax_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end fw-bold fs-6">Grand Total:</td>
                        <td class="text-end fw-bold fs-6 text-primary">₹{{ number_format($order->grand_total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@if($order->goodsReceipts->isNotEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-truck-ramp-box text-success me-2"></i> Linked Goods Receipts (GRN)</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>GRN #</th>
                        <th>Receipt Date</th>
                        <th>Challan / Bill</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->goodsReceipts as $grn)
                        <tr>
                            <td><strong>{{ $grn->grn_number }}</strong></td>
                            <td>{{ $grn->receipt_date->format('d M Y') }}</td>
                            <td>{{ $grn->supplier_challan_no ?: 'Direct' }}</td>
                            <td><span class="badge bg-success">Stock Posted</span></td>
                            <td class="text-end">
                                <a href="{{ route('purchases.grn.show', $grn->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
