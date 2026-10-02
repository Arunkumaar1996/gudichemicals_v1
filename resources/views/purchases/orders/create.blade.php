@extends('layouts.app')

@section('title', 'Create Purchase Order')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-cart-plus text-primary me-2"></i> Create Purchase Order</h5>
                <a href="{{ route('purchases.orders.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Orders
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('purchases.orders.store') }}" id="poForm">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Vendor / Chemical Supplier <span class="text-danger">*</span></label>
                            <select name="vendor_id" class="form-select" required>
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $v)
                                    <option value="{{ $v->id }}">{{ $v->company_name ?: $v->name }} ({{ $v->state_name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Destination Plant / Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Order Date <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Expected Date</label>
                            <input type="date" name="expected_date" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 border-bottom pb-2">Procurement Line Items</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="poTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40%;">Raw Chemical / Packaging Item</th>
                                    <th style="width: 20%;">Quantity</th>
                                    <th style="width: 20%;">Unit Price (₹)</th>
                                    <th style="width: 15%;">GST (%)</th>
                                    <th style="width: 5%;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="poBody">
                                <tr>
                                    <td>
                                        <select name="items[0][product_id]" class="form-select product-select" required onchange="updateProductDetails(this, 0)">
                                            <option value="">Select Item</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" data-cost="{{ $p->purchase_cost }}" data-unit="{{ $p->unit_id }}" data-gst="{{ $p->gst_rate }}">
                                                    {{ $p->name }} ({{ $p->sku }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="items[0][unit_id]" value="1" class="unit-id-input">
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" name="items[0][quantity]" class="form-control" placeholder="Qty" value="100" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" name="items[0][unit_price]" class="form-control price-input" placeholder="Price" value="0" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[0][tax_rate]" class="form-control gst-input" value="18.00" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" disabled><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mb-4" onclick="addPoRow()">
                        <i class="fa-solid fa-plus me-1"></i> Add Another Item
                    </button>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Special Delivery & Safety Instructions</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Chemical tanker delivery guidelines, MSDS certificate required..."></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                            <i class="fa-solid fa-check me-1"></i> Save Purchase Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let poIndex = 1;
    function updateProductDetails(select, idx) {
        const option = select.options[select.selectedIndex];
        const row = select.closest('tr');
        if (option && option.dataset.cost) {
            row.querySelector('.price-input').value = option.dataset.cost;
            row.querySelector('.gst-input').value = option.dataset.gst || 18.00;
            row.querySelector('.unit-id-input').value = option.dataset.unit || 1;
        }
    }

    function addPoRow() {
        const tbody = document.getElementById('poBody');
        const firstRow = tbody.rows[0];
        const newRow = firstRow.cloneNode(true);

        newRow.querySelector('select[name^="items"]').name = `items[${poIndex}][product_id]`;
        newRow.querySelector('.unit-id-input').name = `items[${poIndex}][unit_id]`;
        newRow.querySelector('input[name*="quantity"]').name = `items[${poIndex}][quantity]`;
        newRow.querySelector('.price-input').name = `items[${poIndex}][unit_price]`;
        newRow.querySelector('.gst-input').name = `items[${poIndex}][tax_rate]`;
        newRow.querySelector('button').disabled = false;

        tbody.appendChild(newRow);
        poIndex++;
    }

    function removeRow(btn) {
        const tbody = document.getElementById('poBody');
        if (tbody.rows.length > 1) {
            btn.closest('tr').remove();
        }
    }
</script>
@endpush
@endsection
