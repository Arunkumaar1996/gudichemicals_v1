@extends('errors.layout')

@section('title', '401 Unauthorized — Authentication Required')
@section('code', '401')

@section('extra_styles')
<style>
    .icon-hero-box-401 {
        background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
        color: #ffffff;
        box-shadow: 0 16px 32px -8px rgba(124, 58, 237, 0.45);
        border: 2px solid rgba(221, 214, 254, 0.6);
        position: relative;
    }
    .badge-unauthorized {
        background-color: #f5f3ff;
        color: #5b21b6;
        border: 1px solid #ddd6fe;
    }
    .badge-unauthorized .indicator-dot {
        background-color: #7c3aed;
        box-shadow: 0 0 8px #7c3aed;
    }
    .key-turn-icon {
        display: inline-block;
        animation: keyWiggle 2.5s ease-in-out infinite;
    }
    @keyframes keyWiggle {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(-20deg); }
        75% { transform: rotate(20deg); }
    }
</style>
@endsection

@section('icon_hero')
<div class="icon-hero-box icon-hero-box-401">
    <span class="key-turn-icon"><i class="fa-solid fa-key"></i></span>
</div>
@endsection

@section('badge')
<div class="status-badge badge-unauthorized">
    <span class="indicator-dot"></span>
    <span>HTTP 401 &bull; Authentication Required</span>
</div>
@endsection

@section('error_heading', 'Sign In to Access ERP Workstation')

@section('error_message')
    {{ $exception && $exception->getMessage() ? $exception->getMessage() : 'You must authenticate with a valid Gudi Chemicals staff credential to access this manufacturing and billing terminal.' }}
@endsection

@section('context_box')
<div class="context-box">
    <div class="d-flex align-items-start gap-3">
        <div class="fs-4 text-purple mt-0.5" style="color: #7c3aed;">
            <i class="fa-solid fa-user-lock"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Workstation Authentication Gateway:</div>
            <p class="text-muted mb-2 small" style="line-height: 1.5;">
                Access to formulas, warehouse stock adjustments, customer ledger, and POS billing terminals requires an authorized session for compliance and GST auditing.
            </p>
            <div class="p-2 bg-white rounded border border-purple-subtle text-muted" style="font-size: 0.74rem;">
                <i class="fa-solid fa-id-card text-purple me-1" style="color: #7c3aed;"></i> Please sign in with your staff email (e.g. <code>staff@gudichemicals.com</code>) and designated password.
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('login') }}" class="btn-action-primary" style="background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); box-shadow: 0 4px 12px rgba(124, 58, 237, 0.35);">
        <i class="fa-solid fa-arrow-right-to-bracket"></i> Sign In to Workstation
    </a>
    <a href="{{ route('home') }}" class="btn-action-secondary">
        <i class="fa-solid fa-house"></i> Home
    </a>
@endsection
