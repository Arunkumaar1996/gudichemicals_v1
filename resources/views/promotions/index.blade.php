@extends('layouts.app')

@section('title', 'Promotions & Offers')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-gift text-warning me-2"></i> Promotions & Offers</h4>
        <p class="text-muted small mb-0">Buy 1 Get 1 Free, Buy 2 Get 1 Free, Combo bundles, wholesale slabs and cart discounts</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="{{ route('promotions.create') }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Create New Promotion
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Offer Name & Code</th>
                        <th>Type</th>
                        <th>Offer Rule Details</th>
                        <th>Customer Eligibility</th>
                        <th>Valid Period</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotions as $promo)
                        <tr>
                            <td>
                                <strong>{{ $promo->name }}</strong>
                                <div class="text-muted small">Code: <span class="badge bg-light text-dark border">{{ $promo->code }}</span></div>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    {{ strtoupper(str_replace('_', ' ', $promo->type)) }}
                                </span>
                            </td>
                            <td>
                                @foreach($promo->rules as $r)
                                    <div>
                                        @if(in_array($promo->type, ['bogo', 'b2g1', 'bxgy']))
                                            Buy <strong>{{ number_format($r->buy_quantity, 0) }}</strong> of {{ $r->buyProduct?->name }} &rarr; Get <strong>{{ number_format($r->get_quantity, 0) }}</strong> FREE ({{ $r->getProduct?->name ?: 'Same' }})
                                        @elseif($promo->type === 'percent_discount')
                                            <strong>{{ number_format($r->discount_pct, 0) }}% Off</strong> on {{ $r->buyProduct ? $r->buyProduct->name : 'All Items' }}
                                        @elseif($promo->type === 'flat_discount')
                                            <strong>₹{{ number_format($r->discount_flat, 2) }} Flat Off</strong> on Order
                                        @elseif($promo->type === 'slab_discount')
                                            Buy &gt;= {{ number_format($r->buy_quantity, 0) }} &rarr; Get <strong>{{ number_format($r->discount_pct, 0) }}% Off</strong>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($promo->rules->first()?->customer_type ?: 'All') }}</span>
                            </td>
                            <td>
                                <small>From: {{ $promo->start_date->format('d M Y') }}</small>
                                @if($promo->end_date)
                                    <br><small class="text-muted">To: {{ $promo->end_date->format('d M Y') }}</small>
                                @else
                                    <br><small class="text-success">No Expiry</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $promo->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $promo->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('promotions.toggle', $promo->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $promo->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} py-0 px-2">
                                        {{ $promo->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No promotional schemes configured yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
