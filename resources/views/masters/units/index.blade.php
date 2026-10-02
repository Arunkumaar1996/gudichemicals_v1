@extends('layouts.app')

@section('title', 'Units & Conversions')

@section('content')
<div class="row">
    <!-- Units Management -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-plus-circle text-primary me-2"></i> Add New Unit</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('masters.units.store') }}" class="row g-2">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Unit Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Barrel" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Code</label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="BRL" required>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-gudi-primary w-100 fw-semibold">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-scale-balanced text-primary me-2"></i> Units of Measurement</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Unit Name</th>
                            <th>Code</th>
                            <th>Fractional Allowed</th>
                            <th class="text-center">Products</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $u)
                            <tr>
                                <td><strong>{{ $u->name }}</strong></td>
                                <td><span class="badge bg-light text-dark border">{{ $u->code }}</span></td>
                                <td>{{ $u->is_fractional ? 'Yes (Decimal qty)' : 'No (Integers)' }}</td>
                                <td class="text-center"><span class="badge bg-secondary">{{ $u->products_count }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Unit Conversions -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-arrows-split-up-and-left text-success me-2"></i> Define Conversion Rate</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('masters.units.conversion.store') }}" class="row g-2">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">1 Base Unit</label>
                        <select name="from_unit_id" class="form-select" required>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Equals (Factor)</label>
                        <input type="number" step="0.000001" name="conversion_factor" class="form-control" placeholder="e.g. 1000" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Target Unit</label>
                        <select name="to_unit_id" class="form-select" required>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mt-3 text-end">
                        <button type="submit" class="btn btn-success fw-semibold px-4">Save Conversion</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-list text-primary me-2"></i> Active Unit Conversions</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th>From Unit</th>
                            <th>Factor</th>
                            <th>To Unit</th>
                            <th>Formula</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conversions as $conv)
                            <tr>
                                <td><strong>{{ $conv->fromUnit?->name }}</strong> ({{ $conv->fromUnit?->code }})</td>
                                <td><span class="badge bg-success">{{ number_format($conv->conversion_factor, 2) }}</span></td>
                                <td><strong>{{ $conv->toUnit?->name }}</strong> ({{ $conv->toUnit?->code }})</td>
                                <td class="text-muted small">1 {{ $conv->fromUnit?->code }} = {{ number_format($conv->conversion_factor) }} {{ $conv->toUnit?->code }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No unit conversions defined.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
