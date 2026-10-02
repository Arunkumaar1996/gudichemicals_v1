@extends('layouts.app')

@section('title', 'Sales & GST Tax Report')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-chart-line text-primary me-2"></i> Sales & GST Summary Report</h4>
        <p class="text-muted small mb-0">Periodic sales totals, taxable values, CGST, SGST and IGST breakdowns for GSTR-1 filing</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.sales') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted mb-0">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted mb-0">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-gudi-primary w-100 fw-semibold">Generate Report</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Total Invoices</small>
            <h3 class="fw-bold my-1 text-primary">{{ $summary['total_invoices'] }}</h3>
            <small class="text-muted">Total Bills Generated</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Taxable Sales Value</small>
            <h3 class="fw-bold my-1 text-dark">₹{{ number_format($summary['taxable_sales'], 2) }}</h3>
            <small class="text-muted">Net Base Amount</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Total GST Tax Collected</small>
            <h3 class="fw-bold my-1 text-info">₹{{ number_format($summary['total_gst'], 2) }}</h3>
            <small class="text-muted">CGST+SGST: ₹{{ number_format($summary['cgst_total']+$summary['sgst_total'], 2) }} | IGST: ₹{{ number_format($summary['igst_total'], 2) }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <small class="text-muted fw-semibold text-uppercase">Gross Sales Total</small>
            <h3 class="fw-bold my-1 text-success">₹{{ number_format($summary['gross_sales'], 2) }}</h3>
            <small class="text-muted">All Inclusive Revenue</small>
        </div>
    </div>
</div>

<!-- Detailed Register -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0">Sales Register (GSTR-1 Format)</h6>
        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> Print Summary
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Customer / GSTIN</th>
                        <th>Place of Supply</th>
                        <th class="text-end">Taxable Value</th>
                        <th class="text-end">CGST</th>
                        <th class="text-end">SGST</th>
                        <th class="text-end">IGST</th>
                        <th class="text-end">Invoice Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td><strong>{{ $inv->invoice_number }}</strong></td>
                            <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
                            <td>
                                <div>{{ $inv->customer?->name }}</div>
                                @if($inv->customer?->gstin)
                                    <small class="text-muted"><code>{{ $inv->customer->gstin }}</code></small>
                                @endif
                            </td>
                            <td>{{ $inv->place_of_supply }}</td>
                            <td class="text-end">₹{{ number_format($inv->taxable_amount, 2) }}</td>
                            <td class="text-end">₹{{ number_format($inv->cgst_amount, 2) }}</td>
                            <td class="text-end">₹{{ number_format($inv->sgst_amount, 2) }}</td>
                            <td class="text-end">₹{{ number_format($inv->igst_amount, 2) }}</td>
                            <td class="text-end fw-bold">₹{{ number_format($inv->grand_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No sales records in selected date range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
