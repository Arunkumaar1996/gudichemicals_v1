@extends('errors.layout')

@section('title', '404 Not Found — Record or Page Missing')
@section('code', '404')

@section('extra_styles')
<style>
    .icon-hero-box-404 {
        background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%);
        color: #ffffff;
        box-shadow: 0 16px 32px -8px rgba(79, 70, 229, 0.45);
        border: 2px solid rgba(199, 210, 254, 0.6);
        position: relative;
    }
    .badge-notfound {
        background-color: #eef2ff;
        color: #3730a3;
        border: 1px solid #c7d2fe;
    }
    .badge-notfound .indicator-dot {
        background-color: #4f46e5;
        box-shadow: 0 0 8px #4f46e5;
    }
    .compass-needle-spin {
        display: inline-block;
        animation: needleSpin 6s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }
    @keyframes needleSpin {
        0% { transform: rotate(0deg); }
        30% { transform: rotate(140deg); }
        60% { transform: rotate(-35deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('icon_hero')
<div class="icon-hero-box icon-hero-box-404">
    <span class="compass-needle-spin"><i class="fa-solid fa-compass"></i></span>
</div>
@endsection

@section('badge')
<div class="status-badge badge-notfound">
    <span class="indicator-dot"></span>
    <span>HTTP 404 &bull; Record or Route Not Found</span>
</div>
@endsection

@section('error_heading', 'Page or ERP Record Not Found')

@section('error_message')
    {{ $exception && $exception->getMessage() ? $exception->getMessage() : 'The chemical formulation, production batch, sales invoice, or page URL you requested could not be located in the database.' }}
@endsection

@section('context_box')
<div class="context-box">
    <div class="d-flex align-items-start gap-3">
        <div class="fs-4 text-primary mt-0.5">
            <i class="fa-solid fa-magnifying-glass-location"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">What might have happened?</div>
            <ul class="text-muted ps-3 mb-2 small" style="line-height: 1.6;">
                <li>The link or bookmark you clicked might be outdated or misspelled.</li>
                <li>The chemical batch, customer record, or invoice may have been archived or removed.</li>
                <li>You may have navigated to an experimental or deprecated API endpoint.</li>
            </ul>
            <div class="p-2 bg-white rounded border border-primary-subtle text-muted" style="font-size: 0.74rem;">
                <i class="fa-solid fa-lightbulb text-warning me-1"></i> <strong>Tip:</strong> Use the ERP search bars on the top navigation bar or POS terminal to find records by SKU, batch number, or customer name.
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('dashboard') }}" class="btn-action-primary">
        <i class="fa-solid fa-gauge-high"></i> Go to Dashboard
    </a>
    <a href="javascript:history.back()" class="btn-action-secondary">
        <i class="fa-solid fa-arrow-left"></i> Go Back
    </a>
    <a href="{{ route('inventory.index') }}" class="btn-action-secondary">
        <i class="fa-solid fa-boxes-stacked"></i> Browse Inventory
    </a>
@endsection
