@extends('layouts.app')

@section('title', 'Goods Receipt Notes (GRN)')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-truck-ramp-box text-primary me-2"></i> Goods Receipt Notes (GRN)</h4>
        <p class="text-muted small mb-0">Inward raw chemical shipments, supplier lots, batch tagging and stock reception</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="{{ route('purchases.grn.create') }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Receive Inward Goods (GRN)
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>GRN Number</th>
                        <th>Vendor / Supplier</th>
                        <th>Warehouse</th>
                        <th>Receipt Date</th>
                        <th>Supplier Invoice / Challan</th>
                        <th>Status</th>
                        <th>Received By</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $grn)
                        <tr>
                            <td><strong>{{ $grn->grn_number }}</strong></td>
                            <td>{{ $grn->vendor?->company_name ?: $grn->vendor?->name }}</td>
                            <td>{{ $grn->warehouse?->name }}</td>
                            <td>{{ $grn->receipt_date->format('d M Y') }}</td>
                            <td>{{ $grn->supplier_invoice_no ?: ($grn->supplier_challan_no ?: 'Direct') }}</td>
                            <td><span class="badge bg-success">Posted to Stock</span></td>
                            <td><small class="text-muted">{{ $grn->receiver?->name ?: 'Storekeeper' }}</small></td>
                            <td class="text-end">
                                <a href="{{ route('purchases.grn.show', $grn->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No goods receipt notes posted yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($receipts->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $receipts->links() }}
        </div>
    @endif
</div>
@endsection
