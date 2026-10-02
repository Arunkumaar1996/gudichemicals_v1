@extends('layouts.app')

@section('title', 'Customer Ledger — ' . $customer->name)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-address-book text-primary me-2"></i> {{ $customer->name }}</h4>
        <span class="badge {{ $customer->customer_type === 'wholesale' ? 'bg-primary' : 'bg-info' }} me-2">
            {{ ucfirst($customer->customer_type) }} Account
        </span>
        @if($customer->company_name)
            <span class="text-muted small fw-semibold">{{ $customer->company_name }}</span>
        @endif
    </div>
    <div class="mt-2 mt-md-0 d-flex gap-2">
        <a href="{{ route('masters.customers.edit', $customer->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-pen me-1"></i> Edit Profile
        </a>
        <a href="{{ route('masters.customers.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <small class="text-muted fw-semibold">Outstanding Due Balance</small>
            <h3 class="fw-bold {{ $customer->current_balance > 0 ? 'text-danger' : 'text-success' }} my-1">
                ₹{{ number_format($customer->current_balance, 2) }}
            </h3>
            <small class="text-muted">Credit Limit: ₹{{ number_format($customer->credit_limit, 2) }}</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <small class="text-muted fw-semibold">GSTIN & Compliance</small>
            <h5 class="fw-bold my-1 text-dark">{{ $customer->gstin ?: 'Not Registered' }}</h5>
            <small class="text-muted">{{ $customer->state_name }} (Code: {{ $customer->state_code }})</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <small class="text-muted fw-semibold">Contact Info</small>
            <div class="fw-bold text-dark mt-1"><i class="fa-solid fa-phone me-1 text-primary"></i> {{ $customer->phone }}</div>
            <div class="text-muted small"><i class="fa-regular fa-envelope me-1"></i> {{ $customer->email ?: 'N/A' }}</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-file-invoice text-success me-2"></i> Recent Invoices & Billing History</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Taxable Value</th>
                        <th>GST (CGST/SGST/IGST)</th>
                        <th class="text-end">Grand Total</th>
                        <th class="text-end">Paid Amount</th>
                        <th class="text-end">Balance Due</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->salesInvoices as $inv)
                        <tr>
                            <td><strong>{{ $inv->invoice_number }}</strong></td>
                            <td>{{ $inv->invoice_date->format('d M Y') }}</td>
                            <td>₹{{ number_format($inv->taxable_amount, 2) }}</td>
                            <td>
                                @if($inv->is_interstate)
                                    ₹{{ number_format($inv->igst_amount, 2) }} (IGST)
                                @else
                                    ₹{{ number_format($inv->cgst_amount + $inv->sgst_amount, 2) }} (CGST+SGST)
                                @endif
                            </td>
                            <td class="text-end fw-bold">₹{{ number_format($inv->grand_total, 2) }}</td>
                            <td class="text-end text-success">₹{{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="text-end fw-bold {{ $inv->due_amount > 0 ? 'text-danger' : 'text-muted' }}">
                                ₹{{ number_format($inv->due_amount, 2) }}
                            </td>
                            <td>
                                <span class="badge {{ $inv->payment_status === 'paid' ? 'bg-success' : ($inv->payment_status === 'partially_paid' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ ucfirst($inv->payment_status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-3">No invoices billed to this customer yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
