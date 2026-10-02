@extends('layouts.app')

@section('title', 'GSTR-1 Tax Summary & HSN Breakdown')

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
        <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-file-shield text-info me-2"></i> GSTR-1 Tax Summary & Filing Audit</h4>
        <p class="text-muted small mb-0">Indian GST compliance report: B2B registered invoices, B2C consumer invoices, and Table 12 HSN tax summaries.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('reports.gst', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-success">
            <i class="fa-solid fa-file-excel me-1.5"></i> Export GSTR-1 CSV
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="fa-solid fa-print me-1.5"></i> Print / PDF
        </button>
    </div>
</div>

<!-- Date Filter -->
<div class="card border-0 shadow-sm mb-4 no-print">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.gst') }}" class="row g-3 align-items-end">
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
                    <i class="fa-solid fa-filter me-1"></i> Recalculate GSTR-1
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Table 12: HSN Summary -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-barcode text-primary me-2"></i> Table 12: HSN-wise Chemical Outward Supplies</h6>
            <small class="text-muted">Mandatory HSN disclosure for chemical manufacturing invoices</small>
        </div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1">{{ count($hsnSummary) }} HSN Codes</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">HSN Code</th>
                        <th>Product / Chemical Description</th>
                        <th class="text-center">Total Quantity</th>
                        <th class="text-end">Taxable Value</th>
                        <th class="text-end">CGST (Central)</th>
                        <th class="text-end">SGST (State)</th>
                        <th class="text-end">IGST (Interstate)</th>
                        <th class="text-end pe-3">Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hsnSummary as $hsn)
                        <tr>
                            <td class="ps-3 fw-bold text-dark">{{ $hsn->hsn_code }}</td>
                            <td>{{ $hsn->product_name }}</td>
                            <td class="text-center fw-semibold">{{ number_format($hsn->total_qty, 2) }}</td>
                            <td class="text-end fw-semibold">₹{{ number_format($hsn->total_taxable, 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($hsn->total_cgst, 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($hsn->total_sgst, 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($hsn->total_igst, 2) }}</td>
                            <td class="text-end pe-3 fw-bold text-success">₹{{ number_format($hsn->total_val, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No HSN supply records in this date range.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Table 4: B2B Invoices -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-building text-success me-2"></i> Table 4: B2B Supplies (Registered Commercial Buyers)</h6>
            <small class="text-muted">Invoices issued to GST registered entities claiming Input Tax Credit (ITC)</small>
        </div>
        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">{{ count($b2bInvoices) }} B2B Invoices</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Invoice #</th>
                        <th>Date</th>
                        <th>Buyer Legal Name</th>
                        <th>Buyer GSTIN</th>
                        <th>Place of Supply</th>
                        <th class="text-end">Taxable Value</th>
                        <th class="text-end">CGST</th>
                        <th class="text-end">SGST</th>
                        <th class="text-end">IGST</th>
                        <th class="text-end pe-3">Invoice Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($b2bInvoices as $b2b)
                        <tr>
                            <td class="ps-3 fw-bold"><a href="{{ route('invoices.show', $b2b->id) }}">{{ $b2b->invoice_number }}</a></td>
                            <td>{{ $b2b->invoice_date->format('d-m-Y') }}</td>
                            <td><strong>{{ $b2b->customer?->name }}</strong></td>
                            <td><span class="badge bg-light text-dark border">{{ $b2b->customer?->gstin }}</span></td>
                            <td>{{ $b2b->place_of_supply }}</td>
                            <td class="text-end fw-semibold">₹{{ number_format($b2b->taxable_amount, 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($b2b->cgst_amount, 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($b2b->sgst_amount, 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($b2b->igst_amount, 2) }}</td>
                            <td class="text-end pe-3 fw-bold text-success">₹{{ number_format($b2b->grand_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">No registered B2B invoices in this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
