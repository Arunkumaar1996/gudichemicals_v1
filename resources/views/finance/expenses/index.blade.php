@extends('layouts.app')

@section('title', 'Operating Expenses')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-receipt text-primary me-2"></i> Operating Expenses</h4>
        <p class="text-muted small mb-0">Utilities, freight, plant maintenance, packaging supplies and operational vouchers</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="{{ route('expenses.create') }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Record Expense Voucher
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('expenses.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="category_id" class="form-select">
                    <option value="">All Expense Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From Date">
            </div>
            <div class="col-md-3">
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="To Date">
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
                        <th>Voucher #</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Paid To</th>
                        <th>Method & Reference</th>
                        <th class="text-end">Amount</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr>
                            <td><strong>{{ $exp->expense_number }}</strong></td>
                            <td>{{ $exp->expense_date->format('d M Y') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $exp->category?->name }}</span></td>
                            <td>{{ $exp->paid_to }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ strtoupper($exp->payment_method) }}</span>
                                @if($exp->reference_no)
                                    <small class="text-muted ms-1">{{ $exp->reference_no }}</small>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-danger">₹{{ number_format($exp->amount, 2) }}</td>
                            <td><small class="text-muted">{{ $exp->creator?->name ?: 'Staff' }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No expense vouchers found.</td></tr>
                    @endforelse
                </tbody>
                @if($expenses->isNotEmpty())
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Total Expenses:</td>
                            <td class="text-end fw-bold text-danger fs-6">₹{{ number_format($totalAmount, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
    @if($expenses->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $expenses->links() }}
        </div>
    @endif
</div>
@endsection
