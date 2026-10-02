@extends('layouts.app')

@section('title', 'Chemical Formulas (BOM)')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-mortar-pestle text-primary me-2"></i> Chemical Formulas / BOM</h4>
        <p class="text-muted small mb-0">Standard batch recipes, expected yields, process loss allowances and ingredient ratios</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="{{ route('production.formulas.create') }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Create New Formula
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Formula Code & Name</th>
                        <th>Output Product</th>
                        <th>Version</th>
                        <th class="text-end">Batch Size</th>
                        <th class="text-end">Expected Yield</th>
                        <th>Ingredients</th>
                        <th>Approved By</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($formulas as $f)
                        <tr>
                            <td>
                                <strong>{{ $f->name }}</strong>
                                <div class="text-muted small"><code>{{ $f->formula_code }}</code></div>
                            </td>
                            <td>
                                <strong>{{ $f->product?->name }}</strong>
                                <div class="text-muted small">SKU: {{ $f->product?->sku }}</div>
                            </td>
                            <td><span class="badge bg-secondary">v{{ $f->version }}</span></td>
                            <td class="text-end fw-semibold">{{ number_format($f->standard_batch_qty, 2) }} {{ $f->outputUnit?->code }}</td>
                            <td class="text-end text-success fw-bold">{{ number_format($f->expected_yield_pct, 1) }}%</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $f->items->count() }} Ingredients</span>
                            </td>
                            <td><small class="text-muted">{{ $f->approver?->name ?: 'Chief Chemist' }}</small></td>
                            <td class="text-end">
                                <a href="{{ route('production.formulas.show', $f->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                                    <i class="fa-solid fa-eye"></i> View Recipe
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No chemical formulas configured yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($formulas->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $formulas->links() }}
        </div>
    @endif
</div>
@endsection
