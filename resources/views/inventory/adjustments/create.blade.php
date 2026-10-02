@extends('layouts.app')

@section('title', 'Create Stock Adjustment')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-sliders text-primary me-2"></i> New Stock Adjustment Document</h5>
                <a href="{{ route('inventory.adjustments.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('inventory.adjustments.store') }}" id="adjForm">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Warehouse / Location <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Adjustment Date <span class="text-danger">*</span></label>
                            <input type="date" name="adjustment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Reason <span class="text-danger">*</span></label>
                            <input type="text" name="reason" class="form-control" placeholder="e.g. Physical inventory count discrepancy, spill, QA sample" required>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 border-bottom pb-2">Adjustment Line Items</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50%;">Product / Chemical Item</th>
                                    <th style="width: 20%;">Adjustment Type</th>
                                    <th style="width: 20%;">Quantity</th>
                                    <th style="width: 10%;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr>
                                    <td>
                                        <select name="items[0][product_id]" class="form-select" required>
                                            <option value="">Select Item</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="items[0][type]" class="form-select" required>
                                            <option value="add">Add (+ Stock)</option>
                                            <option value="subtract">Subtract (- Stock)</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" name="items[0][quantity]" class="form-control" placeholder="Quantity" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" disabled><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mb-4" onclick="addRow()">
                        <i class="fa-solid fa-plus me-1"></i> Add Another Item
                    </button>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Audit Notes</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Supervisor notes and physical recount observations..."></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                            <i class="fa-solid fa-check me-1"></i> Post Adjustment to Stock Ledger
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let rowIndex = 1;
    function addRow() {
        const tbody = document.getElementById('itemsBody');
        const firstRow = tbody.rows[0];
        const newRow = firstRow.cloneNode(true);

        newRow.querySelector('select[name^="items"]').name = `items[${rowIndex}][product_id]`;
        newRow.querySelectorAll('select')[1].name = `items[${rowIndex}][type]`;
        newRow.querySelector('input[type="number"]').name = `items[${rowIndex}][quantity]`;
        newRow.querySelector('input[type="number"]').value = '';
        newRow.querySelector('button').disabled = false;

        tbody.appendChild(newRow);
        rowIndex++;
    }

    function removeRow(btn) {
        const tbody = document.getElementById('itemsBody');
        if (tbody.rows.length > 1) {
            btn.closest('tr').remove();
        }
    }
</script>
@endpush
@endsection
