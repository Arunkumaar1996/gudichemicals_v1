@extends('reports.pdf.layout')

@section('title', 'GSTR-1 Monthly / Periodic Return Summary')
@section('report_title', 'GSTR-1 GST Return Summary')
@section('report_period', 'Period: ' . \Carbon\Carbon::parse($fromDate)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($toDate)->format('d M Y'))

@section('content')
<!-- B2B Section -->
<div style="font-size: 10px; font-weight: bold; color: #005a9c; margin-bottom: 4px; text-transform: uppercase;">
    Table 4: B2B Invoices (Taxable Outward Supplies to Registered Persons)
</div>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 12%;">Invoice #</th>
            <th style="width: 9%;">Date</th>
            <th style="width: 22%;">Recipient Business Name</th>
            <th style="width: 15%;">GSTIN of Recipient</th>
            <th style="width: 10%;">Place of Supply</th>
            <th style="width: 11%;" class="text-right">Taxable (Rs)</th>
            <th style="width: 7%;" class="text-right">CGST</th>
            <th style="width: 7%;" class="text-right">SGST</th>
            <th style="width: 7%;" class="text-right">IGST</th>
        </tr>
    </thead>
    <tbody>
        @forelse($b2bInvoices as $inv)
            <tr>
                <td class="fw-bold">{{ $inv->invoice_number }}</td>
                <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
                <td>{{ $inv->customer?->company_name ?: $inv->customer?->name }}</td>
                <td><span style="font-family: monospace; font-weight: bold;">{{ $inv->customer?->gstin }}</span></td>
                <td>{{ $inv->place_of_supply }}</td>
                <td class="text-right">{{ number_format($inv->taxable_amount, 2) }}</td>
                <td class="text-right">{{ number_format($inv->cgst_amount, 2) }}</td>
                <td class="text-right">{{ number_format($inv->sgst_amount, 2) }}</td>
                <td class="text-right">{{ number_format($inv->igst_amount, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="9" class="text-center" style="padding: 10px;">No B2B invoices recorded in this period.</td></tr>
        @endforelse
    </tbody>
    @if($b2bInvoices->count() > 0)
    <tfoot>
        <tr class="total-row">
            <td colspan="5" class="text-right">B2B SUBTOTAL:</td>
            <td class="text-right">{{ number_format($b2bInvoices->sum('taxable_amount'), 2) }}</td>
            <td class="text-right">{{ number_format($b2bInvoices->sum('cgst_amount'), 2) }}</td>
            <td class="text-right">{{ number_format($b2bInvoices->sum('sgst_amount'), 2) }}</td>
            <td class="text-right">{{ number_format($b2bInvoices->sum('igst_amount'), 2) }}</td>
        </tr>
    </tfoot>
    @endif
</table>

<!-- Table 12 HSN Summary -->
<div style="font-size: 10px; font-weight: bold; color: #005a9c; margin-top: 14px; margin-bottom: 4px; text-transform: uppercase;">
    Table 12: HSN-wise Summary of Outward Supplies
</div>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 10%;">HSN Code</th>
            <th style="width: 25%;">Product / Chemical Description</th>
            <th style="width: 10%;" class="text-right">Total Qty</th>
            <th style="width: 13%;" class="text-right">Taxable Value (Rs)</th>
            <th style="width: 10%;" class="text-right">CGST (Rs)</th>
            <th style="width: 10%;" class="text-right">SGST (Rs)</th>
            <th style="width: 10%;" class="text-right">IGST (Rs)</th>
            <th style="width: 12%;" class="text-right">Total Value (Rs)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($hsnSummary as $hsn)
            <tr>
                <td class="fw-bold" style="font-family: monospace;">{{ $hsn->hsn_code }}</td>
                <td>{{ $hsn->product_name }}</td>
                <td class="text-right">{{ number_format($hsn->total_qty, 2) }}</td>
                <td class="text-right">{{ number_format($hsn->total_taxable, 2) }}</td>
                <td class="text-right">{{ number_format($hsn->total_cgst, 2) }}</td>
                <td class="text-right">{{ number_format($hsn->total_sgst, 2) }}</td>
                <td class="text-right">{{ number_format($hsn->total_igst, 2) }}</td>
                <td class="text-right fw-bold">{{ number_format($hsn->total_val, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center" style="padding: 10px;">No HSN summary data found for period.</td></tr>
        @endforelse
    </tbody>
    @if($hsnSummary->count() > 0)
    <tfoot>
        <tr class="total-row">
            <td colspan="2" class="text-right">HSN TOTAL:</td>
            <td class="text-right">{{ number_format($hsnSummary->sum('total_qty'), 2) }}</td>
            <td class="text-right">{{ number_format($hsnSummary->sum('total_taxable'), 2) }}</td>
            <td class="text-right">{{ number_format($hsnSummary->sum('total_cgst'), 2) }}</td>
            <td class="text-right">{{ number_format($hsnSummary->sum('total_sgst'), 2) }}</td>
            <td class="text-right">{{ number_format($hsnSummary->sum('total_igst'), 2) }}</td>
            <td class="text-right">Rs. {{ number_format($hsnSummary->sum('total_val'), 2) }}</td>
        </tr>
    </tfoot>
    @endif
</table>
@endsection
