@extends('layouts.app')

@section('title', 'Formula ' . $formula->formula_code)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-mortar-pestle text-primary me-2"></i> Formula: {{ $formula->name }}</h4>
        <span class="badge bg-secondary me-2">{{ $formula->formula_code }}</span>
        <span class="badge bg-info me-2">v{{ $formula->version }}</span>
        <span class="badge bg-success">Approved Recipe</span>
    </div>
    <div class="mt-2 mt-md-0 d-flex gap-2">
        <a href="{{ route('production.orders.create') }}" class="btn btn-primary btn-sm fw-semibold">
            <i class="fa-solid fa-play me-1"></i> Launch Production Batch
        </a>
        <a href="{{ route('production.formulas.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Formulas
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <small class="text-muted fw-semibold">Target Finished Chemical</small>
            <h5 class="fw-bold text-dark my-1">{{ $formula->product?->name }}</h5>
            <small class="text-muted">SKU: {{ $formula->product?->sku }}</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <small class="text-muted fw-semibold">Standard Batch Size & Yield</small>
            <h5 class="fw-bold text-dark my-1">{{ number_format($formula->standard_batch_qty, 2) }} {{ $formula->outputUnit?->code }}</h5>
            <small class="text-success fw-semibold">Expected Yield: {{ number_format($formula->expected_yield_pct, 1) }}%</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <small class="text-muted fw-semibold">Compounding Protocol</small>
            <div class="small text-muted mt-1">{{ $formula->notes ?: 'Standard industrial chemical blending protocol.' }}</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold mb-0">Ingredients & Standard Consumption Ratios</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Ingredient / Chemical</th>
                        <th>Type</th>
                        <th class="text-end">Required Qty</th>
                        <th>Unit</th>
                        <th class="text-end">Approx. Unit Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formula->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->ingredient?->name }}</strong>
                                <div class="text-muted small">SKU: {{ $item->ingredient?->sku }}</div>
                            </td>
                            <td><span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $item->ingredient?->item_type)) }}</span></td>
                            <td class="text-end fw-bold fs-6">{{ number_format($item->quantity, 2) }}</td>
                            <td>{{ $item->unit?->code }}</td>
                            <td class="text-end">₹{{ number_format($item->ingredient?->purchase_cost, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($formula->productionOrders->isNotEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-industry text-success me-2"></i> Manufactured Batches Using This Formula</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Batch #</th>
                        <th>Date</th>
                        <th>Planned Qty</th>
                        <th>Actual Output</th>
                        <th>Unit Cost</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formula->productionOrders as $batch)
                        <tr>
                            <td><strong>{{ $batch->batch_number }}</strong></td>
                            <td>{{ $batch->order_date->format('d M Y') }}</td>
                            <td>{{ number_format($batch->planned_qty, 2) }}</td>
                            <td class="fw-bold text-success">{{ number_format($batch->actual_qty, 2) }}</td>
                            <td>₹{{ number_format($batch->unit_production_cost, 2) }}</td>
                            <td>
                                <span class="badge {{ $batch->status === 'completed' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $batch->status)) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('production.orders.show', $batch->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
