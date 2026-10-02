@extends('layouts.app')

@section('title', 'Add Opening Stock')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-plus-circle text-primary me-2"></i> Initial Opening Stock Entry</h5>
                <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Inventory
                </a>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-4">
                    <i class="fa-solid fa-circle-info text-info me-1"></i>
                    All opening stock is posted to the immutable stock ledger and balance projection atomically.
                </p>
                <form method="POST" action="{{ route('inventory.opening_stock.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Select Chemical / Product <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-select" required>
                                <option value="">Select Item</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->sku }}) - {{ $p->unit?->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Warehouse / Location <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }} ({{ $wh->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Opening Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" name="quantity" class="form-control" value="{{ old('quantity') }}" placeholder="e.g. 500.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Unit Cost Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" name="unit_cost" class="form-control" value="{{ old('unit_cost', '0') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Batch / Lot Number</label>
                            <input type="text" name="batch_number" class="form-control" value="{{ old('batch_number') }}" placeholder="e.g. LOT-OP-2026">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Expiry Date (if applicable)</label>
                            <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Audit Notes</label>
                            <input type="text" name="notes" class="form-control" value="{{ old('notes', 'Physical opening stock count') }}">
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Post Opening Stock
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
