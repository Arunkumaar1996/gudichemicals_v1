<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #005a9c;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .title {
            color: #005a9c;
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 5px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .no-border, .no-border td { border: none; }
    </style>
</head>
<body>

<div class="header">
    <div class="title">GUDI CHEMICALS</div>
    <div>Chemical Manufacturing & Industrial Solutions</div>
    <div>{{ $company->address ?: 'MIDC Chemical Zone, Pune, Maharashtra' }}</div>
    <div>GSTIN: {{ $company->gstin }} | State: {{ $company->state_name }} ({{ $company->state_code }})</div>
    <div style="font-size: 14px; font-weight: bold; margin-top: 5px;">TAX INVOICE</div>
</div>

<table class="no-border" style="margin-bottom: 15px;">
    <tr>
        <td style="width: 55%; vertical-align: top;">
            <strong>Billed To:</strong><br>
            <span style="font-size: 12px; font-weight: bold;">{{ $invoice->customer?->name }}</span><br>
            @if($invoice->customer?->company_name)
                {{ $invoice->customer->company_name }}<br>
            @endif
            {{ $invoice->customer?->billing_address ?: 'Counter Sale' }}<br>
            GSTIN: {{ $invoice->customer?->gstin ?: 'Unregistered' }}<br>
            State: {{ $invoice->customer?->state_name }} ({{ $invoice->customer?->state_code }})
        </td>
        <td style="width: 45%; vertical-align: top; text-align: right;">
            <strong>Invoice No:</strong> {{ $invoice->invoice_number }}<br>
            <strong>Date:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}<br>
            <strong>Place of Supply:</strong> {{ $invoice->place_of_supply }}<br>
            <strong>Status:</strong> {{ strtoupper($invoice->payment_status) }}
        </td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 35%;">Product Description</th>
            <th style="width: 10%;">HSN</th>
            <th style="width: 8%;" class="text-right">Qty</th>
            <th style="width: 12%;" class="text-right">Rate (₹)</th>
            <th style="width: 15%;" class="text-right">Taxable (₹)</th>
            <th style="width: 15%;" class="text-right">Total (₹)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>
                    {{ $item->product_name }}
                    @if($item->is_free_item)
                        <strong>(FREE)</strong>
                    @endif
                </td>
                <td class="text-center">{{ $item->hsn_code }}</td>
                <td class="text-right">{{ number_format($item->quantity, 1) }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->taxable_amount, 2) }}</td>
                <td class="text-right bold">{{ number_format($item->line_total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-right bold">Taxable Value:</td>
            <td colspan="2" class="text-right bold">₹{{ number_format($invoice->taxable_amount, 2) }}</td>
        </tr>
        @if($invoice->is_interstate)
            <tr>
                <td colspan="5" class="text-right bold">IGST:</td>
                <td colspan="2" class="text-right bold">₹{{ number_format($invoice->igst_amount, 2) }}</td>
            </tr>
        @else
            <tr>
                <td colspan="5" class="text-right bold">CGST:</td>
                <td colspan="2" class="text-right bold">₹{{ number_format($invoice->cgst_amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" class="text-right bold">SGST:</td>
                <td colspan="2" class="text-right bold">₹{{ number_format($invoice->sgst_amount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="5" class="text-right">Rounding:</td>
            <td colspan="2" class="text-right">{{ $invoice->rounding_adjustment >= 0 ? '+' : '' }}₹{{ number_format($invoice->rounding_adjustment, 2) }}</td>
        </tr>
        <tr style="background-color: #f2f2f2;">
            <td colspan="5" class="text-right bold" style="font-size: 13px;">GRAND TOTAL:</td>
            <td colspan="2" class="text-right bold" style="font-size: 13px; color: #005a9c;">₹{{ number_format($invoice->grand_total, 2) }}</td>
        </tr>
    </tfoot>
</table>

<table class="no-border" style="margin-top: 30px;">
    <tr>
        <td style="width: 60%; font-size: 10px;">
            <strong>Bank Details:</strong> HDFC Bank | A/C: 50200012345678 | IFSC: HDFC0001234<br>
            <em>Goods once sold will not be accepted without written approval.</em>
        </td>
        <td style="width: 40%; text-align: right; vertical-align: bottom;">
            <div style="border-top: 1px solid #333; display: inline-block; padding-top: 5px; width: 140px;">
                Authorized Signatory<br>
                <strong>Gudi Chemicals</strong>
            </div>
        </td>
    </tr>
</table>

</body>
</html>
