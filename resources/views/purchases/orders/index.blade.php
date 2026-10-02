@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-cart-shopping text-primary me-2"></i> Purchase Orders</h4>
        <p class="text-muted small mb-0">Raw chemical, acid and packaging procurement orders</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="{{ route('purchases.orders.create') }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Create Purchase Order
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>PO Number</th>
                        <th>Vendor / Supplier</th>
                        <th>Warehouse</th>
                        <th>Order Date</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">GST</th>
                        <th class="text-end">Grand Total</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $po)
                        <tr>
                            <td><strong>{{ $po->po_number }}</strong></td>
                            <td>{{ $po->vendor?->company_name ?: $po->vendor?->name }}</td>
                            <td>{{ $po->warehouse?->name }}</td>
                            <td>{{ $po->order_date->format('d M Y') }}</td>
                            <td class="text-end">₹{{ number_format($po->subtotal, 2) }}</td>
                            <td class="text-end">₹{{ number_format($po->tax_total, 2) }}</td>
                            <td class="text-end fw-bold">₹{{ number_format($po->grand_total, 2) }}</td>
                            <td>
                                <span class="badge {{ $po->status === 'received' ? 'bg-success' : ($po->status === 'approved' ? 'bg-primary' : ($po->status === 'partial' ? 'bg-warning' : 'bg-secondary')) }}">
                                    {{ ucfirst($po->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('purchases.orders.show', $po->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No purchase orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
