@extends('layouts.app')

@section('title', 'Production Yield & Costing Report')

@push('styles')
<style>
    @media print {
        #sidebar, .topbar, .btn-print, .no-print, footer {
            display: none !important;
        }
        .page-content-wrapper {
            padding: 0 !important;
            overflow: visible !important;
            height: auto !important;
        }
        #main-content, #app-layout, body, html {
            height: auto !important;
            overflow: visible !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 no-print">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-industry text-primary me-2"></i> Production Yield & Costing Report</h4>
        <p class="text-muted small mb-0">Analysis of batch conversions, actual vs planned output scaling, chemical process losses, and final unit costs.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('reports.production', ['export' => 'csv']) }}" class="btn btn-outline-success">
            <i class="fa-solid fa-file-excel me-1.5"></i> Export Production CSV
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="fa-solid fa-print me-1.5"></i> Print / PDF
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-flask text-primary me-2"></i> Production Batches Log</h6>
        <span class="badge bg-light text-muted border">{{ $batches->total() }} batches recorded</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Order #</th>
                        <th>Date</th>
                        <th>Formula (BOM)</th>
                        <th>Output Product</th>
                        <th class="text-end">Planned Output</th>
                        <th class="text-end">Actual Output</th>
                        <th class="text-end">Process Loss %</th>
                        <th class="text-end">Unit Cost</th>
                        <th class="text-center">QC Status</th>
                        <th class="text-center pe-3">Batch Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $b)
                        <tr>
                            <td class="ps-3 fw-bold">
                                <a href="{{ route('production.orders.show', $b->id) }}" class="text-primary text-decoration-none">
                                    {{ $b->order_number }}
                                </a>
                            </td>
                            <td>{{ $b->order_date->format('d M Y') }}</td>
                            <td>{{ $b->formula?->name ?: 'Standard BOM' }}</td>
                            <td><strong>{{ $b->outputProduct?->name }}</strong></td>
                            <td class="text-end text-muted">{{ number_format($b->planned_quantity, 2) }} {{ $b->outputProduct?->unit?->code }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($b->actual_output_quantity, 2) }} {{ $b->outputProduct?->unit?->code }}</td>
                            <td class="text-end {{ $b->loss_percentage > 5 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                {{ number_format($b->loss_percentage, 2) }}%
                            </td>
                            <td class="text-end fw-bold">₹{{ number_format($b->actual_unit_cost, 2) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $b->qc_status === 'passed' ? 'bg-success' : ($b->qc_status === 'failed' ? 'bg-danger' : 'bg-warning text-dark') }} px-2 py-1">
                                    {{ strtoupper($b->qc_status) }}
                                </span>
                            </td>
                            <td class="text-center pe-3">
                                <span class="badge {{ $b->status === 'completed' ? 'bg-primary' : 'bg-secondary' }} px-2 py-1">
                                    {{ ucfirst($b->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-flask fa-3x mb-3 text-secondary opacity-50"></i>
                                <p class="mb-0">No production orders generated yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($batches->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $batches->links() }}
        </div>
    @endif
</div>
@endsection
