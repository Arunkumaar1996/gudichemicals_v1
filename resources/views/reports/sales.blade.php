@extends('layouts.app')

@section('title', 'Sales Register & GST Tax Report')

@push('styles')
<style>
    @media print {
        #sidebar, .topbar, .btn-print, .no-print, footer, .card-header-actions {
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
        .table {
            font-size: 11px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 no-print">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-chart-line text-primary me-2"></i> Sales Register & GST Report</h4>
        <p class="text-muted small mb-0">Detailed list of posted sales invoices, taxable turnovers, and GST tax breakdowns.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('reports.sales', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-success">
            <i class="fa-solid fa-file-excel me-1.5"></i> Export to Excel / CSV
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="fa-solid fa-print me-1.5"></i> Print / PDF
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4 no-print">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.sales') }}" class="row g-3 align-items-end">
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
                    <i class="fa-solid fa-filter me-1"></i> Filter Invoices
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Printable Header for Print/PDF -->
<div class="d-none d-print-block mb-4 text-center border-bottom pb-3">
    <h3 class="fw-bold mb-0">GUDI CHEMICALS</h3>
    <p class="small text-muted mb-1">Industrial Chemical Manufacturing & Distribution</p>
    <h5 class="fw-bold mt-2">SALES & GST REGISTER ({{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }})</h5>
</div>

<!-- Summary Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Total Invoices</small>
            <h3 class="fw-bold my-1 text-primary">{{ $summary['total_invoices'] }}</h3>
            <small class="text-muted">Completed Bills</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Taxable Value</small>
            <h3 class="fw-bold my-1 text-dark">₹{{ number_format($summary['taxable_sales'], 2) }}</h3>
            <small class="text-muted">Net Base Turnover</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Total Tax (CGST+SGST+IGST)</small>
            <h3 class="fw-bold my-1 text-info">₹{{ number_format($summary['total_gst'], 2) }}</h3>
            <small class="text-muted">CGST: ₹{{ number_format($summary['cgst_total'], 2) }} | SGST: ₹{{ number_format($summary['sgst_total'], 2) }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Gross Revenue</small>
            <h3 class="fw-bold my-1 text-success">₹{{ number_format($summary['gross_sales'], 2) }}</h3>
            <small class="text-muted">Total Billing Collected</small>
        </div>
    </div>
</div>

<!-- Invoices Data Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list text-primary me-2"></i> Invoices Breakdown</h6>
        <span class="badge bg-light text-muted border">{{ count($invoices) }} records</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Invoice #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>GSTIN</th>
                        <th class="text-end">Taxable Value</th>
                        <th class="text-end">CGST</th>
                        <th class="text-end">SGST</th>
                        <th class="text-end">IGST</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Grand Total</th>
                        <th class="text-center pe-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('invoices.show', $inv->id) }}" class="fw-bold text-primary text-decoration-none">
                                    {{ $inv->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $inv->invoice_date->format('d M Y') }}</td>
                            <td>
                                <strong>{{ $inv->customer?->name ?: 'Walk-in Customer' }}</strong>
                            </td>
                            <td>
                                <small class="text-muted">{{ $inv->customer?->gstin ?: 'Unregistered' }}</small>
                            </td>
                            <td class="text-end fw-semibold">₹{{ number_format($inv->taxable_amount, 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($inv->cgst_amount, 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($inv->sgst_amount, 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($inv->igst_amount, 2) }}</td>
                            <td class="text-end text-success">{{ $inv->discount_total > 0 ? '-₹'.number_format($inv->discount_total, 2) : '—' }}</td>
                            <td class="text-end fw-bold text-success">₹{{ number_format($inv->grand_total, 2) }}</td>
                            <td class="text-center pe-3">
                                <span class="badge {{ $inv->payment_status === 'paid' ? 'bg-success' : ($inv->payment_status === 'partial' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ strtoupper($inv->payment_status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-file-circle-xmark fa-3x mb-3 text-secondary opacity-50"></i>
                                <p class="mb-0">No sales invoices found for the selected period.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
