@extends('layouts.app')

@section('title', 'Record Vendor Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-money-bill-transfer text-primary me-2"></i> Record Vendor Payment</h5>
                <a href="{{ route('purchases.payments.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Payments
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('purchases.payments.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Vendor / Supplier <span class="text-danger">*</span></label>
                            <select name="vendor_id" class="form-select" required>
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $v)
                                    <option value="{{ $v->id }}">
                                        {{ $v->company_name ?: $v->name }} (Payable: ₹{{ number_format($v->current_balance, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Link to Supplier Bill (Optional)</label>
                            <select name="supplier_invoice_id" class="form-select">
                                <option value="">On Account / Advance Payment</option>
                                @foreach($invoices as $inv)
                                    <option value="{{ $inv->id }}">
                                        {{ $inv->bill_number }} - {{ $inv->vendor?->name }} (Due: ₹{{ number_format($inv->due_amount, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Payment Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount in ₹" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="bank_transfer">Bank Transfer (NEFT/RTGS/IMPS)</option>
                                <option value="upi">UPI / Online</option>
                                <option value="cheque">Cheque</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Bank Reference / UTR / Cheque #</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="e.g. UTR12893829">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Notes / Voucher Details</label>
                            <input type="text" name="notes" class="form-control" placeholder="Paid from HDFC Current Account">
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                                <i class="fa-solid fa-check me-1"></i> Record Payment & Deduct Payable
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
