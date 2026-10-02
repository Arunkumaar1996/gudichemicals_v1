@extends('layouts.app')

@section('title', 'Sales Invoices')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-file-invoice text-primary me-2"></i> Sales Invoices</h4>
        <p class="text-muted small mb-0">GST compliant tax invoices, B2B wholesale bills, retail POS invoices and payment status</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="{{ route('pos.index') }}" class="btn btn-success fw-semibold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> New POS Bill
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('invoices.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by invoice #, customer name or phone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="payment_status" class="form-select">
                    <option value="">All Payment Status</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partially_paid" {{ request('payment_status') === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From Date">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="To Date">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Filter Invoices</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Invoice Number</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Tier</th>
                        <th class="text-end">Taxable Value</th>
                        <th class="text-end">GST Total</th>
                        <th class="text-end">Grand Total</th>
                        <th>Payment</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td>
                                <strong>{{ $inv->invoice_number }}</strong>
                            </td>
                            <td>
                                <div>{{ $inv->customer?->name }}</div>
                                @if($inv->customer?->company_name)
                                    <small class="text-muted">{{ $inv->customer->company_name }}</small>
                                @endif
                            </td>
                            <td>{{ $inv->invoice_date->format('d M Y') }}</td>
                            <td>
                                <span class="badge {{ $inv->price_tier === 'wholesale' ? 'bg-primary' : 'bg-info' }}">
                                    {{ ucfirst($inv->price_tier) }}
                                </span>
                            </td>
                            <td class="text-end">₹{{ number_format($inv->taxable_amount, 2) }}</td>
                            <td class="text-end">₹{{ number_format($inv->cgst_amount + $inv->sgst_amount + $inv->igst_amount, 2) }}</td>
                            <td class="text-end fw-bold text-success fs-6">₹{{ number_format($inv->grand_total, 2) }}</td>
                            <td>
                                <span class="badge {{ $inv->payment_status === 'paid' ? 'bg-success' : ($inv->payment_status === 'partially_paid' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ ucfirst($inv->payment_status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="View Details">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Print A4 Tax Invoice">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                <a href="{{ route('invoices.print', [$inv->id, 'format' => 'thermal']) }}" target="_blank" class="btn btn-sm btn-outline-success py-1 px-2" title="Print 80mm Thermal Receipt">
                                    <i class="fa-solid fa-receipt"></i>
                                </a>
                                <a href="{{ route('invoices.pdf', $inv->id) }}" class="btn btn-sm btn-outline-danger py-1 px-2" title="Download PDF">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No sales invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($invoices->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $invoices->links() }}
        </div>
    @endif
</div>
@endsection
