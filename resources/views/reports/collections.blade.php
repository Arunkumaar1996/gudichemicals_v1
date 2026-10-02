@extends('layouts.app')

@section('title', 'Daily Cash & Collections Register')

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
        <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-wallet text-warning me-2"></i> Daily Cash & Collections Register</h4>
        <p class="text-muted small mb-0">Audit sales receipt disbursements across Cash, UPI QR, Credit Card, and Bank Transfers.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('reports.collections', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-outline-danger">
            <i class="fa-solid fa-file-pdf me-1.5"></i> Download PDF
        </a>
        <a href="{{ route('reports.collections', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-success">
            <i class="fa-solid fa-file-excel me-1.5"></i> Export Collections CSV
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="fa-solid fa-print me-1.5"></i> Print
        </button>
    </div>
</div>

<!-- Date Filter -->
<div class="card border-0 shadow-sm mb-4 no-print">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.collections') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-gudi-primary w-100 fw-semibold">
                    <i class="fa-solid fa-filter me-1"></i> Filter Collections
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Collections Breakdown by Tender Mode -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white border-start border-4 border-success">
            <small class="text-muted fw-semibold text-uppercase"><i class="fa-solid fa-money-bill-1 text-success me-1"></i> Cash Tender</small>
            <h3 class="fw-bold my-1 text-success">₹{{ number_format($byMode['cash'] ?? 0, 2) }}</h3>
            <small class="text-muted">Physical Till Balance</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white border-start border-4 border-info">
            <small class="text-muted fw-semibold text-uppercase"><i class="fa-solid fa-qrcode text-info me-1"></i> UPI / QR Code</small>
            <h3 class="fw-bold my-1 text-info">₹{{ number_format($byMode['upi'] ?? 0, 2) }}</h3>
            <small class="text-muted">Instant Digital Settlements</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white border-start border-4 border-primary">
            <small class="text-muted fw-semibold text-uppercase"><i class="fa-solid fa-credit-card text-primary me-1"></i> Card & POS Machine</small>
            <h3 class="fw-bold my-1 text-primary">₹{{ number_format($byMode['card'] ?? 0, 2) }}</h3>
            <small class="text-muted">Debit & Credit Cards</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white border-start border-4 border-dark">
            <small class="text-muted fw-semibold text-uppercase"><i class="fa-solid fa-building-columns text-dark me-1"></i> Bank / Credit</small>
            <h3 class="fw-bold my-1 text-dark">₹{{ number_format(($byMode['bank_transfer'] ?? 0) + ($byMode['credit'] ?? 0), 2) }}</h3>
            <small class="text-muted">NEFT/RTGS & Accounts Due</small>
        </div>
    </div>
</div>

<!-- Detailed Transactions Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-receipt text-primary me-2"></i> Payment Transactions Log</h6>
        <span class="badge bg-light text-muted border">{{ count($payments) }} transactions</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Receipt #</th>
                        <th>Date & Time</th>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Payment Mode</th>
                        <th>Transaction / UTR #</th>
                        <th class="text-end pe-3">Amount Collected</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            <td class="ps-3 fw-bold text-dark">{{ $p->payment_number }}</td>
                            <td>{{ $p->payment_date->format('d M Y') }}</td>
                            <td>
                                @if($p->salesInvoice)
                                    <a href="{{ route('invoices.show', $p->salesInvoice->id) }}" class="fw-semibold text-primary">
                                        {{ $p->salesInvoice->invoice_number }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><strong>{{ $p->salesInvoice?->customer?->name ?: 'Walk-in Customer' }}</strong></td>
                            <td>
                                @php
                                    $modeBadge = match($p->payment_method) {
                                        'cash' => 'bg-success-subtle text-success border-success-subtle',
                                        'upi' => 'bg-info-subtle text-info border-info-subtle',
                                        'card' => 'bg-primary-subtle text-primary border-primary-subtle',
                                        default => 'bg-secondary-subtle text-secondary border-secondary-subtle'
                                    };
                                @endphp
                                <span class="badge border {{ $modeBadge }} px-2 py-1">
                                    {{ strtoupper($p->payment_method) }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $p->reference_number ?: 'N/A' }}</small></td>
                            <td class="text-end pe-3 fw-bold text-success">₹{{ number_format($p->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No collections recorded in this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
