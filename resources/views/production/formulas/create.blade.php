@extends('layouts.app')

@section('title', 'Create Chemical Formula')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-mortar-pestle text-primary me-2"></i> Create Chemical Formula (BOM)</h5>
                    <small class="text-muted">Define standard batch size and required chemical ingredients</small>
                </div>
                <a href="{{ route('production.formulas.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Formulas
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('production.formulas.store') }}" id="formulaForm">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Formula Code <span class="text-danger">*</span></label>
                            <input type="text" name="formula_code" class="form-control text-uppercase" placeholder="e.g. FORM-DEGREASE-01" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">Formula Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Ultra Clean Industrial Degreaser 1L Prep" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Version</label>
                            <input type="number" name="version" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Yield %</label>
                            <input type="number" step="0.01" name="expected_yield_pct" class="form-control" value="100.00" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Target Output Chemical Product <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-select" required>
                                <option value="">Select Output Product</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Standard Batch Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" name="standard_batch_qty" class="form-control" placeholder="e.g. 500" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Output Unit <span class="text-danger">*</span></label>
                            <select name="output_unit_id" class="form-select" required>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 border-bottom pb-2">Ingredients List & Quantities for Standard Batch</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="ingredientsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45%;">Raw Material / Ingredient Product</th>
                                    <th style="width: 25%;">Required Quantity</th>
                                    <th style="width: 20%;">Ingredient Unit</th>
                                    <th style="width: 10%;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="ingredientsBody">
                                <tr>
                                    <td>
                                        <select name="items[0][ingredient_product_id]" class="form-select" required>
                                            <option value="">Select Ingredient</option>
                                            @foreach($ingredients as $ing)
                                                <option value="{{ $ing->id }}" data-unit="{{ $ing->unit_id }}">
                                                    {{ $ing->name }} ({{ $ing->sku }}) - {{ $ing->unit?->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" name="items[0][quantity]" class="form-control" placeholder="e.g. 100" required>
                                    </td>
                                    <td>
                                        <select name="items[0][unit_id]" class="form-select" required>
                                            @foreach($units as $u)
                                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->code }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" disabled><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mb-4" onclick="addIngredientRow()">
                        <i class="fa-solid fa-plus me-1"></i> Add Another Ingredient
                    </button>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Compounding Guidelines & Reaction Instructions</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Mixing speed 400 RPM, add acid slowly into water, maintain temperature under 45°C..."></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                            <i class="fa-solid fa-check me-1"></i> Save & Approve Formula
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let ingIndex = 1;
    function addIngredientRow() {
        const tbody = document.getElementById('ingredientsBody');
        const firstRow = tbody.rows[0];
        const newRow = firstRow.cloneNode(true);

        newRow.querySelector('select[name^="items"]').name = `items[${ingIndex}][ingredient_product_id]`;
        newRow.querySelector('input[type="number"]').name = `items[${ingIndex}][quantity]`;
        newRow.querySelector('input[type="number"]').value = '';
        newRow.querySelectorAll('select')[1].name = `items[${ingIndex}][unit_id]`;
        newRow.querySelector('button').disabled = false;

        tbody.appendChild(newRow);
        ingIndex++;
    }

    function removeRow(btn) {
        const tbody = document.getElementById('ingredientsBody');
        if (tbody.rows.length > 1) {
            btn.closest('tr').remove();
        }
    }
</script>
@endpush
@endsection
