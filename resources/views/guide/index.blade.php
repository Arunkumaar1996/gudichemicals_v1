@extends('layouts.app')

@section('title', 'Application User Guide & Video Tutorials')

@section('content')
<style>
    /* User Guide Styling */
    .guide-hero {
        background: linear-gradient(135deg, #091326 0%, #112240 50%, #004b87 100%);
        border-radius: 12px;
        color: #ffffff;
        padding: 1.5rem 1.75rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }
    .guide-hero::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.18) 0%, rgba(0,0,0,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* Video Player Mockup Container */
    .video-player-card {
        background: #0b1324;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
    }
    .video-screen {
        position: relative;
        background: radial-gradient(circle at center, #1e293b 0%, #0b1324 100%);
        aspect-ratio: 16 / 9;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        overflow: hidden;
    }
    .video-screen-overlay {
        position: absolute;
        inset: 0;
        background: rgba(11, 19, 36, 0.65);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    .video-screen:hover .video-screen-overlay {
        background: rgba(11, 19, 36, 0.5);
    }
    .big-play-btn {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0284c7 0%, #005a9c 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        box-shadow: 0 0 25px rgba(2, 132, 199, 0.7);
        transition: transform 0.2s, box-shadow 0.2s;
        border: 3px solid rgba(255, 255, 255, 0.3);
    }
    .video-screen:hover .big-play-btn {
        transform: scale(1.08);
        box-shadow: 0 0 35px rgba(56, 189, 248, 0.9);
    }
    .video-controls-bar {
        background: #0f172a;
        padding: 0.6rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: #e2e8f0;
    }
    .video-progress-bar {
        flex: 1;
        height: 6px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 3px;
        position: relative;
        cursor: pointer;
    }
    .video-progress-fill {
        height: 100%;
        width: 38%;
        background: linear-gradient(90deg, #0284c7, #38bdf8);
        border-radius: 3px;
    }

    /* Video Playlist Items */
    .playlist-item {
        background: #1e293b;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        color: #ffffff;
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }
    .playlist-item:hover, .playlist-item.active {
        background: linear-gradient(135deg, rgba(2, 132, 199, 0.3) 0%, rgba(14, 165, 233, 0.15) 100%);
        border-color: #0284c7;
    }
    .playlist-item.active .playlist-thumb-icon {
        background: #0284c7;
        color: #ffffff;
    }
    .playlist-thumb-icon {
        width: 38px;
        height: 38px;
        border-radius: 7px;
        background: rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #38bdf8;
        flex-shrink: 0;
    }

    /* Workflow Diagram Box */
    .workflow-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1.25rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }
    .flow-step-node {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.75rem;
        text-align: center;
        position: relative;
        transition: transform 0.2s;
    }
    .flow-step-node:hover {
        transform: translateY(-2px);
        border-color: #005a9c;
        background: #f0f7ff;
    }
    .flow-arrow {
        color: #94a3b8;
        font-size: 1.2rem;
    }

    /* Module Walkthrough Cards */
    .module-walkthrough-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        margin-bottom: 1.25rem;
        overflow: hidden;
    }
    .module-header {
        background: #f8fafc;
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .step-number-badge {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #005a9c;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.82rem;
        margin-right: 0.5rem;
        flex-shrink: 0;
    }
    .keyboard-shortcut-pill {
        background: #e2e8f0;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        padding: 2px 7px;
        font-family: var(--pos-mono, monospace);
        font-size: 0.75rem;
        font-weight: 700;
        color: #1e293b;
    }
</style>

<!-- Hero Section -->
<div class="guide-hero mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                <span class="badge bg-success bg-opacity-25 text-white border border-success border-opacity-25 py-1 px-2.5">
                    <i class="fa-solid fa-graduation-cap me-1 text-warning"></i> Training & Support Center
                </span>
                <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-10 py-1 px-2">
                    <i class="fa-solid fa-layer-group me-1 text-info"></i> {{ $productsCount }} Products Active
                </span>
                <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-10 py-1 px-2">
                    <i class="fa-solid fa-industry me-1 text-success"></i> {{ $batchesCount }} Production Batches
                </span>
            </div>
            <h4 class="fw-bold text-white mb-1">Gudi Chemicals ERP — User Guide & Video Training</h4>
            <p class="text-white-50 small mb-0">Learn how to manage chemical manufacturing, formula BOMs, fast POS GST billing, stock lot tracking, and accounting.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('pos.index') }}" target="_blank" class="btn btn-success fw-bold shadow-sm d-flex align-items-center">
                <i class="fa-solid fa-bolt me-1 text-warning"></i> Open POS Billing
            </a>
            <a href="#moduleGuides" class="btn btn-outline-light fw-bold">
                <i class="fa-solid fa-book-open me-1"></i> Read Manual
            </a>
        </div>
    </div>
</div>

<!-- SECTION 1: Interactive Video Walkthrough Hub -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <div>
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-circle-play text-danger me-2"></i> Interactive Video Training Hub
            </h6>
            <small class="text-muted">Watch high-definition step-by-step visual training modules for each ERP workflow</small>
        </div>
        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 fw-bold">
            <i class="fa-solid fa-video me-1"></i> 5 HD Video Tutorials
        </span>
    </div>

    <div class="card-body p-3 p-lg-4">
        <div class="row g-4">
            <!-- Left: Video Mockup Player -->
            <div class="col-lg-8">
                <div class="video-player-card">
                    <!-- Screen Area -->
                    <div class="video-screen" id="videoScreenMockup" onclick="toggleSimulatedPlayback()">
                        <div class="video-screen-overlay" id="videoOverlay">
                            <div class="big-play-btn mb-2" id="playBtnIcon">
                                <i class="fa-solid fa-play ms-1"></i>
                            </div>
                            <h5 class="text-white fw-bold mb-1" id="activeVideoTitle">Tutorial 1: High-Speed POS Fast Billing & Lot Picking</h5>
                            <span class="badge bg-black bg-opacity-60 text-white-50 px-2 py-1" id="activeVideoDuration">Duration: 03:45 | 1080p Full HD</span>
                        </div>
                    </div>

                    <!-- Video Controls Bar -->
                    <div class="video-controls-bar">
                        <button type="button" class="btn btn-sm btn-link text-white p-0" onclick="toggleSimulatedPlayback()" title="Play/Pause">
                            <i class="fa-solid fa-play" id="barPlayBtn"></i>
                        </button>
                        <div class="small fw-semibold text-white-50" style="font-family: var(--pos-mono); font-size: 0.75rem;" id="videoTimecode">
                            01:15 / 03:45
                        </div>
                        <div class="video-progress-bar" onclick="seekSimulatedVideo(event)">
                            <div class="video-progress-fill" id="videoProgressFill"></div>
                        </div>
                        <span class="badge bg-success bg-opacity-25 text-success py-0.5 px-1.5" style="font-size: 0.65rem;">HD 60FPS</span>
                        <button type="button" class="btn btn-sm btn-link text-white-50 p-0 ms-1" onclick="toggleMute()" title="Mute/Unmute">
                            <i class="fa-solid fa-volume-high"></i>
                        </button>
                    </div>
                </div>

                <!-- Video Summary & Key Takeaways -->
                <div class="mt-3 p-3 bg-light rounded border">
                    <h6 class="fw-bold mb-1.5 text-dark" id="videoSummaryHeading">Key Learnings from this Module:</h6>
                    <ul class="small text-muted mb-0 ps-3" id="videoKeyPoints">
                        <li>How to use barcode scanner or press <code>[F2]</code> for instant chemical SKU discovery.</li>
                        <li>Selecting between multiple production batches (Auto FIFO vs manual lot selection).</li>
                        <li>Switching between Retail and Wholesale pricing with <code>[F7]</code>.</li>
                        <li>Receiving Cash, UPI QR code, Card, or Credit payments and printing GST invoices.</li>
                    </ul>
                </div>
            </div>

            <!-- Right: Selectable Playlist -->
            <div class="col-lg-4">
                <div class="p-2.5 bg-dark rounded-3" style="background: #0b1324 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2 text-white px-1">
                        <span class="fw-bold small text-uppercase tracking-wide" style="font-size: 0.72rem;">Training Playlist</span>
                        <span class="badge bg-secondary text-white-50" style="font-size: 0.68rem;">5 Lessons</span>
                    </div>

                    <!-- Video 1 -->
                    <div class="playlist-item active" onclick="loadVideoLesson(1)">
                        <div class="playlist-thumb-icon">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="fw-bold text-truncate" style="font-size: 0.8rem;">1. POS Billing & Multi-Lot Desk</div>
                            <small class="text-white-50 d-block" style="font-size: 0.68rem;">Barcode scan, FIFO lots, GST bill (3:45)</small>
                        </div>
                    </div>

                    <!-- Video 2 -->
                    <div class="playlist-item" onclick="loadVideoLesson(2)">
                        <div class="playlist-thumb-icon">
                            <i class="fa-solid fa-flask-vial text-info"></i>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="fw-bold text-truncate" style="font-size: 0.8rem;">2. Chemical Formulas & Batch Orders</div>
                            <small class="text-white-50 d-block" style="font-size: 0.68rem;">BOM recipes, scaling, QC release (4:12)</small>
                        </div>
                    </div>

                    <!-- Video 3 -->
                    <div class="playlist-item" onclick="loadVideoLesson(3)">
                        <div class="playlist-thumb-icon">
                            <i class="fa-solid fa-boxes-stacked text-warning"></i>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="fw-bold text-truncate" style="font-size: 0.8rem;">3. Multi-Tier Inventory & Ledger</div>
                            <small class="text-white-50 d-block" style="font-size: 0.68rem;">Warehouses, batch expiry, adjustments (3:20)</small>
                        </div>
                    </div>

                    <!-- Video 4 -->
                    <div class="playlist-item" onclick="loadVideoLesson(4)">
                        <div class="playlist-thumb-icon">
                            <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="fw-bold text-truncate" style="font-size: 0.8rem;">4. Low Stock Alerts & Reorder Levels</div>
                            <small class="text-white-50 d-block" style="font-size: 0.68rem;">Setting minimum thresholds, 1-click PO (2:50)</small>
                        </div>
                    </div>

                    <!-- Video 5 -->
                    <div class="playlist-item" onclick="loadVideoLesson(5)">
                        <div class="playlist-thumb-icon">
                            <i class="fa-solid fa-file-invoice text-success"></i>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="fw-bold text-truncate" style="font-size: 0.8rem;">5. GST Reports & DomPDF Invoice Print</div>
                            <small class="text-white-50 d-block" style="font-size: 0.68rem;">GSTR-1, tax breakdown, PDF exports (3:15)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 2: Visual Workflow Life Cycle Diagrams -->
<div class="row g-3 mb-4">
    <!-- Chemical Manufacturing Flow -->
    <div class="col-lg-6">
        <div class="workflow-card h-100">
            <h6 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-vial text-primary me-2"></i> Chemical Manufacturing Life Cycle
            </h6>
            <p class="text-muted small mb-3">How raw chemicals transform into finished packaged products with full batch tracking</p>

            <div class="row g-2 align-items-center text-center">
                <div class="col-12 col-sm">
                    <div class="flow-step-node">
                        <div class="text-warning mb-1"><i class="fa-solid fa-truck-ramp-box fa-lg"></i></div>
                        <strong class="d-block" style="font-size: 0.75rem;">1. Raw Inward</strong>
                        <small class="text-muted" style="font-size: 0.65rem;">GRN & Lot Creation</small>
                    </div>
                </div>
                <div class="col-auto d-none d-sm-block flow-arrow">&rarr;</div>
                <div class="col-12 col-sm">
                    <div class="flow-step-node">
                        <div class="text-info mb-1"><i class="fa-solid fa-flask fa-lg"></i></div>
                        <strong class="d-block" style="font-size: 0.75rem;">2. Formula BOM</strong>
                        <small class="text-muted" style="font-size: 0.65rem;">Chemical Recipe</small>
                    </div>
                </div>
                <div class="col-auto d-none d-sm-block flow-arrow">&rarr;</div>
                <div class="col-12 col-sm">
                    <div class="flow-step-node">
                        <div class="text-primary mb-1"><i class="fa-solid fa-industry fa-lg"></i></div>
                        <strong class="d-block" style="font-size: 0.75rem;">3. Compounding</strong>
                        <small class="text-muted" style="font-size: 0.65rem;">Batch Execution</small>
                    </div>
                </div>
                <div class="col-auto d-none d-sm-block flow-arrow">&rarr;</div>
                <div class="col-12 col-sm">
                    <div class="flow-step-node">
                        <div class="text-success mb-1"><i class="fa-solid fa-clipboard-check fa-lg"></i></div>
                        <strong class="d-block" style="font-size: 0.75rem;">4. QC & Release</strong>
                        <small class="text-muted" style="font-size: 0.65rem;">Finished Goods Stock</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- POS Billing Workflow -->
    <div class="col-lg-6">
        <div class="workflow-card h-100">
            <h6 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-bolt text-warning me-2"></i> High-Speed POS GST Billing Flow
            </h6>
            <p class="text-muted small mb-3">From barcode scan to tax invoice generation in under 3 seconds</p>

            <div class="row g-2 align-items-center text-center">
                <div class="col-12 col-sm">
                    <div class="flow-step-node">
                        <div class="text-primary mb-1"><i class="fa-solid fa-barcode fa-lg"></i></div>
                        <strong class="d-block" style="font-size: 0.75rem;">1. Scan Barcode</strong>
                        <small class="text-muted" style="font-size: 0.65rem;">F2 or Laser Scan</small>
                    </div>
                </div>
                <div class="col-auto d-none d-sm-block flow-arrow">&rarr;</div>
                <div class="col-12 col-sm">
                    <div class="flow-step-node">
                        <div class="text-secondary mb-1"><i class="fa-solid fa-layer-group fa-lg"></i></div>
                        <strong class="d-block" style="font-size: 0.75rem;">2. Pick Lot</strong>
                        <small class="text-muted" style="font-size: 0.65rem;">Auto FIFO / Lot Select</small>
                    </div>
                </div>
                <div class="col-auto d-none d-sm-block flow-arrow">&rarr;</div>
                <div class="col-12 col-sm">
                    <div class="flow-step-node">
                        <div class="text-info mb-1"><i class="fa-solid fa-tags fa-lg"></i></div>
                        <strong class="d-block" style="font-size: 0.75rem;">3. Price Tier</strong>
                        <small class="text-muted" style="font-size: 0.65rem;">Retail / Wholesale [F7]</small>
                    </div>
                </div>
                <div class="col-auto d-none d-sm-block flow-arrow">&rarr;</div>
                <div class="col-12 col-sm">
                    <div class="flow-step-node">
                        <div class="text-success mb-1"><i class="fa-solid fa-receipt fa-lg"></i></div>
                        <strong class="d-block" style="font-size: 0.75rem;">4. Pay & Print</strong>
                        <small class="text-muted" style="font-size: 0.65rem;">Cash/UPI + DomPDF</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 3: Step-by-Step Module Walkthrough Guides -->
<div id="moduleGuides">
    <!-- Module 1: POS Fast Billing Station -->
    <div class="module-walkthrough-card">
        <div class="module-header">
            <div class="d-flex align-items-center">
                <span class="badge bg-success p-2 me-2.5 rounded-3"><i class="fa-solid fa-bolt fa-lg text-white"></i></span>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Module 1: POS Fast Billing Desk</h5>
                    <small class="text-muted">High-speed retail & wholesale invoicing with barcode scan, multi-lot support, and GST taxes</small>
                </div>
            </div>
            <a href="{{ route('pos.index') }}" target="_blank" class="btn btn-sm btn-success fw-bold">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Launch POS Desk
            </a>
        </div>
        <div class="card-body p-3 p-lg-4">
            <div class="row g-4">
                <div class="col-lg-6">
                    <h6 class="fw-bold text-dark mb-2.5">Step-by-Step Execution:</h6>
                    
                    <div class="d-flex mb-3">
                        <span class="step-number-badge">1</span>
                        <div>
                            <strong>Scan Barcode or Search Product [F2]</strong>
                            <p class="text-muted small mb-0">Use any standard 1D/2D USB laser barcode scanner or press <code>[F2]</code> to focus the search bar. Type chemical name, SKU (e.g. <code>FG-DEGREASE-1L</code>), or barcode.</p>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <span class="step-number-badge">2</span>
                        <div>
                            <strong>Batch / Production Lot Selection (Multi-Lot Support)</strong>
                            <p class="text-muted small mb-0">If a product has multiple lots stored in the active warehouse, a popup displays available batches with manufacturing/expiry dates. Pick a specific batch or choose <strong>"Auto (FIFO - Earliest Expiry)"</strong>.</p>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <span class="step-number-badge">3</span>
                        <div>
                            <strong>Select Customer & Price Tier [F4 & F7]</strong>
                            <p class="text-muted small mb-0">Toggle between <strong>Retail</strong> and <strong>Wholesale</strong> pricing. Select walk-in customer or choose registered B2B clients with GSTIN for automatic B2B tax invoice generation.</p>
                        </div>
                    </div>

                    <div class="d-flex mb-2">
                        <span class="step-number-badge">4</span>
                        <div>
                            <strong>Tender Payment & Print Invoice [F9]</strong>
                            <p class="text-muted small mb-0">Press <code>[F9]</code> to open the payment modal. Select Cash (with change calculator), UPI QR, Card, or Credit. Click <strong>"Save & Print Invoice"</strong> for instant receipt generation.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-keyboard me-1.5 text-primary"></i> Keyboard Hotkeys Cheat Sheet</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered bg-white mb-2" style="font-size: 0.8rem;">
                                <tbody>
                                    <tr><td width="25%"><span class="keyboard-shortcut-pill">F2</span></td><td>Focus Barcode / SKU Search Input</td></tr>
                                    <tr><td><span class="keyboard-shortcut-pill">F4</span></td><td>Jump to Customer Selection Dropdown</td></tr>
                                    <tr><td><span class="keyboard-shortcut-pill">F7</span></td><td>Toggle Retail / Wholesale Price Tier</td></tr>
                                    <tr><td><span class="keyboard-shortcut-pill">F9</span></td><td>Open Payment & Collect Modal</td></tr>
                                    <tr><td><span class="keyboard-shortcut-pill">F10</span></td><td>Clear Current Cart</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info py-1.5 px-2.5 small mb-0" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-lightbulb me-1 text-warning"></i> <strong>Pro Tip:</strong> On mobile phones, use the bottom floating dock to view the bill total and tap to pay from anywhere in the catalog.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Module 2: Manufacturing & Chemical BOM -->
    <div class="module-walkthrough-card">
        <div class="module-header">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary p-2 me-2.5 rounded-3"><i class="fa-solid fa-industry fa-lg text-white"></i></span>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Module 2: Chemical Formulas (BOM) & Production Batches</h5>
                    <small class="text-muted">Formula creation, chemical compounding, proportional ingredient scaling, and batch QC approval</small>
                </div>
            </div>
            <a href="{{ route('production.orders.create') }}" class="btn btn-sm btn-primary fw-bold">
                <i class="fa-solid fa-plus me-1"></i> New Production Batch
            </a>
        </div>
        <div class="card-body p-3 p-lg-4">
            <div class="row g-4">
                <div class="col-lg-6">
                    <h6 class="fw-bold text-dark mb-2.5">Manufacturing Workflow:</h6>
                    <div class="d-flex mb-3">
                        <span class="step-number-badge">1</span>
                        <div>
                            <strong>Create Formula (Bill of Materials)</strong>
                            <p class="text-muted small mb-0">Navigate to <em>Chemical Production &rarr; Formulas (BOM)</em>. Select the target finished product and add raw chemical ingredients with exact percentage or quantity ratios (e.g. 70% Solvent, 20% Water, 10% Surfactant).</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <span class="step-number-badge">2</span>
                        <div>
                            <strong>Launch a Production Batch Order</strong>
                            <p class="text-muted small mb-0">Specify the target output quantity (e.g. 5,000 Liters). The system automatically scales all raw ingredients proportionally and checks warehouse availability.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-2">
                        <span class="step-number-badge">3</span>
                        <div>
                            <strong>Record QC & Finalize Batch</strong>
                            <p class="text-muted small mb-0">Once compounding is completed, enter laboratory quality parameters (pH level, viscosity, specific gravity). On approval, the system automatically deducts raw chemical lots and deposits the finished goods batch into inventory with a unique Lot Number and Expiry Date.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-shield-halved me-1 text-success"></i> Traceability & Compliance</h6>
                        <p class="small text-muted mb-2">Every chemical batch generated in Gudi Chemicals ERP maintains end-to-end forward and backward lot traceability:</p>
                        <ul class="small text-muted mb-2 ps-3">
                            <li>Know exactly which vendor shipment raw chemicals came from.</li>
                            <li>Track which customer invoices received products from that exact manufacturing batch.</li>
                            <li>Instant recall support if any ingredient lot is flagged.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Module 3: Multi-Tier Inventory & Low-Stock Alerts -->
    <div class="module-walkthrough-card">
        <div class="module-header">
            <div class="d-flex align-items-center">
                <span class="badge bg-warning p-2 me-2.5 rounded-3"><i class="fa-solid fa-boxes-stacked fa-lg text-white"></i></span>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Module 3: Multi-Tier Inventory & Stock Alerts</h5>
                    <small class="text-muted">Stock on hand, warehouse locations, reorder thresholds, and stock adjustment ledgers</small>
                </div>
            </div>
            <a href="{{ route('inventory.index', ['filter' => 'low_stock']) }}" class="btn btn-sm btn-outline-danger fw-bold">
                <i class="fa-solid fa-bell me-1"></i> View Low Stock
            </a>
        </div>
        <div class="card-body p-3 p-lg-4">
            <div class="row g-4">
                <div class="col-lg-6">
                    <h6 class="fw-bold text-dark mb-2.5">Inventory Operations:</h6>
                    <div class="d-flex mb-3">
                        <span class="step-number-badge">1</span>
                        <div>
                            <strong>Setting Reorder Thresholds</strong>
                            <p class="text-muted small mb-0">In <em>Multi-Tier Inventory &rarr; Stock on Hand</em>, click <strong>"Set Qty / Reorder Level"</strong> for any chemical product. Enter the minimum quantity below which an alert is required.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <span class="step-number-badge">2</span>
                        <div>
                            <strong>Automatic Low Stock Filtering</strong>
                            <p class="text-muted small mb-0">When inventory drops below the reorder point, the dashboard lights up with a red alert badge. Cashiers and purchase managers can filter all low stock items with 1 click.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-2">
                        <span class="step-number-badge">3</span>
                        <div>
                            <strong>Physical Audits & Stock Adjustments</strong>
                            <p class="text-muted small mb-0">Record chemical evaporation shrinkage, leakage, or physical audit adjustments with audit reason codes under <em>Stock Adjustments</em>.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-file-pdf me-1 text-danger"></i> Reports & DomPDF Export</h6>
                        <p class="small text-muted mb-2">Every report in the ERP supports 1-click professional PDF downloads formatted with Gudi Chemicals letterhead and GST tax breakdown:</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('reports.sales') }}" class="btn btn-xs btn-outline-primary"><i class="fa-solid fa-download me-1"></i> Sales Summary</a>
                            <a href="{{ route('reports.gst') }}" class="btn btn-xs btn-outline-success"><i class="fa-solid fa-file-invoice me-1"></i> GSTR-1 Tax File</a>
                            <a href="{{ route('reports.inventory') }}" class="btn btn-xs btn-outline-warning"><i class="fa-solid fa-boxes me-1"></i> Valuation Report</a>
                            <a href="{{ route('reports.production') }}" class="btn btn-xs btn-outline-info"><i class="fa-solid fa-industry me-1"></i> Yield Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 4: Interactive Frequently Asked Questions (FAQ) -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-circle-question text-primary me-2"></i> Frequently Asked Questions (FAQ)</h6>
    </div>
    <div class="card-body p-3 p-lg-4">
        <div class="accordion" id="guideFaqAccordion">
            <!-- FAQ 1 -->
            <div class="accordion-item border-0 mb-2 rounded bg-light">
                <h2 class="accordion-header" id="faqHeading1">
                    <button class="accordion-button collapsed fw-bold text-dark bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1">
                        1. How do I select which production lot to bill when a chemical has 2 or more batches?
                    </button>
                </h2>
                <div id="faqCollapse1" class="accordion-collapse collapse" data-bs-parent="#guideFaqAccordion">
                    <div class="accordion-body small text-muted pt-0">
                        When you click a product tile or scan its barcode in POS, if multiple batches exist in that warehouse, a lot selection modal appears showing the batch numbers, expiry dates, and available quantities. You can click <strong>"Select"</strong> on any lot, or choose <strong>"Use Auto (FIFO - Earliest Expiry)"</strong> to let the system automatically allocate stock from the oldest batch.
                    </div>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="accordion-item border-0 mb-2 rounded bg-light">
                <h2 class="accordion-header" id="faqHeading2">
                    <button class="accordion-button collapsed fw-bold text-dark bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2">
                        2. How does the system calculate CGST + SGST vs. IGST?
                    </button>
                </h2>
                <div id="faqCollapse2" class="accordion-collapse collapse" data-bs-parent="#guideFaqAccordion">
                    <div class="accordion-body small text-muted pt-0">
                        The calculation is determined by comparing the Company GST state code (first 2 digits of the GSTIN, e.g. <code>27</code> for Maharashtra) with the Customer's GST state code. If both are in Maharashtra (Intrastate), the tax is split into <strong>CGST (9%) + SGST (9%)</strong>. If the customer is in Gujarat or another state (Interstate), the system automatically applies <strong>IGST (18%)</strong>.
                    </div>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="accordion-item border-0 mb-2 rounded bg-light">
                <h2 class="accordion-header" id="faqHeading3">
                    <button class="accordion-button collapsed fw-bold text-dark bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3">
                        3. How do I configure low stock alerts for critical raw chemicals?
                    </button>
                </h2>
                <div id="faqCollapse3" class="accordion-collapse collapse" data-bs-parent="#guideFaqAccordion">
                    <div class="accordion-body small text-muted pt-0">
                        Go to <em>Multi-Tier Inventory &rarr; Stock on Hand</em>, search for your raw material, and click <strong>"Set Qty"</strong>. Set the reorder level (e.g. <code>500.00 KG</code>). Whenever total warehouse stock across all locations falls at or below this value, the dashboard instantly displays a warning badge and the item appears in the Low Stock alert filter.
                    </div>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="accordion-item border-0 rounded bg-light">
                <h2 class="accordion-header" id="faqHeading4">
                    <button class="accordion-button collapsed fw-bold text-dark bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4">
                        4. Can I bill on a smartphone or tablet?
                    </button>
                </h2>
                <div id="faqCollapse4" class="accordion-collapse collapse" data-bs-parent="#guideFaqAccordion">
                    <div class="accordion-body small text-muted pt-0">
                        Yes! The POS station is built with adaptive mobile technology. On a phone or iPad, you get a 2-column catalog grid, swipeable category chips, a persistent floating bottom cart dock, and finger-friendly 32px quantity steppers with 0% horizontal screen overflow.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Interactive Video Player Simulator
    const videoData = {
        1: {
            title: "Tutorial 1: High-Speed POS Fast Billing & Lot Picking",
            duration: "Duration: 03:45 | 1080p Full HD",
            timecode: "01:15 / 03:45",
            progress: "33%",
            heading: "Key Learnings from Module 1 (POS Fast Billing):",
            points: [
                "Using barcode scanner or [F2] for 1-second chemical SKU discovery.",
                "Selecting between multiple manufacturing lots (Auto FIFO vs manual batch pick).",
                "Switching between Retail and Wholesale pricing with [F7].",
                "Receiving Cash, UPI QR code, Card, or Credit payments and printing GST invoices."
            ]
        },
        2: {
            title: "Tutorial 2: Chemical Formulas (BOM) & Production Batch Scaling",
            duration: "Duration: 04:12 | 1080p Full HD",
            timecode: "02:00 / 04:12",
            progress: "48%",
            heading: "Key Learnings from Module 2 (Chemical Production):",
            points: [
                "Creating multi-ingredient Bill of Materials (BOM) recipes with exact ratios.",
                "Launching a production batch and automatically scaling ingredient quantities.",
                "Deducting raw materials and assigning new unique Lot Numbers upon completion.",
                "Recording quality control tests (pH level, specific gravity, viscosity) before release."
            ]
        },
        3: {
            title: "Tutorial 3: Multi-Tier Inventory & Stock Movement Ledger",
            duration: "Duration: 03:20 | 1080p Full HD",
            timecode: "00:45 / 03:20",
            progress: "22%",
            heading: "Key Learnings from Module 3 (Multi-Tier Inventory):",
            points: [
                "Navigating multiple warehouse storage locations (Main Tank, Factory, Retail Shelf).",
                "Viewing the immutable audit ledger for every stock in/out movement.",
                "Performing physical stock reconciliations and entering shrinkage/evaporation adjustments.",
                "Tracking batch expiration dates to prevent chemical degradation."
            ]
        },
        4: {
            title: "Tutorial 4: Low Stock Alerts & Reorder Level Configuration",
            duration: "Duration: 02:50 | 1080p Full HD",
            timecode: "01:30 / 02:50",
            progress: "53%",
            heading: "Key Learnings from Module 4 (Low Stock Alerts):",
            points: [
                "Configuring minimum reorder thresholds for both raw chemicals and finished goods.",
                "Real-time visual alert flags on the Operations Dashboard.",
                "1-click low stock filtering in the Inventory Manager.",
                "Initiating vendor Purchase Orders directly from low stock alert reports."
            ]
        },
        5: {
            title: "Tutorial 5: GST Tax Invoicing, GSTR-1 & DomPDF Print",
            duration: "Duration: 03:15 | 1080p Full HD",
            timecode: "02:40 / 03:15",
            progress: "82%",
            heading: "Key Learnings from Module 5 (GST Reports & PDF Printing):",
            points: [
                "Understanding Intrastate (CGST+SGST) vs Interstate (IGST) automatic tax routing.",
                "Generating official GSTR-1 sales turnover reports.",
                "Downloading clean, high-resolution PDF tax invoices formatted with DomPDF.",
                "Sharing invoices via WhatsApp and Email directly from the invoice view."
            ]
        }
    };

    let isPlaying = false;

    function toggleSimulatedPlayback() {
        isPlaying = !isPlaying;
        const icon = document.getElementById('playBtnIcon');
        const barBtn = document.getElementById('barPlayBtn');
        const overlay = document.getElementById('videoOverlay');

        if (isPlaying) {
            icon.innerHTML = '<i class="fa-solid fa-pause"></i>';
            barBtn.className = 'fa-solid fa-pause';
            overlay.style.background = 'rgba(11, 19, 36, 0.25)';
        } else {
            icon.innerHTML = '<i class="fa-solid fa-play ms-1"></i>';
            barBtn.className = 'fa-solid fa-play';
            overlay.style.background = 'rgba(11, 19, 36, 0.65)';
        }
    }

    function loadVideoLesson(id) {
        // Update active playlist item
        document.querySelectorAll('.playlist-item').forEach((item, index) => {
            if (index + 1 === id) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });

        const data = videoData[id];
        if (!data) return;

        document.getElementById('activeVideoTitle').innerText = data.title;
        document.getElementById('activeVideoDuration').innerText = data.duration;
        document.getElementById('videoTimecode').innerText = data.timecode;
        document.getElementById('videoProgressFill').style.width = data.progress;
        document.getElementById('videoSummaryHeading').innerText = data.heading;

        const pointsContainer = document.getElementById('videoKeyPoints');
        pointsContainer.innerHTML = '';
        data.points.forEach(pt => {
            const li = document.createElement('li');
            li.innerHTML = pt;
            pointsContainer.appendChild(li);
        });

        isPlaying = false;
        toggleSimulatedPlayback();
    }

    function seekSimulatedVideo(e) {
        const bar = e.currentTarget;
        const rect = bar.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const percentage = Math.max(0, Math.min(100, (clickX / rect.width) * 100));
        document.getElementById('videoProgressFill').style.width = percentage + '%';
    }

    function toggleMute() {
        // Toggle visual feedback
    }
</script>
@endpush
