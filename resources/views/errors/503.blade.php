@extends('errors.layout')

@section('title', '503 Service Unavailable — System Maintenance')
@section('code', '503')

@section('extra_styles')
<style>
    .icon-hero-box-503 {
        background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
        color: #ffffff;
        box-shadow: 0 16px 32px -8px rgba(8, 145, 178, 0.45);
        border: 2px solid rgba(165, 243, 252, 0.6);
        position: relative;
    }
    .badge-maintenance {
        background-color: #ecfeff;
        color: #155e75;
        border: 1px solid #a5f3fc;
    }
    .badge-maintenance .indicator-dot {
        background-color: #06b6d4;
        box-shadow: 0 0 8px #06b6d4;
    }
    .gears-rotate-icon {
        display: inline-block;
        animation: rotateGears 8s linear infinite;
    }
    @keyframes rotateGears {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection

@section('icon_hero')
<div class="icon-hero-box icon-hero-box-503">
    <span class="gears-rotate-icon"><i class="fa-solid fa-gears"></i></span>
</div>
@endsection

@section('badge')
<div class="status-badge badge-maintenance">
    <span class="indicator-dot"></span>
    <span>HTTP 503 &bull; Scheduled System Maintenance</span>
</div>
@endsection

@section('error_heading', 'Scheduled System Maintenance')

@section('error_message')
    {{ $exception && $exception->getMessage() ? $exception->getMessage() : 'Gudi Chemicals ERP is undergoing scheduled database maintenance, index re-optimization, or a version upgrade. Normal operations will resume shortly.' }}
@endsection

@section('context_box')
<div class="context-box">
    <div class="d-flex align-items-start gap-3">
        <div class="fs-4 text-info mt-0.5">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">System Optimization in Progress:</div>
            <p class="text-muted mb-2 small" style="line-height: 1.5;">
                We are performing routine security patches and financial year ledger integrity checks to guarantee peak system performance during peak business hours.
            </p>
            <div class="p-2 bg-white rounded border border-info-subtle text-muted" style="font-size: 0.74rem;">
                <i class="fa-solid fa-clock text-info me-1"></i> <strong>Estimated Completion:</strong> Usually takes between 2 to 5 minutes. No data or batch formulas are affected.
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="javascript:window.location.reload();" class="btn-action-primary" style="background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%); box-shadow: 0 4px 12px rgba(8, 145, 178, 0.35);">
        <i class="fa-solid fa-arrows-rotate"></i> Check Status &amp; Reload
    </a>
    <a href="{{ route('dashboard') }}" class="btn-action-secondary">
        <i class="fa-solid fa-house"></i> Home
    </a>
@endsection
