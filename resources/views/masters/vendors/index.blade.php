@extends('layouts.app')

@section('title', 'Vendors & Suppliers')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-handshake text-primary me-2"></i> Vendors & Suppliers</h4>
        <p class="text-muted small mb-0">Chemical manufacturers, acid suppliers, packaging vendors and logistics partners</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="{{ route('masters.vendors.create') }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Add New Vendor
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('masters.vendors.index') }}" class="row g-2 align-items-center">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search vendor by name, firm, phone or GSTIN..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Supplier / Company</th>
                        <th>Contact Person</th>
                        <th>Phone & Email</th>
                        <th>GSTIN / Location</th>
                        <th class="text-end">Credit Days</th>
                        <th class="text-end">Payable Balance</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $v)
                        <tr>
                            <td>
                                <strong>{{ $v->company_name ?: $v->name }}</strong>
                                @if($v->company_name)
                                    <div class="text-muted small">{{ $v->name }}</div>
                                @endif
                            </td>
                            <td>{{ $v->name }}</td>
                            <td>
                                <div><i class="fa-solid fa-phone text-muted me-1 small"></i> {{ $v->phone }}</div>
                                @if($v->email)
                                    <div class="text-muted small"><i class="fa-regular fa-envelope me-1"></i> {{ $v->email }}</div>
                                @endif
                            </td>
                            <td>
                                @if($v->gstin)
                                    <span class="badge bg-light text-dark border">{{ $v->gstin }}</span>
                                @else
                                    <span class="text-muted small">Unregistered</span>
                                @endif
                                <div class="text-muted small">{{ $v->city }}, {{ $v->state_name }}</div>
                            </td>
                            <td class="text-end">{{ $v->credit_period_days }} days</td>
                            <td class="text-end fw-bold text-danger">₹{{ number_format($v->current_balance, 2) }}</td>
                            <td>
                                <span class="badge {{ $v->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $v->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('masters.vendors.edit', $v->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No vendors registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($vendors->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $vendors->links() }}
        </div>
    @endif
</div>
@endsection
