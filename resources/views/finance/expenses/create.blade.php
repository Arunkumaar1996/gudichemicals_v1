@extends('layouts.app')

@section('title', 'Record Operating Expense')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-receipt text-primary me-2"></i> Record Operating Expense Voucher</h5>
                <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Expenses
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('expenses.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Expense Category <span class="text-danger">*</span></label>
                            <select name="expense_category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Paid To (Vendor / Entity / Staff) <span class="text-danger">*</span></label>
                            <input type="text" name="paid_to" class="form-control" placeholder="e.g. Maharashtra State Electricity Distribution" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Amount Paid (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount in ₹" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer (NEFT/RTGS)</option>
                                <option value="upi">UPI / Online</option>
                                <option value="card">Card</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Voucher / Cheque / Transaction #</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="e.g. TXN-290192">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Notes / Purpose of Expense</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Monthly plant electricity bill, tanker unloading charges..."></textarea>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Expense Voucher
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
