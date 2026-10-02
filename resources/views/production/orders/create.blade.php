@extends('layouts.app')

@section('title', 'Launch Production Batch')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-industry text-primary me-2"></i> Launch Production Batch</h5>
                    <small class="text-muted">Select an approved formula and specify the planned production volume</small>
                </div>
                <a href="{{ route('production.orders.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Batches
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('production.orders.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Chemical Formula / BOM Recipe <span class="text-danger">*</span></label>
                            <select name="formula_id" class="form-select" required>
                                <option value="">Select Formula</option>
                                @foreach($formulas as $f)
                                    <option value="{{ $f->id }}">
                                        {{ $f->name }} ({{ $f->formula_code }} v{{ $f->version }}) — Produces: {{ $f->product?->name }} (Standard: {{ number_format($f->standard_batch_qty, 0) }} {{ $f->outputUnit?->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Planned Production Output Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" name="planned_qty" class="form-control" placeholder="e.g. 1000" required>
                            <small class="text-muted">Ingredient requirements will be automatically scaled to this planned batch size.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Destination Plant / Warehouse <span class="text-danger">*</span></label>
                            <select name="target_warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Batch Initiation Date <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Batch Operator / Chemist</label>
                            <input type="text" class="form-control bg-light" value="{{ auth()->user()->name ?? 'Production Manager' }}" disabled>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Production & Compounding Notes</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Reactor vessel #2, cooling system active, operator remarks..."></textarea>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                                <i class="fa-solid fa-play me-1"></i> Initiate Production Order
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
