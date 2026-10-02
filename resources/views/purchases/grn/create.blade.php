@extends('layouts.app')

@section('title', 'Receive Inward Goods (GRN)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-truck-ramp-box text-primary me-2"></i> Inward Goods Receipt Note (GRN)</h5>
                    <small class="text-muted">Posting GRN immediately adds materials to inventory ledger and creates batch records</small>
                </div>
                <a href="{{ route('purchases.grn.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to GRNs
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('purchases.grn.store') }}" id="grnForm">
                    @csrf
                    @if($selectedPo)
                        <input type="hidden" name="purchase_order_id" value="{{ $selectedPo->id }}">
                        <div class="alert alert-info py-2 px-3 small mb-3">
                            <i class="fa-solid fa-link me-1"></i> Linked to Purchase Order: <strong>{{ $selectedPo->po_number }}</strong>
                        </div>
                    @endif

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Vendor / Supplier <span class="text-danger">*</span></label>
                            <select name="vendor_id" class="form-select" required>
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $v)
                                    <option value="{{ $v->id }}" {{ ($selectedPo && $selectedPo->vendor_id == $v->id) ? 'selected' : '' }}>
                                        {{ $v->company_name ?: $v->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Receiving Plant / Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ ($selectedPo && $selectedPo->warehouse_id == $wh->id) ? 'selected' : '' }}>
                                        {{ $wh->name }} ({{ $wh->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Receipt Date <span class="text-danger">*</span></label>
                            <input type="date" name="receipt_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Supplier Delivery Challan #</label>
                            <input type="text" name="supplier_challan_no" class="form-control" placeholder="e.g. CH-99023">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Supplier Tax Invoice # (for AP entry)</label>
                            <input type="text" name="supplier_invoice_no" class="form-control" placeholder="e.g. INV-2026-8871">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 border-bottom pb-2">Received Chemical & Packaging Batches</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="grnTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%;">Product / Raw Material</th>
                                    <th style="width: 15%;">Batch / Lot #</th>
                                    <th style="width: 15%;">Supplier Lot #</th>
                                    <th style="width: 15%;">Received Qty</th>
                                    <th style="width: 15%;">Unit Cost (₹)</th>
                                    <th style="width: 10%;">Expiry Date</th>
                                    <th style="width: 5%;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="grnBody">
                                @if($selectedPo && $selectedPo->items->isNotEmpty())
                                    @foreach($selectedPo->items as $idx => $pItem)
                                        <tr>
                                            <td>
                                                <select name="items[{{ $idx }}][product_id]" class="form-select" required>
                                                    <option value="{{ $pItem->product_id }}">{{ $pItem->product?->name }}</option>
                                                </select>
                                                <input type="hidden" name="items[{{ $idx }}][purchase_order_item_id]" value="{{ $pItem->id }}">
                                                <input type="hidden" name="items[{{ $idx }}][tax_rate]" value="{{ $pItem->tax_rate }}">
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $idx }}][batch_number]" class="form-control" value="LOT-{{ date('Ymd') }}-{{ $idx+1 }}" required>
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $idx }}][supplier_lot_number]" class="form-control" placeholder="Vendor Lot">
                                            </td>
                                            <td>
                                                <input type="number" step="0.0001" name="items[{{ $idx }}][quantity]" class="form-control" value="{{ $pItem->quantity - $pItem->received_quantity }}" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.0001" name="items[{{ $idx }}][unit_cost]" class="form-control" value="{{ $pItem->unit_price }}" required>
                                            </td>
                                            <td>
                                                <input type="date" name="items[{{ $idx }}][expiry_date]" class="form-control">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" disabled><i class="fa-solid fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <select name="items[0][product_id]" class="form-select" required>
                                                <option value="">Select Item</option>
                                                @foreach($products as $p)
                                                    <option value="{{ $p->id }}" data-cost="{{ $p->purchase_cost }}">{{ $p->name }} ({{ $p->sku }})</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="items[0][tax_rate]" value="18.00">
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][batch_number]" class="form-control" value="LOT-{{ date('Ymd') }}-01" required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][supplier_lot_number]" class="form-control" placeholder="Vendor Lot">
                                        </td>
                                        <td>
                                            <input type="number" step="0.0001" name="items[0][quantity]" class="form-control" value="100" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.0001" name="items[0][unit_cost]" class="form-control" value="0" required>
                                        </td>
                                        <td>
                                            <input type="date" name="items[0][expiry_date]" class="form-control">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" disabled><i class="fa-solid fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mb-4" onclick="addGrnRow()">
                        <i class="fa-solid fa-plus me-1"></i> Add Another Inward Item
                    </button>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Quality Inspection & Receipt Remarks</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Container seal verified, raw acid density checked..."></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                            <i class="fa-solid fa-check me-1"></i> Post GRN & Increase Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let grnIndex = 10;
    function addGrnRow() {
        const tbody = document.getElementById('grnBody');
        const firstRow = tbody.rows[0];
        const newRow = firstRow.cloneNode(true);

        newRow.querySelector('select[name^="items"]').name = `items[${grnIndex}][product_id]`;
        newRow.querySelector('input[name*="batch_number"]').name = `items[${grnIndex}][batch_number]`;
        newRow.querySelector('input[name*="supplier_lot_number"]').name = `items[${grnIndex}][supplier_lot_number]`;
        newRow.querySelector('input[name*="quantity"]').name = `items[${grnIndex}][quantity]`;
        newRow.querySelector('input[name*="unit_cost"]').name = `items[${grnIndex}][unit_cost]`;
        newRow.querySelector('input[name*="expiry_date"]').name = `items[${grnIndex}][expiry_date]`;
        newRow.querySelector('button').disabled = false;

        tbody.appendChild(newRow);
        grnIndex++;
    }

    function removeRow(btn) {
        const tbody = document.getElementById('grnBody');
        if (tbody.rows.length > 1) {
            btn.closest('tr').remove();
        }
    }
</script>
@endpush
@endsection
