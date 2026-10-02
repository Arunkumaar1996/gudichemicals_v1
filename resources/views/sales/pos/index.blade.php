@extends('layouts.app')

@section('title', 'POS Fast Billing Terminal')

@push('styles')
<style>
    .pos-viewport {
        height: calc(100vh - 80px);
        margin: -0.5rem -0.5rem 0 -0.5rem;
        display: flex;
        gap: 1rem;
        overflow: hidden;
    }

    /* Left: Product Catalog Panel */
    .catalog-panel {
        flex: 1 1 58%;
        display: flex;
        flex-direction: column;
        height: 100%;
        min-width: 0;
    }

    .catalog-topbar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.85rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        flex-shrink: 0;
    }

    .category-pills-bar {
        display: flex;
        gap: 0.4rem;
        overflow-x: auto;
        padding-bottom: 4px;
        flex-shrink: 0;
    }

    .category-pill {
        white-space: nowrap;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .category-pill:hover, .category-pill.active {
        background: #005a9c;
        border-color: #005a9c;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 90, 156, 0.25);
    }

    .catalog-grid-scroll {
        flex-grow: 1;
        overflow-y: auto;
        padding: 0.25rem 0.25rem 1rem 0;
    }

    .pos-product-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        padding: 0.85rem;
        cursor: pointer;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        transition: all 0.18s ease;
    }

    .pos-product-card:hover {
        transform: translateY(-2px);
        border-color: #005a9c;
        box-shadow: 0 6px 16px rgba(0, 90, 156, 0.12);
    }

    .pos-product-card:active {
        transform: scale(0.98);
    }

    /* Right: Digital Billing Cart Console */
    .cart-console {
        flex: 1 1 42%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        display: flex;
        flex-direction: column;
        height: 100%;
        min-width: 380px;
    }

    .cart-header {
        padding: 0.85rem 1.1rem;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
        border-top-left-radius: 14px;
        border-top-right-radius: 14px;
        flex-shrink: 0;
    }

    .cart-items-scroll {
        flex-grow: 1;
        overflow-y: auto;
        padding: 0.5rem;
        background: #f8fafc;
    }

    .cart-item-row {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        padding: 0.65rem 0.85rem;
        margin-bottom: 0.5rem;
        transition: border-color 0.15s;
    }

    .cart-item-row:hover {
        border-color: #cbd5e1;
    }

    .qty-btn {
        width: 28px;
        height: 28px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-weight: bold;
    }

    .qty-input {
        width: 48px;
        text-align: center;
        font-weight: 700;
        padding: 0.15rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.9rem;
    }

    .cart-footer {
        padding: 0.9rem 1.1rem;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        border-bottom-left-radius: 14px;
        border-bottom-right-radius: 14px;
        flex-shrink: 0;
    }

    .grand-total-display {
        background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
        color: #ffffff;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        box-shadow: 0 4px 12px rgba(4, 120, 87, 0.25);
    }

    /* Hotkey bottom guide */
    .hotkey-badge {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #334155;
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 0.72rem;
        font-weight: 700;
        margin-right: 4px;
    }

    .tender-preset-btn {
        font-size: 0.78rem;
        padding: 0.3rem 0.6rem;
        border-radius: 6px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="pos-viewport">
    <!-- LEFT COLUMN: Product Catalog & Fast Barcode Scan -->
    <div class="catalog-panel">
        <!-- Top Search Bar -->
        <div class="catalog-topbar mb-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white border-primary">
                            <i class="fa-solid fa-barcode"></i>
                        </span>
                        <input type="text" id="barcodeSearch" class="form-control fw-semibold" 
                               placeholder="Scan Barcode / SKU / Chemical Name (Press Enter to Add)..." 
                               autofocus autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" onclick="$('#barcodeSearch').val('').focus()" title="Clear">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center small text-muted">
                            <span class="badge bg-light text-dark border me-1"><i class="fa-solid fa-warehouse me-1 text-primary"></i> WH:</span>
                            <select id="posWarehouse" class="form-select form-select-sm py-1" style="font-size: 0.8rem; width: 140px;">
                                @foreach($warehouses as $w)
                                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1.5 fw-semibold" id="productCountBadge">
                            {{ count($products) }} Items
                        </span>
                    </div>
                </div>
            </div>

            <!-- Category Filter Pills -->
            <div class="category-pills-bar mt-2 pt-2 border-top">
                <button type="button" class="category-pill active" onclick="filterCategory('', this)">
                    <i class="fa-solid fa-border-all me-1"></i> All Products
                </button>
                @foreach($categories as $cat)
                    <button type="button" class="category-pill" onclick="filterCategory('{{ $cat->id }}', this)">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Scrollable Product Tiles Grid -->
        <div class="catalog-grid-scroll">
            <div class="row g-2" id="catalogGrid">
                @foreach($products as $p)
                    @php
                        $inStock = $p->total_stock > 0;
                    @endphp
                    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 catalog-item"
                         data-id="{{ $p->id }}"
                         data-name="{{ strtolower($p->name) }}"
                         data-sku="{{ strtolower($p->sku) }}"
                         data-barcode="{{ $p->barcode }}"
                         data-category="{{ $p->category_id }}"
                         data-retail-price="{{ $p->retail_price }}"
                         data-wholesale-price="{{ $p->wholesale_price }}"
                         data-hsn="{{ $p->hsn_code }}"
                         data-gst="{{ $p->gst_rate }}"
                         data-unit="{{ $p->unit?->code }}"
                         data-stock="{{ $p->total_stock }}"
                         onclick="addItemToCart({{ $p->id }})">
                        <div class="pos-product-card">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="badge {{ $inStock ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}" style="font-size: 0.68rem;">
                                    <i class="fa-solid {{ $inStock ? 'fa-check' : 'fa-triangle-exclamation' }} me-0.5"></i>
                                    {{ number_format($p->total_stock, 1) }} {{ $p->unit?->code }}
                                </span>
                                <small class="text-muted fw-bold" style="font-size: 0.68rem;">{{ $p->sku }}</small>
                            </div>
                            <div class="fw-bold text-dark text-truncate mb-1" style="font-size: 0.88rem;" title="{{ $p->name }}">
                                {{ $p->name }}
                            </div>
                            <div class="text-muted small mb-2" style="font-size: 0.72rem;">
                                <i class="fa-solid fa-tag text-secondary me-1"></i>GST: {{ number_format($p->gst_rate, 0) }}% | HSN: {{ $p->hsn_code }}
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.65rem;">Retail MRP</small>
                                    <span class="fw-bold text-success" style="font-size: 0.95rem;">₹{{ number_format($p->retail_price, 2) }}</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block" style="font-size: 0.65rem;">Wholesale</small>
                                    <span class="fw-semibold text-secondary" style="font-size: 0.82rem;">₹{{ number_format($p->wholesale_price, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div id="catalogEmpty" class="text-center py-5 text-muted d-none">
                <i class="fa-solid fa-magnifying-glass fa-3x mb-3 text-secondary opacity-50"></i>
                <h6>No matching chemical products found</h6>
                <p class="small">Try scanning another barcode or clearing category filters.</p>
            </div>
        </div>

        <!-- Hotkeys Footer Bar -->
        <div class="d-flex align-items-center justify-content-between px-2 pt-2 border-top text-muted small" style="font-size: 0.75rem;">
            <div>
                <span class="hotkey-badge">F2</span> Scan Barcode
                <span class="hotkey-badge ms-2">F4</span> Select Customer
                <span class="hotkey-badge ms-2">F7</span> Switch Price Tier
                <span class="hotkey-badge ms-2">F9</span> Checkout
                <span class="hotkey-badge ms-2">F10</span> Clear
            </div>
            <div>
                <i class="fa-solid fa-user-check text-success me-1"></i> Cashier: <strong>{{ auth()->user()->name }}</strong>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: Interactive Cart & Checkout Console -->
    <div class="cart-console">
        <!-- Customer & Price Tier Header -->
        <div class="cart-header">
            <div class="row g-2 align-items-center mb-2">
                <div class="col-8">
                    <label class="form-label small fw-semibold text-muted mb-1 d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-user me-1 text-primary"></i> Customer Account</span>
                        <a href="javascript:void(0)" class="text-decoration-none small text-primary" data-bs-toggle="modal" data-bs-target="#quickCustomerModal">
                            <i class="fa-solid fa-plus-circle me-0.5"></i> New Customer
                        </a>
                    </label>
                    <select id="posCustomer" class="form-select form-select-sm" onchange="onCustomerChange()">
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" 
                                    data-type="{{ $c->customer_type }}" 
                                    data-gstin="{{ $c->gstin }}"
                                    data-state="{{ $c->state }}"
                                    {{ $c->id == $defaultCustomer->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ ucfirst($c->customer_type) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Price Tier</label>
                    <div class="btn-group btn-group-sm w-100" role="group">
                        <input type="radio" class="btn-check" name="price_tier" id="tierRetail" value="retail" checked onchange="calculateCart()">
                        <label class="btn btn-outline-primary fw-bold" for="tierRetail">Retail</label>
                        
                        <input type="radio" class="btn-check" name="price_tier" id="tierWholesale" value="wholesale" onchange="calculateCart()">
                        <label class="btn btn-outline-primary fw-bold" for="tierWholesale">Wholesale</label>
                    </div>
                </div>
            </div>

            <!-- Customer Details Alert (Shows GSTIN & Outstanding Due) -->
            <div id="customerInfoBar" class="d-flex align-items-center justify-content-between bg-light p-2 rounded small border">
                <div id="custGstinTag" class="text-muted text-truncate">
                    <i class="fa-solid fa-address-card me-1 text-secondary"></i> B2C Retail Walk-in
                </div>
                <div id="custBalanceTag" class="fw-semibold text-success">
                    <i class="fa-solid fa-circle-check me-1"></i> Balance Clear
                </div>
            </div>
        </div>

        <!-- Scrollable Cart Items -->
        <div class="cart-items-scroll">
            <div id="cartItemsList">
                <!-- Dynamically populated rows -->
            </div>

            <div id="cartEmptyState" class="text-center py-5 text-muted">
                <div class="p-3 mb-2 rounded-circle bg-white d-inline-block shadow-sm">
                    <i class="fa-solid fa-cart-shopping fa-3x text-secondary opacity-40"></i>
                </div>
                <h6 class="fw-bold mb-1">Billing Ticket is Empty</h6>
                <p class="small text-muted mb-0">Click products from the catalog or scan barcodes to begin.</p>
            </div>
        </div>

        <!-- Cart Footer & Checkout Panel -->
        <div class="cart-footer">
            <!-- Promotion & Discount Notification -->
            <div id="promoAlertBanner" class="alert alert-warning py-1.5 px-2.5 small mb-2 d-none d-flex align-items-center">
                <i class="fa-solid fa-gift fa-lg text-warning me-2"></i>
                <div id="promoAlertText" class="fw-semibold"></div>
            </div>

            <!-- Taxable & GST Breakdown -->
            <div class="mb-2" style="font-size: 0.82rem;">
                <div class="d-flex justify-content-between mb-1 text-muted">
                    <span>Taxable Base Value:</span>
                    <span class="fw-semibold text-dark" id="lblTaxable">₹0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-muted">
                    <span id="lblGstType">CGST + SGST (Intrastate):</span>
                    <span class="fw-semibold text-primary" id="lblGstTotal">₹0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-muted d-none" id="rowDiscount">
                    <span class="text-success fw-semibold">Discounts Applied:</span>
                    <span class="fw-bold text-success" id="lblDiscount">-₹0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-muted">
                    <span>Round Off Adjustment:</span>
                    <span id="lblRounding">₹0.00</span>
                </div>
            </div>

            <!-- Grand Total Display Banner -->
            <div class="grand-total-display d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="text-white-50 small text-uppercase fw-bold tracking-wider d-block">Grand Total Payable</span>
                    <small class="text-white-50" id="lblItemCount">0 items</small>
                </div>
                <div class="text-end">
                    <h2 class="fw-bold mb-0 text-white" id="lblGrandTotal">₹0.00</h2>
                </div>
            </div>

            <!-- Checkout Action Buttons -->
            <div class="row g-2">
                <div class="col-4">
                    <button type="button" class="btn btn-outline-danger w-100 py-2 fw-semibold" onclick="clearCart()">
                        <i class="fa-solid fa-trash me-1"></i> Clear (F10)
                    </button>
                </div>
                <div class="col-8">
                    <button type="button" class="btn btn-success w-100 py-2 fw-bold shadow-sm fs-6" onclick="openPaymentDrawer()" id="btnCheckout" disabled>
                        <i class="fa-solid fa-credit-card me-1.5"></i> Collect & Pay (F9)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Complete Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-cash-register text-success me-2"></i> Receive Payment & Finalize</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center p-3 mb-3 bg-light rounded border">
                    <span class="text-muted small fw-semibold text-uppercase">Net Invoice Due</span>
                    <h1 class="fw-bold text-success mb-0" id="modalPayable">₹0.00</h1>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Payment Tender Method</label>
                    <div class="row g-2">
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="payment_method_radio" id="payCash" value="cash" checked onchange="onPaymentModeChange('cash')">
                            <label class="btn btn-outline-success w-100 py-2 fw-bold text-center" for="payCash">
                                <i class="fa-solid fa-money-bill-1 d-block mb-1"></i> Cash
                            </label>
                        </div>
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="payment_method_radio" id="payUpi" value="upi" onchange="onPaymentModeChange('upi')">
                            <label class="btn btn-outline-info w-100 py-2 fw-bold text-center" for="payUpi">
                                <i class="fa-solid fa-qrcode d-block mb-1"></i> UPI / QR
                            </label>
                        </div>
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="payment_method_radio" id="payCard" value="card" onchange="onPaymentModeChange('card')">
                            <label class="btn btn-outline-primary w-100 py-2 fw-bold text-center" for="payCard">
                                <i class="fa-solid fa-credit-card d-block mb-1"></i> Card
                            </label>
                        </div>
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="payment_method_radio" id="payCredit" value="credit" onchange="onPaymentModeChange('credit')">
                            <label class="btn btn-outline-dark w-100 py-2 fw-bold text-center" for="payCredit">
                                <i class="fa-solid fa-book d-block mb-1"></i> Credit
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Cash Tender & Change Calculations -->
                <div id="cashDetailsBox" class="mb-3 p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small fw-semibold mb-0">Quick Tender Presets:</label>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary tender-preset-btn" onclick="setCashTender('exact')">Exact</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary tender-preset-btn" onclick="setCashTender(100)">₹100</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary tender-preset-btn" onclick="setCashTender(200)">₹200</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary tender-preset-btn" onclick="setCashTender(500)">₹500</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary tender-preset-btn" onclick="setCashTender(2000)">₹2000</button>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Cash Given (₹)</label>
                            <input type="number" step="1" id="cashTendered" class="form-control fw-bold fs-5 text-dark" oninput="calculateChange()">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Return Change (₹)</label>
                            <input type="text" id="cashChange" class="form-control fw-bold fs-5 bg-white text-danger" value="₹0.00" readonly>
                        </div>
                    </div>
                </div>

                <!-- Reference / UTR for Digital Modes -->
                <div id="refDetailsBox" class="mb-3 d-none">
                    <label class="form-label small fw-semibold">Transaction / UTR Reference #</label>
                    <input type="text" id="payReference" class="form-control" placeholder="e.g. UPI Ref # / Auth Code">
                </div>

                <!-- Bill Notes -->
                <div class="mb-2">
                    <label class="form-label small fw-semibold text-muted">Invoice Notes (Optional)</label>
                    <input type="text" id="invoiceNotes" class="form-control" placeholder="Delivery notes, vehicle #, purchase reference...">
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success fw-bold px-4 py-2" onclick="submitInvoice()" id="btnSubmitInvoice">
                    <i class="fa-solid fa-print me-1.5"></i> Finalize & Print Bill
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Finalized Success Modal -->
<div class="modal fade" id="invoiceSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-circle-check me-2"></i> Bill Finalized Successfully!</h5>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="p-3 mb-3 rounded-circle bg-success-subtle text-success d-inline-block">
                    <i class="fa-solid fa-receipt fa-4x"></i>
                </div>
                <h3 class="fw-bold mb-1 text-dark" id="successInvoiceNum">GC/2026-27/0001</h3>
                <p class="text-muted small mb-4">Stock successfully deducted from warehouse ledger & GST recorded.</p>

                <div class="d-grid gap-2">
                    <a href="javascript:void(0)" id="btnThermalPrint" target="_blank" class="btn btn-primary py-2.5 fw-bold">
                        <i class="fa-solid fa-receipt me-1.5"></i> Print 80mm Thermal Receipt
                    </a>
                    <a href="javascript:void(0)" id="btnA4Print" target="_blank" class="btn btn-outline-secondary py-2 fw-semibold">
                        <i class="fa-solid fa-file-invoice me-1.5"></i> Print A4 GST Tax Invoice
                    </a>
                    <a href="javascript:void(0)" id="btnPdfDownload" class="btn btn-outline-dark py-2 fw-semibold">
                        <i class="fa-solid fa-file-pdf me-1.5"></i> Download PDF Invoice
                    </a>
                </div>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-success px-4 py-2 fw-bold" onclick="resetForNextBill()">
                    <i class="fa-solid fa-plus-circle me-1"></i> Start Next Customer Bill (Enter)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Customer Modal -->
<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i> Register Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickCustomerForm" onsubmit="saveQuickCustomer(event)">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Customer / Business Name <span class="text-danger">*</span></label>
                        <input type="text" id="quickCustName" class="form-control" placeholder="e.g. Balaji Agro Chemicals" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Mobile Number</label>
                        <input type="text" id="quickCustPhone" class="form-control" placeholder="10-digit phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Customer Type</label>
                        <select id="quickCustType" class="form-select">
                            <option value="retail">Retail Consumer</option>
                            <option value="wholesale">Wholesale B2B Buyer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">GSTIN (Optional for Retail)</label>
                        <input type="text" id="quickCustGstin" class="form-control" placeholder="15-character GSTIN">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gudi-primary"><i class="fa-solid fa-check me-1"></i> Save & Select</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cart = {}; // product_id -> { id, name, sku, unit, price, quantity, gst_rate, hsn, stock }
    let lastCalculated = null;

    // Filter by Category
    function filterCategory(catId, btn) {
        $('.category-pill').removeClass('active');
        $(btn).addClass('active');

        let search = $('#barcodeSearch').val().toLowerCase().trim();
        applyFilters(catId, search);
    }

    // Apply Filters (Category + Search text)
    function applyFilters(catId = null, query = null) {
        if (catId === null) {
            catId = $('.category-pill.active').text().includes('All') ? '' : $('.category-pill.active').attr('onclick').match(/'([^']+)'/)?.[1] || '';
        }
        if (query === null) {
            query = $('#barcodeSearch').val().toLowerCase().trim();
        }

        let visibleCount = 0;
        $('.catalog-item').each(function() {
            let item = $(this);
            let itemCat = item.data('category') + '';
            let itemName = (item.data('name') || '') + '';
            let itemSku = (item.data('sku') || '') + '';
            let itemBarcode = (item.data('barcode') || '') + '';

            let matchesCat = (!catId || itemCat === catId);
            let matchesSearch = (!query || itemName.includes(query) || itemSku.includes(query) || itemBarcode.includes(query));

            if (matchesCat && matchesSearch) {
                item.removeClass('d-none');
                visibleCount++;
            } else {
                item.addClass('d-none');
            }
        });

        $('#productCountBadge').text(visibleCount + ' Items');
        if (visibleCount === 0) {
            $('#catalogEmpty').removeClass('d-none');
        } else {
            $('#catalogEmpty').addClass('d-none');
        }
    }

    // Barcode Search listener
    $('#barcodeSearch').on('input', function() {
        applyFilters();
    });

    // Enter in Barcode search adds exact match or first result
    $('#barcodeSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            let query = $(this).val().toLowerCase().trim();
            if (!query) return;

            let exactMatch = $('.catalog-item:not(.d-none)').filter(function() {
                return ($(this).data('barcode') + '').toLowerCase() === query || ($(this).data('sku') + '').toLowerCase() === query;
            });

            if (exactMatch.length > 0) {
                addItemToCart(exactMatch.first().data('id'));
                $(this).val('').focus();
            } else {
                let first = $('.catalog-item:not(.d-none)').first();
                if (first.length > 0) {
                    addItemToCart(first.data('id'));
                    $(this).val('').focus();
                }
            }
        }
    });

    // Add Item to Cart
    function addItemToCart(productId) {
        let el = $(`.catalog-item[data-id="${productId}"]`);
        if (!el.length) return;

        let tier = $('input[name="price_tier"]:checked').val();
        let price = tier === 'wholesale' ? parseFloat(el.data('wholesale-price')) : parseFloat(el.data('retail-price'));

        if (cart[productId]) {
            cart[productId].quantity += 1;
        } else {
            cart[productId] = {
                id: productId,
                name: el.find('.text-truncate').text().trim(),
                sku: el.data('sku').toUpperCase(),
                unit: el.data('unit'),
                retail_price: parseFloat(el.data('retail-price')),
                wholesale_price: parseFloat(el.data('wholesale-price')),
                price: price,
                quantity: 1,
                gst_rate: parseFloat(el.data('gst')),
                hsn: el.data('hsn'),
                stock: parseFloat(el.data('stock')),
            };
        }

        renderCart();
        calculateCart();
    }

    // Change Item Quantity
    function updateQty(productId, delta) {
        if (!cart[productId]) return;
        cart[productId].quantity += delta;
        if (cart[productId].quantity <= 0) {
            delete cart[productId];
        }
        renderCart();
        calculateCart();
    }

    function setQty(productId, val) {
        val = parseFloat(val);
        if (isNaN(val) || val <= 0) {
            delete cart[productId];
        } else {
            cart[productId].quantity = val;
        }
        renderCart();
        calculateCart();
    }

    function removeCartItem(productId) {
        delete cart[productId];
        renderCart();
        calculateCart();
    }

    function clearCart() {
        if (Object.keys(cart).length === 0) return;
        if (confirm('Are you sure you want to clear the current billing ticket?')) {
            cart = {};
            renderCart();
            calculateCart();
            $('#barcodeSearch').focus();
        }
    }

    // Render Cart HTML
    function renderCart() {
        let container = $('#cartItemsList');
        container.empty();

        let keys = Object.keys(cart);
        if (keys.length === 0) {
            $('#cartEmptyState').removeClass('d-none');
            $('#btnCheckout').prop('disabled', true);
            $('#lblItemCount').text('0 items');
            return;
        }

        $('#cartEmptyState').addClass('d-none');
        $('#btnCheckout').prop('disabled', false);

        let totalItems = 0;
        let tier = $('input[name="price_tier"]:checked').val();

        keys.forEach(k => {
            let item = cart[k];
            totalItems += item.quantity;
            item.price = tier === 'wholesale' ? item.wholesale_price : item.retail_price;
            let lineTotal = item.price * item.quantity;

            let html = `
                <div class="cart-item-row" data-id="${item.id}">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <span class="fw-bold text-dark" style="font-size: 0.88rem;">${item.name}</span>
                            <div class="text-muted small" style="font-size: 0.72rem;">
                                ${item.sku} | HSN: ${item.hsn} | GST: ${item.gst_rate}%
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm text-danger p-0 ms-2" onclick="removeCartItem(${item.id})" title="Remove item">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                            <input type="number" step="any" class="qty-input mx-1" value="${item.quantity}" onchange="setQty(${item.id}, this.value)">
                            <button type="button" class="btn btn-sm btn-outline-secondary qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                            <span class="text-muted small ms-1">${item.unit}</span>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small me-2">@ ₹${item.price.toFixed(2)}</span>
                            <span class="fw-bold text-success fs-6">₹${lineTotal.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `;
            container.append(html);
        });

        $('#lblItemCount').text(keys.length + ' items (' + totalItems.toFixed(1) + ' qty)');
    }

    // Server-side AJAX Cart Calculation
    function calculateCart() {
        let keys = Object.keys(cart);
        if (keys.length === 0) {
            $('#lblTaxable').text('₹0.00');
            $('#lblGstTotal').text('₹0.00');
            $('#lblGrandTotal').text('₹0.00');
            $('#lblRounding').text('₹0.00');
            $('#rowDiscount').addClass('d-none');
            $('#promoAlertBanner').addClass('d-none');
            return;
        }

        let customerId = $('#posCustomer').val();
        let priceTier = $('input[name="price_tier"]:checked').val();
        let cartItems = keys.map(k => ({
            product_id: cart[k].id,
            quantity: cart[k].quantity,
            discount_amount: 0
        }));

        $.ajax({
            url: "{{ route('pos.calculate') }}",
            type: "POST",
            data: {
                customer_id: customerId,
                price_tier: priceTier,
                cart_items: cartItems
            },
            success: function(res) {
                lastCalculated = res;
                $('#lblTaxable').text('₹' + parseFloat(res.taxable_amount).toFixed(2));
                
                let gstLabel = res.is_interstate ? 'IGST (Interstate):' : 'CGST + SGST:';
                let gstVal = res.is_interstate ? res.igst_amount : (parseFloat(res.cgst_amount) + parseFloat(res.sgst_amount));
                $('#lblGstType').text(gstLabel);
                $('#lblGstTotal').text('₹' + parseFloat(gstVal).toFixed(2));

                if (res.discount_total > 0) {
                    $('#lblDiscount').text('-₹' + parseFloat(res.discount_total).toFixed(2));
                    $('#rowDiscount').removeClass('d-none');
                } else {
                    $('#rowDiscount').addClass('d-none');
                }

                $('#lblRounding').text((res.rounding_adjustment >= 0 ? '+' : '') + '₹' + parseFloat(res.rounding_adjustment).toFixed(2));
                $('#lblGrandTotal').text('₹' + parseFloat(res.grand_total).toFixed(2));

                // Promotions
                if (res.applied_promotions && res.applied_promotions.length > 0) {
                    let promoNames = res.applied_promotions.map(p => p.name).join(', ');
                    $('#promoAlertText').text('Offer applied: ' + promoNames);
                    $('#promoAlertBanner').removeClass('d-none');
                } else {
                    $('#promoAlertBanner').addClass('d-none');
                }
            }
        });
    }

    // Customer Selection Change
    function onCustomerChange() {
        let opt = $('#posCustomer option:selected');
        let gstin = opt.data('gstin');
        let type = opt.data('type');

        if (gstin) {
            $('#custGstinTag').html('<i class="fa-solid fa-shield-halved text-success me-1"></i> B2B: <strong>' + gstin + '</strong>');
        } else {
            $('#custGstinTag').html('<i class="fa-solid fa-address-card text-secondary me-1"></i> ' + (type === 'wholesale' ? 'B2B (Unregistered)' : 'B2C Retail Walk-in'));
        }

        // Auto toggle Wholesale tier if customer type is wholesale
        if (type === 'wholesale') {
            $('#tierWholesale').prop('checked', true);
        }

        renderCart();
        calculateCart();
    }

    // Open Payment Drawer
    function openPaymentDrawer() {
        if (!lastCalculated || Object.keys(cart).length === 0) return;

        let grandTotal = parseFloat(lastCalculated.grand_total);
        $('#modalPayable').text('₹' + grandTotal.toFixed(2));
        $('#cashTendered').val(grandTotal);
        calculateChange();

        let modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }

    // Payment Mode Selection
    function onPaymentModeChange(mode) {
        if (mode === 'cash') {
            $('#cashDetailsBox').removeClass('d-none');
            $('#refDetailsBox').addClass('d-none');
        } else if (mode === 'upi' || mode === 'card') {
            $('#cashDetailsBox').addClass('d-none');
            $('#refDetailsBox').removeClass('d-none');
        } else {
            // credit
            $('#cashDetailsBox').addClass('d-none');
            $('#refDetailsBox').addClass('d-none');
        }
    }

    function setCashTender(amount) {
        if (amount === 'exact') {
            $('#cashTendered').val(lastCalculated.grand_total);
        } else {
            $('#cashTendered').val(amount);
        }
        calculateChange();
    }

    function calculateChange() {
        let due = lastCalculated ? parseFloat(lastCalculated.grand_total) : 0;
        let given = parseFloat($('#cashTendered').val()) || 0;
        let change = Math.max(0, given - due);
        $('#cashChange').val('₹' + change.toFixed(2));
        if (given < due) {
            $('#cashChange').addClass('text-danger').removeClass('text-success');
        } else {
            $('#cashChange').removeClass('text-danger').addClass('text-success');
        }
    }

    // Submit & Post Invoice
    function submitInvoice() {
        if (!lastCalculated || Object.keys(cart).length === 0) return;

        let btn = $('#btnSubmitInvoice');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Processing...');

        let method = $('input[name="payment_method_radio"]:checked').val() || 'cash';
        let cashGiven = parseFloat($('#cashTendered').val()) || 0;
        let grandTotal = parseFloat(lastCalculated.grand_total);
        let paidAmount = (method === 'credit') ? 0 : grandTotal;

        let payload = {
            customer_id: $('#posCustomer').val(),
            warehouse_id: $('#posWarehouse').val(),
            price_tier: $('input[name="price_tier"]:checked').val(),
            notes: $('#invoiceNotes').val(),
            payment_method: method,
            paid_amount: paidAmount,
            reference_number: $('#payReference').val(),
            cart_items: Object.keys(cart).map(k => ({
                product_id: cart[k].id,
                quantity: cart[k].quantity,
                discount_amount: 0
            }))
        };

        $.ajax({
            url: "{{ route('pos.store') }}",
            type: "POST",
            data: payload,
            success: function(res) {
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                btn.prop('disabled', false).html('<i class="fa-solid fa-print me-1.5"></i> Finalize & Print Bill');

                // Open Success Modal
                $('#successInvoiceNum').text(res.invoice_number);
                $('#btnThermalPrint').attr('href', res.print_url + '?format=thermal');
                $('#btnA4Print').attr('href', res.print_url);
                $('#btnPdfDownload').attr('href', res.pdf_url);

                let successModal = new bootstrap.Modal(document.getElementById('invoiceSuccessModal'));
                successModal.show();
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-print me-1.5"></i> Finalize & Print Bill');
                let err = xhr.responseJSON?.message || 'Error creating invoice. Please try again.';
                alert(err);
            }
        });
    }

    // Reset After Bill is Finalized
    function resetForNextBill() {
        bootstrap.Modal.getInstance(document.getElementById('invoiceSuccessModal')).hide();
        cart = {};
        lastCalculated = null;
        renderCart();
        calculateCart();
        $('#invoiceNotes').val('');
        $('#payReference').val('');
        $('#barcodeSearch').val('').focus();
    }

    // Quick Add Customer via AJAX
    function saveQuickCustomer(e) {
        e.preventDefault();
        let name = $('#quickCustName').val().trim();
        let phone = $('#quickCustPhone').val().trim();
        let type = $('#quickCustType').val();
        let gstin = $('#quickCustGstin').val().trim();

        $.ajax({
            url: "{{ route('pos.customer.quick') }}",
            type: "POST",
            data: {
                name: name,
                phone: phone,
                customer_type: type,
                gstin: gstin
            },
            success: function(res) {
                let c = res.customer;
                $('#posCustomer').append(new Option(c.name + ' (' + c.customer_type + ')', c.id, true, true));
                $('#posCustomer').val(c.id);
                bootstrap.Modal.getInstance(document.getElementById('quickCustomerModal')).hide();
                $('#quickCustomerForm')[0].reset();
                onCustomerChange();
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Error registering customer.');
            }
        });
    }

    // Keyboard Shortcuts
    $(document).on('keydown', function(e) {
        // F2 -> Focus barcode
        if (e.key === 'F2') {
            e.preventDefault();
            $('#barcodeSearch').focus().select();
        }
        // F4 -> Focus customer select
        else if (e.key === 'F4') {
            e.preventDefault();
            $('#posCustomer').focus();
        }
        // F7 -> Toggle Retail/Wholesale
        else if (e.key === 'F7') {
            e.preventDefault();
            if ($('#tierRetail').is(':checked')) {
                $('#tierWholesale').prop('checked', true);
            } else {
                $('#tierRetail').prop('checked', true);
            }
            calculateCart();
        }
        // F9 -> Checkout
        else if (e.key === 'F9') {
            e.preventDefault();
            if (!$('#btnCheckout').is(':disabled')) {
                openPaymentDrawer();
            }
        }
        // F10 -> Clear Cart
        else if (e.key === 'F10') {
            e.preventDefault();
            clearCart();
        }
    });

    // Initial trigger
    $(document).ready(function() {
        onCustomerChange();
    });
</script>
@endpush
