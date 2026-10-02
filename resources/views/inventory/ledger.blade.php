@extends('layouts.app')

@section('title', 'Stock Movement Ledger')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-list-check text-primary me-2"></i> Stock Movement Ledger</h4>
        <p class="text-muted small mb-0">Immutable audit log of all raw material consumption, purchases, production outputs and sales</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('inventory.ledger') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="product_id" class="form-select">
                    <option value="">All Chemical Items</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="movement_type" class="form-select">
                    <option value="">All Movement Types</option>
                    <option value="purchase_receipt" {{ request('movement_type') === 'purchase_receipt' ? 'selected' : '' }}>Purchase Receipt (Inward)</option>
                    <option value="production_consumption" {{ request('movement_type') === 'production_consumption' ? 'selected' : '' }}>Production Consumption (Outward)</option>
                    <option value="production_output" {{ request('movement_type') === 'production_output' ? 'selected' : '' }}>Production Output (Inward)</option>
                    <option value="sales_issue" {{ request('movement_type') === 'sales_issue' ? 'selected' : '' }}>Sales Invoice Issue (Outward)</option>
                    <option value="sales_return" {{ request('movement_type') === 'sales_return' ? 'selected' : '' }}>Sales Return</option>
                    <option value="adjustment_add" {{ request('movement_type') === 'adjustment_add' ? 'selected' : '' }}>Adjustment (Add)</option>
                    <option value="adjustment_sub" {{ request('movement_type') === 'adjustment_sub' ? 'selected' : '' }}>Adjustment (Subtract)</option>
                    <option value="opening_stock" {{ request('movement_type') === 'opening_stock' ? 'selected' : '' }}>Opening Stock</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From Date">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="To Date">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Filter Ledger</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Chemical Item / SKU</th>
                        <th>Warehouse</th>
                        <th>Batch / Lot</th>
                        <th>Movement Type</th>
                        <th class="text-end">Quantity Change</th>
                        <th class="text-end">Unit Cost</th>
                        <th class="text-end">Balance After</th>
                        <th>Responsible User</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                        <tr>
                            <td>
                                <div>{{ $m->movement_date->format('d M Y') }}</div>
                                <small class="text-muted">{{ $m->movement_date->format('h:i A') }}</small>
                            </td>
                            <td>
                                <strong>{{ $m->product?->name }}</strong>
                                <div class="text-muted small">SKU: {{ $m->product?->sku }}</div>
                            </td>
                            <td>{{ $m->warehouse?->name }}</td>
                            <td>{{ $m->batch?->batch_number ?: 'General' }}</td>
                            <td>
                                <span class="badge {{ $m->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucwords(str_replace('_', ' ', $m->movement_type)) }}
                                </span>
                                @if($m->notes)
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $m->notes }}</div>
                                @endif
                            </td>
                            <td class="text-end fw-bold {{ $m->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $m->quantity > 0 ? '+' : '' }}{{ number_format($m->quantity, 2) }} {{ $m->product?->unit?->code }}
                            </td>
                            <td class="text-end">₹{{ number_format($m->unit_cost, 2) }}</td>
                            <td class="text-end fw-semibold text-dark">
                                {{ number_format($m->balance_after, 2) }} {{ $m->product?->unit?->code }}
                            </td>
                            <td><small class="text-muted">{{ $m->creator?->name ?: 'System' }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No stock ledger movements logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($movements->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $movements->links() }}
        </div>
    @endif
</div>
@endsection
