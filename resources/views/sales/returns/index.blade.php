@extends('layouts.app')

@section('title', 'Sales Returns & Credit Notes')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-arrow-rotate-left text-primary me-2"></i> Sales Returns & Credit Notes</h4>
        <p class="text-muted small mb-0">Manage customer returns, damaged/saleable stock inspections and accounts credit notes</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="{{ route('returns.create') }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Process Sales Return
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Credit Note #</th>
                        <th>Original Invoice</th>
                        <th>Customer</th>
                        <th>Return Date</th>
                        <th>Reason</th>
                        <th class="text-end">Refund Amount</th>
                        <th>Settlement</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $ret)
                        <tr>
                            <td><strong>{{ $ret->return_number }}</strong></td>
                            <td>
                                <a href="{{ route('invoices.show', $ret->sales_invoice_id) }}" class="text-decoration-none">
                                    {{ $ret->salesInvoice?->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $ret->customer?->name }}</td>
                            <td>{{ $ret->return_date->format('d M Y') }}</td>
                            <td>{{ $ret->reason }}</td>
                            <td class="text-end fw-bold text-danger">₹{{ number_format($ret->total_refund_amount, 2) }}</td>
                            <td>
                                <span class="badge {{ $ret->refund_status === 'refunded' ? 'bg-success' : 'bg-info' }}">
                                    {{ ucwords(str_replace('_', ' ', $ret->refund_status)) }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $ret->creator?->name ?: 'Staff' }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No sales returns recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($returns->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $returns->links() }}
        </div>
    @endif
</div>
@endsection
