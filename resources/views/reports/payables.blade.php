@extends('layouts.app')

@section('title', 'Vendor Outstanding Payables')

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
        <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-file-invoice-dollar text-danger me-2"></i> Vendor Payables & Raw Material Bills</h4>
        <p class="text-muted small mb-0">Supplier invoices, cumulative disbursements, and outstanding raw chemical purchase dues.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('reports.payables', ['export' => 'csv']) }}" class="btn btn-outline-success">
            <i class="fa-solid fa-file-excel me-1.5"></i> Export Payables CSV
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="fa-solid fa-print me-1.5"></i> Print / PDF
        </button>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Total Chemical Vendors</small>
            <h3 class="fw-bold my-1 text-primary">{{ count($vendors) }}</h3>
            <small class="text-muted">Approved Chemical Suppliers</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Total Accounts Payable</small>
            <h3 class="fw-bold my-1 text-danger">₹{{ number_format($totalPayable, 2) }}</h3>
            <small class="text-muted">Current Liabilities Due to Suppliers</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Vendors Pending Payment</small>
            <h3 class="fw-bold my-1 text-warning">{{ $vendors->filter(fn($v) => $v->outstanding > 0)->count() }}</h3>
            <small class="text-muted">Active Payment Schedules</small>
        </div>
    </div>
</div>

<!-- Vendors Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-truck-field text-primary me-2"></i> Supplier Balances Breakdown</h6>
        <span class="badge bg-light text-muted border">{{ count($vendors) }} vendors</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Supplier Legal Name</th>
                        <th>GSTIN</th>
                        <th>Contact / Phone</th>
                        <th class="text-end">Total Purchased</th>
                        <th class="text-end">Total Disbursed</th>
                        <th class="text-end pe-3">Outstanding Payable</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $v)
                        <tr>
                            <td class="ps-3">
                                <strong>{{ $v->name }}</strong>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $v->gstin ?: '—' }}</span></td>
                            <td>{{ $v->phone ?: '—' }}</td>
                            <td class="text-end fw-semibold">₹{{ number_format($v->total_purchased, 2) }}</td>
                            <td class="text-end text-success">₹{{ number_format($v->total_paid, 2) }}</td>
                            <td class="text-end pe-3">
                                @if($v->outstanding > 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 fw-bold">
                                        ₹{{ number_format($v->outstanding, 2) }}
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                        Settled (₹0.00)
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No vendor records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
