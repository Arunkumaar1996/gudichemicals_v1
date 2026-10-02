@extends('reports.pdf.layout')

@section('title', 'Daily Collections & Payment Register')
@section('report_title', 'Collections Register')
@section('report_period', 'Period: ' . \Carbon\Carbon::parse($fromDate)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($toDate)->format('d M Y'))

@section('content')
<!-- Collections Summary -->
<table class="summary-table">
    <tr>
        <td style="width: 35%;">
            <span class="summary-label">Total Payment Transactions</span>
            <span class="summary-val">{{ $payments->count() }} Payments</span>
        </td>
        <td style="width: 65%;">
            <span class="summary-label">Total Realized Collections</span>
            <span class="summary-val text-success">Rs. {{ number_format($payments->sum('amount'), 2) }}</span>
        </td>
    </tr>
</table>

@if(!empty($byMode) && count($byMode) > 0)
<div style="margin-bottom: 10px;">
    <div style="font-size: 8.5px; font-weight: bold; color: #005a9c; text-transform: uppercase; margin-bottom: 3px;">Collection Breakdown By Payment Mode</div>
    <table class="summary-table" style="margin-bottom: 8px;">
        <tr>
            @foreach($byMode as $mode => $amt)
                <td>
                    <span class="summary-label">{{ strtoupper(str_replace('_', ' ', $mode)) }}</span>
                    <span class="summary-val">Rs. {{ number_format($amt, 2) }}</span>
                </td>
            @endforeach
        </tr>
    </table>
</div>
@endif

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 14%;">Receipt #</th>
            <th style="width: 11%;">Payment Date</th>
            <th style="width: 14%;">Invoice #</th>
            <th style="width: 25%;">Customer Name</th>
            <th style="width: 12%;">Mode</th>
            <th style="width: 12%;">Ref / UTR</th>
            <th style="width: 12%;" class="text-right">Amount (Rs)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payments as $p)
            <tr>
                <td class="fw-bold">{{ $p->payment_number }}</td>
                <td>{{ $p->payment_date->format('d/m/Y') }}</td>
                <td>{{ $p->salesInvoice?->invoice_number ?: '-' }}</td>
                <td>{{ $p->salesInvoice?->customer?->name ?: 'Walk-in Customer' }}</td>
                <td><span class="badge-status badge-paid">{{ strtoupper($p->payment_method) }}</span></td>
                <td>{{ $p->reference_number ?: '-' }}</td>
                <td class="text-right fw-bold text-success">{{ number_format($p->amount, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center" style="padding: 10px;">No payment collections found in this date range.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="6" class="text-right">TOTAL REALIZED:</td>
            <td class="text-right text-success">Rs. {{ number_format($payments->sum('amount'), 2) }}</td>
        </tr>
    </tfoot>
</table>
@endsection
