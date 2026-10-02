<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice — {{ $invoice->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000;
            background: #fff;
            font-size: 13px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
        }
        @media print {
            .no-print { display: none; }
            .invoice-box { border: none; padding: 0; }
        }
    </style>
</head>
<body>

<div class="container py-3">
    <div class="no-print text-end mb-3">
        <button class="btn btn-primary fw-semibold" onclick="window.print()"><i class="fa fa-print"></i> Print Invoice</button>
        <button class="btn btn-secondary" onclick="window.close()">Close</button>
    </div>

    <div class="invoice-box">
        <div class="text-center border-bottom pb-3 mb-3">
            <h3 class="fw-bold mb-1 text-primary">GUDI CHEMICALS</h3>
            <p class="mb-0 text-muted small">Industrial Chemical Formulations & Solutions</p>
            <p class="mb-0 small">{{ $company->address ?: 'MIDC Chemical Zone, Pune, Maharashtra' }}</p>
            <div class="small fw-semibold mt-1">
                GSTIN: {{ $company->gstin }} | State: {{ $company->state_name }} ({{ $company->state_code }}) | Phone: {{ $company->phone ?: '9876543210' }}
            </div>
            <h5 class="fw-bold mt-2 text-uppercase tracking-wider">TAX INVOICE</h5>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <strong>Billed To (Customer):</strong>
                <div class="fw-bold">{{ $invoice->customer?->name }}</div>
                @if($invoice->customer?->company_name)
                    <div>{{ $invoice->customer->company_name }}</div>
                @endif
                <div class="text-muted small">{{ $invoice->customer?->billing_address ?: 'Counter Customer' }}</div>
                <div class="small">GSTIN: {{ $invoice->customer?->gstin ?: 'Unregistered' }}</div>
                <div class="small">State: {{ $invoice->customer?->state_name }} ({{ $invoice->customer?->state_code }})</div>
            </div>
            <div class="col-6 text-end">
                <div><strong>Invoice No:</strong> <span class="fs-6 fw-bold">{{ $invoice->invoice_number }}</span></div>
                <div><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</div>
                <div><strong>Place of Supply:</strong> {{ $invoice->place_of_supply }}</div>
                <div><strong>Payment Status:</strong> {{ strtoupper($invoice->payment_status) }}</div>
            </div>
        </div>

        <table class="table table-bordered table-sm mb-3">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Product Description</th>
                    <th>HSN</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Rate (₹)</th>
                    <th class="text-end">Taxable Value</th>
                    <th class="text-end">GST Rate</th>
                    @if($invoice->is_interstate)
                        <th class="text-end">IGST</th>
                    @else
                        <th class="text-end">CGST</th>
                        <th class="text-end">SGST</th>
                    @endif
                    <th class="text-end">Total (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            {{ $item->product_name }}
                            @if($item->is_free_item)
                                <span class="badge bg-danger">FREE</span>
                            @endif
                        </td>
                        <td>{{ $item->hsn_code }}</td>
                        <td class="text-end">{{ number_format($item->quantity, 2) }} {{ $item->unit?->code }}</td>
                        <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">{{ number_format($item->taxable_amount, 2) }}</td>
                        <td class="text-end">{{ number_format($item->gst_rate, 0) }}%</td>
                        @if($invoice->is_interstate)
                            <td class="text-end">{{ number_format($item->igst_amount, 2) }}</td>
                        @else
                            <td class="text-end">{{ number_format($item->cgst_amount, 2) }}</td>
                            <td class="text-end">{{ number_format($item->sgst_amount, 2) }}</td>
                        @endif
                        <td class="text-end fw-semibold">{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="{{ $invoice->is_interstate ? '7' : '8' }}" class="text-end fw-bold">Taxable Amount:</td>
                    <td colspan="2" class="text-end fw-bold">₹{{ number_format($invoice->taxable_amount, 2) }}</td>
                </tr>
                @if($invoice->is_interstate)
                    <tr>
                        <td colspan="7" class="text-end fw-bold">IGST:</td>
                        <td colspan="2" class="text-end fw-bold">₹{{ number_format($invoice->igst_amount, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="8" class="text-end fw-bold">CGST:</td>
                        <td colspan="2" class="text-end fw-bold">₹{{ number_format($invoice->cgst_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="8" class="text-end fw-bold">SGST:</td>
                        <td colspan="2" class="text-end fw-bold">₹{{ number_format($invoice->sgst_amount, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="{{ $invoice->is_interstate ? '7' : '8' }}" class="text-end">Rounding Adjustment:</td>
                    <td colspan="2" class="text-end">{{ $invoice->rounding_adjustment >= 0 ? '+' : '' }}₹{{ number_format($invoice->rounding_adjustment, 2) }}</td>
                </tr>
                <tr class="table-light">
                    <td colspan="{{ $invoice->is_interstate ? '7' : '8' }}" class="text-end fw-bold fs-6">Grand Total:</td>
                    <td colspan="2" class="text-end fw-bold fs-6">₹{{ number_format($invoice->grand_total, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="row pt-4 mt-4 border-top">
            <div class="col-7 small">
                <strong>Bank Account Details for Electronic Transfer:</strong><br>
                Bank: HDFC Bank | A/C No: 50200012345678 | IFSC: HDFC0001234<br>
                UPI ID: gudichemicals@hdfcbank<br>
                <em>Terms: Goods once sold will not be returned without authorized authorization.</em>
            </div>
            <div class="col-5 text-end">
                <p class="mb-4">For <strong>Gudi Chemicals</strong></p>
                <div class="pt-4 border-top d-inline-block px-3">
                    <small>Authorized Signatory</small>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
