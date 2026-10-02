<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Fast Billing Terminal — Gudi Chemicals</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --pos-primary: #005a9c;
            --pos-primary-dark: #003e6b;
            --pos-secondary: #0d9488;
            --pos-bg: #0f172a;
            --pos-surface: #ffffff;
            --pos-border: #e2e8f0;
            --pos-text: #1e293b;
        }

        * {
            box-sizing: border-box;
            user-select: none;
        }

        input, select, textarea {
            user-select: text !important;
        }

        html, body {
            height: 100vh;
            width: 100vw;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: var(--pos-text);
        }

        /* Full Screen Container */
        #pos-app {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        /* Top POS Station Bar */
        .pos-station-header {
            height: 56px;
            background: linear-gradient(135deg, #07152d 0%, #003666 100%);
            color: #ffffff;
            padding: 0 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            z-index: 100;
        }

        /* Main Workspace: 100% Height - Header */
        .pos-workspace {
            flex-grow: 1;
            height: calc(100vh - 56px);
            display: flex;
            padding: 0.75rem;
            gap: 0.75rem;
            overflow: hidden;
        }

        /* Left Side: Product Discovery & 4,000+ Items Search (62% width) */
        .pos-catalog-section {
            flex: 1 1 62%;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-width: 0;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid var(--pos-border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .search-control-bar {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            background: #ffffff;
            flex-shrink: 0;
        }

        .search-input-box {
            font-size: 1.05rem;
            font-weight: 600;
            padding: 0.65rem 1rem;
            border-radius: 10px;
            border: 2px solid #cbd5e1;
            transition: all 0.2s;
        }

        .search-input-box:focus {
            border-color: #005a9c;
            box-shadow: 0 0 0 4px rgba(0, 90, 156, 0.15);
            outline: none;
        }

        .category-scroll-bar {
            display: flex;
            gap: 0.4rem;
            overflow-x: auto;
            padding: 0.5rem 1rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .cat-pill {
            white-space: nowrap;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.35rem 0.9rem;
            border-radius: 9999px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .cat-pill:hover, .cat-pill.active {
            background: #005a9c;
            border-color: #005a9c;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 90, 156, 0.25);
        }

        .catalog-scroll-area {
            flex-grow: 1;
            overflow-y: auto;
            padding: 0.85rem;
        }

        /* Product Tile Card */
        .product-tile {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.85rem;
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.18s ease;
            position: relative;
        }

        .product-tile:hover {
            border-color: #005a9c;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 90, 156, 0.12);
        }

        .product-tile:active {
            transform: scale(0.97);
        }

        .product-tile .stock-pill {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
        }

        .product-tile .product-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
            margin-top: 0.35rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-tile .price-retail {
            font-size: 1.15rem;
            font-weight: 800;
            color: #059669;
        }

        .product-tile .price-wholesale {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
        }

        /* Right Side: Billing Ticket Console (38% width) */
        .pos-billing-section {
            flex: 1 1 38%;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-width: 420px;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid var(--pos-border);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .billing-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            background: #ffffff;
            flex-shrink: 0;
        }

        .billing-cart-area {
            flex-grow: 1;
            overflow-y: auto;
            padding: 0.65rem;
            background: #f8fafc;
        }

        /* Cart Item Card Row */
        .cart-row {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.65rem 0.85rem;
            margin-bottom: 0.5rem;
            transition: all 0.15s;
        }

        .cart-row:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }

        .cart-row .item-title {
            font-weight: 700;
            font-size: 0.92rem;
            color: #0f172a;
        }

        .cart-row .item-sku {
            font-size: 0.75rem;
            color: #64748b;
        }

        .qty-control-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #1e293b;
            font-weight: 800;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
        }

        .qty-control-btn:hover {
            background: #005a9c;
            border-color: #005a9c;
            color: #ffffff;
        }

        .qty-input-box {
            width: 55px;
            height: 32px;
            text-align: center;
            font-weight: 800;
            font-size: 0.95rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            margin: 0 3px;
        }

        .cart-row-del-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
        }

        .cart-row-del-btn:hover {
            background: #dc2626;
            color: #ffffff;
        }

        /* Billing Summary & Payment Footer */
        .billing-footer {
            padding: 0.85rem 1.1rem;
            background: #ffffff;
            border-top: 1.5px solid #e2e8f0;
            flex-shrink: 0;
        }

        .grand-total-box {
            background: linear-gradient(135deg, #064e3b 0%, #059669 100%);
            border-radius: 12px;
            padding: 0.85rem 1.25rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3);
        }

        .hotkey-badge {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #334155;
            border-radius: 5px;
            padding: 2px 6px;
            font-size: 0.72rem;
            font-weight: 700;
            margin-right: 4px;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body>

<div id="pos-app">
    <!-- Top Station Header Bar -->
    <header class="pos-station-header">
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light me-3 fw-semibold py-1 px-2.5" title="Return to Main ERP Dashboard">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-white p-1 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-flask-vial text-primary fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold tracking-wide" style="font-size: 0.95rem;">GUDI CHEMICALS</h6>
                    <small style="font-size: 0.68rem; opacity: 0.85;">High-Speed POS & GST Billing Station</small>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="d-none d-md-flex align-items-center text-white-50 small">
                <i class="fa-solid fa-warehouse me-1 text-warning"></i>
                <span class="me-1">Store:</span>
                <select id="posWarehouse" class="form-select form-select-sm py-0.5 px-2 bg-dark text-white border-secondary" style="font-size: 0.82rem; width: 150px;" onchange="reloadWarehouseStock()">
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-none d-lg-block text-white small" style="font-family: monospace; font-size: 0.85rem;" id="digitalClock">
                --:--:--
            </div>

            <div class="d-flex align-items-center">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 fw-bold me-2">
                    <i class="fa-solid fa-user me-1"></i> {{ auth()->user()->name }}
                </span>
                <button type="button" class="btn btn-sm btn-outline-light py-1 px-2" onclick="toggleFullScreen()" title="Toggle Fullscreen">
                    <i class="fa-solid fa-expand"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Main POS Workspace: Two Full Height Panels -->
    <div class="pos-workspace">
        <!-- LEFT PANEL: High Speed Product Finding Engine -->
        <div class="pos-catalog-section">
            <!-- Search & Barcode Scan Bar -->
            <div class="search-control-bar">
                <div class="row g-2 align-items-center">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white border-primary fs-5 px-3">
                                <i class="fa-solid fa-barcode"></i>
                            </span>
                            <input type="text" id="barcodeSearch" class="form-control search-input-box" 
                                   placeholder="Scan Barcode / SKU / Chemical Name (Press Enter to Add instantly)... [F2]" 
                                   autofocus autocomplete="off">
                            <button class="btn btn-outline-secondary px-3" type="button" onclick="clearSearch()" title="Clear search">
                                <i class="fa-solid fa-xmark fs-5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Instant Category Filter Bar -->
                <div class="category-scroll-bar mt-2">
                    <button type="button" class="cat-pill active" onclick="selectCategory('', this)">
                        <i class="fa-solid fa-border-all me-1"></i> All Chemicals
                    </button>
                    @foreach($categories as $cat)
                        <button type="button" class="cat-pill" onclick="selectCategory('{{ $cat->id }}', this)">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Cards Catalog Area (Debounced AJAX / Preloaded) -->
            <div class="catalog-scroll-area">
                <div class="row g-2" id="productsGrid">
                    @foreach($products as $p)
                        @php
                            $stock = $p->stockBalances->sum('quantity');
                            $inStock = $stock > 0;
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 product-card-col"
                             onclick="addProductToCart({{ json_encode([
                                 'id' => $p->id,
                                 'name' => $p->name,
                                 'sku' => $p->sku,
                                 'barcode' => $p->barcode,
                                 'unit' => $p->unit?->code ?: 'NOS',
                                 'retail_price' => (float)$p->retail_price,
                                 'wholesale_price' => (float)$p->wholesale_price,
                                 'gst_rate' => (float)$p->gst_rate,
                                 'hsn_code' => $p->hsn_code,
                                 'stock' => (float)$stock
                             ]) }})">
                            <div class="product-tile">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start">
                                        <span class="stock-pill {{ $inStock ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                                            <i class="fa-solid {{ $inStock ? 'fa-check' : 'fa-triangle-exclamation' }} me-0.5"></i>
                                            {{ number_format($stock, 1) }} {{ $p->unit?->code }}
                                        </span>
                                        <small class="text-muted fw-bold">{{ $p->sku }}</small>
                                    </div>
                                    <div class="product-title" title="{{ $p->name }}">{{ $p->name }}</div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                        GST: {{ number_format($p->gst_rate, 0) }}% | HSN: {{ $p->hsn_code }}
                                    </small>
                                </div>
                                <div class="d-flex align-items-end justify-content-between pt-2 border-top mt-2">
                                    <div>
                                        <small class="text-muted d-block" style="font-size: 0.65rem;">Retail MRP</small>
                                        <div class="price-retail">₹{{ number_format($p->retail_price, 2) }}</div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block" style="font-size: 0.65rem;">Wholesale</small>
                                        <div class="price-wholesale">₹{{ number_format($p->wholesale_price, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Empty State -->
                <div id="noProductsFound" class="text-center py-5 text-muted d-none">
                    <i class="fa-solid fa-magnifying-glass fa-3x mb-3 text-secondary opacity-40"></i>
                    <h5 class="fw-bold">No matching chemical items found</h5>
                    <p class="small text-muted">Scan another barcode or clear search filters to view catalog.</p>
                </div>
            </div>

            <!-- Hotkeys Legend Bar -->
            <div class="p-2 border-top bg-light text-muted small d-flex justify-content-between align-items-center" style="font-size: 0.76rem;">
                <div>
                    <span class="hotkey-badge">F2</span> Focus Barcode
                    <span class="hotkey-badge ms-2">F4</span> Change Customer
                    <span class="hotkey-badge ms-2">F7</span> Switch Tier
                    <span class="hotkey-badge ms-2">F9</span> Collect & Pay
                    <span class="hotkey-badge ms-2">F10</span> Clear
                </div>
                <div class="fw-semibold text-primary" id="catalogCountText">
                    Showing {{ count($products) }} items
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Digital Billing Ticket Console -->
        <div class="pos-billing-section">
            <!-- Customer & Price Tier Header -->
            <div class="billing-header">
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-8">
                        <label class="form-label small fw-semibold text-muted mb-1 d-flex align-items-center justify-content-between">
                            <span><i class="fa-solid fa-user text-primary me-1"></i> Customer [F4]</span>
                            <a href="javascript:void(0)" class="text-decoration-none small text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#quickCustomerModal">
                                <i class="fa-solid fa-plus-circle me-0.5"></i> + Customer
                            </a>
                        </label>
                        <select id="posCustomer" class="form-select form-select-sm fw-semibold" onchange="onCustomerSelect()">
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" 
                                        data-type="{{ $c->customer_type }}" 
                                        data-gstin="{{ $c->gstin }}"
                                        {{ $c->id == $defaultCustomer->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ ucfirst($c->customer_type) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-4">
                        <label class="form-label small fw-semibold text-muted mb-1">Tier [F7]</label>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <input type="radio" class="btn-check" name="pos_price_tier" id="tierRetail" value="retail" checked onchange="onTierChange()">
                            <label class="btn btn-outline-primary fw-bold" for="tierRetail">Retail</label>
                            
                            <input type="radio" class="btn-check" name="pos_price_tier" id="tierWholesale" value="wholesale" onchange="onTierChange()">
                            <label class="btn btn-outline-primary fw-bold" for="tierWholesale">Wholesale</label>
                        </div>
                    </div>
                </div>

                <!-- Customer Details Info Bar -->
                <div id="custInfoBar" class="d-flex align-items-center justify-content-between bg-light p-2 rounded small border" style="font-size: 0.78rem;">
                    <div id="custGstinDisplay" class="text-muted text-truncate">
                        <i class="fa-solid fa-address-card me-1 text-secondary"></i> B2C Walk-in Customer
                    </div>
                    <div id="custStatusDisplay" class="fw-semibold text-success">
                        <i class="fa-solid fa-circle-check me-1"></i> Ready
                    </div>
                </div>
            </div>

            <!-- Scrollable Cart Items -->
            <div class="billing-cart-area">
                <div id="cartItemsContainer">
                    <!-- Populated dynamically via JS -->
                </div>

                <div id="emptyCartView" class="text-center py-5 text-muted">
                    <div class="p-3 mb-2 rounded-circle bg-white d-inline-block shadow-sm">
                        <i class="fa-solid fa-cart-arrow-down fa-3x text-secondary opacity-40"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Billing Ticket Empty</h5>
                    <p class="small text-muted mb-0">Scan barcodes with your scanner or click products from the catalog to build invoice.</p>
                </div>
            </div>

            <!-- Summary & Checkout Footer -->
            <div class="billing-footer">
                <!-- Promo banner -->
                <div id="promoAlertBanner" class="alert alert-warning py-1.5 px-2.5 small mb-2 d-none d-flex align-items-center">
                    <i class="fa-solid fa-gift fa-lg text-warning me-2"></i>
                    <div id="promoAlertText" class="fw-semibold"></div>
                </div>

                <!-- Tax Breakdown -->
                <div class="mb-2" style="font-size: 0.82rem;">
                    <div class="d-flex justify-content-between mb-1 text-muted">
                        <span>Taxable Value:</span>
                        <span class="fw-semibold text-dark" id="lblTaxable">₹0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 text-muted">
                        <span id="lblGstType">CGST + SGST (9%+9%):</span>
                        <span class="fw-semibold text-primary" id="lblGstTotal">₹0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 text-muted d-none" id="rowDiscount">
                        <span class="text-success fw-semibold">Discounts Applied:</span>
                        <span class="fw-bold text-success" id="lblDiscount">-₹0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 text-muted">
                        <span>Round Off:</span>
                        <span id="lblRounding">₹0.00</span>
                    </div>
                </div>

                <!-- Giant High-Contrast Grand Total -->
                <div class="grand-total-box mb-3">
                    <div>
                        <span class="text-white-50 small text-uppercase fw-bold tracking-wider d-block">Grand Total</span>
                        <span class="text-white small" id="lblItemCount">0 items</span>
                    </div>
                    <div class="text-end">
                        <h1 class="fw-bold mb-0 text-white" id="lblGrandTotal" style="font-size: 2.2rem;">₹0.00</h1>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row g-2">
                    <div class="col-4">
                        <button type="button" class="btn btn-outline-danger w-100 py-2.5 fw-bold" onclick="clearCart()">
                            <i class="fa-solid fa-trash me-1"></i> Clear (F10)
                        </button>
                    </div>
                    <div class="col-8">
                        <button type="button" class="btn btn-success w-100 py-2.5 fw-bold shadow fs-6" onclick="openPaymentModal()" id="btnCheckout" disabled>
                            <i class="fa-solid fa-credit-card me-1.5"></i> Collect & Pay (F9)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Checkout Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-cash-register text-success me-2"></i> Finalize Bill & Receive Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center p-3 mb-3 bg-light rounded border">
                    <span class="text-muted small fw-bold text-uppercase">Net Invoice Due</span>
                    <h1 class="fw-bold text-success mb-0" id="modalPayable">₹0.00</h1>
                </div>

                <!-- Tender Modes -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Select Payment Method</label>
                    <div class="row g-2">
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="payment_mode" id="modeCash" value="cash" checked onchange="changePayMode('cash')">
                            <label class="btn btn-outline-success w-100 py-2.5 fw-bold text-center" for="modeCash">
                                <i class="fa-solid fa-money-bill-1 d-block mb-1 fs-5"></i> Cash
                            </label>
                        </div>
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="payment_mode" id="modeUpi" value="upi" onchange="changePayMode('upi')">
                            <label class="btn btn-outline-info w-100 py-2.5 fw-bold text-center" for="modeUpi">
                                <i class="fa-solid fa-qrcode d-block mb-1 fs-5"></i> UPI / QR
                            </label>
                        </div>
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="payment_mode" id="modeCard" value="card" onchange="changePayMode('card')">
                            <label class="btn btn-outline-primary w-100 py-2.5 fw-bold text-center" for="modeCard">
                                <i class="fa-solid fa-credit-card d-block mb-1 fs-5"></i> Card
                            </label>
                        </div>
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="payment_mode" id="modeCredit" value="credit" onchange="changePayMode('credit')">
                            <label class="btn btn-outline-dark w-100 py-2.5 fw-bold text-center" for="modeCredit">
                                <i class="fa-solid fa-book d-block mb-1 fs-5"></i> Credit
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Cash Tender Presets & Change Calculation -->
                <div id="cashBox" class="p-3 bg-light rounded border mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small fw-bold mb-0">Cash Tender Shortcuts:</label>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2" onclick="setCash('exact')">Exact</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2" onclick="setCash(100)">₹100</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2" onclick="setCash(200)">₹200</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2" onclick="setCash(500)">₹500</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2" onclick="setCash(2000)">₹2000</button>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Cash Tendered (₹)</label>
                            <input type="number" step="1" id="cashTendered" class="form-control fw-bold fs-4 text-dark" oninput="recalcChange()">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Change to Return (₹)</label>
                            <input type="text" id="cashChange" class="form-control fw-bold fs-4 bg-white text-danger" value="₹0.00" readonly>
                        </div>
                    </div>
                </div>

                <!-- Reference / UTR for Digital -->
                <div id="refBox" class="mb-3 d-none">
                    <label class="form-label small fw-bold text-muted">Transaction / UTR Reference #</label>
                    <input type="text" id="paymentReference" class="form-control" placeholder="e.g. UPI Ref / Bank UTR / Card Auth Code">
                </div>

                <!-- Notes -->
                <div class="mb-2">
                    <label class="form-label small fw-semibold text-muted">Bill Notes (Optional)</label>
                    <input type="text" id="billNotes" class="form-control" placeholder="Vehicle number, customer PO, delivery instructions...">
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success fw-bold px-4 py-2" onclick="submitInvoiceOrder()" id="btnSubmitOrder">
                    <i class="fa-solid fa-print me-1.5"></i> Finalize & Print Bill
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Finalized Success Modal -->
<div class="modal fade" id="invoiceSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-circle-check me-2"></i> Bill Finalized Successfully!</h5>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="p-3 mb-3 rounded-circle bg-success-subtle text-success d-inline-block">
                    <i class="fa-solid fa-receipt fa-4x"></i>
                </div>
                <h3 class="fw-bold mb-1 text-dark" id="resInvoiceNumber">GC/2026-27/0001</h3>
                <p class="text-muted small mb-4">Stock successfully deducted from warehouse ledger & GST recorded.</p>

                <div class="d-grid gap-2">
                    <a href="javascript:void(0)" id="linkThermalPrint" target="_blank" class="btn btn-primary py-2.5 fw-bold fs-6">
                        <i class="fa-solid fa-receipt me-1.5"></i> Print 80mm Thermal Receipt
                    </a>
                    <a href="javascript:void(0)" id="linkA4Print" target="_blank" class="btn btn-outline-secondary py-2 fw-semibold">
                        <i class="fa-solid fa-file-invoice me-1.5"></i> Print A4 GST Tax Invoice
                    </a>
                    <a href="javascript:void(0)" id="linkPdfDownload" class="btn btn-outline-dark py-2 fw-semibold">
                        <i class="fa-solid fa-file-pdf me-1.5"></i> Download PDF Invoice
                    </a>
                </div>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-success px-4 py-2 fw-bold" onclick="resetDeskForNextCustomer()">
                    <i class="fa-solid fa-plus-circle me-1"></i> Start Next Customer Bill (Enter)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Register Customer Modal -->
<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i> Register New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form onsubmit="handleQuickCustomer(event)">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Customer / Business Name <span class="text-danger">*</span></label>
                        <input type="text" id="qcName" class="form-control" placeholder="e.g. Ramesh Agro Agencies" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Mobile Number</label>
                        <input type="text" id="qcPhone" class="form-control" placeholder="10-digit mobile number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Customer Category</label>
                        <select id="qcType" class="form-select">
                            <option value="retail">Retail Consumer</option>
                            <option value="wholesale">Wholesale B2B Trader</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">GSTIN (Optional for Retail)</label>
                        <input type="text" id="qcGstin" class="form-control" placeholder="15-character GSTIN">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check me-1"></i> Save & Select</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 & jQuery JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    // CSRF Header
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    let cart = {}; // id -> product object
    let lastCalculation = null;
    let searchDebounceTimer = null;
    let selectedCategory = '';

    // Audio synthesizer for barcode scan feedback (Web Audio API)
    function playBeep(success = true) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.value = success ? 880 : 330;
            gain.gain.setValueAtTime(0.08, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.005, ctx.currentTime + 0.1);
            osc.start();
            osc.stop(ctx.currentTime + 0.1);
        } catch(e) {}
    }

    // Digital Clock
    function updateClock() {
        const now = new Date();
        $('#digitalClock').text(now.toLocaleDateString('en-IN', { day: '2-digit', month: 'short' }) + ' ' + now.toLocaleTimeString('en-IN'));
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Fullscreen Toggle
    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(() => {});
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }

    // Debounced High-Speed Search (150ms) for 4,000+ items
    $('#barcodeSearch').on('input', function() {
        clearTimeout(searchDebounceTimer);
        let q = $(this).val().trim();
        searchDebounceTimer = setTimeout(() => {
            fetchProducts(q, selectedCategory);
        }, 150);
    });

    // Enter Key Handler on Barcode Input:
    // If it looks like a barcode scan or Enter pressed, execute instant exact barcode match
    $('#barcodeSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            let code = $(this).val().trim();
            if (!code) return;

            // Direct 1-ms exact barcode/SKU lookup
            $.ajax({
                url: "{{ route('pos.barcode') }}",
                type: "GET",
                data: {
                    code: code,
                    warehouse_id: $('#posWarehouse').val()
                },
                success: function(res) {
                    if (res.found && res.product) {
                        playBeep(true);
                        addProductToCart(res.product);
                        $('#barcodeSearch').val('').focus();
                    }
                },
                error: function() {
                    // Fallback: pick the first search result in grid
                    let firstTile = $('#productsGrid .product-card-col').first();
                    if (firstTile.length) {
                        firstTile.click();
                        playBeep(true);
                        $('#barcodeSearch').val('').focus();
                    } else {
                        playBeep(false);
                    }
                }
            });
        }
    });

    function clearSearch() {
        $('#barcodeSearch').val('').focus();
        fetchProducts('', selectedCategory);
    }

    function selectCategory(catId, btn) {
        $('.cat-pill').removeClass('active');
        $(btn).addClass('active');
        selectedCategory = catId;
        fetchProducts($('#barcodeSearch').val().trim(), catId);
    }

    // Query 4,000+ items via optimized AJAX API
    function fetchProducts(query = '', categoryId = '') {
        $.ajax({
            url: "{{ route('pos.search') }}",
            type: "GET",
            data: {
                q: query,
                category_id: categoryId,
                warehouse_id: $('#posWarehouse').val()
            },
            success: function(items) {
                renderProductTiles(items);
            }
        });
    }

    function reloadWarehouseStock() {
        fetchProducts($('#barcodeSearch').val().trim(), selectedCategory);
    }

    // Render Product Tiles into Grid
    function renderProductTiles(items) {
        const grid = $('#productsGrid');
        grid.empty();

        if (!items || items.length === 0) {
            $('#noProductsFound').removeClass('d-none');
            $('#catalogCountText').text('0 items found');
            return;
        }

        $('#noProductsFound').addClass('d-none');
        $('#catalogCountText').text('Showing ' + items.length + ' items');

        items.forEach(p => {
            const inStock = p.stock > 0;
            const stockClass = inStock ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle';
            const jsonStr = JSON.stringify(p).replace(/"/g, '&quot;');

            const tile = `
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 product-card-col" onclick="addProductToCart(${jsonStr})">
                    <div class="product-tile">
                        <div>
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="stock-pill ${stockClass}">
                                    <i class="fa-solid ${inStock ? 'fa-check' : 'fa-triangle-exclamation'} me-0.5"></i>
                                    ${p.stock.toFixed(1)} ${p.unit}
                                </span>
                                <small class="text-muted fw-bold">${p.sku}</small>
                            </div>
                            <div class="product-title" title="${p.name}">${p.name}</div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                GST: ${p.gst_rate}% | HSN: ${p.hsn_code}
                            </small>
                        </div>
                        <div class="d-flex align-items-end justify-content-between pt-2 border-top mt-2">
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.65rem;">Retail MRP</small>
                                <div class="price-retail">₹${p.retail_price.toFixed(2)}</div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block" style="font-size: 0.65rem;">Wholesale</small>
                                <div class="price-wholesale">₹${p.wholesale_price.toFixed(2)}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            grid.append(tile);
        });
    }

    // Add Product to Cart
    function addProductToCart(product) {
        if (!product || !product.id) return;

        playBeep(true);
        const tier = $('input[name="pos_price_tier"]:checked').val();
        const price = tier === 'wholesale' ? product.wholesale_price : product.retail_price;

        if (cart[product.id]) {
            cart[product.id].quantity += 1;
        } else {
            cart[product.id] = {
                id: product.id,
                name: product.name,
                sku: product.sku,
                unit: product.unit,
                retail_price: parseFloat(product.retail_price),
                wholesale_price: parseFloat(product.wholesale_price),
                price: price,
                quantity: 1,
                gst_rate: parseFloat(product.gst_rate),
                hsn_code: product.hsn_code,
                stock: parseFloat(product.stock)
            };
        }

        renderCartUI();
        syncCalculateCart();
    }

    // Modify Quantity
    function changeQty(productId, delta) {
        if (!cart[productId]) return;
        cart[productId].quantity += delta;
        if (cart[productId].quantity <= 0) {
            delete cart[productId];
        }
        renderCartUI();
        syncCalculateCart();
    }

    function setDirectQty(productId, value) {
        const val = parseFloat(value);
        if (isNaN(val) || val <= 0) {
            delete cart[productId];
        } else {
            cart[productId].quantity = val;
        }
        renderCartUI();
        syncCalculateCart();
    }

    function removeProductRow(productId) {
        delete cart[productId];
        renderCartUI();
        syncCalculateCart();
    }

    function clearCart() {
        if (Object.keys(cart).length === 0) return;
        if (confirm('Clear the entire billing ticket?')) {
            cart = {};
            lastCalculation = null;
            renderCartUI();
            syncCalculateCart();
            $('#barcodeSearch').focus();
        }
    }

    // Render Cart HTML
    function renderCartUI() {
        const container = $('#cartItemsContainer');
        container.empty();

        const keys = Object.keys(cart);
        if (keys.length === 0) {
            $('#emptyCartView').removeClass('d-none');
            $('#btnCheckout').prop('disabled', true);
            $('#lblItemCount').text('0 items');
            return;
        }

        $('#emptyCartView').addClass('d-none');
        $('#btnCheckout').prop('disabled', false);

        let totalQty = 0;
        const tier = $('input[name="pos_price_tier"]:checked').val();

        keys.forEach(k => {
            const item = cart[k];
            totalQty += item.quantity;
            item.price = tier === 'wholesale' ? item.wholesale_price : item.retail_price;
            const lineTotal = item.price * item.quantity;

            const html = `
                <div class="cart-row" data-id="${item.id}">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <div class="item-title">${item.name}</div>
                            <div class="item-sku">${item.sku} | HSN: ${item.hsn_code} | GST: ${item.gst_rate}%</div>
                        </div>
                        <button type="button" class="cart-row-del-btn" onclick="removeProductRow(${item.id})" title="Delete item">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="d-flex align-items-center">
                            <button type="button" class="qty-control-btn" onclick="changeQty(${item.id}, -1)">-</button>
                            <input type="number" step="any" class="qty-input-box" value="${item.quantity}" onchange="setDirectQty(${item.id}, this.value)">
                            <button type="button" class="qty-control-btn" onclick="changeQty(${item.id}, 1)">+</button>
                            <span class="text-muted small ms-1.5 fw-semibold">${item.unit}</span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted me-2">@ ₹${item.price.toFixed(2)}</small>
                            <span class="fw-bold text-success fs-5">₹${lineTotal.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `;
            container.append(html);
        });

        $('#lblItemCount').text(keys.length + ' items (' + totalQty.toFixed(1) + ' qty)');
    }

    // Reactive Cart Calculation
    function syncCalculateCart() {
        const keys = Object.keys(cart);
        if (keys.length === 0) {
            $('#lblTaxable').text('₹0.00');
            $('#lblGstTotal').text('₹0.00');
            $('#lblGrandTotal').text('₹0.00');
            $('#lblRounding').text('₹0.00');
            $('#rowDiscount').addClass('d-none');
            $('#promoAlertBanner').addClass('d-none');
            return;
        }

        const customerId = $('#posCustomer').val();
        const priceTier = $('input[name="pos_price_tier"]:checked').val();
        const cartItems = keys.map(k => ({
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
                lastCalculation = res;
                $('#lblTaxable').text('₹' + parseFloat(res.taxable_amount).toFixed(2));
                
                const gstLabel = res.is_interstate ? 'IGST (Interstate):' : 'CGST + SGST (9%+9%):';
                const gstVal = res.is_interstate ? res.igst_amount : (parseFloat(res.cgst_amount) + parseFloat(res.sgst_amount));
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
                    const promoNames = res.applied_promotions.map(p => p.name).join(', ');
                    $('#promoAlertText').text('Offer applied: ' + promoNames);
                    $('#promoAlertBanner').removeClass('d-none');
                } else {
                    $('#promoAlertBanner').addClass('d-none');
                }
            }
        });
    }

    // Customer Selection
    function onCustomerSelect() {
        const opt = $('#posCustomer option:selected');
        const gstin = opt.data('gstin');
        const type = opt.data('type');

        if (gstin) {
            $('#custGstinDisplay').html('<i class="fa-solid fa-shield-halved text-success me-1"></i> B2B: <strong>' + gstin + '</strong>');
        } else {
            $('#custGstinDisplay').html('<i class="fa-solid fa-address-card text-secondary me-1"></i> ' + (type === 'wholesale' ? 'Wholesale (Unregistered)' : 'B2C Retail Walk-in'));
        }

        if (type === 'wholesale') {
            $('#tierWholesale').prop('checked', true);
        }

        renderCartUI();
        syncCalculateCart();
    }

    function onTierChange() {
        renderCartUI();
        syncCalculateCart();
    }

    // Open Payment Modal
    function openPaymentModal() {
        if (!lastCalculation || Object.keys(cart).length === 0) return;

        const total = parseFloat(lastCalculation.grand_total);
        $('#modalPayable').text('₹' + total.toFixed(2));
        $('#cashTendered').val(total);
        recalcChange();

        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }

    function changePayMode(mode) {
        if (mode === 'cash') {
            $('#cashBox').removeClass('d-none');
            $('#refBox').addClass('d-none');
        } else if (mode === 'upi' || mode === 'card') {
            $('#cashBox').addClass('d-none');
            $('#refBox').removeClass('d-none');
        } else {
            $('#cashBox').addClass('d-none');
            $('#refBox').addClass('d-none');
        }
    }

    function setCash(amt) {
        if (amt === 'exact') {
            $('#cashTendered').val(lastCalculation.grand_total);
        } else {
            $('#cashTendered').val(amt);
        }
        recalcChange();
    }

    function recalcChange() {
        const due = lastCalculation ? parseFloat(lastCalculation.grand_total) : 0;
        const given = parseFloat($('#cashTendered').val()) || 0;
        const change = Math.max(0, given - due);
        $('#cashChange').val('₹' + change.toFixed(2));
        if (given < due) {
            $('#cashChange').addClass('text-danger').removeClass('text-success');
        } else {
            $('#cashChange').removeClass('text-danger').addClass('text-success');
        }
    }

    // Submit Invoice & Pay: Fixes "The payments field is required."
    function submitInvoiceOrder() {
        if (!lastCalculation || Object.keys(cart).length === 0) return;

        const btn = $('#btnSubmitOrder');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1.5"></i> Generating Invoice...');

        const mode = $('input[name="payment_mode"]:checked').val() || 'cash';
        const total = parseFloat(lastCalculation.grand_total);
        const paid = (mode === 'credit') ? 0 : total;
        const refNo = $('#paymentReference').val();

        // Standardized payments array & single-shorthand fields for 100% compliance
        const payload = {
            customer_id: $('#posCustomer').val(),
            warehouse_id: $('#posWarehouse').val(),
            price_tier: $('input[name="pos_price_tier"]:checked').val(),
            notes: $('#billNotes').val(),
            payment_method: mode,
            paid_amount: paid,
            reference_number: refNo,
            payments: [
                {
                    payment_method: mode,
                    amount: paid,
                    reference_no: refNo
                }
            ],
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

                // Launch Success Modal
                $('#resInvoiceNumber').text(res.invoice_number);
                $('#linkThermalPrint').attr('href', res.print_url + '?format=thermal');
                $('#linkA4Print').attr('href', res.print_url);
                $('#linkPdfDownload').attr('href', res.pdf_url);

                const sModal = new bootstrap.Modal(document.getElementById('invoiceSuccessModal'));
                sModal.show();
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-print me-1.5"></i> Finalize & Print Bill');
                const errMsg = xhr.responseJSON?.message || (xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors).flat().join('\n') : 'Billing transaction failed.');
                alert('Billing Error:\n' + errMsg);
            }
        });
    }

    function resetDeskForNextCustomer() {
        bootstrap.Modal.getInstance(document.getElementById('invoiceSuccessModal')).hide();
        cart = {};
        lastCalculation = null;
        renderCartUI();
        syncCalculateCart();
        $('#billNotes').val('');
        $('#paymentReference').val('');
        $('#barcodeSearch').val('').focus();
    }

    // Quick Add Customer Modal Handler
    function handleQuickCustomer(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('pos.customer.quick') }}",
            type: "POST",
            data: {
                name: $('#qcName').val().trim(),
                phone: $('#qcPhone').val().trim(),
                customer_type: $('#qcType').val(),
                gstin: $('#qcGstin').val().trim()
            },
            success: function(res) {
                const c = res.customer;
                $('#posCustomer').append(new Option(c.name + ' (' + c.customer_type + ')', c.id, true, true));
                $('#posCustomer').val(c.id);
                bootstrap.Modal.getInstance(document.getElementById('quickCustomerModal')).hide();
                onCustomerSelect();
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Error registering customer.');
            }
        });
    }

    // Hotkey bindings
    $(document).on('keydown', function(e) {
        if (e.key === 'F2') {
            e.preventDefault();
            $('#barcodeSearch').focus().select();
        } else if (e.key === 'F4') {
            e.preventDefault();
            $('#posCustomer').focus();
        } else if (e.key === 'F7') {
            e.preventDefault();
            if ($('#tierRetail').is(':checked')) {
                $('#tierWholesale').prop('checked', true);
            } else {
                $('#tierRetail').prop('checked', true);
            }
            onTierChange();
        } else if (e.key === 'F9') {
            e.preventDefault();
            if (!$('#btnCheckout').is(':disabled')) {
                openPaymentModal();
            }
        } else if (e.key === 'F10') {
            e.preventDefault();
            clearCart();
        }
    });

    // Auto-focus barcode on load
    $(document).ready(function() {
        $('#barcodeSearch').focus();
        onCustomerSelect();
    });
</script>
</body>
</html>
