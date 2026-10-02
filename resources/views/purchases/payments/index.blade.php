@extends('layouts.app')

@section('title', 'Vendor Payments')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-money-bill-transfer text-primary me-2"></i> Vendor Payments</h4>
        <p class="text-muted small mb-0">Record chemical supplier settlements, RTGS/NEFT payments and accounts payable reconciliation</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="{{ route('purchases.payments.create') }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Record Vendor Payment
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Payment #</th>
                        <th>Vendor / Supplier</th>
                        <th>Payment Date</th>
                        <th>Method</th>
                        <th>Reference / UTR #</th>
                        <th class="text-end">Amount Paid</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $pay)
                        <tr>
                            <td><strong>{{ $pay->payment_number }}</strong></td>
                            <td>{{ $pay->vendor?->company_name ?: $pay->vendor?->name }}</td>
                            <td>{{ $pay->payment_date->format('d M Y') }}</td>
                            <td><span class="badge bg-secondary">{{ strtoupper(str_replace('_', ' ', $pay->payment_method)) }}</span></td>
                            <td><code>{{ $pay->reference_no ?: '-' }}</code></td>
                            <td class="text-end fw-bold text-success fs-6">₹{{ number_format($pay->amount, 2) }}</td>
                            <td><small class="text-muted">{{ $pay->creator?->name ?: 'Accountant' }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No vendor payments recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payments->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection
