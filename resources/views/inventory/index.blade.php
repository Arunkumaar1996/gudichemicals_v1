@extends('layouts.app')

@section('title', 'Current Stock On Hand')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-boxes-stacked text-primary me-2"></i> Current Stock On Hand</h4>
        <p class="text-muted small mb-0">Live chemical inventory balances, lot allocations and warehouse stock levels</p>
    </div>
    <div class="mt-2 mt-md-0 d-flex gap-2">
        <a href="{{ route('inventory.opening_stock') }}" class="btn btn-outline-primary fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Add Opening Stock
        </a>
        <a href="{{ route('inventory.adjustments.create') }}" class="btn btn-secondary fw-semibold">
            <i class="fa-solid fa-sliders me-1"></i> Stock Adjustment
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('inventory.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search by chemical name or SKU..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="warehouse_id" class="form-select">
                    <option value="">All Warehouses / Plant</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }} ({{ $wh->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Product / Chemical Item</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th class="text-end">Cost Price</th>
                        <th class="text-end">Reorder Level</th>
                        <th class="text-end">Total Stock</th>
                        <th>Batch / Lot Breakdown</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $prod)
                        <tr>
                            <td>
                                <strong>{{ $prod->name }}</strong>
                                <div class="text-muted small">SKU: {{ $prod->sku }} | HSN: {{ $prod->hsn_code }}</div>
                            </td>
                            <td><span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $prod->item_type)) }}</span></td>
                            <td>{{ $prod->category?->name }}</td>
                            <td class="text-end">₹{{ number_format($prod->purchase_cost, 2) }}</td>
                            <td class="text-end text-muted">{{ number_format($prod->reorder_level, 2) }} {{ $prod->unit?->code }}</td>
                            <td class="text-end fw-bold fs-6 {{ $prod->isLowStock() ? 'text-danger' : 'text-success' }}">
                                {{ number_format($prod->total_stock, 2) }} {{ $prod->unit?->code }}
                            </td>
                            <td>
                                @if($prod->stockBalances->isNotEmpty())
                                    <div class="small">
                                        @foreach($prod->stockBalances as $sb)
                                            <span class="badge bg-light text-dark border me-1 mb-1">
                                                {{ $sb->batch ? 'Lot: ' . $sb->batch->batch_number : 'General' }}:
                                                <strong>{{ number_format($sb->quantity, 2) }} {{ $prod->unit?->code }}</strong>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small">No batches allocated</span>
                                @endif
                            </td>
                            <td>
                                @if($prod->isLowStock())
                                    <span class="badge bg-danger">Reorder Alert</span>
                                @else
                                    <span class="badge bg-success">Adequate</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No products found in inventory.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
