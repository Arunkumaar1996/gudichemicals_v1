@extends('layouts.app')

@section('title', 'Production Yield & Costing Report')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-vial text-primary me-2"></i> Production Yield & Costing Report</h4>
        <p class="text-muted small mb-0">Analysis of chemical batches, planned vs actual yields, process loss and finished unit costs</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Batch #</th>
                        <th>Output Chemical</th>
                        <th>Formula Recipe</th>
                        <th>Date</th>
                        <th class="text-end">Planned Qty</th>
                        <th class="text-end">Actual Yield</th>
                        <th class="text-end">Raw Material Cost</th>
                        <th class="text-end">Unit Cost</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $b)
                        <tr>
                            <td><strong>{{ $b->batch_number }}</strong></td>
                            <td>{{ $b->outputProduct?->name }}</td>
                            <td>{{ $b->formula?->name }} (v{{ $b->formula?->version }})</td>
                            <td>{{ $b->order_date->format('d M Y') }}</td>
                            <td class="text-end">{{ number_format($b->planned_qty, 2) }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($b->actual_qty, 2) }}</td>
                            <td class="text-end">₹{{ number_format($b->total_material_cost, 2) }}</td>
                            <td class="text-end fw-bold text-primary">₹{{ number_format($b->unit_production_cost, 2) }}</td>
                            <td>
                                <span class="badge {{ $b->status === 'completed' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $b->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No production batches found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($batches->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $batches->links() }}
        </div>
    @endif
</div>
@endsection
