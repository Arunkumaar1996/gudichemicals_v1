@extends('reports.pdf.layout')

@section('title', 'Customer Receivables & Outstanding Aging Report')
@section('report_title', 'Customer Receivables Report')
@section('report_period', 'As of Date: ' . now()->format('d M Y'))

@section('content')
<table class="summary-table">
    <tr>
        <td style="width: 50%;">
            <span class="summary-label">Total Debtors Listed</span>
            <span class="summary-val">{{ $customers->count() }} Customer Accounts</span>
        </td>
        <td style="width: 50%;">
            <span class="summary-label">Total Outstanding Receivables</span>
            <span class="summary-val text-danger">Rs. {{ number_format($totalReceivable, 2) }}</span>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 25%;">Customer Name / Trade</th>
            <th style="width: 14%;">Phone</th>
            <th style="width: 14%;">GSTIN</th>
            <th style="width: 10%;">Type</th>
            <th style="width: 10%;" class="text-right">Credit Limit</th>
            <th style="width: 11%;" class="text-right">Total Billed</th>
            <th style="width: 11%;" class="text-right">Outstanding</th>
        </tr>
    </thead>
    <tbody>
        @forelse($customers as $idx => $c)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td class="fw-bold">
                    {{ $c->name }}
                    @if($c->company_name)
                        <br><span style="font-size: 7.5px; color: #64748b;">{{ $c->company_name }}</span>
                    @endif
                </td>
                <td>{{ $c->phone ?: '-' }}</td>
                <td style="font-family: monospace;">{{ $c->gstin ?: 'Unregistered' }}</td>
                <td>{{ strtoupper($c->customer_type) }}</td>
                <td class="text-right">{{ number_format($c->credit_limit, 2) }}</td>
                <td class="text-right">{{ number_format($c->total_billed, 2) }}</td>
                <td class="text-right fw-bold {{ $c->outstanding > 0 ? 'text-danger' : 'text-success' }}">
                    Rs. {{ number_format($c->outstanding, 2) }}
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center" style="padding: 10px;">No customer accounts found.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="6" class="text-right">TOTAL RECEIVABLE:</td>
            <td class="text-right">{{ number_format($customers->sum('total_billed'), 2) }}</td>
            <td class="text-right text-danger">Rs. {{ number_format($totalReceivable, 2) }}</td>
        </tr>
    </tfoot>
</table>
@endsection
