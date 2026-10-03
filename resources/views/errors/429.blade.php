@extends('errors.layout')

@section('title', '429 Too Many Requests — Rate Limit Exceeded')
@section('code', '429')

@section('extra_styles')
<style>
    .icon-hero-box-429 {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        box-shadow: 0 16px 32px -8px rgba(2, 132, 199, 0.45);
        border: 2px solid rgba(186, 230, 253, 0.6);
        position: relative;
    }
    .badge-ratelimit {
        background-color: #f0f9ff;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .badge-ratelimit .indicator-dot {
        background-color: #0284c7;
        box-shadow: 0 0 8px #0284c7;
    }
</style>
@endsection

@section('icon_hero')
<div class="icon-hero-box icon-hero-box-429">
    <i class="fa-solid fa-gauge-simple-high"></i>
</div>
@endsection

@section('badge')
<div class="status-badge badge-ratelimit">
    <span class="indicator-dot"></span>
    <span>HTTP 429 &bull; Request Rate Limit Exceeded</span>
</div>
@endsection

@section('error_heading', 'Too Many Requests &bull; Slow Down')

@section('error_message')
    {{ $exception && $exception->getMessage() ? $exception->getMessage() : 'Too many queries or barcode scans were sent from your workstation in a brief interval. Please wait a few seconds before retrying.' }}
@endsection

@section('context_box')
<div class="context-box">
    <div class="d-flex align-items-start gap-3">
        <div class="fs-4 text-info mt-0.5">
            <i class="fa-solid fa-stopwatch"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Workstation Protection Active:</div>
            <p class="text-muted mb-2 small" style="line-height: 1.5;">
                Rate limiting safeguards database query queues and protects barcode scanner throughput during peak POS billing rush hours.
            </p>
            <div class="p-2 bg-white rounded border border-info-subtle text-muted" style="font-size: 0.74rem;">
                <i class="fa-solid fa-clock-rotate-left text-info me-1"></i> Please wait <strong>15–30 seconds</strong>, then refresh to proceed.
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="javascript:window.location.reload();" class="btn-action-primary" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);">
        <i class="fa-solid fa-arrows-rotate"></i> Retry Now
    </a>
    <a href="{{ route('dashboard') }}" class="btn-action-secondary">
        <i class="fa-solid fa-gauge-high"></i> Dashboard
    </a>
@endsection
