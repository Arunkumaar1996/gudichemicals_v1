@extends('reports.pdf.layout')

@section('title', 'Chemical Inventory Valuation & Stock Status Report')
@section('report_title', 'Inventory Valuation & Stock Status')
@section('report_period', 'As of Date: ' . now()->format('d M Y'))

@section('content')
<table class="summary-table">
    <tr>
        <td style="width: 25%;">
            <span class="summary-label">Total SKUs / Items</span>
            <span class="summary-val">{{ $products->count() }} Chemicals</span>
        </td>
        <td style="width: 25%;">
            <span class="summary-label">Low Stock Alerts</span>
            <span class="summary-val text-danger">
                {{ $products->filter(fn($p) => $p->reorder_level > 0 && $p->total_stock <= $p->reorder_level)->count() }} Items
            </span>
        </td>
        <td style="width: 50%;">
            <span class="summary-label">Total Inventory Valuation</span>
            <span class="summary-val text-primary">Rs. {{ number_format($totalValuation, 2) }}</span>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 10%;">SKU</th>
            <th style="width: 25%;">Chemical Product Name</th>
            <th style="width: 12%;">Type</th>
            <th style="width: 13%;">Category</th>
            <th style="width: 10%;" class="text-right">Stock</th>
            <th style="width: 9%;" class="text-right">Reorder</th>
            <th style="width: 10%;" class="text-right">Cost (Rs)</th>
            <th style="width: 11%;" class="text-right">Valuation (Rs)</th>
            <th style="width: 9%;" class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $p)
            @php
                $stock = (float)$p->total_stock;
                $val = $stock * (float)$p->purchase_cost;
                $isLow = $p->reorder_level > 0 && $stock <= $p->reorder_level;
            @endphp
            <tr>
                <td class="fw-bold" style="font-family: monospace;">{{ $p->sku }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $p->item_type)) }}</td>
                <td>{{ $p->category?->name ?: '-' }}</td>
                <td class="text-right fw-bold {{ $isLow ? 'text-danger' : '' }}">
                    {{ number_format($stock, 2) }} {{ $p->unit?->code }}
                </td>
                <td class="text-right">{{ number_format($p->reorder_level, 2) }}</td>
                <td class="text-right">{{ number_format($p->purchase_cost, 2) }}</td>
                <td class="text-right fw-bold">Rs. {{ number_format($val, 2) }}</td>
                <td class="text-center">
                    @if($isLow)
                        <span class="badge-status badge-low">Low Stock</span>
                    @else
                        <span class="badge-status badge-optimal">Optimal</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="text-center" style="padding: 10px;">No inventory items found.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="7" class="text-right">TOTAL ASSET VALUATION:</td>
            <td class="text-right text-primary">Rs. {{ number_format($totalValuation, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
@endsection
