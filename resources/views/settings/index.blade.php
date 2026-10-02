@extends('layouts.app')

@section('title', 'Company & GST Settings')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-sliders text-primary me-2"></i> Company Profile & GST Configuration</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Legal Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $setting->company_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Trade / Brand Name</label>
                            <input type="text" name="trade_name" class="form-control" value="{{ old('trade_name', $setting->trade_name) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Company GSTIN (15 chars) <span class="text-danger">*</span></label>
                            <input type="text" name="gstin" class="form-control text-uppercase fw-bold" value="{{ old('gstin', $setting->gstin) }}" maxlength="15" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Permanent Account Number (PAN)</label>
                            <input type="text" name="pan" class="form-control text-uppercase" value="{{ old('pan', $setting->pan) }}" maxlength="10">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">State Name <span class="text-danger">*</span></label>
                            <input type="text" name="state_name" class="form-control" value="{{ old('state_name', $setting->state_name) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">State Code <span class="text-danger">*</span></label>
                            <input type="text" name="state_code" class="form-control" value="{{ old('state_code', $setting->state_code) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $setting->city ?: 'Pune') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Pincode</label>
                            <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $setting->pincode ?: '411018') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Factory / Registered Office Address <span class="text-danger">*</span></label>
                            <textarea name="address" rows="2" class="form-control" required>{{ old('address', $setting->address) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Contact Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $setting->phone) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Official Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $setting->email) }}">
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-file-invoice text-primary me-2"></i> Invoice Numbering & Financial Year</h6>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Invoice Series Prefix <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_prefix" class="form-control text-uppercase" value="{{ old('invoice_prefix', $setting->invoice_prefix) }}" required>
                            <small class="text-muted">Invoices will format as <code>PREFIX/FY/0001</code></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Financial Year Code <span class="text-danger">*</span></label>
                            <input type="text" name="fy_code" class="form-control" value="{{ old('fy_code', $setting->fy_code) }}" required>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-building-columns text-success me-2"></i> Bank Details for Invoices & Electronic Payment</h6>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $setting->bank_name ?: 'HDFC Bank') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Account Number</label>
                            <input type="text" name="bank_account_no" class="form-control" value="{{ old('bank_account_no', $setting->bank_account_no ?: '50200012345678') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">IFSC Code</label>
                            <input type="text" name="bank_ifsc" class="form-control text-uppercase" value="{{ old('bank_ifsc', $setting->bank_ifsc ?: 'HDFC0001234') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Branch Name</label>
                            <input type="text" name="bank_branch" class="form-control" value="{{ old('bank_branch', $setting->bank_branch ?: 'MIDC Industrial Branch') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Merchant UPI ID</label>
                            <input type="text" name="upi_id" class="form-control" value="{{ old('upi_id', $setting->upi_id ?: 'gudichemicals@hdfcbank') }}">
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Configuration
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
