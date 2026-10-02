@extends('layouts.app')

@section('title', 'Process Sales Return')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-arrow-rotate-left text-primary me-2"></i> Process Sales Return & Credit Note</h5>
                    <small class="text-muted">Saleable returns re-enter warehouse stock; damaged items are quarantined</small>
                </div>
                <a href="{{ route('returns.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Returns
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('returns.store') }}">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Select Original Sales Invoice <span class="text-danger">*</span></label>
                            <select name="sales_invoice_id" class="form-select" required onchange="location.href='{{ route('returns.create') }}?invoice_id=' + this.value">
                                <option value="">Select Invoice</option>
                                @foreach($recentInvoices as $inv)
                                    <option value="{{ $inv->id }}" {{ ($selectedInvoice && $selectedInvoice->id == $inv->id) ? 'selected' : '' }}>
                                        {{ $inv->invoice_number }} — {{ $inv->customer?->name }} (₹{{ number_format($inv->grand_total, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Return Date <span class="text-danger">*</span></label>
                            <input type="date" name="return_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Refund Settlement <span class="text-danger">*</span></label>
                            <select name="refund_status" class="form-select" required>
                                <option value="refunded">Direct Cash/UPI Refund</option>
                                <option value="credited_to_account">Credit to Customer Account</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Return Reason <span class="text-danger">*</span></label>
                            <input type="text" name="reason" class="form-control" placeholder="e.g. Excess order quantity returned, container seal intact" required>
                        </div>
                    </div>

                    @if($selectedInvoice)
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Select Items to Return from Invoice {{ $selectedInvoice->invoice_number }}</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Original Invoiced Qty</th>
                                        <th style="width: 20%;">Return Quantity</th>
                                        <th style="width: 20%;">Stock Condition</th>
                                        <th class="text-end">Unit Refund (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedInvoice->items as $idx => $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->product_name }}</strong>
                                                <input type="hidden" name="items[{{ $idx }}][product_id]" value="{{ $item->product_id }}">
                                            </td>
                                            <td class="text-end">{{ number_format($item->quantity, 2) }} {{ $item->unit?->code }}</td>
                                            <td>
                                                <input type="number" step="0.0001" min="0" max="{{ $item->quantity }}" name="items[{{ $idx }}][quantity]" class="form-control" value="0">
                                            </td>
                                            <td>
                                                <select name="items[{{ $idx }}][condition]" class="form-select">
                                                    <option value="saleable">Saleable (Adds to Stock)</option>
                                                    <option value="damaged">Damaged (Quarantined)</option>
                                                </select>
                                            </td>
                                            <td class="text-end">
                                                <input type="number" step="0.01" name="items[{{ $idx }}][unit_price]" class="form-control text-end" value="{{ $item->unit_price }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                                <i class="fa-solid fa-check me-1"></i> Post Return & Generate Credit Note
                            </button>
                        </div>
                    @else
                        <div class="alert alert-info small">
                            <i class="fa-solid fa-arrow-up me-1"></i> Please choose an invoice from the dropdown above to load items.
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
