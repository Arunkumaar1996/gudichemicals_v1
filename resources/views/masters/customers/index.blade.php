@extends('layouts.app')

@section('title', 'Customers Directory')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-users text-primary me-2"></i> Customers Directory</h4>
        <p class="text-muted small mb-0">Retail counter accounts, B2B wholesale buyers, GSTIN and credit limits</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="{{ route('masters.customers.create') }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-user-plus me-1"></i> Add New Customer
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('masters.customers.index') }}" class="row g-2 align-items-center">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, company, phone or GSTIN..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="customer_type" class="form-select">
                    <option value="">All Customer Types</option>
                    <option value="retail" {{ request('customer_type') === 'retail' ? 'selected' : '' }}>Retail</option>
                    <option value="wholesale" {{ request('customer_type') === 'wholesale' ? 'selected' : '' }}>Wholesale / B2B</option>
                </select>
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
                        <th>Customer / Company</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>GSTIN / State</th>
                        <th class="text-end">Credit Limit</th>
                        <th class="text-end">Current Balance</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        <tr>
                            <td>
                                <strong>{{ $c->name }}</strong>
                                @if($c->company_name)
                                    <div class="text-muted small">{{ $c->company_name }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $c->customer_type === 'wholesale' ? 'bg-primary' : 'bg-info' }}">
                                    {{ ucfirst($c->customer_type) }}
                                </span>
                            </td>
                            <td>
                                <div><i class="fa-solid fa-phone text-muted me-1 small"></i> {{ $c->phone }}</div>
                                @if($c->email)
                                    <div class="text-muted small"><i class="fa-regular fa-envelope me-1"></i> {{ $c->email }}</div>
                                @endif
                            </td>
                            <td>
                                @if($c->gstin)
                                    <span class="badge bg-light text-dark border">{{ $c->gstin }}</span>
                                @else
                                    <span class="text-muted small">Unregistered</span>
                                @endif
                                <div class="text-muted small">{{ $c->state_name }} ({{ $c->state_code }})</div>
                            </td>
                            <td class="text-end">₹{{ number_format($c->credit_limit, 2) }}</td>
                            <td class="text-end fw-bold {{ $c->current_balance > 0 ? 'text-danger' : 'text-success' }}">
                                ₹{{ number_format($c->current_balance, 2) }}
                            </td>
                            <td>
                                <span class="badge {{ $c->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $c->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('masters.customers.show', $c->id) }}" class="btn btn-sm btn-outline-info py-1 px-2">
                                    <i class="fa-solid fa-eye"></i> Ledger
                                </a>
                                <a href="{{ route('masters.customers.edit', $c->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection
