@extends('layouts.app')

@section('title', 'Application User Guide & Training Manual')

@section('content')
<style>
    /* User Guide & Training Center Styling */
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

    /* Sticky Training Playlist Sidebar */
    .playlist-nav-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        position: sticky;
        top: 80px;
        overflow: hidden;
    }
    .playlist-nav-header {
        background: #0f172a;
        color: #ffffff;
        padding: 0.85rem 1.15rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .playlist-menu-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.8rem 1.1rem;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
        cursor: pointer;
    }
    .playlist-menu-item:last-child {
        border-bottom: none;
    }
    .playlist-menu-item:hover, .playlist-menu-item.active {
        background: #f0f9ff;
        color: #005a9c;
        border-left: 4px solid #005a9c;
        font-weight: 600;
    }
    .playlist-item-num {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .playlist-menu-item.active .playlist-item-num {
        background: #005a9c;
        color: #ffffff;
    }

    /* High-Fidelity UI Window Mockup Container */
    .ui-window-mockup {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin: 1.25rem 0;
    }
    .ui-window-topbar {
        background: #1e293b;
        color: #94a3b8;
        padding: 0.45rem 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 0.75rem;
    }
    .ui-window-dots {
        display: flex;
        gap: 5px;
    }
    .ui-window-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .dot-red { background: #ef4444; }
    .dot-yellow { background: #f59e0b; }
    .dot-green { background: #10b981; }

    .ui-window-address {
        background: #0f172a;
        color: #38bdf8;
        padding: 2px 12px;
        border-radius: 4px;
        font-family: var(--pos-mono, monospace);
        font-size: 0.70rem;
        flex-grow: 1;
        max-width: 480px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .ui-window-content {
        padding: 1rem;
        background: #f8fafc;
    }

    /* Callout Badges (❶, ❷, ❸...) */
    .callout-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        background: #005a9c;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.70rem;
        border-radius: 50%;
        margin-right: 4px;
        box-shadow: 0 0 0 2px rgba(0, 90, 156, 0.25);
    }
    .callout-pill-red {
        background: #dc2626;
        box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.25);
    }
    .callout-pill-green {
        background: #059669;
        box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.25);
    }
    .callout-pill-amber {
        background: #d97706;
        box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.25);
    }

    /* Training Module Detail Card */
    .training-module-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 2rem;
        overflow: hidden;
        scroll-margin-top: 80px;
    }
    .training-module-header {
        padding: 1rem 1.4rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
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
            <h4 class="fw-bold text-white mb-1">Gudi Chemicals ERP — Training Manual & Workflow Guide</h4>
            <p class="text-white-50 small mb-0">Complete screen-by-screen visual documentation and operating procedures for Chemical Manufacturing, POS Billing, Stock Lots, and GST.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('pos.index') }}" target="_blank" class="btn btn-success fw-bold shadow-sm d-flex align-items-center">
                <i class="fa-solid fa-bolt me-1 text-warning"></i> Launch POS Desk
            </a>
            <button type="button" class="btn btn-outline-light fw-bold" onclick="window.print()">
                <i class="fa-solid fa-print me-1"></i> Print Manual
            </button>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- LEFT SIDEBAR: Interactive Training Playlist Curriculum -->
    <div class="col-lg-4 col-xl-3.5">
        <div class="playlist-nav-card">
            <div class="playlist-nav-header">
                <div>
                    <h6 class="fw-bold mb-0 text-white"><i class="fa-solid fa-list-check me-2 text-info"></i> Training Playlist</h6>
                    <small class="text-white-50" style="font-size: 0.70rem;">Training Playlist & Module Manuals</small>
                </div>
                <span class="badge bg-primary text-white" style="font-size: 0.65rem;">6 Modules</span>
            </div>

            <!-- Search Filter for Playlist -->
            <div class="p-2 border-bottom bg-light">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" id="playlistFilterInput" class="form-control border-start-0" placeholder="Filter manuals by topic..." oninput="filterPlaylistItems(this.value)">
                </div>
            </div>

            <!-- Playlist Lessons -->
            <div class="playlist-nav-items" id="playlistItemsContainer">
                <a href="#modulePos" class="playlist-menu-item active" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">1</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate fw-bold" style="font-size: 0.80rem;">Module 1: POS Fast Billing Desk</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Barcode scan, multi-lot FIFO, GST cart</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#moduleManufacturing" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">2</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 2: Chemical Formulas (BOM)</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Ingredient scaling, compounding, QC release</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#moduleInventory" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">3</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 3: Multi-Tier Inventory</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Warehouses, reorder alerts, stock audit</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#modulePurchasing" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">4</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 4: Purchasing & GRN</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Inward goods, supplier batches, vendor dues</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#moduleInvoicing" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">5</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 5: Sales Invoicing & GST</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">B2B tax bills, GSTR-1, DomPDF printing</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#moduleSettings" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">6</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 6: Settings & User Roles</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Company profile, FY code, operator roles</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#moduleFaq" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">7</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 7: Hotkeys & FAQ</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Keyboard shortcuts, common solutions</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- RIGHT CONTENT: Detailed Screen-wise Visual Walkthrough Modules -->
    <div class="col-lg-8 col-xl-8.5">

        <!-- ============================================================ -->
        <!-- MODULE 1: POS Fast Billing Desk                             -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="modulePos">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success p-2 rounded-3 text-white"><i class="fa-solid fa-bolt fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 1: POS Fast Billing Desk</h5>
                        <small class="text-muted">High-speed retail & wholesale invoicing, barcode scanning, multi-lot dispatch, and 1-tap tender</small>
                    </div>
                </div>
                <a href="{{ route('pos.index') }}" target="_blank" class="btn btn-sm btn-success fw-bold">
                    <i class="fa-solid fa-bolt me-1"></i> Open POS Station
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-desktop text-primary me-1.5"></i> Screen Architecture & Visual Breakdown</h6>
                <p class="text-muted small mb-2">The POS billing station uses an adaptive split architecture designed for sub-second transaction throughput:</p>

                <!-- UI WINDOW MOCKUP: POS Desk Screen -->
                <div class="ui-window-mockup">
                    <div class="ui-window-topbar">
                        <div class="ui-window-dots">
                            <span class="ui-window-dot dot-red"></span>
                            <span class="ui-window-dot dot-yellow"></span>
                            <span class="ui-window-dot dot-green"></span>
                        </div>
                        <span class="ui-window-address">http://127.0.0.1:8000/pos — POS Fast Billing Workstation</span>
                    </div>
                    <div class="ui-window-content">
                        <div class="row g-2">
                            <!-- Left: Product Catalog Mockup -->
                            <div class="col-md-7">
                                <div class="bg-white p-2 rounded border">
                                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                                        <span class="badge bg-primary px-2 py-1"><span class="callout-pill">❶</span> Barcode Search Strip [F2]</span>
                                        <small class="text-muted font-monospace" style="font-size: 0.68rem;">Scan / SKU Lookup</small>
                                    </div>
                                    <div class="d-flex gap-1 mb-2 overflow-hidden">
                                        <span class="badge bg-primary">All</span>
                                        <span class="badge bg-light text-dark border">Acids</span>
                                        <span class="badge bg-light text-dark border">Solvents</span>
                                        <span class="badge bg-light text-dark border">Surfactants</span>
                                    </div>
                                    <div class="row g-1.5">
                                        <div class="col-6">
                                            <div class="p-2 border rounded bg-light">
                                                <div class="d-flex justify-content-between small mb-1">
                                                    <span class="badge bg-success-subtle text-success border">150.0 L</span>
                                                    <span class="badge bg-info-subtle text-info border"><span class="callout-pill">❷</span> 2 Lots</span>
                                                </div>
                                                <strong class="d-block text-truncate" style="font-size: 0.78rem;">Caustic Soda Lye 48%</strong>
                                                <span class="fw-bold text-success fs-6">₹145.00</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 border rounded bg-light">
                                                <div class="d-flex justify-content-between small mb-1">
                                                    <span class="badge bg-success-subtle text-success border">85.0 KG</span>
                                                    <small class="text-muted">Lot: B26-04</small>
                                                </div>
                                                <strong class="d-block text-truncate" style="font-size: 0.78rem;">Industrial Degreaser</strong>
                                                <span class="fw-bold text-success fs-6">₹320.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Cart Console Mockup -->
                            <div class="col-md-5">
                                <div class="bg-white p-2 rounded border h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge bg-dark"><span class="callout-pill">❸</span> Walk-in / B2B</span>
                                            <div class="btn-group btn-group-xs">
                                                <button class="btn btn-xs btn-primary py-0 px-1">Retail</button>
                                                <button class="btn btn-xs btn-outline-secondary py-0 px-1">Wholesale [F7]</button>
                                            </div>
                                        </div>
                                        <div class="p-1.5 border rounded bg-light mb-2">
                                            <div class="d-flex justify-content-between small">
                                                <strong style="font-size: 0.75rem;">Caustic Soda Lye 48%</strong>
                                                <span class="text-danger fw-bold">✕</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <span class="badge bg-white text-dark border"><span class="callout-pill">❹</span> [- 2 +] L</span>
                                                <span class="fw-bold text-success" style="font-family: var(--pos-mono);">₹290.00</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-2 rounded bg-success text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="callout-pill callout-pill-green">❺</span>
                                                <small class="text-uppercase fw-bold" style="font-size: 0.65rem;">Total Payable</small>
                                            </div>
                                            <span class="fw-bold fs-5" style="font-family: var(--pos-mono);">₹342.20</span>
                                        </div>
                                        <button class="btn btn-sm btn-light w-100 fw-bold mt-1 text-success">
                                            Collect & Pay (F9) &rarr;
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Screen Annotated Guide Table -->
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th width="12%">Callout</th>
                                <th width="28%">Screen Element</th>
                                <th>Operational Description & Pro-Tip</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="callout-pill">❶</span></td>
                                <td><strong>Barcode Search Strip [F2]</strong></td>
                                <td>Press <code>[F2]</code> from anywhere on the page to focus the search bar. Supports laser scanning or partial chemical SKU typing. Pressing <em>Enter</em> immediately adds the top match to cart.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❷</span></td>
                                <td><strong>Multi-Lot Badge Pill</strong></td>
                                <td>When a chemical product has multiple batches in stock, a lot indicator appears (e.g. <em>"2 Lots"</em>). Clicking opens the batch selector modal allowing you to pick a specific manufacturing lot or choose <strong>Auto (FIFO)</strong>.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❸</span></td>
                                <td><strong>Customer & Price Tier [F7]</strong></td>
                                <td>Select Retail walk-in or a registered B2B client with GSTIN. Press <code>[F7]</code> to instantly toggle between Retail and Wholesale discounted rate structures.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❹</span></td>
                                <td><strong>Cart Quantity Steppers</strong></td>
                                <td>Finger-friendly 32px <code>[+]</code> and <code>[-]</code> touch buttons. Directly clicking the quantity input allows bulk quantity typing (e.g. <code>250 Liters</code>).</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❺</span></td>
                                <td><strong>Grand Total & Tender [F9]</strong></td>
                                <td>Calculates CGST + SGST or IGST in real time with auto-rounding. Press <code>[F9]</code> to tender Cash, UPI QR code, Card, or Credit and generate the tax bill.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-1.5"><i class="fa-solid fa-mobile-screen text-success me-1.5"></i> Mobile & Tablet Billing Workflow:</h6>
                    <ul class="small text-muted mb-0 ps-3">
                        <li>On phones, the screen defaults to full-width product search with swipeable category chips.</li>
                        <li>A persistent bottom dock <code>🛒 [Count] • ₹Total | View Bill ➔</code> remains visible at all times.</li>
                        <li>Tapping the dock instantly slides up the Cart & Checkout sheet with zero lost data.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 2: Chemical Formulas (BOM) & Compounding Production  -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="moduleManufacturing">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary p-2 rounded-3 text-white"><i class="fa-solid fa-industry fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 2: Chemical Formulas (BOM) & Production</h5>
                        <small class="text-muted">Formula creation, chemical compounding, proportional ingredient scaling, and batch QC approval</small>
                    </div>
                </div>
                <a href="{{ route('production.formulas.index') }}" class="btn btn-sm btn-primary fw-bold">
                    <i class="fa-solid fa-vial me-1"></i> Formulas (BOM)
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-flask-vial text-info me-1.5"></i> Chemical Compounding Architecture</h6>
                <p class="text-muted small mb-2">Manage recipes, raw chemical ratios, batch scaling, and laboratory parameter checks:</p>

                <!-- UI WINDOW MOCKUP: Chemical Production Screen -->
                <div class="ui-window-mockup">
                    <div class="ui-window-topbar">
                        <div class="ui-window-dots">
                            <span class="ui-window-dot dot-red"></span>
                            <span class="ui-window-dot dot-yellow"></span>
                            <span class="ui-window-dot dot-green"></span>
                        </div>
                        <span class="ui-window-address">http://127.0.0.1:8000/production/orders/create — Production Batch Compounding</span>
                    </div>
                    <div class="ui-window-content">
                        <div class="bg-white p-3 rounded border">
                            <div class="row g-2 mb-2 pb-2 border-bottom">
                                <div class="col-md-6">
                                    <span class="callout-pill">❶</span> <strong>Target Output:</strong>
                                    <span class="text-primary fw-bold">Industrial Degreaser (1L Bottle)</span>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <span class="callout-pill">❷</span> <strong>Planned Compounding Batch:</strong>
                                    <span class="badge bg-success fs-6">2,500.00 Liters</span>
                                </div>
                            </div>

                            <strong class="d-block small text-muted mb-1"><span class="callout-pill">❸</span> Proportional Bill of Materials (BOM) Allocation:</strong>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered bg-light mb-2" style="font-size: 0.75rem;">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>Raw Chemical Ingredient</th>
                                            <th>Ratio</th>
                                            <th class="text-end">Required Qty</th>
                                            <th class="text-end">Warehouse Stock</th>
                                            <th class="text-center">Availability Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Sodium Hydroxide Flakes (Caustic)</td>
                                            <td>12%</td>
                                            <td class="text-end fw-bold">300.00 KG</td>
                                            <td class="text-end">1,250.00 KG</td>
                                            <td class="text-center"><span class="badge bg-success">In Stock (Available)</span></td>
                                        </tr>
                                        <tr>
                                            <td>Sulfonic Acid 90% (LABSA)</td>
                                            <td>18%</td>
                                            <td class="text-end fw-bold">450.00 L</td>
                                            <td class="text-end">850.00 L</td>
                                            <td class="text-center"><span class="badge bg-success">In Stock (Available)</span></td>
                                        </tr>
                                        <tr>
                                            <td>Demineralized Water (DM)</td>
                                            <td>70%</td>
                                            <td class="text-end fw-bold">1,750.00 L</td>
                                            <td class="text-end">5,000.00 L</td>
                                            <td class="text-center"><span class="badge bg-success">In Stock (Available)</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div>
                                    <span class="callout-pill">❹</span>
                                    <span class="badge bg-info text-white"><i class="fa-solid fa-vial-circle-check me-1"></i> QC Parameters: pH 11.2 | Specific Gravity 1.05</span>
                                </div>
                                <button class="btn btn-sm btn-primary fw-bold">
                                    <i class="fa-solid fa-circle-check me-1"></i> Finalize Batch & Post to Stock
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Explanation Steps -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th width="12%">Step</th>
                                <th width="28%">Action / Workflow</th>
                                <th>System Operation & Accounting</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="callout-pill">❶</span></td>
                                <td><strong>Formulate Recipe (BOM)</strong></td>
                                <td>Go to <em>Chemical Production &rarr; Formulas (BOM)</em>. Define the standard recipe for 1 unit (e.g. 1L, 1KG, or 1 Barrel) specifying all active raw chemicals, water ratios, and packaging bottles.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❷</span></td>
                                <td><strong>Scale Batch Quantity</strong></td>
                                <td>When launching a production batch, enter the total quantity to manufacture (e.g. 2,500 L). The ERP calculates exact ingredient weights automatically.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❸</span></td>
                                <td><strong>Automated Stock Deduction</strong></td>
                                <td>Checks warehouse stock levels. On batch initiation, raw chemicals are committed so other operators cannot double-allocate the same tanks.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❹</span></td>
                                <td><strong>QC Approval & Lot Generation</strong></td>
                                <td>Enter quality check metrics. Upon final approval, raw chemicals are deducted from stock and a brand new finished product Lot Number is registered into inventory with its calculated expiry date.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 3: Multi-Tier Inventory & Stock Alerts               -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="moduleInventory">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning p-2 rounded-3 text-white"><i class="fa-solid fa-boxes-stacked fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 3: Multi-Tier Inventory & Low-Stock Alerts</h5>
                        <small class="text-muted">Stock on hand, warehouse locations, reorder thresholds, and stock adjustment ledgers</small>
                    </div>
                </div>
                <a href="{{ route('inventory.index', ['filter' => 'low_stock']) }}" class="btn btn-sm btn-outline-danger fw-bold">
                    <i class="fa-solid fa-bell me-1"></i> Low Stock Alerts
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-warehouse text-warning me-1.5"></i> Multi-Warehouse & Threshold Architecture</h6>
                <p class="text-muted small mb-2">Maintain full stock visibility across chemical raw materials, semi-finished blends, packaging, and finished goods:</p>

                <!-- UI WINDOW MOCKUP: Inventory Screen -->
                <div class="ui-window-mockup">
                    <div class="ui-window-topbar">
                        <div class="ui-window-dots">
                            <span class="ui-window-dot dot-red"></span>
                            <span class="ui-window-dot dot-yellow"></span>
                            <span class="ui-window-dot dot-green"></span>
                        </div>
                        <span class="ui-window-address">http://127.0.0.1:8000/inventory — Multi-Tier Inventory & Alerts</span>
                    </div>
                    <div class="ui-window-content">
                        <div class="bg-white p-3 rounded border">
                            <div class="d-flex justify-content-between align-items-center mb-2.5 flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="callout-pill">❶</span>
                                    <span class="badge bg-danger rounded-pill px-2.5 py-1">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> 3 Items Below Reorder Level
                                    </span>
                                </div>
                                <div class="d-flex gap-1.5">
                                    <span class="badge bg-light text-dark border px-2 py-1"><span class="callout-pill">❷</span> Wh: Main Plant</span>
                                    <span class="badge bg-light text-dark border px-2 py-1">Wh: Retail Godown</span>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.78rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Chemical Item</th>
                                            <th>Category</th>
                                            <th class="text-end">Current Stock</th>
                                            <th class="text-end"><span class="callout-pill">❸</span> Reorder Level</th>
                                            <th>Stock Health</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Hydrochloric Acid 33%</strong><div class="text-muted small">RAW-HCL-33</div></td>
                                            <td>Acids</td>
                                            <td class="text-end fw-bold text-danger">120.00 L</td>
                                            <td class="text-end fw-semibold">500.00 L</td>
                                            <td><span class="badge bg-danger">Critical Low Stock</span></td>
                                            <td class="text-end"><button class="btn btn-xs btn-outline-primary py-0 px-1.5">Set Qty</button></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Caustic Soda Flakes</strong><div class="text-muted small">RAW-CAUSTIC-FLAKE</div></td>
                                            <td>Alkalis</td>
                                            <td class="text-end fw-bold text-success">1,450.00 KG</td>
                                            <td class="text-end fw-semibold">400.00 KG</td>
                                            <td><span class="badge bg-success">Optimal</span></td>
                                            <td class="text-end"><button class="btn btn-xs btn-outline-primary py-0 px-1.5">Set Qty</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Explanation Steps -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th width="12%">Step</th>
                                <th width="28%">Feature</th>
                                <th>How to Use & Pro-Tip</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="callout-pill">❶</span></td>
                                <td><strong>Low Stock Dashboard Flag</strong></td>
                                <td>The system continuously monitors on-hand quantities against minimum thresholds. Clicking the red alert takes you directly to the low-stock watchlist.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❷</span></td>
                                <td><strong>Multi-Warehouse Locations</strong></td>
                                <td>Switch between chemical bulk storage tanks, compounding floor, and packaging stores. Stock movements between locations are permanently recorded in the ledger.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❸</span></td>
                                <td><strong>Configuring Thresholds</strong></td>
                                <td>Click <strong>"Set Qty"</strong> next to any chemical. Enter the minimum reorder quantity. The system automatically prompts purchase managers when supplies dwindle.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 4: Purchasing & Inward Goods Receipt (GRN)           -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="modulePurchasing">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info p-2 rounded-3 text-white"><i class="fa-solid fa-truck-ramp-box fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 4: Purchasing & Goods Receipt (GRN)</h5>
                        <small class="text-muted">Purchase orders, supplier shipments, Goods Receipt Notes (GRN), and vendor ledger dues</small>
                    </div>
                </div>
                <a href="{{ route('purchases.grn.index') }}" class="btn btn-sm btn-info text-white fw-bold">
                    <i class="fa-solid fa-truck-ramp-box me-1"></i> Inward (GRN)
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-truck text-info me-1.5"></i> Inward Goods Ingestion & Lot Registration</h6>
                <p class="text-muted small mb-2">When a tanker or truck delivers raw chemicals, generate a Goods Receipt Note (GRN):</p>

                <!-- UI WINDOW MOCKUP: GRN Screen -->
                <div class="ui-window-mockup">
                    <div class="ui-window-topbar">
                        <div class="ui-window-dots">
                            <span class="ui-window-dot dot-red"></span>
                            <span class="ui-window-dot dot-yellow"></span>
                            <span class="ui-window-dot dot-green"></span>
                        </div>
                        <span class="ui-window-address">http://127.0.0.1:8000/purchases/grn/create — Inward Goods Receipt (GRN)</span>
                    </div>
                    <div class="ui-window-content">
                        <div class="bg-white p-3 rounded border">
                            <div class="row g-2 mb-2 pb-2 border-bottom">
                                <div class="col-md-4">
                                    <span class="callout-pill">❶</span> <strong>Supplier / Vendor:</strong>
                                    <div class="text-primary fw-bold">Gujarat Alkalies & Chemicals Ltd</div>
                                </div>
                                <div class="col-md-4">
                                    <span class="callout-pill">❷</span> <strong>Delivery Challan / Bill #:</strong>
                                    <div class="text-dark font-monospace fw-bold">GACL-INV-9921</div>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <span class="callout-pill">❸</span> <strong>Receiving Warehouse:</strong>
                                    <span class="badge bg-secondary">Main Chemical Plant</span>
                                </div>
                            </div>

                            <strong class="d-block small text-muted mb-1"><span class="callout-pill">❹</span> Inward Chemical Lot Details:</strong>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered bg-light mb-2" style="font-size: 0.75rem;">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>Raw Chemical Item</th>
                                            <th>Vendor Lot / Batch #</th>
                                            <th>Mfg Date</th>
                                            <th>Expiry Date</th>
                                            <th class="text-end">Received Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Sulfuric Acid 98% Concentrated</td>
                                            <td><span class="badge bg-dark font-monospace">LOT-SA-20260401</span></td>
                                            <td>01-Apr-2026</td>
                                            <td>31-Mar-2028</td>
                                            <td class="text-end fw-bold text-success">5,000.00 L</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Explanation Steps -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th width="12%">Step</th>
                                <th width="28%">Action</th>
                                <th>Operational Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="callout-pill">❶</span></td>
                                <td><strong>Select Vendor & PO</strong></td>
                                <td>Select the chemical supplier. You can link the inward shipment to an existing approved Purchase Order or enter a direct delivery.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❷</span></td>
                                <td><strong>Challan & Invoice #</strong></td>
                                <td>Enter the supplier's challan / invoice number for GST input tax credit (ITC) reconciliation.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❸</span></td>
                                <td><strong>Destination Warehouse</strong></td>
                                <td>Assign which tank, bay, or godown the incoming chemicals are being transferred into.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❹</span></td>
                                <td><strong>Lot Number & Expiry</strong></td>
                                <td>Assign the supplier's batch number and expiry date. The system automatically registers this lot into inventory and credits the vendor's ledger balance.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 5: Sales Invoicing & GST Tax Filing                  -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="moduleInvoicing">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary p-2 rounded-3 text-white"><i class="fa-solid fa-file-invoice fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 5: Sales Invoicing & GST Tax Filing</h5>
                        <small class="text-muted">B2B GST invoices, automatic CGST+SGST vs IGST routing, GSTR-1 turnover reports, and DomPDF downloads</small>
                    </div>
                </div>
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-primary fw-bold">
                    <i class="fa-solid fa-receipt me-1"></i> Invoices
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-receipt text-primary me-1.5"></i> GST Tax Invoice Architecture</h6>
                <p class="text-muted small mb-2">Automated GST compliance for chemical distributors, wholesalers, and retail buyers:</p>

                <!-- UI WINDOW MOCKUP: Invoice Screen -->
                <div class="ui-window-mockup">
                    <div class="ui-window-topbar">
                        <div class="ui-window-dots">
                            <span class="ui-window-dot dot-red"></span>
                            <span class="ui-window-dot dot-yellow"></span>
                            <span class="ui-window-dot dot-green"></span>
                        </div>
                        <span class="ui-window-address">http://127.0.0.1:8000/invoices/1 — Tax Invoice Document</span>
                    </div>
                    <div class="ui-window-content">
                        <div class="bg-white p-3 rounded border">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <h6 class="fw-bold mb-0 text-primary">GUDI CHEMICALS</h6>
                                    <small class="text-muted">GSTIN: 27AAACG1234D1Z5 | State: Maharashtra (27)</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success"><span class="callout-pill callout-pill-green">❶</span> TAX INVOICE: INV-2026-0001</span>
                                    <div class="small text-muted font-monospace mt-0.5">{{ date('d M Y') }}</div>
                                </div>
                            </div>

                            <div class="row g-2 mb-2 small">
                                <div class="col-6">
                                    <span class="callout-pill">❷</span> <strong>Billed To (Customer):</strong>
                                    <div>Bharat Chemical Corp (B2B Registered)</div>
                                    <div class="text-muted font-monospace">GSTIN: 27AABCB9912E1Z8 (Maharashtra)</div>
                                </div>
                                <div class="col-6 text-end">
                                    <span class="callout-pill">❸</span> <strong>Tax Routing:</strong>
                                    <span class="badge bg-primary">Intrastate (CGST 9% + SGST 9%)</span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div class="fw-bold">
                                    Grand Total: <span class="text-success fs-5" style="font-family: var(--pos-mono);">₹12,480.00</span>
                                </div>
                                <div class="d-flex gap-1.5">
                                    <span class="callout-pill">❹</span>
                                    <button class="btn btn-sm btn-outline-danger fw-bold"><i class="fa-solid fa-file-pdf me-1"></i> Download PDF (DomPDF)</button>
                                    <button class="btn btn-sm btn-outline-success fw-bold"><i class="fa-brands fa-whatsapp me-1"></i> WhatsApp</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Explanation Steps -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th width="12%">Callout</th>
                                <th width="28%">GST Mechanism</th>
                                <th>How the ERP Automates Tax Accounting</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="callout-pill">❶</span></td>
                                <td><strong>Invoice Sequence</strong></td>
                                <td>Generates continuous financial-year sequential numbers (e.g. <code>INV-2026-0001</code>) compliant with GST audit standards.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❷</span></td>
                                <td><strong>B2B vs B2C Handling</strong></td>
                                <td>If customer has a GSTIN, it marks the sale as B2B and includes their tax ID on the bill. For walk-in retail, it formats as B2C Consumer invoice.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❸</span></td>
                                <td><strong>Intrastate vs Interstate</strong></td>
                                <td>Compares the first 2 digits of the customer GSTIN. Same state (e.g. 27 = MH) splits into CGST + SGST. Out of state (e.g. 24 = Gujarat) applies IGST.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❹</span></td>
                                <td><strong>DomPDF Invoice Printing</strong></td>
                                <td>1-click PDF download generates an A4 tax invoice ready for physical print, email, or WhatsApp sharing with customer.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 6: Settings, Financial Year & User Roles             -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="moduleSettings">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary p-2 rounded-3 text-white"><i class="fa-solid fa-gear fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 6: Company Settings & User Roles</h5>
                        <small class="text-muted">GST configuration, invoice prefixes, financial year codes, and role-based access control</small>
                    </div>
                </div>
                <a href="{{ route('settings.index') }}" class="btn btn-sm btn-outline-secondary fw-bold">
                    <i class="fa-solid fa-gear me-1"></i> ERP Settings
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-users-gear text-secondary me-1.5"></i> Security, Permissions & Company Profile</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border h-100">
                            <h6 class="fw-bold text-dark mb-1.5"><i class="fa-solid fa-user-shield text-primary me-1"></i> User Roles & Access Rights</h6>
                            <ul class="small text-muted mb-0 ps-3">
                                <li><strong>Super Admin / Admin:</strong> Full permissions across all manufacturing, stock adjustments, pricing, and financial audits.</li>
                                <li><strong>Production Manager:</strong> Access to Chemical Formulas (BOM), batch compounding orders, and laboratory QC parameter recording.</li>
                                <li><strong>Cashier / Sales:</strong> Access to POS Fast Billing Desk and customer invoice printing; restricted from editing BOMs or company settings.</li>
                                <li><strong>Storekeeper:</strong> Access to Inward Goods Receipts (GRN) and physical stock adjustments.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border h-100">
                            <h6 class="fw-bold text-dark mb-1.5"><i class="fa-solid fa-building text-success me-1"></i> Company Profile & Tax Setup</h6>
                            <ul class="small text-muted mb-0 ps-3">
                                <li><strong>GSTIN & State Code:</strong> Ensure company GSTIN is entered under <em>Settings</em> (default: <code>27AAACG1234D1Z5</code>).</li>
                                <li><strong>Financial Year (FY):</strong> Configures the active fiscal period (e.g. <code>2026-27</code>) used on all invoice prefixes.</li>
                                <li><strong>Thermal vs. A4 Invoice:</strong> Set default print paper width for cash counter receipt printers.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 7: Hotkeys Cheat Sheet & FAQ                         -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="moduleFaq">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark p-2 rounded-3 text-white"><i class="fa-solid fa-keyboard fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 7: Keyboard Shortcuts & Troubleshooting FAQ</h5>
                        <small class="text-muted">High-speed keyboard commands and solutions to common chemical ERP operations</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-3 p-lg-4">
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-keyboard text-primary me-1.5"></i> Fast Billing Hotkeys</h6>
                        <table class="table table-sm table-bordered bg-white" style="font-size: 0.80rem;">
                            <thead class="table-light">
                                <tr><th>Key</th><th>Function</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <tr><td><span class="keyboard-shortcut-pill">F2</span></td><td>Barcode Search</td><td>Jumps directly to search bar</td></tr>
                                <tr><td><span class="keyboard-shortcut-pill">F4</span></td><td>Customer Pick</td><td>Opens customer selection dropdown</td></tr>
                                <tr><td><span class="keyboard-shortcut-pill">F7</span></td><td>Price Tier</td><td>Toggles between Retail & Wholesale</td></tr>
                                <tr><td><span class="keyboard-shortcut-pill">F9</span></td><td>Checkout Tender</td><td>Opens payment modal to collect Cash/UPI</td></tr>
                                <tr><td><span class="keyboard-shortcut-pill">F10</span></td><td>Clear Cart</td><td>Empties current unbilled items</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-circle-question text-info me-1.5"></i> Common Operational Questions</h6>
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item border-0 mb-1.5 rounded bg-light">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark py-2 px-3 bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        How to select between 2 batches of 1 product in POS?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse">
                                    <div class="accordion-body small text-muted pt-0 px-3 pb-2">
                                        Click the product card in POS. A batch popup displays all active lots with manufacturing/expiry dates. Pick a specific batch or choose <strong>"Auto (FIFO)"</strong>.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 rounded bg-light">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark py-2 px-3 bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        How do I download DomPDF tax invoices?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse">
                                    <div class="accordion-body small text-muted pt-0 px-3 pb-2">
                                        Open any invoice under <em>Sales Invoices</em> or from the POS completion dialog, and click <strong>"Download PDF"</strong>. The system generates an official A4 document ready for printing.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Playlist Navigation & Smooth Scroll
    function activatePlaylistItem(element) {
        document.querySelectorAll('.playlist-menu-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
    }

    // Filter Playlist Lessons by Keyword
    function filterPlaylistItems(query) {
        query = query.toLowerCase().trim();
        const items = document.querySelectorAll('.playlist-menu-item');
        items.forEach(item => {
            const text = item.innerText.toLowerCase();
            if (text.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Highlight Playlist Item on Scroll
    window.addEventListener('scroll', function() {
        const modules = document.querySelectorAll('.training-module-card');
        const scrollPos = window.scrollY + 120;

        modules.forEach(mod => {
            const top = mod.offsetTop;
            const height = mod.offsetHeight;
            const id = mod.getAttribute('id');

            if (scrollPos >= top && scrollPos < top + height) {
                document.querySelectorAll('.playlist-menu-item').forEach(link => {
                    if (link.getAttribute('href') === '#' + id) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            }
        });
    });
</script>
@endpush
