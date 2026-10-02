@extends('layouts.app')

@section('title', 'Product Categories')

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-folder-plus text-primary me-2"></i> Add New Category</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('masters.categories.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Acid Descalers" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Chemical grouping details..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-gudi-primary w-100 fw-semibold">
                        <i class="fa-solid fa-plus me-1"></i> Save Category
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-tags text-primary me-2"></i> Chemical Categories Master</h6>
                <span class="badge bg-secondary">{{ count($categories) }} Categories</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Category Name</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th class="text-center">Products</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $cat)
                                <tr>
                                    <td><strong>{{ $cat->name }}</strong></td>
                                    <td><code>{{ $cat->slug }}</code></td>
                                    <td class="text-muted small">{{ $cat->description ?: '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $cat->products_count }}</span>
                                    </td>
                                    <td class="text-end">
                                        @if($cat->products_count == 0)
                                            <form method="POST" action="{{ route('masters.categories.destroy', $cat->id) }}" onsubmit="return confirm('Delete this category?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small">In Use</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No categories found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
