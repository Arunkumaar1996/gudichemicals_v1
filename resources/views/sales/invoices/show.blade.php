@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-file-invoice text-primary me-2"></i> Tax Invoice: {{ $invoice->invoice_number }}</h4>
        <span class="badge {{ $invoice->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }} me-2">
            Payment: {{ ucfirst($invoice->payment_status) }}
        </span>
        <span class="text-muted small">Date: {{ $invoice->invoice_date->format('d M Y') }}</span>
    </div>
    <div class="mt-2 mt-md-0 d-flex gap-2">
        <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-print me-1"></i> Print A4 Invoice
        </a>
        <a href="{{ route('invoices.print', [$invoice->id, 'format' => 'thermal']) }}" target="_blank" class="btn btn-outline-success btn-sm">
            <i class="fa-solid fa-receipt me-1"></i> Thermal Receipt
        </a>
        <a href="{{ route('invoices.pdf', $invoice->id) }}" class="btn btn-outline-danger btn-sm">
            <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
        </a>
        <a href="{{ route('returns.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-warning btn-sm">
            <i class="fa-solid fa-arrow-rotate-left me-1"></i> Process Return
        </a>
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Company & Invoice Details -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <h6 class="fw-bold text-muted small text-uppercase mb-2">Seller / Consignor</h6>
            <h5 class="fw-bold text-primary mb-1">{{ $company->company_name }}</h5>
            <div class="small text-muted mb-1">{{ $company->address ?: 'MIDC Chemical Zone, Pune, Maharashtra' }}</div>
            <div class="small">
                <strong>GSTIN:</strong> {{ $company->gstin }} | <strong>State:</strong> {{ $company->state_name }} ({{ $company->state_code }})
            </div>
        </div>
    </div>

    <!-- Customer Details -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <h6 class="fw-bold text-muted small text-uppercase mb-2">Billed To (Customer)</h6>
            <h5 class="fw-bold text-dark mb-1">{{ $invoice->customer?->name }}</h5>
            @if($invoice->customer?->company_name)
                <div class="text-muted small fw-semibold">{{ $invoice->customer->company_name }}</div>
            @endif
            <div class="small text-muted mb-1">{{ $invoice->customer?->billing_address ?: 'Counter Sale' }}</div>
            <div class="small">
                <strong>GSTIN:</strong> {{ $invoice->customer?->gstin ?: 'Unregistered' }} |
                <strong>Place of Supply:</strong> {{ $invoice->place_of_supply }}
            </div>
        </div>
    </div>
</div>

<!-- Invoice Items Table -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold mb-0">Billed Chemical Products & GST Breakdown</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product & SKU</th>
                        <th>HSN Code</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Rate (₹)</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Taxable Value</th>
                        <th class="text-end">GST %</th>
                        @if($invoice->is_interstate)
                            <th class="text-end">IGST Amount</th>
                        @else
                            <th class="text-end">CGST</th>
                            <th class="text-end">SGST</th>
                        @endif
                        <th class="text-end">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $item->product_name }}</strong>
                                <div class="text-muted small">SKU: {{ $item->sku }}</div>
                                @if($item->is_free_item)
                                    <span class="badge bg-danger">FREE PROMOTIONAL ITEM</span>
                                @endif
                            </td>
                            <td>{{ $item->hsn_code }}</td>
                            <td class="text-end fw-semibold">{{ number_format($item->quantity, 2) }} {{ $item->unit?->code }}</td>
                            <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($item->discount_amount, 2) }}</td>
                            <td class="text-end fw-semibold">₹{{ number_format($item->taxable_amount, 2) }}</td>
                            <td class="text-end">{{ number_format($item->gst_rate, 0) }}%</td>
                            @if($invoice->is_interstate)
                                <td class="text-end">₹{{ number_format($item->igst_amount, 2) }}</td>
                            @else
                                <td class="text-end">₹{{ number_format($item->cgst_amount, 2) }}</td>
                                <td class="text-end">₹{{ number_format($item->sgst_amount, 2) }}</td>
                            @endif
                            <td class="text-end fw-bold">₹{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="{{ $invoice->is_interstate ? '8' : '9' }}" class="text-end fw-semibold">Taxable Amount:</td>
                        <td class="text-end fw-bold">₹{{ number_format($invoice->taxable_amount, 2) }}</td>
                    </tr>
                    @if($invoice->is_interstate)
                        <tr>
                            <td colspan="8" class="text-end fw-semibold">IGST Total:</td>
                            <td class="text-end fw-bold">₹{{ number_format($invoice->igst_amount, 2) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="9" class="text-end fw-semibold">CGST Total:</td>
                            <td class="text-end fw-bold">₹{{ number_format($invoice->cgst_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-end fw-semibold">SGST Total:</td>
                            <td class="text-end fw-bold">₹{{ number_format($invoice->sgst_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="{{ $invoice->is_interstate ? '8' : '9' }}" class="text-end fw-semibold">Rounding Adjustment:</td>
                        <td class="text-end">{{ $invoice->rounding_adjustment >= 0 ? '+' : '' }}₹{{ number_format($invoice->rounding_adjustment, 2) }}</td>
                    </tr>
                    <tr class="table-primary">
                        <td colspan="{{ $invoice->is_interstate ? '8' : '9' }}" class="text-end fw-bold fs-5">Grand Total:</td>
                        <td class="text-end fw-bold fs-5 text-dark">₹{{ number_format($invoice->grand_total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Payments History -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-credit-card text-success me-2"></i> Payment Collections Recorded</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th>Reference / UTR</th>
                    <th class="text-end">Amount</th>
                    <th>Cashier</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->payments as $p)
                    <tr>
                        <td>{{ $p->payment_date->format('d M Y') }}</td>
                        <td><span class="badge bg-secondary">{{ strtoupper($p->payment_method) }}</span></td>
                        <td><code>{{ $p->reference_no ?: '-' }}</code></td>
                        <td class="text-end fw-bold text-success">₹{{ number_format($p->amount, 2) }}</td>
                        <td><small class="text-muted">{{ $p->receiver?->name ?: 'Cashier' }}</small></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">No payments recorded against this invoice yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
