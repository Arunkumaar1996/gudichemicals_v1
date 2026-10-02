@extends('reports.pdf.layout')

@section('title', 'Production Yield & Costing Report')
@section('report_title', 'Production Yield & Costing Report')
@section('report_period', 'As of Date: ' . now()->format('d M Y'))

@section('content')
<table class="summary-table">
    <tr>
        <td style="width: 25%;">
            <span class="summary-label">Total Batches</span>
            <span class="summary-val">{{ $batches->count() }} Orders</span>
        </td>
        <td style="width: 25%;">
            <span class="summary-label">Completed Batches</span>
            <span class="summary-val text-success">{{ $batches->where('status', 'completed')->count() }}</span>
        </td>
        <td style="width: 25%;">
            <span class="summary-label">In Progress / QC</span>
            <span class="summary-val text-primary">{{ $batches->whereIn('status', ['in_progress', 'qc_pending'])->count() }}</span>
        </td>
        <td style="width: 25%;">
            <span class="summary-label">Total Output Cost</span>
            <span class="summary-val text-primary">Rs. {{ number_format($batches->sum('total_cost'), 2) }}</span>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 12%;">Batch Order #</th>
            <th style="width: 9%;">Date</th>
            <th style="width: 18%;">Formula Used</th>
            <th style="width: 18%;">Finished Chemical</th>
            <th style="width: 9%;" class="text-right">Planned</th>
            <th style="width: 9%;" class="text-right">Output</th>
            <th style="width: 7%;" class="text-right">Loss %</th>
            <th style="width: 9%;" class="text-right">Unit Cost</th>
            <th style="width: 9%;" class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($batches as $b)
            <tr>
                <td class="fw-bold">{{ $b->batch_number ?: $b->order_number }}</td>
                <td>{{ $b->order_date ? \Carbon\Carbon::parse($b->order_date)->format('d/m/Y') : '-' }}</td>
                <td>{{ $b->formula?->name ?: '-' }}</td>
                <td>{{ $b->outputProduct?->name ?: '-' }}</td>
                <td class="text-right">{{ number_format($b->planned_quantity ?? $b->planned_qty ?? 0, 2) }}</td>
                <td class="text-right fw-bold">{{ number_format($b->actual_output_quantity ?? $b->actual_qty ?? 0, 2) }}</td>
                <td class="text-right {{ ($b->loss_percentage ?? 0) > 5 ? 'text-danger' : '' }}">
                    {{ number_format($b->loss_percentage ?? 0, 2) }}%
                </td>
                <td class="text-right">Rs. {{ number_format($b->actual_unit_cost ?? $b->unit_cost ?? 0, 2) }}</td>
                <td class="text-center">
                    <span class="badge-status {{ $b->status === 'completed' ? 'badge-paid' : ($b->status === 'in_progress' ? 'badge-partial' : 'badge-unpaid') }}">
                        {{ ucfirst(str_replace('_', ' ', $b->status)) }}
                    </span>
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="text-center" style="padding: 10px;">No production batches recorded.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="4" class="text-right">TOTALS:</td>
            <td class="text-right">{{ number_format($batches->sum(fn($b) => $b->planned_quantity ?? $b->planned_qty ?? 0), 2) }}</td>
            <td class="text-right">{{ number_format($batches->sum(fn($b) => $b->actual_output_quantity ?? $b->actual_qty ?? 0), 2) }}</td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
@endsection
