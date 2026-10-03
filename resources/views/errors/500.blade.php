@extends('errors.layout')

@section('title', '500 Server Error — Internal Exception')
@section('code', '500')

@section('extra_styles')
<style>
    .icon-hero-box-500 {
        background: linear-gradient(135deg, #e11d48 0%, #9f1239 100%);
        color: #ffffff;
        box-shadow: 0 16px 32px -8px rgba(225, 29, 72, 0.45);
        border: 2px solid rgba(254, 205, 211, 0.6);
        position: relative;
    }
    .badge-server-error {
        background-color: #fff1f2;
        color: #9f1239;
        border: 1px solid #fecdd3;
    }
    .badge-server-error .indicator-dot {
        background-color: #e11d48;
        box-shadow: 0 0 8px #e11d48;
    }
    .server-alert-subicon {
        position: absolute;
        top: -6px;
        right: -6px;
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
        animation: pulseWarning 1.5s infinite;
    }
    @keyframes pulseWarning {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.15); }
    }
</style>
@endsection

@section('icon_hero')
<div class="icon-hero-box icon-hero-box-500">
    <i class="fa-solid fa-server"></i>
    <div class="server-alert-subicon" title="Server Exception">
        <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
</div>
@endsection

@section('badge')
<div class="status-badge badge-server-error">
    <span class="indicator-dot"></span>
    <span>HTTP 500 &bull; Internal Server Exception</span>
</div>
@endsection

@section('error_heading', 'Internal Server Error &bull; System Fault')

@section('error_message')
    {{ $exception && $exception->getMessage() ? $exception->getMessage() : 'An unexpected exception or database deadlock occurred while processing this transaction. Our engineering diagnostics have recorded this incident.' }}
@endsection

@section('context_box')
<div class="context-box">
    <div class="d-flex align-items-start gap-3">
        <div class="fs-4 text-danger mt-0.5">
            <i class="fa-solid fa-microchip"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">System Diagnostic Reference:</div>
            <div class="text-muted small mb-2" style="font-family: monospace;">
                Incident ID: <strong class="text-dark">ERR-{{ strtoupper(substr(md5(microtime()), 0, 10)) }}</strong> &bull; {{ date('d-M-Y H:i:s T') }}
            </div>
            <p class="text-muted mb-2 small" style="line-height: 1.5;">
                Your inventory balances, batch quantities, and posted invoices remain integral. You may safely retry the transaction or return to the main dashboard.
            </p>
            <div class="p-2 bg-white rounded border border-danger-subtle text-muted" style="font-size: 0.74rem;">
                <i class="fa-solid fa-headset text-danger me-1"></i> If this issue persists during critical billing or chemical batch finalization, please alert the technical administrator at <a href="mailto:admin@gudichemicals.com" class="text-danger fw-semibold">admin@gudichemicals.com</a>.
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="javascript:window.location.reload();" class="btn-action-primary" style="background: linear-gradient(135deg, #e11d48 0%, #9f1239 100%); box-shadow: 0 4px 12px rgba(225, 29, 72, 0.35);">
        <i class="fa-solid fa-arrow-rotate-right"></i> Retry Operation
    </a>
    <a href="{{ route('dashboard') }}" class="btn-action-secondary">
        <i class="fa-solid fa-gauge-high"></i> Return to Dashboard
    </a>
    <a href="javascript:history.back()" class="btn-action-secondary">
        <i class="fa-solid fa-arrow-left"></i> Go Back
    </a>
@endsection
