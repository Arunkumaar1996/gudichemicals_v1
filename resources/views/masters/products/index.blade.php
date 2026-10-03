@extends('layouts.app')

@section('title', 'Product Master')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-box-archive text-primary me-2"></i> Product Master</h4>
        <p class="text-muted small mb-0">Raw chemicals, packaging, bulk liquid and finished chemical products</p>
    </div>
    <div class="mt-2 mt-md-0">
        @can('products.create')
        <a href="{{ route('masters.products.create') }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Add New Product / SKU
        </a>
        @endcan
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('masters.products.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, SKU or barcode..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="item_type" class="form-select">
                    <option value="">All Item Types</option>
                    <option value="raw_material" {{ request('item_type') === 'raw_material' ? 'selected' : '' }}>Raw Chemical</option>
                    <option value="semi_finished" {{ request('item_type') === 'semi_finished' ? 'selected' : '' }}>Semi-Finished (Bulk Liquid)</option>
                    <option value="finished_goods" {{ request('item_type') === 'finished_goods' ? 'selected' : '' }}>Finished Chemical Product</option>
                    <option value="packaging" {{ request('item_type') === 'packaging' ? 'selected' : '' }}>Packaging Material</option>
                    <option value="trading" {{ request('item_type') === 'trading' ? 'selected' : '' }}>Trading Resale Item</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
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
                        <th>Product & SKU</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>HSN & GST</th>
                        <th class="text-end">Cost Price</th>
                        <th class="text-end">Retail Price</th>
                        <th class="text-end">Wholesale</th>
                        <th class="text-end">Available Stock</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $prod)
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $prod->name }}</strong>
                                <div class="text-muted small">
                                    SKU: <span class="badge bg-light text-dark border">{{ $prod->sku }}</span>
                                    @if($prod->barcode) | Barcode: {{ $prod->barcode }} @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucwords(str_replace('_', ' ', $prod->item_type)) }}
                                </span>
                            </td>
                            <td>{{ $prod->category?->name }}</td>
                            <td>
                                <div>{{ $prod->hsn_code }}</div>
                                <small class="text-muted">{{ number_format($prod->gst_rate, 0) }}% GST</small>
                            </td>
                            <td class="text-end">₹{{ number_format($prod->purchase_cost, 2) }}</td>
                            <td class="text-end fw-semibold text-success">
                                @if($prod->retail_price > 0) ₹{{ number_format($prod->retail_price, 2) }} @else - @endif
                            </td>
                            <td class="text-end">
                                @if($prod->wholesale_price > 0) ₹{{ number_format($prod->wholesale_price, 2) }} @else - @endif
                            </td>
                            <td class="text-end fw-bold {{ $prod->isLowStock() ? 'text-danger' : 'text-primary' }}">
                                {{ number_format($prod->total_stock, 2) }} {{ $prod->unit?->code }}
                            </td>
                            <td>
                                <span class="badge {{ $prod->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $prod->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @can('products.update')
                                    <a href="{{ route('masters.products.edit', $prod->id) }}" class="btn btn-outline-secondary py-1 px-2" title="Edit Product">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    @endcan
                                    @can('products.delete')
                                    <form method="POST" action="{{ route('masters.products.destroy', $prod->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete chemical product {{ $prod->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger py-1 px-2" title="Delete Product">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
