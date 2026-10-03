@extends('errors.layout')

@section('title', '403 Forbidden — Access Restricted')
@section('code', '403')

@section('extra_styles')
<style>
    .icon-hero-box-403 {
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        color: #ffffff;
        box-shadow: 0 16px 32px -8px rgba(220, 38, 38, 0.45);
        border: 2px solid rgba(254, 202, 202, 0.6);
        position: relative;
    }
    .badge-forbidden {
        background-color: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    .badge-forbidden .indicator-dot {
        background-color: #dc2626;
        box-shadow: 0 0 8px #dc2626;
    }
    .role-badge-pill {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        background: #e2e8f0;
        border-radius: 6px;
        font-weight: 700;
        color: #334155;
    }
    .shield-lock-subicon {
        position: absolute;
        bottom: -4px;
        right: -4px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f59e0b;
        color: #ffffff;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ffffff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.18);
        animation: swingPendulum 4s ease-in-out infinite;
    }
</style>
@endsection

@section('icon_hero')
<div class="icon-hero-box icon-hero-box-403">
    <i class="fa-solid fa-shield-halved"></i>
    <div class="shield-lock-subicon" title="Restricted Access">
        <i class="fa-solid fa-lock"></i>
    </div>
</div>
@endsection

@section('badge')
<div class="status-badge badge-forbidden">
    <span class="indicator-dot"></span>
    <span>HTTP 403 &bull; Access Forbidden</span>
</div>
@endsection

@section('error_heading', 'Access Restricted &bull; Permission Required')

@section('error_message')
    {{ $exception && $exception->getMessage() ? $exception->getMessage() : 'You do not have the required security permissions or user role to access this ERP module. This action has been logged for security monitoring.' }}
@endsection

@section('context_box')
<div class="context-box">
    <div class="d-flex align-items-start gap-3">
        <div class="fs-4 text-danger mt-0.5">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Security Verification Summary:</div>
            @if(auth()->check())
                <div class="text-muted mb-1">
                    Workstation User: <strong class="text-dark">{{ auth()->user()->name }}</strong> 
                    (<span class="text-secondary">{{ auth()->user()->email }}</span>)
                </div>
                <div class="text-muted mb-2">
                    Current Assigned Role: <span class="role-badge-pill">{{ auth()->user()->role ?? (auth()->user()->roles->first()?->name ?? 'Staff User') }}</span>
                </div>
                <div class="p-2 bg-white rounded border border-danger-subtle text-muted" style="font-size: 0.74rem;">
                    <i class="fa-solid fa-circle-info text-danger me-1"></i> If you require operational access to this module (e.g. Sales, Compounding, Financial Reports, or Settings), please request your <strong>Super Administrator</strong> or <strong>Branch Manager</strong> to grant the necessary permission in <em>Settings &rarr; Staff Roles</em>.
                </div>
            @else
                <div class="text-muted mb-1">
                    No active authenticated session detected.
                </div>
                <div class="p-2 bg-white rounded border border-danger-subtle text-muted" style="font-size: 0.74rem;">
                    <i class="fa-solid fa-arrow-right-to-bracket text-danger me-1"></i> Please log in with an authorized Gudi Chemicals staff account.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('dashboard') }}" class="btn-action-primary">
        <i class="fa-solid fa-gauge-high"></i> Return to Dashboard
    </a>
    <a href="javascript:history.back()" class="btn-action-secondary">
        <i class="fa-solid fa-arrow-left"></i> Go Back
    </a>
    <a href="{{ route('guide.index') }}" class="btn-action-secondary">
        <i class="fa-solid fa-book-open"></i> View User Guide
    </a>
@endsection
