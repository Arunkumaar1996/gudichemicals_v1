@extends('reports.pdf.layout')

@section('title', 'Sales Register & GST Tax Report')
@section('report_title', 'Sales Register & GST Tax Report')
@section('report_period', 'Period: ' . \Carbon\Carbon::parse($fromDate)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($toDate)->format('d M Y'))

@section('content')
<!-- Financial Summary Strip -->
<table class="summary-table">
    <tr>
        <td style="width: 20%;">
            <span class="summary-label">Total Invoices</span>
            <span class="summary-val">{{ $summary['total_invoices'] }}</span>
        </td>
        <td style="width: 25%;">
            <span class="summary-label">Taxable Turnover</span>
            <span class="summary-val">Rs. {{ number_format($summary['taxable_sales'], 2) }}</span>
        </td>
        <td style="width: 25%;">
            <span class="summary-label">Total GST Collected</span>
            <span class="summary-val text-primary">Rs. {{ number_format($summary['total_gst'], 2) }}</span>
        </td>
        <td style="width: 30%;">
            <span class="summary-label">Gross Billed Sales</span>
            <span class="summary-val text-success">Rs. {{ number_format($summary['gross_sales'], 2) }}</span>
        </td>
    </tr>
</table>

<!-- Invoices Data Table -->
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 11%;">Invoice #</th>
            <th style="width: 9%;">Date</th>
            <th style="width: 20%;">Customer Name</th>
            <th style="width: 13%;">GSTIN</th>
            <th style="width: 10%;" class="text-right">Taxable</th>
            <th style="width: 7%;" class="text-right">CGST</th>
            <th style="width: 7%;" class="text-right">SGST</th>
            <th style="width: 7%;" class="text-right">IGST</th>
            <th style="width: 9%;" class="text-right">Grand Total</th>
            <th style="width: 7%;" class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($invoices as $inv)
            <tr>
                <td class="fw-bold">{{ $inv->invoice_number }}</td>
                <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
                <td>
                    {{ $inv->customer?->name ?? 'Walk-in / Cash' }}
                    @if($inv->customer?->company_name)
                        <br><span style="font-size: 7.5px; color: #64748b;">{{ $inv->customer->company_name }}</span>
                    @endif
                </td>
                <td>{{ $inv->customer?->gstin ?: 'Unregistered' }}</td>
                <td class="text-right">{{ number_format($inv->taxable_amount, 2) }}</td>
                <td class="text-right">{{ number_format($inv->cgst_amount, 2) }}</td>
                <td class="text-right">{{ number_format($inv->sgst_amount, 2) }}</td>
                <td class="text-right">{{ number_format($inv->igst_amount, 2) }}</td>
                <td class="text-right fw-bold">Rs. {{ number_format($inv->grand_total, 2) }}</td>
                <td class="text-center">
                    <span class="badge-status {{ $inv->payment_status === 'paid' ? 'badge-paid' : ($inv->payment_status === 'partially_paid' ? 'badge-partial' : 'badge-unpaid') }}">
                        {{ ucfirst($inv->payment_status) }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center" style="padding: 15px;">No sales invoices posted in this period.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="4" class="text-right">TOTAL:</td>
            <td class="text-right">{{ number_format($summary['taxable_sales'], 2) }}</td>
            <td class="text-right">{{ number_format($summary['cgst_total'], 2) }}</td>
            <td class="text-right">{{ number_format($summary['sgst_total'], 2) }}</td>
            <td class="text-right">{{ number_format($summary['igst_total'], 2) }}</td>
            <td class="text-right">Rs. {{ number_format($summary['gross_sales'], 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

@if(!empty($collections))
<!-- Collections Breakdown Summary -->
<div style="margin-top: 10px; width: 50%;">
    <div style="font-size: 9px; font-weight: bold; margin-bottom: 4px; color: #005a9c; text-transform: uppercase;">Payment Mode Collections in Period</div>
    <table class="data-table" style="margin-bottom: 0;">
        <thead>
            <tr>
                <th>Payment Mode</th>
                <th class="text-right">Amount Collected</th>
            </tr>
        </thead>
        <tbody>
            @foreach($collections as $mode => $amt)
                <tr>
                    <td class="fw-bold">{{ strtoupper(str_replace('_', ' ', $mode)) }}</td>
                    <td class="text-right">Rs. {{ number_format($amt, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
