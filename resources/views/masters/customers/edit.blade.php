@extends('layouts.app')

@section('title', 'Edit Customer — ' . $customer->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Customer: {{ $customer->name }}</h5>
                <a href="{{ route('masters.customers.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('masters.customers.update', $customer->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Customer Type <span class="text-danger">*</span></label>
                            <select name="customer_type" class="form-select" required>
                                <option value="retail" {{ old('customer_type', $customer->customer_type) === 'retail' ? 'selected' : '' }}>Retail (Walk-in / Counter)</option>
                                <option value="wholesale" {{ old('customer_type', $customer->customer_type) === 'wholesale' ? 'selected' : '' }}>Wholesale (B2B Distributor / Fabricator)</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Contact Person / Trade Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Company / Firm Name</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $customer->company_name) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">GSTIN</label>
                            <input type="text" name="gstin" class="form-control text-uppercase" value="{{ old('gstin', $customer->gstin) }}" maxlength="15">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">PAN</label>
                            <input type="text" name="pan" class="form-control text-uppercase" value="{{ old('pan', $customer->pan) }}" maxlength="10">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">State Name <span class="text-danger">*</span></label>
                            <input type="text" name="state_name" class="form-control" value="{{ old('state_name', $customer->state_name) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">State Code <span class="text-danger">*</span></label>
                            <input type="text" name="state_code" class="form-control" value="{{ old('state_code', $customer->state_code) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Pincode</label>
                            <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $customer->pincode) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Credit Limit (₹)</label>
                            <input type="number" step="0.01" name="credit_limit" class="form-control" value="{{ old('credit_limit', $customer->credit_limit) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Active Status</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="chkActive" {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold" for="chkActive">Customer account active for sales</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Billing Address</label>
                            <textarea name="billing_address" rows="2" class="form-control">{{ old('billing_address', $customer->billing_address) }}</textarea>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Update Customer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
