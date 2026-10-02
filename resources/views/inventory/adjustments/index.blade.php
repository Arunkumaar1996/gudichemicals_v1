@extends('layouts.app')

@section('title', 'Stock Adjustments')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-sliders text-primary me-2"></i> Stock Adjustments</h4>
        <p class="text-muted small mb-0">Controlled inventory corrections, physical stock reconciliation, breakage and sample adjustments</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="{{ route('inventory.adjustments.create') }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> New Stock Adjustment
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Adjustment #</th>
                        <th>Date</th>
                        <th>Warehouse</th>
                        <th>Reason</th>
                        <th>Items Adjusted</th>
                        <th>Status</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $adj)
                        <tr>
                            <td><strong>{{ $adj->adjustment_number }}</strong></td>
                            <td>{{ $adj->adjustment_date->format('d M Y') }}</td>
                            <td>{{ $adj->warehouse?->name }}</td>
                            <td>{{ $adj->reason }}</td>
                            <td>
                                @foreach($adj->items as $item)
                                    <span class="badge {{ $item->type === 'add' ? 'bg-success' : 'bg-danger' }} me-1 mb-1">
                                        {{ $item->product?->name }}: {{ $item->type === 'add' ? '+' : '-' }}{{ number_format($item->quantity, 2) }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                <span class="badge {{ $adj->status === 'approved' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($adj->status) }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $adj->creator?->name ?: 'Admin' }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No stock adjustments logged.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($adjustments->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $adjustments->links() }}
        </div>
    @endif
</div>
@endsection
