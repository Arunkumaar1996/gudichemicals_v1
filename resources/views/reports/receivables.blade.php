@extends('layouts.app')

@section('title', 'Customer Outstanding Receivables')

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
        <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-hand-holding-dollar text-success me-2"></i> Customer Receivables & Outstanding Due</h4>
        <p class="text-muted small mb-0">Customer ledger balances, credit limit monitoring, and pending debt recovery.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('reports.receivables', ['export' => 'csv']) }}" class="btn btn-outline-success">
            <i class="fa-solid fa-file-excel me-1.5"></i> Export Receivables CSV
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="fa-solid fa-print me-1.5"></i> Print / PDF
        </button>
    </div>
</div>

<!-- Key Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Total Customers Tracked</small>
            <h3 class="fw-bold my-1 text-primary">{{ count($customers) }}</h3>
            <small class="text-muted">Retail & Wholesale Accounts</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Total Outstanding Market Debt</small>
            <h3 class="fw-bold my-1 text-danger">₹{{ number_format($totalReceivable, 2) }}</h3>
            <small class="text-muted">Pending Customer Collections</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Accounts with Overdue Balances</small>
            <h3 class="fw-bold my-1 text-warning">{{ $customers->filter(fn($c) => $c->outstanding > 0)->count() }}</h3>
            <small class="text-muted">Requires Recovery Follow-up</small>
        </div>
    </div>
</div>

<!-- Customers Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-users text-primary me-2"></i> Customer Balances Breakdown</h6>
        <span class="badge bg-light text-muted border">{{ count($customers) }} accounts</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Customer Name</th>
                        <th>Contact / Phone</th>
                        <th>GSTIN</th>
                        <th>Type</th>
                        <th class="text-end">Credit Limit</th>
                        <th class="text-end">Total Invoiced</th>
                        <th class="text-end">Total Paid</th>
                        <th class="text-end pe-3">Outstanding Due</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('masters.customers.show', $c->id) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $c->name }}
                                </a>
                            </td>
                            <td>{{ $c->phone ?: '—' }}</td>
                            <td><small class="text-muted">{{ $c->gstin ?: 'Unregistered' }}</small></td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ ucfirst($c->customer_type) }}</span>
                            </td>
                            <td class="text-end text-muted">₹{{ number_format($c->credit_limit, 2) }}</td>
                            <td class="text-end fw-semibold">₹{{ number_format($c->total_billed, 2) }}</td>
                            <td class="text-end text-success">₹{{ number_format($c->total_paid, 2) }}</td>
                            <td class="text-end pe-3">
                                @if($c->outstanding > 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 fw-bold">
                                        ₹{{ number_format($c->outstanding, 2) }}
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                        Cleared (₹0.00)
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No customers registered in system.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
