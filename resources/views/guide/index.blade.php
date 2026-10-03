@extends('layouts.app')

@section('title', 'Application User Guide & Comprehensive Training Manual')

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
        width: 340px;
        height: 340px;
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
        max-height: calc(100vh - 100px);
        display: flex;
        flex-direction: column;
    }
    .playlist-nav-header {
        background: #0f172a;
        color: #ffffff;
        padding: 0.85rem 1.15rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .playlist-nav-items {
        overflow-y: auto;
        flex-grow: 1;
    }
    .playlist-menu-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
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
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #475569;
        font-size: 0.70rem;
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
        max-width: 520px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .ui-window-content {
        padding: 1rem;
        background: #f8fafc;
    }

    /* Callout Badges (❶, ❷, ❸, ❹, ❺, ❻, ❼) */
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
        margin-bottom: 2.25rem;
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
                    <i class="fa-solid fa-layer-group me-1 text-info"></i> {{ $productsCount }} Chemical SKUs
                </span>
                <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-10 py-1 px-2">
                    <i class="fa-solid fa-industry me-1 text-success"></i> {{ $batchesCount }} Production Batches
                </span>
                <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-10 py-1 px-2">
                    <i class="fa-solid fa-file-invoice-dollar me-1 text-warning"></i> {{ $invoicesCount }} Invoices Processed
                </span>
            </div>
            <h4 class="fw-bold text-white mb-1">Gudi Chemicals ERP — Comprehensive Screen-by-Screen Training Manual</h4>
            <p class="text-white-50 small mb-0">Exhaustive visual manual and Standard Operating Procedures (SOP) covering all 11 enterprise modules, multi-lot tracking, POS billing, QC testing, and GST compliance.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('pos.index') }}" target="_blank" class="btn btn-success fw-bold shadow-sm d-flex align-items-center">
                <i class="fa-solid fa-bolt me-1 text-warning"></i> Launch POS Desk
            </a>
            <button type="button" class="btn btn-outline-light fw-bold" onclick="window.print()">
                <i class="fa-solid fa-print me-1"></i> Print / PDF Manual
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
                    <h6 class="fw-bold mb-0 text-white"><i class="fa-solid fa-book-open-reader me-2 text-info"></i> Training Playlist</h6>
                    <small class="text-white-50" style="font-size: 0.70rem;">Training Playlist & Module Manuals</small>
                </div>
                <span class="badge bg-primary text-white" style="font-size: 0.65rem;">Workflow + 11 Modules</span>
            </div>

            <!-- Search Filter for Playlist -->
            <div class="p-2 border-bottom bg-light">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" id="playlistFilterInput" class="form-control border-start-0" placeholder="Filter screens or topics..." oninput="filterPlaylistItems(this.value)">
                </div>
            </div>

            <!-- Playlist Lessons -->
            <div class="playlist-nav-items" id="playlistItemsContainer">
                <a href="#moduleWorkflow" class="playlist-menu-item active" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num bg-primary text-white"><i class="fa-solid fa-diagram-project" style="font-size: 0.65rem;"></i></span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate fw-bold" style="font-size: 0.80rem;">Full ERP Workflow</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">End-to-end chemical enterprise lifecycle</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#modulePos" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">1</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate fw-bold" style="font-size: 0.80rem;">Module 1: POS Fast Billing Desk</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Barcode scan, multi-lot FIFO, tender modal</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#moduleDashboard" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">2</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 2: Operations Dashboard</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Executive KPIs, 14-day sales trend, low stock</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#moduleInvoicing" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">3</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 3: Invoicing, Receipts & Returns</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">A4 Tax Invoice, 80mm thermal, Credit Notes</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#moduleManufacturing" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">4</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 4: Chemical Manufacturing</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">BOM formulas, batch scaling, QC tests, lot inward</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#moduleInventory" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">5</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 5: Inventory & Stock Ledger</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Warehouses, reorder threshold, adjustments</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#modulePurchasing" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">6</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 6: Purchasing & GRN</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">PO workflow, supplier lots, expiry tagging, payments</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#moduleMasters" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">7</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 7: Master Data Management</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Products, B2B Customers, Vendors, UOM & Conversions</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#modulePromotions" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">8</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 8: Promotions & Discount Rules</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">BOGO offers, percentage markdowns, combo packs</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#moduleFinance" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">9</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 9: Expenses & Financial Reports</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">GSTR-1, Collections, Aging, Valuation & PDF export</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#moduleSettings" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">10</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 10: Settings, Users & Profile</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">GSTIN setup, FY Code, User roles, Password</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>

                <a href="#moduleFaq" class="playlist-menu-item" onclick="activatePlaylistItem(this)">
                    <span class="playlist-item-num">11</span>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-truncate" style="font-size: 0.80rem;">Module 11: Hotkeys & Chemical FAQ</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">Billing shortcuts, FIFO multi-batch, Safety SOPs</small>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- RIGHT CONTENT: Detailed Screen-wise Visual Walkthrough Modules -->
    <div class="col-lg-8 col-xl-8.5">

        <!-- ============================================================ -->
        <!-- MASTER SECTION: Full Application Work Flow                   -->
        <!-- ============================================================ -->
        <div class="training-module-card border-primary shadow-sm" id="moduleWorkflow">
            <div class="training-module-header bg-primary bg-opacity-10 border-bottom border-primary border-opacity-25">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary p-2 rounded-3 text-white"><i class="fa-solid fa-diagram-project fa-lg"></i></span>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h5 class="fw-bold text-dark mb-0">Full Application Workflow — Complete End-to-End Enterprise Lifecycle</h5>
                            <span class="badge bg-primary text-white" style="font-size: 0.65rem;">End-to-End Architecture</span>
                        </div>
                        <small class="text-muted">Interactive visual roadmap connecting Master Data, Purchasing & Tanker GRN, Reactor Compounding, Laboratory QC, Multi-Lot POS Billing, and GSTR-1 Tax Filing</small>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary fw-bold" onclick="window.print()">
                    <i class="fa-solid fa-print me-1"></i> Print Roadmap
                </button>
            </div>

            <div class="card-body p-3 p-lg-4">
                <!-- Visual Pipeline Cards Grid -->
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-arrows-split-up-and-left text-primary me-1.5"></i> 6-Stage Chemical Business Operational Pipeline</h6>
                <p class="text-muted small mb-3">Every operation in Gudi Chemicals ERP is interconnected. Data created in procurement seamlessly flows into plant production, warehouse lot tracking, POS billing, and government tax returns:</p>

                <!-- 6 STAGES RESPONSIVE GRID -->
                <div class="row g-2 mb-4">
                    <!-- Stage 1 -->
                    <div class="col-md-4 col-sm-6">
                        <div class="p-2.5 rounded border bg-light h-100 position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-dark"><span class="callout-pill">Phase 1</span> Master Setup</span>
                                <small class="text-muted fw-bold">Step 1</small>
                            </div>
                            <strong class="d-block text-dark small mb-1"><i class="fa-solid fa-database text-primary me-1"></i> Master Data & BOM</strong>
                            <p class="text-muted mb-2" style="font-size: 0.72rem;">
                                Configure raw chemicals, finished SKUs, UOM conversions (Drums to Liters), customer GSTINs, and approved Bill of Materials (BOM) formulas.
                            </p>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('masters.products.index') }}" class="badge bg-white text-dark border text-decoration-none">Products</a>
                                <a href="{{ route('production.formulas.index') }}" class="badge bg-white text-dark border text-decoration-none">BOM Formulas</a>
                            </div>
                        </div>
                    </div>

                    <!-- Stage 2 -->
                    <div class="col-md-4 col-sm-6">
                        <div class="p-2.5 rounded border bg-light h-100 position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-danger"><span class="callout-pill">Phase 2</span> Procurement</span>
                                <small class="text-muted fw-bold">Step 2</small>
                            </div>
                            <strong class="d-block text-dark small mb-1"><i class="fa-solid fa-truck-ramp-box text-danger me-1"></i> PO & Inward GRN</strong>
                            <p class="text-muted mb-2" style="font-size: 0.72rem;">
                                Raise Purchase Order to chemical suppliers. At the factory gate, generate Goods Receipt Note (GRN) with supplier batch #, expiry date & COA purity.
                            </p>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('purchases.orders.index') }}" class="badge bg-white text-dark border text-decoration-none">Purchase Orders</a>
                                <a href="{{ route('purchases.grn.index') }}" class="badge bg-white text-dark border text-decoration-none">GRN Inward</a>
                            </div>
                        </div>
                    </div>

                    <!-- Stage 3 -->
                    <div class="col-md-4 col-sm-6">
                        <div class="p-2.5 rounded border bg-light h-100 position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-info text-dark"><span class="callout-pill">Phase 3</span> Compounding</span>
                                <small class="text-muted fw-bold">Step 3</small>
                            </div>
                            <strong class="d-block text-dark small mb-1"><i class="fa-solid fa-flask-vial text-info me-1"></i> Reactor Batch & QC</strong>
                            <p class="text-muted mb-2" style="font-size: 0.72rem;">
                                Scale BOM to target volume. Reactor compounding executes. Laboratory chemist records pH, Viscosity & SG assays before lot finalization.
                            </p>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('production.orders.index') }}" class="badge bg-white text-dark border text-decoration-none">Batch Orders</a>
                                <span class="badge bg-white text-dark border">Lab QC Tests</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stage 4 -->
                    <div class="col-md-4 col-sm-6">
                        <div class="p-2.5 rounded border bg-light h-100 position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-success"><span class="callout-pill">Phase 4</span> Sales & POS</span>
                                <small class="text-muted fw-bold">Step 4</small>
                            </div>
                            <strong class="d-block text-dark small mb-1"><i class="fa-solid fa-bolt text-success me-1"></i> Multi-Lot POS Billing</strong>
                            <p class="text-muted mb-2" style="font-size: 0.72rem;">
                                Scan barcode, choose specific batch or Auto-FIFO, toggle Retail/Wholesale tier, auto-split CGST+SGST/IGST, and tender payment in sub-seconds.
                            </p>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('pos.index') }}" class="badge bg-white text-dark border text-decoration-none">POS Station</a>
                                <a href="{{ route('invoices.index') }}" class="badge bg-white text-dark border text-decoration-none">Tax Invoices</a>
                            </div>
                        </div>
                    </div>

                    <!-- Stage 5 -->
                    <div class="col-md-4 col-sm-6">
                        <div class="p-2.5 rounded border bg-light h-100 position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-secondary"><span class="callout-pill">Phase 5</span> Inventory</span>
                                <small class="text-muted fw-bold">Step 5</small>
                            </div>
                            <strong class="d-block text-dark small mb-1"><i class="fa-solid fa-boxes-stacked text-secondary me-1"></i> Reorders & Adjustments</strong>
                            <p class="text-muted mb-2" style="font-size: 0.72rem;">
                                Live godown ledger logs every gram. Automatic low-stock reorder thresholds alert the dashboard. Log evaporation shrinkage or customer returns.
                            </p>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('inventory.index') }}" class="badge bg-white text-dark border text-decoration-none">Stock Ledger</a>
                                <a href="{{ route('returns.index') }}" class="badge bg-white text-dark border text-decoration-none">Returns</a>
                            </div>
                        </div>
                    </div>

                    <!-- Stage 6 -->
                    <div class="col-md-4 col-sm-6">
                        <div class="p-2.5 rounded border bg-light h-100 position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-primary"><span class="callout-pill">Phase 6</span> Accounting</span>
                                <small class="text-muted fw-bold">Step 6</small>
                            </div>
                            <strong class="d-block text-dark small mb-1"><i class="fa-solid fa-file-invoice-dollar text-primary me-1"></i> GSTR-1, Aging & Audits</strong>
                            <p class="text-muted mb-2" style="font-size: 0.72rem;">
                                Reconcile daily cash and UPI collections, review Accounts Receivable / Payable aging, generate GSTR-1 tax summaries, and 1-click DomPDF exports.
                            </p>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('reports.gst') }}" class="badge bg-white text-dark border text-decoration-none">GSTR-1 Tax</a>
                                <a href="{{ route('reports.collections') }}" class="badge bg-white text-dark border text-decoration-none">Collections</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- END-TO-END CHEMICAL JOURNEY WALKTHROUGH -->
                <div class="border rounded p-3 bg-white mb-3 shadow-sm">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="fa-solid fa-route text-success me-1.5"></i> Practical Chemical Lifecycle: Tracing 1 Batch from Tanker Inward to Customer Tax Invoice
                    </h6>
                    <p class="small text-muted mb-3">
                        Here is an exact practical example of how data flows through all ERP screens when manufacturing and distributing <strong>Industrial Degreaser</strong>:
                    </p>

                    <div class="workflow-timeline ps-3 border-start border-3 border-primary ms-2 mb-2">
                        <div class="mb-3 position-relative ps-2">
                            <span class="badge bg-primary px-2 py-0.5 mb-1" style="font-size: 0.70rem;">1. Tanker Arrival & GRN Inward</span>
                            <p class="small text-muted mb-0">
                                Chemical supplier delivers 1,000 Liters of Caustic Soda Lye. Storekeeper opens <strong>Purchasing > Goods Receipt Note (GRN)</strong> (<a href="{{ route('purchases.grn.create') }}">create GRN</a>), inputs supplier lot <code>GACL-984</code>, and attaches COA. Stock is auto-inwarded to Raw Material Bay under batch <code>LOT-CS-2026-01</code>.
                            </p>
                        </div>

                        <div class="mb-3 position-relative ps-2">
                            <span class="badge bg-info text-dark px-2 py-0.5 mb-1" style="font-size: 0.70rem;">2. Production Compounding & Proportional Scaling</span>
                            <p class="small text-muted mb-0">
                                Production Manager opens <strong>Manufacturing > Batch Orders</strong> (<a href="{{ route('production.orders.create') }}">create batch</a>), picks formula <em>Industrial Degreaser High-Foam</em>, and inputs <code>500 L</code> target volume. The system auto-calculates 75 KG Caustic Soda + 75 KG SLES + 350 L DM Water and reserves stock.
                            </p>
                        </div>

                        <div class="mb-3 position-relative ps-2">
                            <span class="badge bg-warning text-dark px-2 py-0.5 mb-1" style="font-size: 0.70rem;">3. Laboratory Quality Control (QC) & Finalization</span>
                            <p class="small text-muted mb-0">
                                After reactor mixing, Quality Chemist draws a sample, records pH = 12.1 and Viscosity = 280 cP in the batch QC modal, and clicks <strong>"Finalize Batch"</strong>. Raw chemicals are deducted from inventory, and 500 L of Finished Goods is inwarded under new batch <code>LOT-DG-2026-08</code>.
                            </p>
                        </div>

                        <div class="mb-3 position-relative ps-2">
                            <span class="badge bg-success px-2 py-0.5 mb-1" style="font-size: 0.70rem;">4. POS Fast Billing & Batch Dispatch</span>
                            <p class="small text-muted mb-0">
                                Apex Dyeing Mills orders 50 L. Cashier opens <strong>POS Desk</strong> (<a href="{{ route('pos.index') }}">launch POS</a>), scans barcode, selects batch <code>LOT-DG-2026-08</code>, enters customer GSTIN <code>27AAACP9876C1ZV</code>, and presses <span class="keyboard-shortcut-pill">F9</span>. Customer pays via UPI QR (UTR entered). Bill is settled in 10 seconds.
                            </p>
                        </div>

                        <div class="position-relative ps-2">
                            <span class="badge bg-dark px-2 py-0.5 mb-1" style="font-size: 0.70rem;">5. Real-Time Stock Deduction & GSTR-1 Tax Record</span>
                            <p class="small text-muted mb-0">
                                The system immediately decrements 50 L from batch <code>LOT-DG-2026-08</code> (leaving 450 L). The transaction logs to the Stock Movement Ledger, prints an A4 Tax Invoice, and records the 18% GST (9% CGST + 9% SGST) in the monthly <strong>GSTR-1 Report</strong> (<a href="{{ route('reports.gst') }}">view GSTR-1</a>).
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ROLE MATRIX -->
                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-users text-primary me-1"></i> Who Does What? Department Responsibilities Across the Workflow</h6>
                    <div class="row g-2 pt-1" style="font-size: 0.75rem;">
                        <div class="col-md-3 col-6">
                            <strong>Plant & QC Chemist:</strong>
                            <div class="text-muted">Formulas (BOM), Compounding Batches, Laboratory Assays, Lot Finalization.</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <strong>Storekeeper:</strong>
                            <div class="text-muted">Tanker Weighing, GRN Inward, Stock Adjustments, Spillage & Shrinkage entries.</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <strong>Cashier / Sales:</strong>
                            <div class="text-muted">Fast POS Billing, Barcode Scanning, Multi-Lot FIFO selection, Cash/UPI tender.</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <strong>Accountant / Admin:</strong>
                            <div class="text-muted">Collections Reconciliation, GSTR-1 Filings, Aging Debtors, DomPDF Exports.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 1: POS Fast Billing Desk & Cash Station               -->
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
                                                <div class="d-flex align-items-center gap-1">
                                                    <button class="btn btn-xs btn-outline-secondary py-0 px-1">-</button>
                                                    <span class="fw-bold px-1 font-monospace" style="font-size: 0.75rem;">5.0</span>
                                                    <button class="btn btn-xs btn-outline-secondary py-0 px-1">+</button>
                                                </div>
                                                <span class="text-muted font-monospace" style="font-size: 0.75rem;">₹725.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between mt-1 pt-1 border-top" style="font-size: 0.65rem;">
                                                <span class="badge bg-secondary">Lot: LOT-2026-01</span>
                                                <span class="text-muted">GST 18%: ₹130.50</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top pt-1.5">
                                        <div class="d-flex justify-content-between fw-bold text-dark mb-1">
                                            <span>Payable Total:</span>
                                            <span class="text-success fs-6">₹855.50</span>
                                        </div>
                                        <button class="btn btn-sm btn-success w-100 fw-bold py-1.5">
                                            <span class="callout-pill callout-pill-green">❹</span> Settle Bill [F9]
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Callout Elements Table -->
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">Callout</th>
                                <th width="28%">Screen Component</th>
                                <th>Operational Function & Hotkey</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="callout-pill">❶</span></td>
                                <td><strong>Barcode & SKU Search Bar</strong></td>
                                <td>Press <span class="keyboard-shortcut-pill">F2</span> to focus. Optical barcode scanner inputs automatically press Enter to instantly add matching SKU to cart.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❷</span></td>
                                <td><strong>Multi-Lot Badge ("2 Lots")</strong></td>
                                <td>Indicates that the chemical has multiple active batches in stock with distinct manufacturing and expiry dates. Clicking opens the <strong>Batch Picker Dialog</strong>.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❸</span></td>
                                <td><strong>Customer Selector & Quick Add</strong></td>
                                <td>Press <span class="keyboard-shortcut-pill">F4</span> to choose an existing B2B account (auto-applies GSTIN for tax invoice) or click <strong>"+"</strong> to quickly register a new walk-in buyer.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill callout-pill-green">❹</span></td>
                                <td><strong>Tender & Settlement [F9]</strong></td>
                                <td>Opens the multi-tender payment popup: handles Cash with automatic change calculation, UPI QR with UTR recording, and Card swipe.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- SPECIAL DETAIL: How to Select Between 2 Batches of 1 Product -->
                <div class="border rounded p-3 bg-white mb-3 shadow-sm">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="fa-solid fa-layer-group text-info me-1"></i> Multi-Batch Selection: Choosing Specific Lots vs Auto FIFO
                    </h6>
                    <p class="small text-muted mb-2">When a product like <em>Caustic Soda Lye</em> has multiple manufacturing batches in stock, the ERP provides two flexible dispatch options:</p>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="p-2.5 bg-light rounded border h-100">
                                <strong class="d-block text-primary small mb-1"><i class="fa-solid fa-clock-rotate-left me-1"></i> Option A: Automatic FIFO (First-In, First-Out)</strong>
                                <p class="small text-muted mb-0" style="font-size: 0.75rem;">
                                    Click <strong>"Auto (FIFO)"</strong> in the batch modal. The ERP automatically picks the oldest active chemical lot based on inward date to prevent expiry spoilage.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2.5 bg-light rounded border h-100">
                                <strong class="d-block text-success small mb-1"><i class="fa-solid fa-hand-pointer me-1"></i> Option B: Specific Customer Batch Selection</strong>
                                <p class="small text-muted mb-0" style="font-size: 0.75rem;">
                                    If a customer requests a specific batch (e.g. <code>LOT-2026-02</code> with 99% assay), click that specific lot row. The cart locks that batch number and prints it on the tax invoice.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step-by-Step SOP -->
                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-list-check text-success me-1"></i> Cashier SOP: Processing a 10-Second Checkout</h6>
                    <ol class="small text-muted mb-0 ps-3">
                        <li><strong>Step 1:</strong> Scan product barcode with scanner or press <span class="keyboard-shortcut-pill">F2</span> and type SKU name.</li>
                        <li><strong>Step 2:</strong> If multi-lot popup appears, select desired lot or click <strong>Auto FIFO</strong>.</li>
                        <li><strong>Step 3:</strong> Select customer (<span class="keyboard-shortcut-pill">F4</span>) or leave as Walk-in Retail. Toggle Wholesale (<span class="keyboard-shortcut-pill">F7</span>) if applicable.</li>
                        <li><strong>Step 4:</strong> Press <span class="keyboard-shortcut-pill">F9</span> to open Tender dialog. Enter cash received (e.g. ₹1000). System shows exact change due.</li>
                        <li><strong>Step 5:</strong> Press Enter to finalize. Bill prints automatically to 80mm thermal receipt printer and stock is instantly decremented.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 2: Operations Dashboard & Executive Analytics         -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="moduleDashboard">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary p-2 rounded-3 text-white"><i class="fa-solid fa-gauge-high fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 2: Operations Dashboard & Executive Analytics</h5>
                        <small class="text-muted">Real-time KPI metrics, revenue trajectory charts, category distribution donut, and low stock monitors</small>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-primary fw-bold">
                    <i class="fa-solid fa-gauge me-1"></i> Open Dashboard
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-desktop text-primary me-1.5"></i> Dashboard Screen Architecture</h6>
                <p class="text-muted small mb-2">The main command center consolidates financial, inventory, and plant compounding data into real-time visual telemetry:</p>

                <!-- UI WINDOW MOCKUP: Dashboard -->
                <div class="ui-window-mockup">
                    <div class="ui-window-topbar">
                        <div class="ui-window-dots">
                            <span class="ui-window-dot dot-red"></span>
                            <span class="ui-window-dot dot-yellow"></span>
                            <span class="ui-window-dot dot-green"></span>
                        </div>
                        <span class="ui-window-address">http://127.0.0.1:8000/dashboard — Executive Command Center</span>
                    </div>
                    <div class="ui-window-content">
                        <!-- Top Stat Cards Mockup -->
                        <div class="row g-2 mb-2">
                            <div class="col-3">
                                <div class="bg-white p-2 rounded border border-start-primary border-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted" style="font-size: 0.68rem;">Today's Revenue</small>
                                        <span class="callout-pill">❶</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0">₹42,850</h6>
                                    <small class="text-success" style="font-size: 0.65rem;">+14% vs yesterday</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="bg-white p-2 rounded border border-start-success border-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted" style="font-size: 0.68rem;">Monthly Sales</small>
                                        <span class="callout-pill">❷</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0">₹6,84,200</h6>
                                    <small class="text-muted" style="font-size: 0.65rem;">April 2026</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="bg-white p-2 rounded border border-start-warning border-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted" style="font-size: 0.68rem;">Low Stock SKUs</small>
                                        <span class="callout-pill callout-pill-red">❸</span>
                                    </div>
                                    <h6 class="fw-bold text-danger mb-0">4 SKUs</h6>
                                    <small class="text-danger" style="font-size: 0.65rem;">Below threshold</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="bg-white p-2 rounded border border-start-info border-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted" style="font-size: 0.68rem;">Active Batches</small>
                                        <span class="callout-pill">❹</span>
                                    </div>
                                    <h6 class="fw-bold text-info mb-0">3 Batches</h6>
                                    <small class="text-muted" style="font-size: 0.65rem;">Compounding / QC</small>
                                </div>
                            </div>
                        </div>

                        <!-- Mid Charts Mockup -->
                        <div class="row g-2 mb-2">
                            <div class="col-7">
                                <div class="bg-white p-2 rounded border">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong style="font-size: 0.75rem;"><span class="callout-pill">❺</span> 14-Day Sales Revenue Trajectory</strong>
                                        <span class="badge bg-primary-subtle text-primary" style="font-size: 0.65rem;">Last 14 Days</span>
                                    </div>
                                    <div class="bg-light rounded p-3 text-center" style="height: 75px; display: flex; align-items: flex-end; justify-content: space-around;">
                                        <div style="width: 14px; height: 35%; background: #005a9c; border-radius: 2px;"></div>
                                        <div style="width: 14px; height: 50%; background: #005a9c; border-radius: 2px;"></div>
                                        <div style="width: 14px; height: 40%; background: #005a9c; border-radius: 2px;"></div>
                                        <div style="width: 14px; height: 70%; background: #005a9c; border-radius: 2px;"></div>
                                        <div style="width: 14px; height: 60%; background: #005a9c; border-radius: 2px;"></div>
                                        <div style="width: 14px; height: 90%; background: #059669; border-radius: 2px;"></div>
                                        <div style="width: 14px; height: 80%; background: #005a9c; border-radius: 2px;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="bg-white p-2 rounded border">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong style="font-size: 0.75rem;"><span class="callout-pill">❻</span> Category Share</strong>
                                        <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">Revenue %</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-around py-2">
                                        <div style="width: 48px; height: 48px; border-radius: 50%; border: 6px solid #005a9c; border-top-color: #059669; border-right-color: #d97706;"></div>
                                        <div class="small" style="font-size: 0.68rem;">
                                            <div><span class="badge bg-primary p-1 me-1"></span> Acids (45%)</div>
                                            <div><span class="badge bg-success p-1 me-1"></span> Solvents (30%)</div>
                                            <div><span class="badge bg-warning p-1 me-1"></span> Detergents (25%)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Low Stock Table Strip Mockup -->
                        <div class="bg-white p-2 rounded border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-danger" style="font-size: 0.75rem;"><span class="callout-pill callout-pill-red">❼</span> Critical Low Stock Alert Table</strong>
                                <span class="badge bg-danger-subtle text-danger" style="font-size: 0.65rem;">Action Needed</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-xs table-bordered mb-0" style="font-size: 0.70rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th>Current Stock</th>
                                            <th>Reorder Level</th>
                                            <th>Status</th>
                                            <th>Quick Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Hydrochloric Acid 33%</strong></td>
                                            <td>Industrial Acids</td>
                                            <td class="text-danger fw-bold">12.00 L</td>
                                            <td>50.00 L</td>
                                            <td><span class="badge bg-danger">Critical Shortage</span></td>
                                            <td><button class="btn btn-xs btn-outline-primary py-0 px-1" style="font-size: 0.65rem;">Create PO</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Callout Table -->
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">Callout</th>
                                <th width="28%">Screen Component</th>
                                <th>Operational Purpose & Automatic Logic</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="callout-pill">❶</span></td>
                                <td><strong>Today's Sales Stat Card</strong></td>
                                <td>Aggregates net settled POS and invoice collections since midnight (00:00). Shows percentage comparison against prior business day.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❷</span></td>
                                <td><strong>Monthly Sales Turnover</strong></td>
                                <td>Calculates gross revenue for the current calendar month across both B2C walk-in retail and B2B wholesale tax invoices.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill callout-pill-red">❸</span></td>
                                <td><strong>Low Stock Counter</strong></td>
                                <td>Scans all chemical inventory in real time. Flags any SKU whose total stock across all active lots is less than or equal to its minimum reorder threshold.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❹</span></td>
                                <td><strong>Active Production Batches</strong></td>
                                <td>Displays ongoing compounding orders currently in <em>Draft</em>, <em>Compounding</em>, or <em>QC Pending</em> status in the production plant.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❺</span></td>
                                <td><strong>14-Day Sales Chart</strong></td>
                                <td>Visual line/bar telemetry illustrating daily billing volume trends to anticipate raw chemical material demand spikes.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❻</span></td>
                                <td><strong>Category Donut Breakdown</strong></td>
                                <td>Proportional revenue split by product family (Acids, Solvents, Detergents, Specialty Polymers) to analyze high-margin categories.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill callout-pill-red">❼</span></td>
                                <td><strong>Low Stock Warning Table</strong></td>
                                <td>Prioritized shortage table with 1-click shortcut to launch a Chemical Purchase Order (PO) to replenish depleted stock immediately.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Standard Operating Procedure -->
                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-list-check text-success me-1"></i> Daily Executive SOP: Morning Review</h6>
                    <ol class="small text-muted mb-0 ps-3">
                        <li><strong>Step 1: Check Low Stock SKUs:</strong> Review the Critical Low Stock Alert Table before plant startup. Verify raw chemical availability for today's compounding schedule.</li>
                        <li><strong>Step 2: Inspect In-Process Batches:</strong> Check if any batch is awaiting Laboratory QC parameters (pH, specific gravity) before bottling.</li>
                        <li><strong>Step 3: Monitor Daily Collections:</strong> Verify that yesterday's cash, UPI QR settlements, and card payments match bank reconciliations.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 3: Invoicing, Receipts & Sales Returns                -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="moduleInvoicing">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning p-2 rounded-3 text-dark"><i class="fa-solid fa-file-invoice-dollar fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 3: Sales Invoicing, Thermal Receipts & Sales Returns</h5>
                        <small class="text-muted">GST tax invoice register, A4 DomPDF export, 80mm thermal slips, and Credit Notes</small>
                    </div>
                </div>
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-warning text-dark fw-bold">
                    <i class="fa-solid fa-list me-1"></i> View Invoices
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-desktop text-primary me-1.5"></i> Sales Invoices Screen & Tax Breakdown</h6>
                <p class="text-muted small mb-2">Manage tax invoices, track payment status (Paid, Partial, Unpaid), and generate formal GST-compliant documents:</p>

                <!-- UI WINDOW MOCKUP: Invoice Details -->
                <div class="ui-window-mockup">
                    <div class="ui-window-topbar">
                        <div class="ui-window-dots">
                            <span class="ui-window-dot dot-red"></span>
                            <span class="ui-window-dot dot-yellow"></span>
                            <span class="ui-window-dot dot-green"></span>
                        </div>
                        <span class="ui-window-address">http://127.0.0.1:8000/invoices/INV-2026-0001 — Tax Invoice Document</span>
                    </div>
                    <div class="ui-window-content">
                        <div class="bg-white p-3 rounded border">
                            <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">TAX INVOICE</h6>
                                    <span class="badge bg-primary"><span class="callout-pill">❶</span> INV-2026-0001</span>
                                    <small class="text-muted ms-2">Date: 03-Oct-2026</small>
                                </div>
                                <div class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-danger btn-xs"><span class="callout-pill callout-pill-red">❷</span> PDF Download</button>
                                        <button class="btn btn-outline-dark btn-xs"><span class="callout-pill">❸</span> Thermal 80mm</button>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 mb-2" style="font-size: 0.75rem;">
                                <div class="col-6">
                                    <strong>Billed To:</strong>
                                    <div class="text-dark fw-bold">Apex Dyeing & Chemical Mills</div>
                                    <div class="text-muted">GSTIN: <span class="badge bg-light text-dark border">27AAACP9876C1ZV</span></div>
                                </div>
                                <div class="col-6 text-end">
                                    <strong>Place of Supply:</strong>
                                    <div>Maharashtra (State Code: 27)</div>
                                    <div class="text-success fw-bold">Status: PAID (Cash)</div>
                                </div>
                            </div>

                            <table class="table table-xs table-bordered mb-2" style="font-size: 0.70rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item & Lot</th>
                                        <th>HSN</th>
                                        <th>Qty</th>
                                        <th>Rate</th>
                                        <th>Taxable</th>
                                        <th>CGST</th>
                                        <th>SGST</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Caustic Soda Lye [Lot: B26-01]</td>
                                        <td>2815</td>
                                        <td>10.0 L</td>
                                        <td>₹145.00</td>
                                        <td>₹1,450.00</td>
                                        <td>₹130.50 (9%)</td>
                                        <td>₹130.50 (9%)</td>
                                        <td class="fw-bold">₹1,711.00</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-between align-items-center pt-1 border-top">
                                <small class="text-muted" style="font-size: 0.68rem;">E. & O.E. — Subject to Mumbai Jurisdiction</small>
                                <strong class="fs-6 text-dark"><span class="callout-pill callout-pill-green">❹</span> Net Total: ₹1,711.00</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Callout Breakdown -->
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">Callout</th>
                                <th width="28%">Screen Feature</th>
                                <th>Operational Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="callout-pill">❶</span></td>
                                <td><strong>Financial Year Invoice ID</strong></td>
                                <td>Strict continuous serial numbering (e.g. <code>INV-2026-0001</code>) compliant with Section 31 of CGST Act.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill callout-pill-red">❷</span></td>
                                <td><strong>DomPDF A4 Download</strong></td>
                                <td>Generates an official A4 PDF formatted with company logo, GSTIN, HSN summary, and authorized signatory signature box.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❸</span></td>
                                <td><strong>80mm POS Thermal Slip</strong></td>
                                <td>Sends ESC/POS optimized raw layout to thermal receipt printers for walk-in retail purchases.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill callout-pill-green">❹</span></td>
                                <td><strong>Automated Tax Split (CGST/SGST vs IGST)</strong></td>
                                <td>Compares Customer GST state code against company state (27). Intrastate splits into 9% CGST + 9% SGST; Interstate charges 18% IGST.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Sales Returns & Credit Note SOP -->
                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-rotate-left text-danger me-1"></i> Sales Return & Credit Note SOP</h6>
                    <p class="small text-muted mb-2">When a customer returns a defective or unused chemical carboy/drum:</p>
                    <ol class="small text-muted mb-0 ps-3">
                        <li>Navigate to <strong>Sales Billing > Returns / Credit Notes</strong> (<a href="{{ route('returns.create') }}">Create Return</a>).</li>
                        <li>Select the original invoice number and pick the product lot being returned.</li>
                        <li>Enter return quantity, condition (Intact / Damaged), and reason (e.g. customer ordered wrong grade).</li>
                        <li>Submitting automatically rests the physical stock into the warehouse and issues an official Credit Note.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 4: Chemical Manufacturing & Batch Compounding         -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="moduleManufacturing">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info p-2 rounded-3 text-white"><i class="fa-solid fa-flask-vial fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 4: Chemical Manufacturing & Batch Compounding</h5>
                        <small class="text-muted">Bill of Materials (BOM) formulas, auto proportional scaling, laboratory QC tests, and lot inward</small>
                    </div>
                </div>
                <a href="{{ route('production.orders.index') }}" class="btn btn-sm btn-outline-info text-dark fw-bold">
                    <i class="fa-solid fa-industry me-1"></i> Batch Orders
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-desktop text-primary me-1.5"></i> Production Batch Screen & Scaling Engine</h6>
                <p class="text-muted small mb-2">Standardizes chemical compounding, auto-calculates raw material consumption, and records laboratory assays:</p>

                <!-- UI WINDOW MOCKUP: Production Order -->
                <div class="ui-window-mockup">
                    <div class="ui-window-topbar">
                        <div class="ui-window-dots">
                            <span class="ui-window-dot dot-red"></span>
                            <span class="ui-window-dot dot-yellow"></span>
                            <span class="ui-window-dot dot-green"></span>
                        </div>
                        <span class="ui-window-address">http://127.0.0.1:8000/production/orders/create — Compounding Order</span>
                    </div>
                    <div class="ui-window-content">
                        <div class="bg-white p-3 rounded border">
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold mb-0">Select Approved Formula (BOM):</label>
                                    <div class="badge bg-primary-subtle text-primary p-1.5 w-100 text-start border">
                                        <span class="callout-pill">❶</span> Formula: Industrial Degreaser High-Foam (v1.2)
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold mb-0">Target Output Qty:</label>
                                    <div class="badge bg-light text-dark p-1.5 w-100 text-start border font-monospace">
                                        <span class="callout-pill">❷</span> 500.00 Liters
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold mb-0">Assigned Batch #:</label>
                                    <div class="badge bg-light text-dark p-1.5 w-100 text-start border font-monospace">
                                        BATCH-2026-08
                                    </div>
                                </div>
                            </div>

                            <strong class="d-block small text-muted mb-1"><span class="callout-pill">❸</span> Proportional Raw Material Auto-Scaling Table:</strong>
                            <table class="table table-xs table-bordered mb-2" style="font-size: 0.70rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Raw Chemical</th>
                                        <th>Base %</th>
                                        <th>Scaled Quantity Required</th>
                                        <th>Warehouse Stock</th>
                                        <th>Availability</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Demineralized Water (DM Water)</td>
                                        <td>70.0%</td>
                                        <td class="fw-bold font-monospace">350.00 L</td>
                                        <td>2,400.00 L</td>
                                        <td><span class="badge bg-success">In Stock</span></td>
                                    </tr>
                                    <tr>
                                        <td>Caustic Soda Flakes 99%</td>
                                        <td>15.0%</td>
                                        <td class="fw-bold font-monospace">75.00 KG</td>
                                        <td>450.00 KG</td>
                                        <td><span class="badge bg-success">In Stock</span></td>
                                    </tr>
                                    <tr>
                                        <td>Sodium Lauryl Ether Sulfate (SLES)</td>
                                        <td>15.0%</td>
                                        <td class="fw-bold font-monospace">75.00 KG</td>
                                        <td>120.00 KG</td>
                                        <td><span class="badge bg-success">In Stock</span></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="p-2 bg-light rounded border">
                                <strong class="small text-dark d-block mb-1"><span class="callout-pill callout-pill-amber">❹</span> Laboratory QC Test Requirements:</strong>
                                <div class="d-flex gap-3 small text-muted" style="font-size: 0.70rem;">
                                    <span>pH Target: <strong>11.5 - 12.5</strong></span>
                                    <span>Viscosity: <strong>250 - 300 cP</strong></span>
                                    <span>Specific Gravity: <strong>1.08 ± 0.02</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Callout Explanation Table -->
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">Callout</th>
                                <th width="28%">Screen Component</th>
                                <th>Operational Mechanism</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="callout-pill">❶</span></td>
                                <td><strong>Bill of Materials (BOM) Formula</strong></td>
                                <td>Approved standard formulation. Defines raw ingredient ratios, batch order instructions, and hazard handling precautions.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❷</span></td>
                                <td><strong>Proportional Scaling Multiplier</strong></td>
                                <td>Enter any target volume (e.g. 500 L or 2,500 L). The engine auto-multiplies each raw chemical percentage down to exact grams/milliliters.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❸</span></td>
                                <td><strong>Raw Material Deduction</strong></td>
                                <td>Upon releasing the batch order, the required raw materials are deducted from the raw materials warehouse stock ledger.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill callout-pill-amber">❹</span></td>
                                <td><strong>Laboratory QC Release Form</strong></td>
                                <td>Chemists enter measured pH, viscosity, and appearance. If values are within tolerance, clicking <strong>Finalize</strong> creates the new Finished Goods Lot and makes it available for sale in POS.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Plant SOP -->
                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-vial-circle-check text-info me-1"></i> Chemist SOP: Batch Compounding to Finished Lot</h6>
                    <ol class="small text-muted mb-0 ps-3">
                        <li><strong>Step 1 (Formula Selection):</strong> Go to <em>Production > Batch Orders > Create</em>. Pick target formula.</li>
                        <li><strong>Step 2 (Scaling):</strong> Enter target production volume. System checks stock and verifies raw chemical availability.</li>
                        <li><strong>Step 3 (Compounding):</strong> Plant operator compounds chemicals according to reaction sequence in reactor tank.</li>
                        <li><strong>Step 4 (Lab QC Assay):</strong> Quality Chemist tests sample and inputs pH, Viscosity, and SG on the batch detail screen.</li>
                        <li><strong>Step 5 (Finalization):</strong> Click <em>"Finalize & Inward to Warehouse"</em>. The ERP creates a new finished goods lot with QR barcode and auto-inwards stock.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 5: Multi-Tier Inventory & Stock Movement Ledger       -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="moduleInventory">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary p-2 rounded-3 text-white"><i class="fa-solid fa-boxes-stacked fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 5: Multi-Tier Inventory, Reorders & Stock Ledger</h5>
                        <small class="text-muted">Warehouse stock registers, minimum reorder thresholds, audit adjustments, and complete movement logs</small>
                    </div>
                </div>
                <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-secondary fw-bold">
                    <i class="fa-solid fa-boxes-stacked me-1"></i> Stock Registry
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-desktop text-primary me-1.5"></i> Inventory Ledger & Dynamic Reorder Engine</h6>
                <p class="text-muted small mb-2">Track inventory across Raw Materials, Finished Goods, and Hazardous Storage with multi-lot traceability:</p>

                <!-- UI WINDOW MOCKUP: Inventory -->
                <div class="ui-window-mockup">
                    <div class="ui-window-topbar">
                        <div class="ui-window-dots">
                            <span class="ui-window-dot dot-red"></span>
                            <span class="ui-window-dot dot-yellow"></span>
                            <span class="ui-window-dot dot-green"></span>
                        </div>
                        <span class="ui-window-address">http://127.0.0.1:8000/inventory — Stock on Hand & Warehouse Ledger</span>
                    </div>
                    <div class="ui-window-content">
                        <div class="bg-white p-3 rounded border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex gap-2 align-items-center">
                                    <span class="badge bg-primary"><span class="callout-pill">❶</span> Main Chemical Godown</span>
                                    <span class="badge bg-light text-dark border">Hazardous Shed #2</span>
                                </div>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-xs btn-outline-primary py-0 px-2"><span class="callout-pill">❷</span> Opening Stock</button>
                                    <button class="btn btn-xs btn-outline-danger py-0 px-2"><span class="callout-pill callout-pill-red">❸</span> Stock Adjust</button>
                                </div>
                            </div>

                            <table class="table table-xs table-bordered mb-2" style="font-size: 0.70rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product / SKU</th>
                                        <th>Type</th>
                                        <th>Active Lots</th>
                                        <th>Total On Hand</th>
                                        <th>Min Reorder Threshold</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Nitric Acid 68% Technical</strong></td>
                                        <td>Raw Material</td>
                                        <td><span class="badge bg-info-subtle text-info border">LOT-NA-01 (180 L)</span></td>
                                        <td class="fw-bold font-monospace">180.00 L</td>
                                        <td>
                                            <span class="callout-pill">❹</span>
                                            <span class="badge bg-light text-dark border font-monospace">100.00 L</span>
                                        </td>
                                        <td><span class="badge bg-success">Adequate</span></td>
                                        <td><button class="btn btn-xs btn-light border py-0 px-1">Ledger</button></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Industrial Bleach Liquid</strong></td>
                                        <td>Finished Goods</td>
                                        <td><span class="badge bg-danger-subtle text-danger border">LOT-BL-04 (15 L)</span></td>
                                        <td class="fw-bold font-monospace text-danger">15.00 L</td>
                                        <td><span class="badge bg-light text-dark border font-monospace">50.00 L</span></td>
                                        <td><span class="badge bg-danger">Low Stock Alert</span></td>
                                        <td><button class="btn btn-xs btn-primary py-0 px-1">Restock</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Callout Table -->
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">Callout</th>
                                <th width="28%">Screen Component</th>
                                <th>Operational Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="callout-pill">❶</span></td>
                                <td><strong>Warehouse Selector</strong></td>
                                <td>Filters stock levels by physical storage locations (e.g. Raw Material Bay, Acid Tank Farm, Retail Showroom).</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❷</span></td>
                                <td><strong>Opening Stock Entry Form</strong></td>
                                <td>Used during initial system setup to initialize bulk physical stock with lot identifiers and cost valuation.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill callout-pill-red">❸</span></td>
                                <td><strong>Stock Adjustments Desk</strong></td>
                                <td>Used by Storekeepers to reconcile stock for chemical evaporation, spillage, drum leakage, or physical inventory audit counts.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❹</span></td>
                                <td><strong>Dynamic Reorder Level Input</strong></td>
                                <td>Storekeepers can adjust the safety buffer quantity per SKU. When stock dips below this limit, it triggers warnings across the entire ERP.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Storekeeper SOP -->
                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-clipboard-check text-secondary me-1"></i> Storekeeper SOP: Recording Spillage or Evaporation Loss</h6>
                    <ol class="small text-muted mb-0 ps-3">
                        <li>Navigate to <strong>Inventory > Adjustments</strong> and click <em>Create Stock Adjustment</em>.</li>
                        <li>Pick the affected chemical SKU, Warehouse, and specific Batch Lot.</li>
                        <li>Select Adjustment Type: <strong>Decrease (Shrinkage / Leakage / Evaporation)</strong>.</li>
                        <li>Enter quantity lost (e.g. <code>2.5 L</code>) and mandatory audit reason (e.g. <em>Valve seal weeping during summer storage</em>).</li>
                        <li>Submitting posts an immutable debit entry to the <strong>Stock Movement Ledger</strong> (<a href="{{ route('inventory.ledger') }}">view ledger</a>).</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 6: Purchasing & Goods Receipt Notes (GRN)             -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="modulePurchasing">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger p-2 rounded-3 text-white"><i class="fa-solid fa-cart-shopping fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 6: Purchasing, GRN Lot Creation & Vendor Payments</h5>
                        <small class="text-muted">Purchase orders, manager approval flow, inward Goods Receipt Notes with supplier lots, and payment ledgers</small>
                    </div>
                </div>
                <a href="{{ route('purchases.orders.index') }}" class="btn btn-sm btn-outline-danger fw-bold">
                    <i class="fa-solid fa-cart-shopping me-1"></i> Purchase Orders
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-desktop text-primary me-1.5"></i> Purchasing Flow & Inward GRN Window</h6>
                <p class="text-muted small mb-2">Complete procurement lifecycle from PO requisition to physical tanker inward and supplier settlement:</p>

                <!-- UI WINDOW MOCKUP: GRN Screen -->
                <div class="ui-window-mockup">
                    <div class="ui-window-topbar">
                        <div class="ui-window-dots">
                            <span class="ui-window-dot dot-red"></span>
                            <span class="ui-window-dot dot-yellow"></span>
                            <span class="ui-window-dot dot-green"></span>
                        </div>
                        <span class="ui-window-address">http://127.0.0.1:8000/purchases/grn/create — Goods Receipt Note (GRN)</span>
                    </div>
                    <div class="ui-window-content">
                        <div class="bg-white p-3 rounded border">
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold mb-0">Select Approved Purchase Order:</label>
                                    <div class="badge bg-primary-subtle text-primary p-1.5 w-100 text-start border">
                                        <span class="callout-pill">❶</span> PO-2026-003 — Gujarat Alkalies & Chemicals Ltd.
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold mb-0">Supplier Challan / Invoice #:</label>
                                    <div class="badge bg-light text-dark p-1.5 w-100 text-start border font-monospace">
                                        <span class="callout-pill">❷</span> GACL/INV/2026/894
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold mb-0">Receiving Warehouse:</label>
                                    <div class="badge bg-light text-dark p-1.5 w-100 text-start border">
                                        Bulk Tank Farm (Bay A)
                                    </div>
                                </div>
                            </div>

                            <strong class="d-block small text-muted mb-1"><span class="callout-pill">❸</span> Inward Chemical Lot Registration & Quality Check:</strong>
                            <table class="table table-xs table-bordered mb-2" style="font-size: 0.70rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Material</th>
                                        <th>PO Qty</th>
                                        <th>Received Qty</th>
                                        <th>Assigned Inward Lot #</th>
                                        <th>Expiry Date</th>
                                        <th>QC Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Sulfuric Acid 98%</td>
                                        <td>1,000.0 L</td>
                                        <td class="fw-bold font-monospace">1,000.0 L</td>
                                        <td><span class="badge bg-info">LOT-SA-2026-04</span></td>
                                        <td><span class="font-monospace">31-Mar-2028</span></td>
                                        <td><span class="badge bg-success">Passed</span></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-between align-items-center pt-1 border-top">
                                <span class="badge bg-secondary">COA (Certificate of Analysis) Attached</span>
                                <button class="btn btn-sm btn-success fw-bold py-1 px-3">
                                    <span class="callout-pill callout-pill-green">❹</span> Post GRN & Inward Stock
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Callout Table -->
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">Callout</th>
                                <th width="28%">Screen Feature</th>
                                <th>Procurement Control Function</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="callout-pill">❶</span></td>
                                <td><strong>Approved PO Requisition</strong></td>
                                <td>Only purchase orders approved by management can have Goods Receipts (GRN) generated against them, preventing unauthorized inward deliveries.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❷</span></td>
                                <td><strong>Supplier Delivery Challan #</strong></td>
                                <td>Records vendor invoice number and transport vehicle number (tanker tanker registration) for audit tracking.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❸</span></td>
                                <td><strong>Inward Lot & Expiry Tagging</strong></td>
                                <td>Every received chemical delivery is tagged with a unique batch number and manufacturer expiry date, ensuring FIFO tracking in POS.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill callout-pill-green">❹</span></td>
                                <td><strong>Automatic Ledger & Payable Booking</strong></td>
                                <td>Posting the GRN automatically increments warehouse physical stock and creates an Accounts Payable liability in the vendor ledger.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Procurement SOP -->
                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-truck-ramp-box text-danger me-1"></i> Inward Gate SOP: Chemical Tanker Receiving</h6>
                    <ol class="small text-muted mb-0 ps-3">
                        <li>Verify that the physical delivery matches the PO in <strong>Purchasing > Purchase Orders</strong>.</li>
                        <li>Weigh delivery tanker on weighbridge and verify Certificate of Analysis (COA) purity report.</li>
                        <li>Open <strong>Purchasing > Goods Receipt (GRN) > Create</strong> and enter received quantity and supplier batch number.</li>
                        <li>Submit GRN. Stock is instantly credited to the warehouse and ready for compounding or POS distribution.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 7: Master Data Management                             -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="moduleMasters">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary p-2 rounded-3 text-white"><i class="fa-solid fa-database fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 7: Master Data Management</h5>
                        <small class="text-muted">Chemical product catalog, HSN codes, B2B customer GSTINs, vendors, and metric UOM conversions</small>
                    </div>
                </div>
                <a href="{{ route('masters.products.index') }}" class="btn btn-sm btn-outline-primary fw-bold">
                    <i class="fa-solid fa-cubes me-1"></i> Product Master
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-desktop text-primary me-1.5"></i> Product Master & Chemical Classification</h6>
                <p class="text-muted small mb-2">Centralized registry managing all chemical attributes, pricing tiers, and hazard classifications:</p>

                <!-- UI WINDOW MOCKUP: Product Master -->
                <div class="ui-window-mockup">
                    <div class="ui-window-topbar">
                        <div class="ui-window-dots">
                            <span class="ui-window-dot dot-red"></span>
                            <span class="ui-window-dot dot-yellow"></span>
                            <span class="ui-window-dot dot-green"></span>
                        </div>
                        <span class="ui-window-address">http://127.0.0.1:8000/masters/products/create — Chemical Product Master</span>
                    </div>
                    <div class="ui-window-content">
                        <div class="bg-white p-3 rounded border">
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold mb-0">Chemical Name & Strength:</label>
                                    <div class="badge bg-light text-dark p-1.5 w-100 text-start border">
                                        <span class="callout-pill">❶</span> Sodium Hypochlorite Solution 10-12%
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold mb-0">Product Type:</label>
                                    <div class="badge bg-primary-subtle text-primary p-1.5 w-100 text-start border">
                                        <span class="callout-pill">❷</span> Finished Goods
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold mb-0">HSN & Tax Rate:</label>
                                    <div class="badge bg-light text-dark p-1.5 w-100 text-start border font-monospace">
                                        <span class="callout-pill">❸</span> HSN: 282890 | GST: 18%
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold mb-0">Base Unit (UOM):</label>
                                    <div class="badge bg-light text-dark p-1.5 w-100 text-start border">
                                        Liters (L)
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold mb-0">Retail Selling Price:</label>
                                    <div class="badge bg-success-subtle text-success p-1.5 w-100 text-start border font-monospace">
                                        <span class="callout-pill">❹</span> ₹45.00 / Liter
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold mb-0">Wholesale Tier Price:</label>
                                    <div class="badge bg-info-subtle text-info p-1.5 w-100 text-start border font-monospace">
                                        ₹36.00 / Liter
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Callout Table -->
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.80rem;">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">Callout</th>
                                <th width="28%">Master Attribute</th>
                                <th>Operational Impact Across ERP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="callout-pill">❶</span></td>
                                <td><strong>Chemical Name & Grade</strong></td>
                                <td>Defines the SKU title on POS search, purchase orders, lab QC logs, and tax bills.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❷</span></td>
                                <td><strong>Product Type Flag</strong></td>
                                <td>Select between <em>Raw Material</em> (used in formulas), <em>Finished Goods</em> (compounded outputs), <em>Packaging</em> (carboys, drums), or <em>Trading Item</em>.</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❸</span></td>
                                <td><strong>HSN Code & GST Rate %</strong></td>
                                <td>Auto-populates on POS invoices and GSTR-1 tax filings (e.g. 2815, 2807, 2828).</td>
                            </tr>
                            <tr>
                                <td><span class="callout-pill">❹</span></td>
                                <td><strong>Dual Pricing (Retail vs Wholesale)</strong></td>
                                <td>Enables cashiers to toggle between walk-in customer pricing and contracted bulk factory rates (<span class="keyboard-shortcut-pill">F7</span>).</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Customer & Vendor Master Note -->
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="p-2.5 bg-light rounded border h-100">
                            <strong class="d-block text-dark small mb-1"><i class="fa-solid fa-users text-primary me-1"></i> B2B Customer Master:</strong>
                            <p class="small text-muted mb-0" style="font-size: 0.75rem;">
                                Stores Customer GSTIN, billing address, phone, and credit limit. Entering a valid 15-digit GSTIN automatically enables B2B Tax Invoicing.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2.5 bg-light rounded border h-100">
                            <strong class="d-block text-dark small mb-1"><i class="fa-solid fa-scale-balanced text-success me-1"></i> UOM Metric Conversions:</strong>
                            <p class="small text-muted mb-0" style="font-size: 0.75rem;">
                                Configure conversion factors (e.g. <code>1 Drum = 200 Liters</code>, <code>1 Ton = 1000 KG</code>) so purchasing can buy in tons while POS bills in liters.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 8: Promotions & Dynamic Discount Rules                -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="modulePromotions">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success p-2 rounded-3 text-white"><i class="fa-solid fa-tags fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 8: Promotions & Dynamic Discount Rules</h5>
                        <small class="text-muted">Automated BOGO (Buy 1 Get 1), percentage discounts, combo packaging, and promotional rules</small>
                    </div>
                </div>
                <a href="{{ route('promotions.index') }}" class="btn btn-sm btn-outline-success fw-bold">
                    <i class="fa-solid fa-tag me-1"></i> Promotions Desk
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-desktop text-primary me-1.5"></i> Promotions Engine in POS Cart</h6>
                <p class="text-muted small mb-2">Automate seasonal discounts and bulk wholesale volume incentives:</p>

                <!-- UI WINDOW MOCKUP: Promotions -->
                <div class="ui-window-mockup">
                    <div class="ui-window-topbar">
                        <div class="ui-window-dots">
                            <span class="ui-window-dot dot-red"></span>
                            <span class="ui-window-dot dot-yellow"></span>
                            <span class="ui-window-dot dot-green"></span>
                        </div>
                        <span class="ui-window-address">http://127.0.0.1:8000/promotions — Active Discount Campaigns</span>
                    </div>
                    <div class="ui-window-content">
                        <div class="bg-white p-3 rounded border">
                            <div class="table-responsive">
                                <table class="table table-xs table-bordered mb-0" style="font-size: 0.70rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Campaign Name</th>
                                            <th>Promo Type</th>
                                            <th>Applicable SKU</th>
                                            <th>Condition / Trigger</th>
                                            <th>Discount Applied</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Monsoon Cleaning Drive</strong></td>
                                            <td><span class="badge bg-primary">Buy X Get Y</span></td>
                                            <td>Disinfectant Floor Cleaner 5L</td>
                                            <td>Buy 2 Units</td>
                                            <td><span class="badge bg-success">1 Unit Free</span></td>
                                            <td><span class="badge bg-success">Active</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Bulk Textile Dyer Volume</strong></td>
                                            <td><span class="badge bg-info">Percentage</span></td>
                                            <td>All Industrial Solvents</td>
                                            <td>Cart &gt; ₹25,000</td>
                                            <td><span class="badge bg-warning text-dark">5.0% Off Cart</span></td>
                                            <td><span class="badge bg-success">Active</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-wand-magic-sparkles text-primary me-1"></i> Automatic Application at POS</h6>
                    <p class="small text-muted mb-0">
                        Cashiers do not need to memorize promo codes. As soon as the customer's cart reaches the qualifying quantity or value threshold, the POS calculation engine instantly injects the discount line and reflects the net tax savings on the printed invoice.
                    </p>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 9: Financial Reports, Expenses & DomPDF Export        -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="moduleFinance">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger p-2 rounded-3 text-white"><i class="fa-solid fa-chart-pie fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 9: Operating Expenses & Financial Reports</h5>
                        <small class="text-muted">Daily expense tracking, GSTR-1 tax filing reports, payment collections, and 1-click DomPDF export</small>
                    </div>
                </div>
                <a href="{{ route('reports.gst') }}" class="btn btn-sm btn-outline-danger fw-bold">
                    <i class="fa-solid fa-file-pdf me-1"></i> GSTR-1 Report
                </a>
            </div>

            <div class="card-body p-3 p-lg-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-desktop text-primary me-1.5"></i> Financial Telemetry & PDF Export Architecture</h6>
                <p class="text-muted small mb-2">Every report screen includes instant on-screen aggregation with a 1-click DomPDF export button:</p>

                <!-- UI WINDOW MOCKUP: Reports -->
                <div class="ui-window-mockup">
                    <div class="ui-window-topbar">
                        <div class="ui-window-dots">
                            <span class="ui-window-dot dot-red"></span>
                            <span class="ui-window-dot dot-yellow"></span>
                            <span class="ui-window-dot dot-green"></span>
                        </div>
                        <span class="ui-window-address">http://127.0.0.1:8000/reports/gst?export=pdf — GSTR-1 Tax Return Report</span>
                    </div>
                    <div class="ui-window-content">
                        <div class="bg-white p-3 rounded border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong class="d-block" style="font-size: 0.78rem;">Gudi Chemicals — GSTR-1 Tax Breakdown</strong>
                                    <small class="text-muted" style="font-size: 0.68rem;">Period: Current Financial Quarter</small>
                                </div>
                                <button class="btn btn-sm btn-danger fw-bold py-1 px-2" style="font-size: 0.70rem;">
                                    <i class="fa-solid fa-file-pdf me-1"></i> Export DomPDF
                                </button>
                            </div>

                            <table class="table table-xs table-bordered mb-0" style="font-size: 0.70rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Rate</th>
                                        <th>Taxable Value</th>
                                        <th>CGST (₹)</th>
                                        <th>SGST (₹)</th>
                                        <th>IGST (₹)</th>
                                        <th>Total Tax Liability</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>GST 18%</strong></td>
                                        <td class="font-monospace">₹1,24,500.00</td>
                                        <td class="font-monospace">₹11,205.00</td>
                                        <td class="font-monospace">₹11,205.00</td>
                                        <td class="font-monospace">₹0.00</td>
                                        <td class="fw-bold font-monospace text-danger">₹22,410.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Report Directory Grid -->
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <div class="p-2.5 bg-light rounded border h-100">
                            <strong class="d-block text-dark small mb-1"><i class="fa-solid fa-receipt text-primary me-1"></i> Daily Collections:</strong>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Audits all cash drawer receipts, UPI QR transactions, and card swipes for cashier end-of-day handoff.</small>
                            <a href="{{ route('reports.collections') }}" class="btn btn-xs btn-outline-primary mt-1 py-0 px-1" style="font-size: 0.68rem;">Open Collections</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2.5 bg-light rounded border h-100">
                            <strong class="d-block text-dark small mb-1"><i class="fa-solid fa-hand-holding-dollar text-warning me-1"></i> Accounts Receivable:</strong>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Aging report (0-30, 31-60, 60+ days) tracking unpaid customer balances and credit limits.</small>
                            <a href="{{ route('reports.receivables') }}" class="btn btn-xs btn-outline-warning text-dark mt-1 py-0 px-1" style="font-size: 0.68rem;">Open Receivables</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2.5 bg-light rounded border h-100">
                            <strong class="d-block text-dark small mb-1"><i class="fa-solid fa-boxes-packing text-success me-1"></i> Inventory Valuation:</strong>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Calculates total financial asset value of raw chemicals and finished goods at weighted average cost.</small>
                            <a href="{{ route('reports.inventory') }}" class="btn btn-xs btn-outline-success mt-1 py-0 px-1" style="font-size: 0.68rem;">Open Valuation</a>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-file-invoice text-danger me-1"></i> DomPDF Export Instructions</h6>
                    <p class="small text-muted mb-0">
                        Append <code>?export=pdf</code> to any report URL or click the red <strong>"Export PDF"</strong> button at the top-right of any report screen. The system generates a print-ready A4 document formatted with page numbering and totals summary.
                    </p>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODULE 10: Settings, User Roles & Security                   -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="moduleSettings">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary p-2 rounded-3 text-white"><i class="fa-solid fa-gear fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 10: Company Settings, User Roles & Security</h5>
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
        <!-- MODULE 11: Keyboard Shortcuts Cheat Sheet & FAQ              -->
        <!-- ============================================================ -->
        <div class="training-module-card" id="moduleFaq">
            <div class="training-module-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark p-2 rounded-3 text-white"><i class="fa-solid fa-keyboard fa-lg"></i></span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Module 11: Keyboard Shortcuts & Troubleshooting FAQ</h5>
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

                            <div class="accordion-item border-0 mb-1.5 rounded bg-light">
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

                            <div class="accordion-item border-0 rounded bg-light">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark py-2 px-3 bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Where are chemical batch lab test parameters logged?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse">
                                    <div class="accordion-body small text-muted pt-0 px-3 pb-2">
                                        In <strong>Manufacturing > Batch Orders</strong>, open any in-progress batch order. Click <em>"Record QC Parameters"</em> to save pH, Viscosity, Specific Gravity, and appearance observations.
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
