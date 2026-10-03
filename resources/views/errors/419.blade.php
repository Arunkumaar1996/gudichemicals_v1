@extends('errors.layout')

@section('title', '419 Page Expired — Session Timeout')
@section('code', '419')

@section('extra_styles')
<style>
    .icon-hero-box-419 {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        color: #ffffff;
        box-shadow: 0 16px 32px -8px rgba(217, 119, 6, 0.45);
        border: 2px solid rgba(253, 230, 138, 0.6);
        position: relative;
    }
    .badge-expired {
        background-color: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .badge-expired .indicator-dot {
        background-color: #f59e0b;
        box-shadow: 0 0 8px #f59e0b;
    }
    .hourglass-flip-icon {
        display: inline-block;
        animation: flipHourglass 3.5s ease-in-out infinite;
    }
    @keyframes flipHourglass {
        0%, 80% { transform: rotate(0deg); }
        90%, 100% { transform: rotate(180deg); }
    }
</style>
@endsection

@section('icon_hero')
<div class="icon-hero-box icon-hero-box-419">
    <span class="hourglass-flip-icon"><i class="fa-solid fa-hourglass-half"></i></span>
</div>
@endsection

@section('badge')
<div class="status-badge badge-expired">
    <span class="indicator-dot"></span>
    <span>HTTP 419 &bull; Session Security Timeout</span>
</div>
@endsection

@section('error_heading', 'Workstation Session Expired')

@section('error_message')
    {{ $exception && $exception->getMessage() ? $exception->getMessage() : 'Your ERP session or CSRF security token has expired due to inactivity. This safeguard prevents unauthorized transactions on your account.' }}
@endsection

@section('context_box')
<div class="context-box">
    <div class="d-flex align-items-start gap-3">
        <div class="fs-4 text-warning mt-0.5">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Security Session Protection:</div>
            <p class="text-muted mb-2 small" style="line-height: 1.5;">
                To safeguard financial invoices, compounding recipes, and warehouse ledger entries against cross-site request tampering, sessions automatically expire after prolonged inactivity.
            </p>
            <div class="p-2 bg-white rounded border border-warning-subtle text-muted" style="font-size: 0.74rem;">
                <i class="fa-solid fa-rotate text-warning me-1"></i> Simply click <strong>"Refresh Page"</strong> or re-login to renew your secure security token and continue your work without losing state.
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="javascript:window.location.reload();" class="btn-action-primary" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);">
        <i class="fa-solid fa-arrows-rotate"></i> Refresh Page &amp; Retry
    </a>
    <a href="{{ route('login') }}" class="btn-action-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket"></i> Log In Again
    </a>
    <a href="{{ route('dashboard') }}" class="btn-action-secondary">
        <i class="fa-solid fa-gauge-high"></i> Dashboard
    </a>
@endsection
