@extends('layouts.app')

@section('title', 'Edit Vendor — ' . $vendor->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Vendor: {{ $vendor->company_name ?: $vendor->name }}</h5>
                <a href="{{ route('masters.vendors.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('masters.vendors.update', $vendor->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Company / Manufacturer Name</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $vendor->company_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Contact Person Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $vendor->name) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $vendor->phone) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $vendor->email) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Credit Period (Days)</label>
                            <input type="number" name="credit_period_days" class="form-control" value="{{ old('credit_period_days', $vendor->credit_period_days) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">GSTIN</label>
                            <input type="text" name="gstin" class="form-control text-uppercase" value="{{ old('gstin', $vendor->gstin) }}" maxlength="15">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">PAN</label>
                            <input type="text" name="pan" class="form-control text-uppercase" value="{{ old('pan', $vendor->pan) }}" maxlength="10">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">State Name <span class="text-danger">*</span></label>
                            <input type="text" name="state_name" class="form-control" value="{{ old('state_name', $vendor->state_name) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">State Code <span class="text-danger">*</span></label>
                            <input type="text" name="state_code" class="form-control" value="{{ old('state_code', $vendor->state_code) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $vendor->city) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Factory / Office Address</label>
                            <textarea name="address" rows="2" class="form-control">{{ old('address', $vendor->address) }}</textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="chkActive" {{ old('is_active', $vendor->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold" for="chkActive">Vendor active for purchases</label>
                            </div>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Update Vendor
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
