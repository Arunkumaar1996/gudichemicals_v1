@extends('layouts.app')

@section('title', 'Edit Product — ' . $product->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Product: {{ $product->name }}</h5>
                <a href="{{ route('masters.products.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('masters.products.update', $product->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Product / Material Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">SKU Code <span class="text-danger">*</span></label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Barcode</label>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Item Type <span class="text-danger">*</span></label>
                            <select name="item_type" class="form-select" required>
                                <option value="raw_material" {{ old('item_type', $product->item_type) === 'raw_material' ? 'selected' : '' }}>Raw Material (Chemical, Acid, Solvent)</option>
                                <option value="semi_finished" {{ old('item_type', $product->item_type) === 'semi_finished' ? 'selected' : '' }}>Semi-Finished (Compounded Liquid / Intermediate)</option>
                                <option value="finished_goods" {{ old('item_type', $product->item_type) === 'finished_goods' ? 'selected' : '' }}>Finished Chemical Product (Bottled / Packaged)</option>
                                <option value="packaging" {{ old('item_type', $product->item_type) === 'packaging' ? 'selected' : '' }}>Packaging (Bottles, Cans, Caps, Labels)</option>
                                <option value="trading" {{ old('item_type', $product->item_type) === 'trading' ? 'selected' : '' }}>Trading / Resale Item</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Base Stock Unit <span class="text-danger">*</span></label>
                            <select name="unit_id" class="form-select" required>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">HSN Code <span class="text-danger">*</span></label>
                            <input type="text" name="hsn_code" class="form-control" value="{{ old('hsn_code', $product->hsn_code) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">GST Rate (%) <span class="text-danger">*</span></label>
                            <select name="gst_rate" class="form-select" required>
                                <option value="18.00" {{ old('gst_rate', $product->gst_rate) == '18.00' ? 'selected' : '' }}>18% GST</option>
                                <option value="12.00" {{ old('gst_rate', $product->gst_rate) == '12.00' ? 'selected' : '' }}>12% GST</option>
                                <option value="5.00" {{ old('gst_rate', $product->gst_rate) == '5.00' ? 'selected' : '' }}>5% GST</option>
                                <option value="28.00" {{ old('gst_rate', $product->gst_rate) == '28.00' ? 'selected' : '' }}>28% GST</option>
                                <option value="0.00" {{ old('gst_rate', $product->gst_rate) == '0.00' ? 'selected' : '' }}>0% (Exempt)</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Purchase Cost / Unit (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" name="purchase_cost" class="form-control" value="{{ old('purchase_cost', $product->purchase_cost) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Retail Price (₹)</label>
                            <input type="number" step="0.0001" name="retail_price" class="form-control" value="{{ old('retail_price', $product->retail_price) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Wholesale Price (₹)</label>
                            <input type="number" step="0.0001" name="wholesale_price" class="form-control" value="{{ old('wholesale_price', $product->wholesale_price) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Reorder Threshold</label>
                            <input type="number" step="0.01" name="reorder_level" class="form-control" value="{{ old('reorder_level', $product->reorder_level) }}">
                        </div>

                        <div class="col-12">
                            <div class="d-flex gap-4 p-3 bg-light rounded border">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_batch_tracking" value="1" id="chkBatch" {{ old('has_batch_tracking', $product->has_batch_tracking) ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="chkBatch">Enable Batch / Lot Tracking</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_expiry_tracking" value="1" id="chkExpiry" {{ old('has_expiry_tracking', $product->has_expiry_tracking) ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="chkExpiry">Track Expiry / Retest Dates</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="chkActive" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="chkActive">Active for Transactions</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Description & Chemical Specifications</label>
                            <textarea name="description" rows="2" class="form-control">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="col-12 mt-4 d-flex justify-content-between">
                            <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Update Product
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
