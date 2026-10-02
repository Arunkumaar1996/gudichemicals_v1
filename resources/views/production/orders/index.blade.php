@extends('layouts.app')

@section('title', 'Production Batches')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-industry text-primary me-2"></i> Production Batches</h4>
        <p class="text-muted small mb-0">Chemical manufacturing orders, batch compounding, QC tests and finished goods yield</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="{{ route('production.orders.create') }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Launch New Batch
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Batch Number</th>
                        <th>Target Product</th>
                        <th>Formula Recipe</th>
                        <th>Date</th>
                        <th class="text-end">Planned Qty</th>
                        <th class="text-end">Actual Output</th>
                        <th class="text-end">Unit Cost</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $batch)
                        <tr>
                            <td>
                                <strong>{{ $batch->batch_number }}</strong>
                            </td>
                            <td>
                                <strong>{{ $batch->outputProduct?->name }}</strong>
                                <div class="text-muted small">SKU: {{ $batch->outputProduct?->sku }}</div>
                            </td>
                            <td>{{ $batch->formula?->name }} (v{{ $batch->formula?->version }})</td>
                            <td>{{ $batch->order_date->format('d M Y') }}</td>
                            <td class="text-end fw-semibold">{{ number_format($batch->planned_qty, 2) }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($batch->actual_qty, 2) }}</td>
                            <td class="text-end">₹{{ number_format($batch->unit_production_cost, 2) }}</td>
                            <td>
                                <span class="badge {{ $batch->status === 'completed' ? 'bg-success' : ($batch->status === 'in_progress' ? 'bg-info' : 'bg-secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $batch->status)) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('production.orders.show', $batch->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                                    <i class="fa-solid fa-eye"></i> Manage Batch
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No production batches found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
