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

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('inventory.index', request()->except('low_stock', 'page')) }}" 
                   class="btn {{ !request()->boolean('low_stock') ? 'btn-primary fw-bold' : 'btn-outline-secondary' }}">
                    <i class="fa-solid fa-boxes-stacked me-1"></i> All Inventory Items
                </a>
                <a href="{{ route('inventory.index', array_merge(request()->except('page'), ['low_stock' => 1])) }}" 
                   class="btn {{ request()->boolean('low_stock') ? 'btn-danger fw-bold' : 'btn-outline-danger' }}">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Low Stock Alerts
                    @if(isset($lowStockCount) && $lowStockCount > 0)
                        <span class="badge bg-white text-danger ms-1 fw-bold">{{ $lowStockCount }}</span>
                    @endif
                </a>
            </div>

            @if(request()->boolean('low_stock'))
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 fw-semibold">
                    <i class="fa-solid fa-filter me-1"></i> Filtering items at or below reorder threshold
                </span>
            @endif
        </div>

        <form method="GET" action="{{ route('inventory.index') }}" class="row g-2 align-items-center">
            @if(request()->boolean('low_stock'))
                <input type="hidden" name="low_stock" value="1">
            @endif
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
                        <tr class="{{ $prod->isLowStock() ? 'table-danger table-opacity-10' : '' }}">
                            <td>
                                <strong>{{ $prod->name }}</strong>
                                <div class="text-muted small">SKU: {{ $prod->sku }} | HSN: {{ $prod->hsn_code }}</div>
                            </td>
                            <td><span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $prod->item_type)) }}</span></td>
                            <td>{{ $prod->category?->name }}</td>
                            <td class="text-end">₹{{ number_format($prod->purchase_cost, 2) }}</td>
                            <td class="text-end">
                                <span class="fw-semibold {{ $prod->reorder_level > 0 ? 'text-dark' : 'text-muted' }}">
                                    {{ number_format($prod->reorder_level, 2) }} {{ $prod->unit?->code }}
                                </span>
                                <button type="button" class="btn btn-sm btn-link text-primary text-decoration-none p-0 ms-1" 
                                        onclick="openReorderModal({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ (float)$prod->reorder_level }}, '{{ $prod->unit?->code }}')" 
                                        title="Set Low Stock Quantity Threshold">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </td>
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
                                    <span class="badge bg-danger"><i class="fa-solid fa-triangle-exclamation me-0.5"></i> Reorder Alert</span>
                                @else
                                    <span class="badge bg-success">Adequate</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No products found matching criteria.</td>
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

<!-- ========================================== -->
<!-- MODAL: SET LOW STOCK / REORDER QUANTITY    -->
<!-- ========================================== -->
<div class="modal fade" id="setReorderLevelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-2.5 px-3">
                <h6 class="modal-title fw-bold text-dark mb-0">
                    <i class="fa-solid fa-sliders text-primary me-1.5"></i> Set Low Stock Qty Threshold
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSetReorderLevel" method="POST" action="">
                @csrf
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-0.5">Chemical Product</label>
                        <h6 class="fw-bold text-primary mb-0" id="reorderProductName">Chemical Name</h6>
                    </div>
                    <div class="mb-3">
                        <label for="reorderInputQty" class="form-label small fw-semibold mb-1">
                            Low Stock Alert Level (<span id="reorderUnitLabel">Unit</span>) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" step="any" min="0" name="reorder_level" id="reorderInputQty" class="form-control fw-bold fs-5" required>
                            <span class="input-group-text bg-light text-muted fw-semibold" id="reorderUnitAddon">KG</span>
                        </div>
                        <small class="text-muted">An alert triggers on Dashboard and Inventory whenever total stock falls to or below this quantity.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">
                        <i class="fa-solid fa-save me-1"></i> Save Threshold
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openReorderModal(productId, productName, currentLevel, unit) {
        $('#reorderProductName').text(productName);
        $('#reorderInputQty').val(currentLevel);
        $('#reorderUnitLabel').text(unit);
        $('#reorderUnitAddon').text(unit);
        $('#formSetReorderLevel').attr('action', '/inventory/' + productId + '/reorder-level');
        const modal = new bootstrap.Modal(document.getElementById('setReorderLevelModal'));
        modal.show();
    }
</script>
@endpush
@endsection
