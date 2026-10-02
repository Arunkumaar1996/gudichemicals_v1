@extends('reports.pdf.layout')

@section('title', 'Vendor Payables & Procurement Liability Report')
@section('report_title', 'Vendor Payables Report')
@section('report_period', 'As of Date: ' . now()->format('d M Y'))

@section('content')
<table class="summary-table">
    <tr>
        <td style="width: 50%;">
            <span class="summary-label">Total Creditors / Suppliers</span>
            <span class="summary-val">{{ $vendors->count() }} Vendors Listed</span>
        </td>
        <td style="width: 50%;">
            <span class="summary-label">Total Outstanding Payables</span>
            <span class="summary-val text-danger">Rs. {{ number_format($totalPayable, 2) }}</span>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 28%;">Vendor / Supplier Name</th>
            <th style="width: 15%;">GSTIN</th>
            <th style="width: 14%;">Phone</th>
            <th style="width: 13%;" class="text-right">Total Invoiced</th>
            <th style="width: 12%;" class="text-right">Total Paid</th>
            <th style="width: 13%;" class="text-right">Outstanding Due</th>
        </tr>
    </thead>
    <tbody>
        @forelse($vendors as $idx => $v)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td class="fw-bold">
                    {{ $v->name }}
                    @if($v->company_name)
                        <br><span style="font-size: 7.5px; color: #64748b;">{{ $v->company_name }}</span>
                    @endif
                </td>
                <td style="font-family: monospace;">{{ $v->gstin ?: '-' }}</td>
                <td>{{ $v->phone ?: '-' }}</td>
                <td class="text-right">{{ number_format($v->total_purchased, 2) }}</td>
                <td class="text-right">{{ number_format($v->total_paid, 2) }}</td>
                <td class="text-right fw-bold {{ $v->outstanding > 0 ? 'text-danger' : 'text-success' }}">
                    Rs. {{ number_format($v->outstanding, 2) }}
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center" style="padding: 10px;">No vendors registered yet.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="4" class="text-right">TOTAL PAYABLE:</td>
            <td class="text-right">{{ number_format($vendors->sum('total_purchased'), 2) }}</td>
            <td class="text-right">{{ number_format($vendors->sum('total_paid'), 2) }}</td>
            <td class="text-right text-danger">Rs. {{ number_format($totalPayable, 2) }}</td>
        </tr>
    </tfoot>
</table>
@endsection
