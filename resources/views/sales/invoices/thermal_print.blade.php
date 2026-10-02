<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
            width: 300px;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 2px 0; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print" style="margin-bottom: 10px; text-align: center;">
    <button onclick="window.print()">Print</button>
    <button onclick="window.close()">Close</button>
</div>

<div class="text-center">
    <h3 style="margin: 0;">GUDI CHEMICALS</h3>
    <small>MIDC Chemical Zone, Pune</small><br>
    <small>GSTIN: {{ $company->gstin }}</small><br>
    <small>Phone: {{ $company->phone ?: '9876543210' }}</small>
</div>

<div class="divider"></div>

<div>
    <strong>Bill:</strong> {{ $invoice->invoice_number }}<br>
    <strong>Date:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}<br>
    <strong>Customer:</strong> {{ $invoice->customer?->name }}
</div>

<div class="divider"></div>

<table>
    <thead>
        <tr>
            <th align="left">Item</th>
            <th class="text-center">Qty</th>
            <th class="text-end">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td colspan="3" class="bold">{{ $item->product_name }}</td>
            </tr>
            <tr>
                <td>{{ $item->hsn_code }} ({{ number_format($item->gst_rate, 0) }}%)</td>
                <td class="text-center">{{ number_format($item->quantity, 1) }}</td>
                <td class="text-end">₹{{ number_format($item->line_total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="divider"></div>

<table>
    <tr>
        <td>Taxable Amt:</td>
        <td class="text-end">₹{{ number_format($invoice->taxable_amount, 2) }}</td>
    </tr>
    @if($invoice->is_interstate)
        <tr>
            <td>IGST Total:</td>
            <td class="text-end">₹{{ number_format($invoice->igst_amount, 2) }}</td>
        </tr>
    @else
        <tr>
            <td>CGST (9%):</td>
            <td class="text-end">₹{{ number_format($invoice->cgst_amount, 2) }}</td>
        </tr>
        <tr>
            <td>SGST (9%):</td>
            <td class="text-end">₹{{ number_format($invoice->sgst_amount, 2) }}</td>
        </tr>
    @endif
    <tr>
        <td>Rounding:</td>
        <td class="text-end">{{ $invoice->rounding_adjustment >= 0 ? '+' : '' }}₹{{ number_format($invoice->rounding_adjustment, 2) }}</td>
    </tr>
    <tr class="bold" style="font-size: 14px;">
        <td>TOTAL:</td>
        <td class="text-end">₹{{ number_format($invoice->grand_total, 2) }}</td>
    </tr>
    <tr>
        <td>Paid Amount:</td>
        <td class="text-end">₹{{ number_format($invoice->paid_amount, 2) }}</td>
    </tr>
</table>

<div class="divider"></div>

<div class="text-center">
    <small>Thank you for choosing Gudi Chemicals!</small><br>
    <small>Safe Handling — Quality Certified</small>
</div>

</body>
</html>
