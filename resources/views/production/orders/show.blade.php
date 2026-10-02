@extends('layouts.app')

@section('title', 'Batch ' . $order->batch_number)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-industry text-primary me-2"></i> Batch Order: {{ $order->batch_number }}</h4>
        <span class="badge {{ $order->status === 'completed' ? 'bg-success' : 'bg-primary' }} me-2">
            Status: {{ ucfirst(str_replace('_', ' ', $order->status)) }}
        </span>
        <span class="text-muted small">Date: {{ $order->order_date->format('d M Y') }}</span>
    </div>
    <div class="mt-2 mt-md-0 d-flex gap-2">
        @if($order->status !== 'completed' && $order->status !== 'cancelled')
            <button type="button" class="btn btn-outline-info btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#qcModal">
                <i class="fa-solid fa-vial me-1"></i> Record QC Test
            </button>
            <button type="button" class="btn btn-success btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#finalizeModal">
                <i class="fa-solid fa-check-double me-1"></i> Finalize Batch & Post Stock
            </button>
        @endif
        <a href="{{ route('production.orders.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <small class="text-muted fw-semibold">Target Finished Chemical</small>
            <h6 class="fw-bold text-dark my-1">{{ $order->outputProduct?->name }}</h6>
            <small class="text-muted">SKU: {{ $order->outputProduct?->sku }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <small class="text-muted fw-semibold">Planned vs Actual Output</small>
            <h6 class="fw-bold my-1 text-primary">
                {{ number_format($order->planned_qty, 2) }} {{ $order->formula?->outputUnit?->code }} (Planned)
            </h6>
            <small class="text-success fw-bold">Actual: {{ number_format($order->actual_qty, 2) }} {{ $order->formula?->outputUnit?->code }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <small class="text-muted fw-semibold">Total & Unit Production Cost</small>
            <h6 class="fw-bold my-1 text-dark">₹{{ number_format($order->total_material_cost + $order->packaging_cost + $order->overhead_cost, 2) }}</h6>
            <small class="text-muted">Unit Cost: ₹{{ number_format($order->unit_production_cost, 2) }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <small class="text-muted fw-semibold">Formula Recipe Snapshot</small>
            <h6 class="fw-bold my-1 text-dark">{{ $order->formula_snapshot['formula_code'] ?? 'FORM' }} (v{{ $order->formula_snapshot['version'] ?? 1 }})</h6>
            <small class="text-muted">Warehouse: {{ $order->targetWarehouse?->name }}</small>
        </div>
    </div>
</div>

<!-- Ingredients & Consumption Table -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-mortar-pestle text-primary me-2"></i> Scaled Chemical Ingredients & Raw Material Consumptions</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Ingredient / Chemical</th>
                        <th class="text-end">Planned Requirement</th>
                        <th class="text-end">Actual Consumed</th>
                        <th class="text-end">Unit Cost</th>
                        <th class="text-end">Total Line Cost</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->consumptions as $c)
                        <tr>
                            <td>
                                <strong>{{ $c->product?->name }}</strong>
                                <div class="text-muted small">SKU: {{ $c->product?->sku }}</div>
                            </td>
                            <td class="text-end fw-semibold">{{ number_format($c->planned_qty, 2) }} {{ $c->product?->unit?->code }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($c->actual_consumed_qty, 2) }} {{ $c->product?->unit?->code }}</td>
                            <td class="text-end">₹{{ number_format($c->unit_cost, 2) }}</td>
                            <td class="text-end fw-bold">₹{{ number_format($c->total_cost, 2) }}</td>
                            <td>
                                <span class="badge {{ $order->status === 'completed' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $order->status === 'completed' ? 'Deducted from Stock' : 'Reserved' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quality Control (QC) Checks -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-vial-circle-check text-success me-2"></i> Quality Control (QC) Laboratory Checks</h6>
        @if($order->status !== 'completed')
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#qcModal">
                <i class="fa-solid fa-plus me-1"></i> Add Parameter
            </button>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Parameter</th>
                        <th>Standard Specification</th>
                        <th>Observed Reading</th>
                        <th>Result</th>
                        <th>Tested By</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->qualityChecks as $qc)
                        <tr>
                            <td><strong>{{ $qc->parameter_name }}</strong></td>
                            <td>{{ $qc->standard_specification }}</td>
                            <td><span class="badge bg-light text-dark border fs-6">{{ $qc->observed_value }}</span></td>
                            <td>
                                <span class="badge {{ $qc->is_passed ? 'bg-success' : 'bg-danger' }}">
                                    {{ $qc->is_passed ? 'PASSED' : 'REJECTED' }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $qc->tester?->name ?: 'Chemist' }}</small></td>
                            <td><small class="text-muted">{{ $qc->remarks ?: '-' }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                No QC parameters recorded yet. Click "Record QC Test" before finalization.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- QC Modal -->
<div class="modal fade" id="qcModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('production.orders.qc', $order->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-vial text-primary me-2"></i> Record QC Laboratory Check</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Parameter Name</label>
                        <select name="parameter_name" class="form-select" required>
                            <option value="pH Value">pH Value</option>
                            <option value="Viscosity (cPs)">Viscosity (cPs)</option>
                            <option value="Specific Gravity / Density">Specific Gravity / Density</option>
                            <option value="Active Matter %">Active Matter %</option>
                            <option value="Color & Clarity">Color & Clarity</option>
                            <option value="Odor">Odor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Standard Approved Specification</label>
                        <input type="text" name="standard_specification" class="form-control" placeholder="e.g. 1.5 - 2.0 pH, Clear Red Liquid" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Observed Laboratory Reading</label>
                        <input type="text" name="observed_value" class="form-control" placeholder="e.g. 1.8 pH" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">QC Result</label>
                        <select name="is_passed" class="form-select" required>
                            <option value="1">PASSED (Complies with standard)</option>
                            <option value="0">FAILED / REJECTED</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Remarks</label>
                        <input type="text" name="remarks" class="form-control" placeholder="Calibrated benchtop meter reading...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gudi-primary fw-semibold">Save QC Result</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Finalize Batch Modal -->
<div class="modal fade" id="finalizeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('production.orders.finalize', $order->id) }}">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-success"><i class="fa-solid fa-check-double me-2"></i> Finalize Production Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning small">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        Finalization atomically deducts actual consumed chemicals from inventory, adds approved finished output into the warehouse, and locks this batch permanently.
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Actual Yield Output Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" name="actual_qty" class="form-control fw-bold fs-6" value="{{ $order->planned_qty }}" required>
                            <small class="text-muted">Unit: {{ $order->formula?->outputUnit?->code }}</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Packaging Material Cost (₹)</label>
                            <input type="number" step="0.01" name="packaging_cost" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Power & Overhead Cost (₹)</label>
                            <input type="number" step="0.01" name="overhead_cost" class="form-control" value="0.00">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Manufacturing Date</label>
                            <input type="date" name="mfg_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Expiry / Retest Date</label>
                            <input type="date" name="expiry_date" class="form-control" value="{{ date('Y-m-d', strtotime('+2 years')) }}">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-2">Confirm Actual Ingredients Consumed</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Ingredient Chemical</th>
                                    <th>Planned Qty</th>
                                    <th>Actual Consumed Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->consumptions as $idx => $con)
                                    <tr>
                                        <td>
                                            <strong>{{ $con->product?->name }}</strong>
                                            <input type="hidden" name="consumptions[{{ $idx }}][consumption_id]" value="{{ $con->id }}">
                                        </td>
                                        <td>{{ number_format($con->planned_qty, 2) }} {{ $con->product?->unit?->code }}</td>
                                        <td>
                                            <input type="number" step="0.0001" name="consumptions[{{ $idx }}][actual_consumed_qty]" class="form-control form-control-sm" value="{{ $con->actual_consumed_qty }}" required>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-semibold px-4">
                        <i class="fa-solid fa-check me-1"></i> Confirm & Finalize Batch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
