@extends('layouts.app')

@section('title', 'Add New Vendor')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-plus-circle text-primary me-2"></i> Register New Vendor / Supplier</h5>
                <a href="{{ route('masters.vendors.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('masters.vendors.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Company / Manufacturer Name</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="e.g. Gujarat Alkalies & Chemicals Ltd">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Contact Person Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Anand Sharma" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="10-digit mobile" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="sales@supplier.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Credit Period (Days)</label>
                            <input type="number" name="credit_period_days" class="form-control" value="{{ old('credit_period_days', '30') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">GSTIN</label>
                            <input type="text" name="gstin" class="form-control text-uppercase" value="{{ old('gstin') }}" placeholder="24AAACG1234D1Z2" maxlength="15">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">PAN</label>
                            <input type="text" name="pan" class="form-control text-uppercase" value="{{ old('pan') }}" placeholder="AAACG1234D" maxlength="10">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">State Name <span class="text-danger">*</span></label>
                            <input type="text" name="state_name" class="form-control" value="{{ old('state_name', 'Gujarat') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">State Code <span class="text-danger">*</span></label>
                            <input type="text" name="state_code" class="form-control" value="{{ old('state_code', '24') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', 'Vadodara') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Factory / Office Address</label>
                            <textarea name="address" rows="2" class="form-control" placeholder="Plot / Industrial Area / Street...">{{ old('address') }}</textarea>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Register Vendor
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
