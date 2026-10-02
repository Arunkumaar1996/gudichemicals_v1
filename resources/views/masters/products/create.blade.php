@extends('layouts.app')

@section('title', 'Add New Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-plus-circle text-primary me-2"></i> Add New Product / Chemical SKU</h5>
                <a href="{{ route('masters.products.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('masters.products.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Product / Material Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Commercial Sulphuric Acid 98%" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">SKU Code <span class="text-danger">*</span></label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" placeholder="e.g. RAW-SULPH-02" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Barcode (Optional)</label>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}" placeholder="Scan or enter barcode">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Item Type <span class="text-danger">*</span></label>
                            <select name="item_type" class="form-select" required>
                                <option value="">Select Item Classification</option>
                                <option value="raw_material" {{ old('item_type') === 'raw_material' ? 'selected' : '' }}>Raw Material (Chemical, Acid, Solvent)</option>
                                <option value="semi_finished" {{ old('item_type') === 'semi_finished' ? 'selected' : '' }}>Semi-Finished (Compounded Liquid / Intermediate)</option>
                                <option value="finished_goods" {{ old('item_type') === 'finished_goods' ? 'selected' : '' }}>Finished Chemical Product (Bottled / Packaged)</option>
                                <option value="packaging" {{ old('item_type') === 'packaging' ? 'selected' : '' }}>Packaging (Bottles, Cans, Caps, Labels)</option>
                                <option value="trading" {{ old('item_type') === 'trading' ? 'selected' : '' }}>Trading / Resale Item</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Base Stock Unit <span class="text-danger">*</span></label>
                            <select name="unit_id" class="form-select" required>
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">HSN Code <span class="text-danger">*</span></label>
                            <input type="text" name="hsn_code" class="form-control" value="{{ old('hsn_code', '3402') }}" placeholder="e.g. 2807, 3402" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">GST Rate (%) <span class="text-danger">*</span></label>
                            <select name="gst_rate" class="form-select" required>
                                <option value="18.00" {{ old('gst_rate', '18.00') == '18.00' ? 'selected' : '' }}>18% GST (Standard Chemical)</option>
                                <option value="12.00" {{ old('gst_rate') == '12.00' ? 'selected' : '' }}>12% GST</option>
                                <option value="5.00" {{ old('gst_rate') == '5.00' ? 'selected' : '' }}>5% GST</option>
                                <option value="28.00" {{ old('gst_rate') == '28.00' ? 'selected' : '' }}>28% GST</option>
                                <option value="0.00" {{ old('gst_rate') == '0.00' ? 'selected' : '' }}>0% (Exempt)</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Purchase Cost / Unit (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" name="purchase_cost" class="form-control" value="{{ old('purchase_cost', '0') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Retail Price (₹)</label>
                            <input type="number" step="0.0001" name="retail_price" class="form-control" value="{{ old('retail_price', '0') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Wholesale Price (₹)</label>
                            <input type="number" step="0.0001" name="wholesale_price" class="form-control" value="{{ old('wholesale_price', '0') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Reorder Threshold</label>
                            <input type="number" step="0.01" name="reorder_level" class="form-control" value="{{ old('reorder_level', '50') }}">
                        </div>

                        <div class="col-12">
                            <div class="d-flex gap-4 p-3 bg-light rounded border">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_batch_tracking" value="1" id="chkBatch" {{ old('has_batch_tracking', true) ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="chkBatch">Enable Batch / Lot Tracking</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_expiry_tracking" value="1" id="chkExpiry" {{ old('has_expiry_tracking') ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="chkExpiry">Track Expiry / Retest Dates</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="chkActive" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="chkActive">Active for Transactions</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Description & Chemical Specifications</label>
                            <textarea name="description" rows="2" class="form-control" placeholder="Chemical grade, purity %, application notes...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Product
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
