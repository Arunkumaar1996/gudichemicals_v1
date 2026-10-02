@extends('layouts.app')

@section('title', 'Configure New Promotion')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-gift text-warning me-2"></i> Configure Promotional Scheme</h5>
                    <small class="text-muted">Create BOGO, B2G1, combo bundles or quantity tier discounts</small>
                </div>
                <a href="{{ route('promotions.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Promotions
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('promotions.store') }}">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Promotion Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Buy 2 Cleaners Get 1 Bottle Free" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Promo Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control text-uppercase" placeholder="e.g. B2G1-CLEAN" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Promotion Type <span class="text-danger">*</span></label>
                            <select name="type" id="promoType" class="form-select" required onchange="adjustPromoFields()">
                                <option value="bogo">Buy 1 Get 1 Free (BOGO)</option>
                                <option value="b2g1">Buy 2 Get 1 Free (B2G1)</option>
                                <option value="bxgy">Buy X Get Y (BXGY)</option>
                                <option value="percent_discount">Percentage Discount (%)</option>
                                <option value="flat_discount">Flat Order Discount (₹)</option>
                                <option value="slab_discount">Quantity Slab Discount</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">End Date (Optional)</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Target Buy Product</label>
                            <select name="buy_product_id" class="form-select">
                                <option value="">Applies to All Products / General Order</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6" id="getProductSection">
                            <label class="form-label small fw-semibold">Free Reward Product</label>
                            <select name="get_product_id" class="form-select">
                                <option value="">Same as Buy Product</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3" id="buyQtySection">
                            <label class="form-label small fw-semibold">Buy Quantity Requirement</label>
                            <input type="number" step="1" name="buy_quantity" class="form-control" value="2">
                        </div>

                        <div class="col-md-3" id="getQtySection">
                            <label class="form-label small fw-semibold">Reward Quantity (Free)</label>
                            <input type="number" step="1" name="get_quantity" class="form-control" value="1">
                        </div>

                        <div class="col-md-3" id="discountPctSection">
                            <label class="form-label small fw-semibold">Discount Percentage (%)</label>
                            <input type="number" step="0.01" name="discount_pct" class="form-control" value="0.00">
                        </div>

                        <div class="col-md-3" id="discountFlatSection">
                            <label class="form-label small fw-semibold">Flat Discount Amount (₹)</label>
                            <input type="number" step="0.01" name="discount_flat" class="form-control" value="0.00">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Customer Eligibility</label>
                            <select name="customer_type" class="form-select" required>
                                <option value="all">All Customers (Retail & Wholesale)</option>
                                <option value="retail">Retail Counter Customers Only</option>
                                <option value="wholesale">Wholesale B2B Customers Only</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Evaluation Priority (1 = Highest)</label>
                            <input type="number" name="priority" class="form-control" value="1" min="1" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Campaign Description</label>
                            <textarea name="description" rows="2" class="form-control" placeholder="Promotional campaign details..."></textarea>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                                <i class="fa-solid fa-gift me-1"></i> Save & Activate Promotion
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function adjustPromoFields() {
        const type = document.getElementById('promoType').value;
        const buyQty = document.querySelector('input[name="buy_quantity"]');
        const getQty = document.querySelector('input[name="get_quantity"]');

        if (type === 'bogo') {
            buyQty.value = 1;
            getQty.value = 1;
        } else if (type === 'b2g1') {
            buyQty.value = 2;
            getQty.value = 1;
        }
    }
</script>
@endpush
@endsection
