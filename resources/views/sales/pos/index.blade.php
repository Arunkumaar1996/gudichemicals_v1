<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Fast Billing Station — Gudi Chemicals</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --pos-base-font-size: 15px; /* Default normal readable font size */
            --pos-primary: #005a9c;
            --pos-primary-dark: #003e6b;
            --pos-secondary: #0d9488;
            --pos-bg: #0b1324;
            --pos-panel-bg: #ffffff;
            --pos-border: #e2e8f0;
            --pos-font: 'Inter', -apple-system, sans-serif;
            --pos-mono: 'JetBrains Mono', monospace;
        }

        * {
            box-sizing: border-box;
            user-select: none;
        }

        input, select, textarea {
            user-select: text !important;
        }

        html {
            font-size: var(--pos-base-font-size, 15px);
            transition: font-size 0.15s ease-in-out;
        }

        body {
            height: 100vh;
            width: 100vw;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: var(--pos-font);
            font-size: 1rem; /* Normal readable font size (15px) */
            background-color: #f1f5f9;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }

        #pos-app {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        /* Top Bar */
        .pos-top-nav {
            height: 50px;
            background: linear-gradient(135deg, #091326 0%, #003666 100%);
            color: #ffffff;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        /* Workspace Grid: Full Height */
        .pos-workspace-grid {
            flex-grow: 1;
            height: calc(100vh - 50px);
            display: flex;
            padding: 0.5rem;
            gap: 0.5rem;
            overflow: hidden;
        }

        /* Left Side: Product Discovery & Catalog (58% width) */
        .catalog-container {
            flex: 1 1 58%;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-width: 0;
            background: var(--pos-panel-bg);
            border-radius: 10px;
            border: 1px solid var(--pos-border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .catalog-search-strip {
            padding: 0.6rem 0.85rem;
            border-bottom: 1px solid var(--pos-border);
            background: #ffffff;
            flex-shrink: 0;
        }

        .compact-search-input {
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.45rem 0.85rem;
            border-radius: 7px;
            border: 1.5px solid #cbd5e1;
            transition: all 0.15s;
        }

        .compact-search-input:focus {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 3px rgba(0, 90, 156, 0.15);
            outline: none;
        }

        .category-tab-strip {
            display: flex;
            gap: 0.35rem;
            overflow-x: auto;
            padding: 0.4rem 0.85rem;
            background: #f8fafc;
            border-bottom: 1px solid var(--pos-border);
            flex-shrink: 0;
        }

        .cat-tab {
            white-space: nowrap;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.3rem 0.85rem;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s;
        }

        .cat-tab:hover, .cat-tab.active {
            background: var(--pos-primary);
            border-color: var(--pos-primary);
            color: #ffffff;
        }

        .catalog-scroll-body {
            flex-grow: 1;
            overflow-y: auto;
            padding: 0.6rem;
        }

        /* Product Tile */
        .compact-product-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.65rem;
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.15s ease;
            position: relative;
        }

        .compact-product-card:hover {
            border-color: var(--pos-primary);
            box-shadow: 0 4px 14px rgba(0, 90, 156, 0.15);
            transform: translateY(-1px);
        }

        .compact-product-card.out-of-stock {
            opacity: 0.65;
            background: #fff8f8;
            border-color: #fecaca;
        }

        .compact-product-card.out-of-stock:hover {
            border-color: #ef4444;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.15);
        }

        .compact-product-card .sku-tag {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
        }

        .compact-product-card .stock-tag {
            font-size: 0.78rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
        }

        .compact-product-card .item-name {
            font-size: 0.92rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
            margin: 0.3rem 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.4rem;
        }

        .compact-product-card .batch-count-pill {
            font-size: 0.72rem;
            background: #e0f2fe;
            color: #0369a1;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            display: inline-block;
        }

        .compact-product-card .price-tag {
            font-family: var(--pos-mono);
            font-weight: 800;
            font-size: 1.05rem;
            color: #059669;
        }

        /* Right Side: Billing Ticket Console (42% width) */
        .cart-container {
            flex: 1 1 42%;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-width: 420px;
            background: var(--pos-panel-bg);
            border-radius: 10px;
            border: 1px solid var(--pos-border);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .cart-top-bar {
            padding: 0.6rem 0.85rem;
            border-bottom: 1px solid var(--pos-border);
            background: #ffffff;
            flex-shrink: 0;
        }

        .cart-table-body {
            flex-grow: 1;
            overflow-y: auto;
            background: #f8fafc;
            padding: 0.5rem;
        }

        /* Cart Item Row */
        .cart-item-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.6rem 0.75rem;
            margin-bottom: 0.5rem;
            transition: all 0.15s;
        }

        .cart-item-card:hover {
            border-color: #cbd5e1;
        }

        .cart-item-card .card-title-text {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
        }

        .batch-selector-pill {
            font-size: 0.75rem;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 2px 6px;
            color: #0f172a;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s;
        }

        .batch-selector-pill:hover {
            background: #e0f2fe;
            border-color: #0284c7;
            color: #0369a1;
        }

        .mini-qty-btn {
            width: 28px;
            height: 28px;
            padding: 0;
            font-size: 0.95rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }

        .mini-qty-input {
            width: 50px;
            height: 28px;
            text-align: center;
            font-size: 0.92rem;
            font-weight: 700;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 0;
            margin: 0 3px;
        }

        .mini-del-btn {
            width: 26px;
            height: 26px;
            border-radius: 4px;
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.78rem;
            transition: all 0.15s;
        }

        .mini-del-btn:hover {
            background: #dc2626;
            color: #ffffff;
        }

        /* Cart Footer & Calculations */
        .cart-bottom-bar {
            padding: 0.75rem 0.95rem;
            background: #ffffff;
            border-top: 1.5px solid var(--pos-border);
            flex-shrink: 0;
        }

        .compact-total-banner {
            background: linear-gradient(135deg, #064e3b 0%, #059669 100%);
            border-radius: 8px;
            padding: 0.6rem 0.95rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .hotkey-tip {
            font-size: 0.72rem;
            background: #e2e8f0;
            border-radius: 3px;
            padding: 1px 5px;
            font-weight: 700;
            color: #334155;
            margin-right: 2px;
        }

        .font-scaler-btn {
            color: rgba(255, 255, 255, 0.75);
            transition: color 0.15s;
        }
        .font-scaler-btn:hover {
            color: #ffffff !important;
        }
        .font-scaler-reset:hover {
            color: #fef08a !important;
        }

        /* Mobile Segmented Switcher & Floating Bottom Cart */
        .pos-mobile-nav-tabs {
            display: none;
            background: #091326;
            padding: 0.35rem 0.5rem;
            gap: 0.4rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            position: sticky;
            top: 46px;
            z-index: 99;
        }
        .pos-mobile-tab-btn {
            flex: 1;
            color: rgba(255, 255, 255, 0.75);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.45rem 0.6rem;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease-in-out;
        }
        .pos-mobile-tab-btn.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #005a9c 0%, #0070c0 100%) !important;
            border-color: #38bdf8 !important;
            box-shadow: 0 2px 8px rgba(0, 90, 156, 0.5);
        }
        .pos-mobile-floating-cart {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #062b4d 0%, #004b87 100%);
            color: #ffffff;
            padding: 0.6rem 1rem;
            display: none;
            align-items: center;
            justify-content: space-between;
            z-index: 1040;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.35);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
            backdrop-filter: blur(8px);
        }
        .cart-floating-icon {
            position: relative;
            font-size: 1.35rem;
            color: #38bdf8;
        }
        .cart-floating-icon .badge {
            position: absolute;
            top: -7px;
            right: -9px;
            font-size: 0.65rem;
            padding: 0.22em 0.45em;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        /* Responsive Media Queries for Tablets and Mobile Devices */
        @media (max-width: 991.98px) {
            body, #pos-app {
                height: auto !important;
                min-height: 100vh;
                overflow-y: auto !important;
            }
            .pos-mobile-nav-tabs {
                display: flex !important;
            }
            .pos-workspace-grid {
                flex-direction: column;
                height: auto;
                overflow: visible;
                padding: 0.5rem;
                gap: 0.75rem;
            }
            .catalog-container.mobile-hidden,
            .cart-container.mobile-hidden {
                display: none !important;
            }
            .catalog-container {
                flex: none;
                width: 100%;
                height: auto !important;
                min-height: calc(100vh - 120px);
                margin-bottom: 70px;
            }
            .catalog-scroll-body {
                overflow: visible !important;
                max-height: none !important;
            }
            .cart-container {
                flex: none;
                width: 100%;
                height: auto !important;
                min-height: calc(100vh - 120px);
            }
            .cart-table-body {
                max-height: 52vh !important;
            }
        }

        @media (max-width: 575.98px) {
            .pos-top-nav {
                padding: 0 0.5rem;
                height: 46px;
            }
            .pos-workspace-grid {
                padding: 0.25rem;
                gap: 0.5rem;
            }
            .compact-product-card {
                padding: 0.45rem;
            }
            .compact-total-banner {
                padding: 0.45rem 0.75rem;
            }
            #lblGrandTotal {
                font-size: 1.35rem !important;
            }
            #posWarehouse {
                min-width: 110px !important;
                max-width: 135px;
            }
            .modal-dialog {
                margin: 0.5rem;
            }
        }
    </style>
</head>
<body>

<div id="pos-app">
    <!-- Floating Out-Of-Stock & Notification Toast -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 10500;">
        <div id="posAlertToast" class="toast align-items-center text-bg-danger border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-bold py-2.5 px-3" id="posAlertToastMessage" style="font-size: 0.92rem;">
                    <!-- Alert message -->
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Top Control Bar -->
    <header class="pos-top-nav">
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light py-0.5 px-2 me-2 fw-semibold" style="font-size: 0.78rem;" title="Back to Dashboard">
                <i class="fa-solid fa-arrow-left me-0.5"></i> <span class="d-none d-sm-inline">Dashboard</span>
            </a>
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-flask-vial text-warning me-1.5 fs-5"></i>
                <span class="fw-bold tracking-wide d-none d-sm-inline" style="font-size: 0.92rem;">GUDI CHEMICALS</span>
                <span class="badge bg-secondary bg-opacity-25 ms-1.5 text-white-50 d-none d-md-inline" style="font-size: 0.70rem;">POS Workstation</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-1.5 gap-sm-2">
            <!-- Warehouse Selector -->
            <div class="d-flex align-items-center text-white-50 small" style="font-size: 0.80rem;">
                <i class="fa-solid fa-warehouse me-1 text-warning d-none d-sm-inline"></i>
                <select id="posWarehouse" class="form-select form-select-sm py-0.5 px-1.5 bg-dark text-white border-secondary fw-semibold" style="font-size: 0.78rem; min-width: 130px;" onchange="onWarehouseChange()">
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Live Clock -->
            <div class="text-white-50 small d-none d-lg-block px-1" style="font-family: var(--pos-mono); font-size: 0.80rem;" id="liveClock">
                --:--:--
            </div>

            <!-- DYNAMIC FONT SIZE ADJUSTER BUTTONS (A- / 100% / A+) -->
            <div class="d-flex align-items-center bg-black bg-opacity-25 rounded px-1.5 py-0.5 border border-white border-opacity-15 me-0.5" title="Adjust Interface Font Size">
                <span class="text-white-50 me-1 d-none d-xl-inline" style="font-size: 0.72rem;"><i class="fa-solid fa-text-height me-0.5"></i> Text:</span>
                <button type="button" class="btn btn-sm btn-link text-white text-decoration-none p-0 px-1 font-scaler-btn" onclick="adjustFontSize(-1)" title="Decrease Font Size (A-)">
                    <i class="fa-solid fa-font fa-xs"></i><i class="fa-solid fa-minus fa-2xs ms-0.5"></i>
                </button>
                <button type="button" class="btn btn-sm btn-link text-warning fw-bold text-decoration-none p-0 px-1 font-scaler-reset" onclick="resetFontSize()" title="Click to Reset Font Size (100% Normal)" style="font-size: 0.78rem; min-width: 40px;">
                    <span id="lblFontSizePercent">100%</span>
                </button>
                <button type="button" class="btn btn-sm btn-link text-white text-decoration-none p-0 px-1 font-scaler-btn" onclick="adjustFontSize(1)" title="Increase Font Size (A+)">
                    <i class="fa-solid fa-font fa-sm"></i><i class="fa-solid fa-plus fa-2xs ms-0.5"></i>
                </button>
            </div>

            <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2 fw-semibold d-none d-md-inline" style="font-size: 0.72rem;">
                <i class="fa-solid fa-user me-1"></i> {{ auth()->user()->name }}
            </span>

            <button type="button" class="btn btn-sm btn-outline-light py-0.5 px-2" onclick="toggleFullScreen()" title="Fullscreen" style="font-size: 0.78rem;">
                <i class="fa-solid fa-expand"></i>
            </button>
        </div>
    </header>

    <!-- Mobile Segmented Navigation Switcher (Shown only on Mobile/Tablet < 992px) -->
    <div class="pos-mobile-nav-tabs d-lg-none">
        <button type="button" class="pos-mobile-tab-btn active" id="tabBtnCatalog" onclick="switchMobilePosTab('catalog')">
            <i class="fa-solid fa-boxes-stacked me-1.5 text-warning"></i> Products Catalog
        </button>
        <button type="button" class="pos-mobile-tab-btn" id="tabBtnCart" onclick="switchMobilePosTab('cart')">
            <i class="fa-solid fa-cart-shopping me-1.5 text-info"></i> Cart (<span id="mobileCartBadge">0</span>) • <span id="mobileCartTotal" class="text-warning">₹0.00</span>
        </button>
    </div>

    <!-- Main POS Grid Workspace -->
    <div class="pos-workspace-grid">
        <!-- LEFT PANEL: High Speed Product Finding Engine -->
        <div class="catalog-container" id="catalogContainer">
            <!-- Search & Barcode Scan Strip -->
            <div class="catalog-search-strip">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-primary text-white border-primary px-2.5">
                        <i class="fa-solid fa-barcode"></i>
                    </span>
                    <input type="text" id="barcodeSearch" class="form-control compact-search-input" 
                           placeholder="Scan Barcode / SKU / Chemical Name... [F2] (Enter to Add)" 
                           autofocus autocomplete="off">
                    <button class="btn btn-outline-secondary py-0 px-2" type="button" onclick="clearSearch()" title="Clear">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Category Tabs -->
                <div class="category-tab-strip mt-1.5">
                    <button type="button" class="cat-tab active" onclick="filterCategory('', this)">
                        <i class="fa-solid fa-border-all me-1"></i> All
                    </button>
                    @foreach($categories as $cat)
                        <button type="button" class="cat-tab" onclick="filterCategory('{{ $cat->id }}', this)">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Cards Catalog Area (Compact 4-6 Col Layout) -->
            <div class="catalog-scroll-body">
                <div class="row g-2" id="catalogGrid">
                    @foreach($products as $p)
                        @php
                            $inStock = $p['stock'] > 0;
                            $batchesCount = count($p['batches'] ?? []);
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 product-tile-col"
                             onclick="handleProductClick({{ json_encode($p) }})">
                            <div class="compact-product-card {{ !$inStock ? 'out-of-stock' : '' }}">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="stock-tag {{ $inStock ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger text-white fw-bold' }}">
                                            {{ $inStock ? number_format($p['stock'], 1) . ' ' . $p['unit'] : 'Out of Stock (0)' }}
                                        </span>
                                        <span class="sku-tag">{{ $p['sku'] }}</span>
                                    </div>
                                    <div class="item-name" title="{{ $p['name'] }}">{{ $p['name'] }}</div>
                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                        <small class="text-muted" style="font-size: 0.78rem;">GST: {{ $p['gst_rate'] }}%</small>
                                        @if($batchesCount > 1)
                                            <span class="batch-count-pill" title="Multiple production lots available in warehouse">
                                                <i class="fa-solid fa-layer-group me-0.5"></i> {{ $batchesCount }} Lots
                                            </span>
                                        @elseif($batchesCount === 1)
                                            <small class="text-muted fw-semibold" style="font-size: 0.75rem;">Lot: {{ $p['batches'][0]['batch_number'] }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-1.5 border-top mt-1.5">
                                    <div class="price-tag">₹{{ number_format($p['retail_price'], 2) }}</div>
                                    <small class="text-muted" style="font-size: 0.78rem;">WS: ₹{{ number_format($p['wholesale_price'], 2) }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="noProductsAlert" class="text-center py-4 text-muted d-none">
                    <i class="fa-solid fa-magnifying-glass fa-2x mb-2 text-secondary opacity-40"></i>
                    <p class="small mb-0">No matching chemical items found.</p>
                </div>
            </div>

            <!-- Footer Hotkeys Strip -->
            <div class="px-2.5 py-1 border-top bg-light text-muted d-flex justify-content-between align-items-center" style="font-size: 0.7rem;">
                <div>
                    <span class="hotkey-tip">F2</span> Barcode
                    <span class="hotkey-tip ms-1.5">F4</span> Customer
                    <span class="hotkey-tip ms-1.5">F7</span> Tier
                    <span class="hotkey-tip ms-1.5">F9</span> Checkout
                    <span class="hotkey-tip ms-1.5">F10</span> Clear
                </div>
                <div id="itemsCountDisplay" class="fw-semibold text-secondary">
                    {{ count($products) }} items loaded
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Billing Cart Console -->
        <div class="cart-container mobile-hidden" id="cartContainer">
            <!-- Mobile Return to Catalog Button -->
            <div class="d-flex align-items-center justify-content-between p-2 bg-light border-bottom d-lg-none">
                <button type="button" class="btn btn-sm btn-outline-primary fw-bold py-1 px-2.5" onclick="switchMobilePosTab('catalog')">
                    <i class="fa-solid fa-arrow-left me-1"></i> Add More Products
                </button>
                <span class="badge bg-primary px-2.5 py-1.5"><i class="fa-solid fa-cart-shopping me-1"></i> Billing Cart</span>
            </div>

            <!-- Customer Bar -->
            <div class="cart-top-bar">
                <div class="row g-1.5 align-items-center">
                    <div class="col-8">
                        <div class="d-flex justify-content-between align-items-center mb-0.5">
                            <span class="text-muted small fw-semibold" style="font-size: 0.72rem;"><i class="fa-solid fa-user me-1 text-primary"></i> Customer [F4]</span>
                            <a href="javascript:void(0)" class="text-decoration-none small text-primary fw-bold" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#quickCustomerModal">
                                + New
                            </a>
                        </div>
                        <select id="posCustomer" class="form-select form-select-sm py-0.5 px-2" style="font-size: 0.78rem;" onchange="onCustomerSelect()">
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
                        <label class="text-muted small fw-semibold mb-0.5 d-block" style="font-size: 0.72rem;">Price Tier [F7]</label>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <input type="radio" class="btn-check" name="price_tier_radio" id="tierRetail" value="retail" checked onchange="onTierToggle()">
                            <label class="btn btn-outline-primary py-0.5 px-1 fw-bold" style="font-size: 0.72rem;" for="tierRetail">Retail</label>
                            
                            <input type="radio" class="btn-check" name="price_tier_radio" id="tierWholesale" value="wholesale" onchange="onTierToggle()">
                            <label class="btn btn-outline-primary py-0.5 px-1 fw-bold" style="font-size: 0.72rem;" for="tierWholesale">Wholesale</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-1 px-1.5 py-0.5 bg-light rounded border text-muted" style="font-size: 0.7rem;">
                    <span id="custGstinTag"><i class="fa-solid fa-address-card me-1"></i> B2C Retail Walk-in</span>
                    <span class="text-success fw-bold"><i class="fa-solid fa-circle-check me-0.5"></i> Active</span>
                </div>
            </div>

            <!-- Cart Table Items -->
            <div class="cart-table-body">
                <div id="cartItemsList">
                    <!-- Dynamic Cart Rows -->
                </div>

                <div id="cartEmptyView" class="text-center py-5 text-muted">
                    <i class="fa-solid fa-cart-arrow-down fa-2x text-secondary opacity-40 mb-1.5"></i>
                    <div class="fw-bold small">Cart is empty</div>
                    <small class="text-muted">Scan barcode or click items from catalog</small>
                </div>
            </div>

            <!-- Cart Summary & Checkout Footer -->
            <div class="cart-bottom-bar">
                <!-- Promo banner -->
                <div id="promoAlertBanner" class="alert alert-warning py-1 px-2 small mb-1.5 d-none" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-gift text-warning me-1"></i> <span id="promoAlertText" class="fw-semibold"></span>
                </div>

                <!-- Totals Breakdown -->
                <div class="mb-1.5" style="font-size: 0.75rem;">
                    <div class="d-flex justify-content-between mb-0.5 text-muted">
                        <span>Taxable Value:</span>
                        <span class="fw-semibold text-dark" id="lblTaxable">₹0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-0.5 text-muted">
                        <span id="lblGstType">CGST + SGST (9%+9%):</span>
                        <span class="fw-semibold text-primary" id="lblGstTotal">₹0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-0.5 text-muted d-none" id="rowDiscount">
                        <span class="text-success fw-semibold">Discounts:</span>
                        <span class="fw-bold text-success" id="lblDiscount">-₹0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-0.5 text-muted">
                        <span>Round Off:</span>
                        <span id="lblRounding">₹0.00</span>
                    </div>
                </div>

                <!-- Grand Total Banner -->
                <div class="compact-total-banner mb-2">
                    <div>
                        <span class="text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.05em;">Total Payable</span>
                        <div class="text-white small" style="font-size: 0.72rem;" id="lblCartCount">0 items</div>
                    </div>
                    <div class="text-end">
                        <span class="fw-bold text-white" style="font-family: var(--pos-mono); font-size: 1.6rem;" id="lblGrandTotal">₹0.00</span>
                    </div>
                </div>

                <!-- Checkout Actions -->
                <div class="row g-1.5">
                    <div class="col-4">
                        <button type="button" class="btn btn-outline-danger btn-sm w-100 py-1.5 fw-bold" style="font-size: 0.75rem;" onclick="clearCart()">
                            <i class="fa-solid fa-trash me-1"></i> Clear (F10)
                        </button>
                    </div>
                    <div class="col-8">
                        <button type="button" class="btn btn-success btn-sm w-100 py-1.5 fw-bold shadow fs-6" onclick="openPaymentModal()" id="btnCheckout" disabled>
                            <i class="fa-solid fa-credit-card me-1.5"></i> Collect & Pay (F9)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Bottom Checkout Bar for Mobile Catalog View -->
    <div class="pos-mobile-floating-cart d-lg-none" id="mobileFloatingCartBar" onclick="switchMobilePosTab('cart')">
        <div class="d-flex align-items-center">
            <div class="cart-floating-icon me-2.5">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="badge rounded-pill bg-danger" id="floatingCartCount">0</span>
            </div>
            <div>
                <div class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.65rem; line-height: 1;">Total Payable</div>
                <div class="fw-bold text-white fs-6" id="floatingCartTotal">₹0.00</div>
            </div>
        </div>
        <div class="btn btn-sm btn-success fw-bold px-3 py-1.5 shadow d-flex align-items-center">
            <span>View Cart & Pay</span>
            <i class="fa-solid fa-arrow-right ms-1.5"></i>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- BATCH / LOT SELECTION MODAL (MULTI-LOT)   -->
<!-- ========================================== -->
<div class="modal fade" id="batchSelectModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-2 px-3">
                <div>
                    <h6 class="modal-title fw-bold mb-0 text-dark"><i class="fa-solid fa-layer-group text-primary me-1.5"></i> Select Production Lot / Batch</h6>
                    <small class="text-muted" id="batchModalProductName">Product Name</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="alert alert-info py-1 px-2.5 small mb-2.5" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-circle-info me-1"></i> Multiple lots available. Select which manufacturing batch to dispatch from:
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.8rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Batch / Lot #</th>
                                <th>Mfg / Expiry</th>
                                <th class="text-end">Avail Stock</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="batchModalTableBody">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light py-1.5 px-3 d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAutoFifoBatch()">
                    Use Auto (FIFO - Earliest Expiry)
                </button>
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- PAYMENT CHECKOUT MODAL                     -->
<!-- ========================================== -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-2 px-3">
                <h6 class="modal-title fw-bold mb-0 text-success"><i class="fa-solid fa-cash-register me-1.5"></i> Finalize Bill & Receive Payment</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="text-center p-2.5 mb-2.5 bg-light rounded border">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Net Invoice Due</small>
                    <h2 class="fw-bold text-success mb-0" style="font-family: var(--pos-mono);" id="modalPayable">₹0.00</h2>
                </div>

                <!-- Tender Mode Radio Buttons -->
                <div class="mb-2.5">
                    <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.72rem;">Payment Method</label>
                    <div class="row g-1.5">
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="pay_mode" id="payCash" value="cash" checked onchange="onPaymentModeChange('cash')">
                            <label class="btn btn-outline-success btn-sm w-100 py-1.5 fw-bold text-center" style="font-size: 0.75rem;" for="payCash">
                                <i class="fa-solid fa-money-bill-1 d-block mb-0.5"></i> Cash
                            </label>
                        </div>
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="pay_mode" id="payUpi" value="upi" onchange="onPaymentModeChange('upi')">
                            <label class="btn btn-outline-info btn-sm w-100 py-1.5 fw-bold text-center" style="font-size: 0.75rem;" for="payUpi">
                                <i class="fa-solid fa-qrcode d-block mb-0.5"></i> UPI / QR
                            </label>
                        </div>
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="pay_mode" id="payCard" value="card" onchange="onPaymentModeChange('card')">
                            <label class="btn btn-outline-primary btn-sm w-100 py-1.5 fw-bold text-center" style="font-size: 0.75rem;" for="payCard">
                                <i class="fa-solid fa-credit-card d-block mb-0.5"></i> Card
                            </label>
                        </div>
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="pay_mode" id="payCredit" value="credit" onchange="onPaymentModeChange('credit')">
                            <label class="btn btn-outline-dark btn-sm w-100 py-1.5 fw-bold text-center" style="font-size: 0.75rem;" for="payCredit">
                                <i class="fa-solid fa-book d-block mb-0.5"></i> Credit
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Cash Tender Shortcuts & Change Box -->
                <div id="cashBox" class="p-2.5 bg-light rounded border mb-2.5">
                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                        <small class="text-muted fw-bold" style="font-size: 0.7rem;">Cash Presets:</small>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1.5" style="font-size: 0.7rem;" onclick="setCashTender('exact')">Exact</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1.5" style="font-size: 0.7rem;" onclick="setCashTender(100)">₹100</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1.5" style="font-size: 0.7rem;" onclick="setCashTender(200)">₹200</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1.5" style="font-size: 0.7rem;" onclick="setCashTender(500)">₹500</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1.5" style="font-size: 0.7rem;" onclick="setCashTender(2000)">₹2000</button>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="small text-muted fw-bold mb-0.5" style="font-size: 0.7rem;">Cash Given (₹)</label>
                            <input type="number" step="1" id="cashTenderedInput" class="form-control form-control-sm fw-bold" style="font-size: 1rem;" oninput="computeChange()">
                        </div>
                        <div class="col-6">
                            <label class="small text-muted fw-bold mb-0.5" style="font-size: 0.7rem;">Return Change (₹)</label>
                            <input type="text" id="cashChangeDisplay" class="form-control form-control-sm fw-bold bg-white text-danger" style="font-size: 1rem;" value="₹0.00" readonly>
                        </div>
                    </div>
                </div>

                <!-- Digital Reference / UTR Box -->
                <div id="refBox" class="mb-2.5 d-none">
                    <label class="small text-muted fw-bold mb-0.5" style="font-size: 0.7rem;">Transaction / UTR Reference #</label>
                    <input type="text" id="paymentReferenceInput" class="form-control form-control-sm" placeholder="e.g. UPI Ref / Bank UTR / Card Auth Code">
                </div>

                <!-- Invoice Notes -->
                <div class="mb-1">
                    <label class="small text-muted fw-semibold mb-0.5" style="font-size: 0.7rem;">Bill Notes (Optional)</label>
                    <input type="text" id="billNotesInput" class="form-control form-control-sm" placeholder="Vehicle number, PO reference, delivery notes...">
                </div>
            </div>
            <div class="modal-footer bg-light py-1.5 px-3">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-success fw-bold px-3 py-1.5" onclick="submitInvoiceCheckout()" id="btnSubmitInvoice">
                    <i class="fa-solid fa-print me-1"></i> Finalize & Print Bill
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- BILL SUCCESS MODAL                         -->
<!-- ========================================== -->
<div class="modal fade" id="invoiceSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-2 px-3">
                <h6 class="modal-title fw-bold mb-0"><i class="fa-solid fa-circle-check me-1.5"></i> Bill Finalized Successfully!</h6>
            </div>
            <div class="modal-body p-3 text-center">
                <div class="p-2 mb-2 rounded-circle bg-success-subtle text-success d-inline-block">
                    <i class="fa-solid fa-receipt fa-3x"></i>
                </div>
                <h4 class="fw-bold mb-1 text-dark" id="resInvoiceNum">GC/2026-27/0001</h4>
                <p class="text-muted small mb-3">Stock deducted from inventory ledger and GST transaction posted.</p>

                <div class="d-grid gap-1.5">
                    <a href="javascript:void(0)" id="linkThermalPrint" target="_blank" class="btn btn-primary btn-sm py-2 fw-bold">
                        <i class="fa-solid fa-receipt me-1"></i> Print 80mm Thermal Receipt
                    </a>
                    <a href="javascript:void(0)" id="linkA4Print" target="_blank" class="btn btn-outline-secondary btn-sm py-1.5 fw-semibold">
                        <i class="fa-solid fa-file-invoice me-1"></i> Print A4 GST Tax Invoice
                    </a>
                    <a href="javascript:void(0)" id="linkPdfDownload" class="btn btn-outline-dark btn-sm py-1.5 fw-semibold">
                        <i class="fa-solid fa-file-pdf me-1"></i> Download PDF Invoice
                    </a>
                </div>
            </div>
            <div class="modal-footer bg-light py-1.5 px-3 justify-content-center">
                <button type="button" class="btn btn-sm btn-success px-4 py-1.5 fw-bold" onclick="resetDeskForNextCustomer()">
                    <i class="fa-solid fa-plus-circle me-1"></i> Start Next Customer Bill (Enter)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- QUICK CUSTOMER REGISTRATION MODAL          -->
<!-- ========================================== -->
<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-2 px-3">
                <h6 class="modal-title fw-bold mb-0 text-primary"><i class="fa-solid fa-user-plus me-1.5"></i> Register Customer</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form onsubmit="handleQuickCustomer(event)">
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold mb-0.5">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" id="qcName" class="form-control form-control-sm" placeholder="e.g. Ramesh Agro Agencies" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold mb-0.5">Mobile Phone</label>
                        <input type="text" id="qcPhone" class="form-control form-control-sm" placeholder="10-digit mobile">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold mb-0.5">Customer Type</label>
                        <select id="qcType" class="form-select form-select-sm">
                            <option value="retail">Retail Consumer</option>
                            <option value="wholesale">Wholesale B2B Trader</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-semibold mb-0.5">GSTIN (Optional)</label>
                        <input type="text" id="qcGstin" class="form-control form-control-sm" placeholder="15-character GSTIN">
                    </div>
                </div>
                <div class="modal-footer bg-light py-1.5 px-3">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold">Save & Select</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ==========================================
    // DYNAMIC FONT SIZE ADJUSTMENT ENGINE
    // ==========================================
    const fontScalePresets = [
        { label: '85%', size: '13px' },
        { label: '100%', size: '15px' }, // Normal Default (readable)
        { label: '115%', size: '17px' },
        { label: '130%', size: '19.5px' },
    ];
    let currentScaleIdx = 1; // 100% Normal

    function applyFontScale(idx) {
        if (idx < 0) idx = 0;
        if (idx >= fontScalePresets.length) idx = fontScalePresets.length - 1;
        currentScaleIdx = idx;
        const preset = fontScalePresets[currentScaleIdx];
        document.documentElement.style.setProperty('--pos-base-font-size', preset.size);
        $('#lblFontSizePercent').text(preset.label);
        try {
            localStorage.setItem('pos_font_scale_idx', currentScaleIdx);
        } catch(e) {}
    }

    function adjustFontSize(delta) {
        applyFontScale(currentScaleIdx + delta);
    }

    function resetFontSize() {
        applyFontScale(1); // Reset to 100% (15px Normal)
    }

    // Restore saved font size preference on load
    try {
        const savedIdx = localStorage.getItem('pos_font_scale_idx');
        if (savedIdx !== null && !isNaN(parseInt(savedIdx, 10))) {
            applyFontScale(parseInt(savedIdx, 10));
        } else {
            applyFontScale(1);
        }
    } catch(e) {
        applyFontScale(1);
    }

    // Friendly Toast Alert
    function showStockWarningToast(message, isDanger = true) {
        try {
            playScanBeep(false);
            const toastEl = $('#posAlertToast');
            toastEl.removeClass('text-bg-danger text-bg-warning text-bg-success')
                   .addClass(isDanger ? 'text-bg-danger' : 'text-bg-warning');
            $('#posAlertToastMessage').html('<i class="fa-solid fa-triangle-exclamation me-1.5"></i> ' + message);
            const toast = new bootstrap.Toast(toastEl[0], { delay: 4500 });
            toast.show();
        } catch(e) {
            alert(message);
        }
    }

    let cart = {}; // cartKey (product_id + '_' + batch_id) -> item
    let lastCalculation = null;
    let searchDebounceTimer = null;
    let selectedCategory = '';
    let pendingModalProduct = null;
    let currentMobileTab = 'catalog';

    // Switch between Catalog and Cart on mobile/tablet viewports (< 992px)
    function switchMobilePosTab(tab) {
        currentMobileTab = tab;
        if (window.innerWidth >= 992) {
            $('#catalogContainer').removeClass('mobile-hidden');
            $('#cartContainer').removeClass('mobile-hidden');
            return;
        }

        if (tab === 'cart') {
            $('#catalogContainer').addClass('mobile-hidden');
            $('#cartContainer').removeClass('mobile-hidden');
            $('#tabBtnCatalog').removeClass('active');
            $('#tabBtnCart').addClass('active');
            $('#mobileFloatingCartBar').hide();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            $('#catalogContainer').removeClass('mobile-hidden');
            $('#cartContainer').addClass('mobile-hidden');
            $('#tabBtnCatalog').addClass('active');
            $('#tabBtnCart').removeClass('active');
            if (Object.keys(cart).length > 0) {
                $('#mobileFloatingCartBar').css('display', 'flex');
            } else {
                $('#mobileFloatingCartBar').hide();
            }
        }
    }

    $(window).on('resize', function() {
        if (window.innerWidth >= 992) {
            $('#catalogContainer').removeClass('mobile-hidden');
            $('#cartContainer').removeClass('mobile-hidden');
            $('#mobileFloatingCartBar').hide();
        } else {
            switchMobilePosTab(currentMobileTab);
        }
    });

    // Web Audio Synthesizer Beep for scanner feedback
    function playScanBeep(success = true) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.value = success ? 900 : 320;
            gain.gain.setValueAtTime(0.06, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.005, ctx.currentTime + 0.08);
            osc.start();
            osc.stop(ctx.currentTime + 0.08);
        } catch(e) {}
    }

    // Live Clock
    function updateClock() {
        const now = new Date();
        $('#liveClock').text(now.toLocaleTimeString('en-IN'));
    }
    setInterval(updateClock, 1000);
    updateClock();

    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(() => {});
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }

    // High Speed Debounced Search for 4,000+ items
    $('#barcodeSearch').on('input', function() {
        clearTimeout(searchDebounceTimer);
        const q = $(this).val().trim();
        searchDebounceTimer = setTimeout(() => {
            fetchProducts(q, selectedCategory);
        }, 150);
    });

    // Enter Key on Search Box -> Instant Barcode Exact Match
    $('#barcodeSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const code = $(this).val().trim();
            if (!code) return;

            $.ajax({
                url: "{{ route('pos.barcode') }}",
                type: "GET",
                data: {
                    code: code,
                    warehouse_id: $('#posWarehouse').val()
                },
                success: function(res) {
                    if (res.found && res.product) {
                        handleProductClick(res.product);
                        $('#barcodeSearch').val('').focus();
                    }
                },
                error: function() {
                    const firstTile = $('#catalogGrid .product-tile-col').first();
                    if (firstTile.length) {
                        firstTile.click();
                        $('#barcodeSearch').val('').focus();
                    } else {
                        playScanBeep(false);
                    }
                }
            });
        }
    });

    function clearSearch() {
        $('#barcodeSearch').val('').focus();
        fetchProducts('', selectedCategory);
    }

    function filterCategory(catId, btn) {
        $('.cat-tab').removeClass('active');
        $(btn).addClass('active');
        selectedCategory = catId;
        fetchProducts($('#barcodeSearch').val().trim(), catId);
    }

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
                renderCatalogTiles(items);
            }
        });
    }

    function onWarehouseChange() {
        const whName = $('#posWarehouse option:selected').text();
        fetchProducts($('#barcodeSearch').val().trim(), selectedCategory);
        if (Object.keys(cart).length > 0) {
            showStockWarningToast(`Switched active warehouse to: ${whName}. Stock levels and available batches have been updated.`, false);
        }
    }

    // Render Product Cards
    function renderCatalogTiles(items) {
        const grid = $('#catalogGrid');
        grid.empty();

        if (!items || items.length === 0) {
            $('#noProductsAlert').removeClass('d-none');
            $('#itemsCountDisplay').text('0 items found');
            return;
        }

        $('#noProductsAlert').addClass('d-none');
        $('#itemsCountDisplay').text(items.length + ' items');

        items.forEach(p => {
            const inStock = p.stock > 0;
            const stockClass = inStock ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger text-white fw-bold';
            const stockText = inStock ? `${p.stock.toFixed(1)} ${p.unit}` : 'Out of Stock (0)';
            const cardClass = inStock ? 'compact-product-card' : 'compact-product-card out-of-stock';
            const batchesCount = (p.batches || []).length;
            const jsonStr = JSON.stringify(p).replace(/"/g, '&quot;');

            let batchBadge = '';
            if (batchesCount > 1) {
                batchBadge = `<span class="batch-count-pill"><i class="fa-solid fa-layer-group me-0.5"></i> ${batchesCount} Lots</span>`;
            } else if (batchesCount === 1) {
                batchBadge = `<small class="text-muted fw-semibold" style="font-size: 0.75rem;">Lot: ${p.batches[0].batch_number}</small>`;
            }

            const tile = `
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 product-tile-col" onclick="handleProductClick(${jsonStr})">
                    <div class="${cardClass}">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="stock-tag ${stockClass}">
                                    ${stockText}
                                </span>
                                <span class="sku-tag">${p.sku}</span>
                            </div>
                            <div class="item-name" title="${p.name}">${p.name}</div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <small class="text-muted" style="font-size: 0.78rem;">GST: ${p.gst_rate}%</small>
                                ${batchBadge}
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-1.5 border-top mt-1.5">
                            <div class="price-tag">₹${p.retail_price.toFixed(2)}</div>
                            <small class="text-muted" style="font-size: 0.78rem;">WS: ₹${p.wholesale_price.toFixed(2)}</small>
                        </div>
                    </div>
                </div>
            `;
            grid.append(tile);
        });
    }

    // ==========================================
    // MULTI-BATCH / LOT SELECTION LOGIC
    // ==========================================
    function handleProductClick(product) {
        if (!product || !product.id) return;

        // Guard against out-of-stock items
        if (product.stock <= 0) {
            const whName = $('#posWarehouse option:selected').text();
            showStockWarningToast(`Out of Stock: "${product.name}" has 0 stock in ${whName}. Please select another warehouse or restock.`);
            return;
        }

        const batches = product.batches || [];

        // If product has more than 1 active batch in this warehouse, prompt modal to choose batch
        if (batches.length > 1) {
            openBatchSelectModal(product);
        } else {
            // Only 1 batch or no explicit batch -> add directly with FIFO / single batch
            const singleBatch = batches.length === 1 ? batches[0] : null;
            addCartItem(product, singleBatch ? singleBatch.id : null, singleBatch ? singleBatch.batch_number : null, singleBatch ? singleBatch.expiry_date : null);
        }
    }

    function openBatchSelectModal(product) {
        pendingModalProduct = product;
        $('#batchModalProductName').text(product.name + ' (' + product.sku + ')');

        const tbody = $('#batchModalTableBody');
        tbody.empty();

        product.batches.forEach(b => {
            const row = `
                <tr>
                    <td>
                        <strong class="text-dark">${b.batch_number}</strong>
                        ${b.supplier_lot_number ? '<div class="text-muted small">Mfg Lot: ' + b.supplier_lot_number + '</div>' : ''}
                    </td>
                    <td>
                        <div>Exp: <strong class="text-dark">${b.expiry_date || 'N/A'}</strong></div>
                        <small class="text-muted">Mfg: ${b.mfg_date || 'N/A'}</small>
                    </td>
                    <td class="text-end fw-bold text-success">
                        ${b.stock.toFixed(1)} ${product.unit}
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-primary py-0.5 px-2.5 fw-bold" style="font-size: 0.72rem;" onclick="confirmBatchChoice(${b.id}, '${b.batch_number}', '${b.expiry_date || ''}')">
                            Select Lot
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        const modal = new bootstrap.Modal(document.getElementById('batchSelectModal'));
        modal.show();
    }

    function confirmBatchChoice(batchId, batchNumber, expiryDate) {
        bootstrap.Modal.getInstance(document.getElementById('batchSelectModal')).hide();
        if (pendingModalProduct) {
            addCartItem(pendingModalProduct, batchId, batchNumber, expiryDate);
            pendingModalProduct = null;
        }
    }

    function selectAutoFifoBatch() {
        bootstrap.Modal.getInstance(document.getElementById('batchSelectModal')).hide();
        if (pendingModalProduct) {
            addCartItem(pendingModalProduct, null, null, null);
            pendingModalProduct = null;
        }
    }

    // Add Item to Cart with Specific Batch
    function addCartItem(product, batchId, batchNumber, expiryDate) {
        playScanBeep(true);

        const cartKey = product.id + '_' + (batchId || 'auto');
        const tier = $('input[name="price_tier_radio"]:checked').val();
        const price = tier === 'wholesale' ? product.wholesale_price : product.retail_price;

        if (cart[cartKey]) {
            cart[cartKey].quantity += 1;
        } else {
            cart[cartKey] = {
                key: cartKey,
                product_id: product.id,
                name: product.name,
                sku: product.sku,
                unit: product.unit,
                retail_price: parseFloat(product.retail_price),
                wholesale_price: parseFloat(product.wholesale_price),
                price: price,
                quantity: 1,
                gst_rate: parseFloat(product.gst_rate),
                hsn_code: product.hsn_code,
                stock: parseFloat(product.stock),
                batch_id: batchId,
                batch_number: batchNumber,
                expiry_date: expiryDate,
                batches: product.batches || []
            };
        }

        renderCart();
        syncCalculate();
    }

    // Modify Item Batch directly from cart dropdown
    function switchItemBatch(cartKey, newBatchId) {
        const item = cart[cartKey];
        if (!item) return;

        const qty = item.quantity;
        delete cart[cartKey];

        const targetBatch = (item.batches || []).find(b => b.id == newBatchId);
        const newKey = item.product_id + '_' + (newBatchId || 'auto');

        cart[newKey] = {
            ...item,
            key: newKey,
            batch_id: newBatchId ? parseInt(newBatchId) : null,
            batch_number: targetBatch ? targetBatch.batch_number : null,
            expiry_date: targetBatch ? targetBatch.expiry_date : null,
            quantity: qty
        };

        renderCart();
        syncCalculate();
    }

    function changeQty(cartKey, delta) {
        if (!cart[cartKey]) return;
        cart[cartKey].quantity += delta;
        if (cart[cartKey].quantity <= 0) {
            delete cart[cartKey];
        }
        renderCart();
        syncCalculate();
    }

    function setDirectQty(cartKey, val) {
        const q = parseFloat(val);
        if (isNaN(q) || q <= 0) {
            delete cart[cartKey];
        } else {
            cart[cartKey].quantity = q;
        }
        renderCart();
        syncCalculate();
    }

    function removeCartRow(cartKey) {
        delete cart[cartKey];
        renderCart();
        syncCalculate();
    }

    function clearCart() {
        if (Object.keys(cart).length === 0) return;
        if (confirm('Clear the entire billing ticket?')) {
            cart = {};
            lastCalculation = null;
            renderCart();
            syncCalculate();
            $('#barcodeSearch').focus();
        }
    }

    // Render Compact Cart Table
    function renderCart() {
        const container = $('#cartItemsList');
        container.empty();

        const keys = Object.keys(cart);
        if (keys.length === 0) {
            $('#cartEmptyView').removeClass('d-none');
            $('#btnCheckout').prop('disabled', true);
            $('#lblCartCount').text('0 items');
            return;
        }

        $('#cartEmptyView').addClass('d-none');
        $('#btnCheckout').prop('disabled', false);

        let totalQty = 0;
        const tier = $('input[name="price_tier_radio"]:checked').val();

        keys.forEach(k => {
            const item = cart[k];
            totalQty += item.quantity;
            item.price = tier === 'wholesale' ? item.wholesale_price : item.retail_price;
            const lineTotal = item.price * item.quantity;

            // Batch selection control
            let batchHtml = '';
            if (item.batches && item.batches.length > 1) {
                let options = `<option value="" ${!item.batch_id ? 'selected' : ''}>Auto (FIFO Allocation)</option>`;
                item.batches.forEach(b => {
                    const sel = (item.batch_id == b.id) ? 'selected' : '';
                    options += `<option value="${b.id}" ${sel}>Lot: ${b.batch_number} (Exp: ${b.expiry_date || 'N/A'} | Avail: ${b.stock.toFixed(1)} ${item.unit})</option>`;
                });
                batchHtml = `
                    <div class="mt-1">
                        <select class="form-select form-select-sm py-0.5 px-1.5 border-secondary border-opacity-50 text-dark fw-semibold" style="font-size: 0.8rem;" onchange="switchItemBatch('${item.key}', this.value)">
                            ${options}
                        </select>
                    </div>
                `;
            } else if (item.batch_number) {
                batchHtml = `
                    <div class="text-muted mt-1" style="font-size: 0.8rem;">
                        <i class="fa-solid fa-tag me-0.5 text-secondary"></i> Lot: <strong class="text-dark">${item.batch_number}</strong> ${item.expiry_date ? '(Exp: ' + item.expiry_date + ')' : ''}
                    </div>
                `;
            }

            const html = `
                <div class="cart-item-card" data-key="${item.key}">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div class="me-2">
                            <span class="card-title-text">${item.name}</span>
                            <div class="text-muted" style="font-size: 0.8rem;">
                                ${item.sku} | HSN: ${item.hsn_code} | GST: ${item.gst_rate}%
                            </div>
                            ${batchHtml}
                        </div>
                        <button type="button" class="mini-del-btn" onclick="removeCartRow('${item.key}')" title="Delete">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1.5 pt-1.5 border-top border-light">
                        <div class="d-flex align-items-center">
                            <button type="button" class="mini-qty-btn" onclick="changeQty('${item.key}', -1)">-</button>
                            <input type="number" step="any" class="mini-qty-input" value="${item.quantity}" onchange="setDirectQty('${item.key}', this.value)">
                            <button type="button" class="mini-qty-btn" onclick="changeQty('${item.key}', 1)">+</button>
                            <small class="text-muted ms-1.5 fw-semibold" style="font-size: 0.82rem;">${item.unit}</small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted me-1.5" style="font-size: 0.82rem;">@ ₹${item.price.toFixed(2)}</small>
                            <span class="fw-bold text-success" style="font-family: var(--pos-mono); font-size: 1.05rem;">₹${lineTotal.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `;
            container.append(html);
        });

        const count = keys.length;
        const totalFormatted = (lastCalculation && lastCalculation.grand_total) ? '₹' + parseFloat(lastCalculation.grand_total).toFixed(2) : '₹0.00';
        $('#lblCartCount').text(count + ' items (' + totalQty.toFixed(1) + ' ' + (count === 1 ? 'qty' : 'total') + ')');
        $('#mobileCartBadge').text(count);
        $('#mobileCartTotal').text(totalFormatted);
        $('#floatingCartCount').text(count);
        $('#floatingCartTotal').text(totalFormatted);

        if (window.innerWidth < 992) {
            if (count > 0 && currentMobileTab === 'catalog') {
                $('#mobileFloatingCartBar').css('display', 'flex');
            } else {
                $('#mobileFloatingCartBar').hide();
            }
        }
    }

    // Server-Side Reactive Cart Calculation
    function syncCalculate() {
        const keys = Object.keys(cart);
        if (keys.length === 0) {
            $('#lblTaxable').text('₹0.00');
            $('#lblGstTotal').text('₹0.00');
            $('#lblGrandTotal').text('₹0.00');
            $('#lblRounding').text('₹0.00');
            $('#mobileCartTotal').text('₹0.00');
            $('#floatingCartTotal').text('₹0.00');
            $('#mobileFloatingCartBar').hide();
            $('#rowDiscount').addClass('d-none');
            $('#promoAlertBanner').addClass('d-none');
            return;
        }

        const customerId = $('#posCustomer').val();
        const priceTier = $('input[name="price_tier_radio"]:checked').val();
        const cartItems = keys.map(k => ({
            product_id: cart[k].product_id,
            quantity: cart[k].quantity,
            batch_id: cart[k].batch_id,
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
                const grandStr = '₹' + parseFloat(res.grand_total).toFixed(2);
                $('#lblGrandTotal').text(grandStr);
                $('#mobileCartTotal').text(grandStr);
                $('#floatingCartTotal').text(grandStr);

                if (window.innerWidth < 992 && keys.length > 0 && currentMobileTab === 'catalog') {
                    $('#mobileFloatingCartBar').css('display', 'flex');
                }

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

    function onCustomerSelect() {
        const opt = $('#posCustomer option:selected');
        const gstin = opt.data('gstin');
        const type = opt.data('type');

        if (gstin) {
            $('#custGstinTag').html('<i class="fa-solid fa-shield-halved text-success me-1"></i> B2B: <strong>' + gstin + '</strong>');
        } else {
            $('#custGstinTag').html('<i class="fa-solid fa-address-card text-secondary me-1"></i> ' + (type === 'wholesale' ? 'Wholesale (Unregistered)' : 'B2C Retail Walk-in'));
        }

        if (type === 'wholesale') {
            $('#tierWholesale').prop('checked', true);
        }

        renderCart();
        syncCalculate();
    }

    function onTierToggle() {
        renderCart();
        syncCalculate();
    }

    // Payment Checkout Modal
    function openPaymentModal() {
        if (!lastCalculation || Object.keys(cart).length === 0) return;

        const total = parseFloat(lastCalculation.grand_total);
        $('#modalPayable').text('₹' + total.toFixed(2));
        $('#cashTenderedInput').val(total);
        computeChange();

        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }

    function onPaymentModeChange(mode) {
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

    function setCashTender(amt) {
        if (amt === 'exact') {
            $('#cashTenderedInput').val(lastCalculation.grand_total);
        } else {
            $('#cashTenderedInput').val(amt);
        }
        computeChange();
    }

    function computeChange() {
        const due = lastCalculation ? parseFloat(lastCalculation.grand_total) : 0;
        const given = parseFloat($('#cashTenderedInput').val()) || 0;
        const change = Math.max(0, given - due);
        $('#cashChangeDisplay').val('₹' + change.toFixed(2));
        if (given < due) {
            $('#cashChangeDisplay').addClass('text-danger').removeClass('text-success');
        } else {
            $('#cashChangeDisplay').removeClass('text-danger').addClass('text-success');
        }
    }

    // Submit Checkout: Both payments array and shorthand fields sent
    function submitInvoiceCheckout() {
        if (!lastCalculation || Object.keys(cart).length === 0) return;

        const btn = $('#btnSubmitInvoice');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Finalizing...');

        const mode = $('input[name="pay_mode"]:checked').val() || 'cash';
        const total = parseFloat(lastCalculation.grand_total);
        const paid = (mode === 'credit') ? 0 : total;
        const refNo = $('#paymentReferenceInput').val();

        const payload = {
            customer_id: $('#posCustomer').val(),
            warehouse_id: $('#posWarehouse').val(),
            price_tier: $('input[name="price_tier_radio"]:checked').val(),
            notes: $('#billNotesInput').val(),
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
                product_id: cart[k].product_id,
                quantity: cart[k].quantity,
                batch_id: cart[k].batch_id,
                discount_amount: 0
            }))
        };

        $.ajax({
            url: "{{ route('pos.store') }}",
            type: "POST",
            data: payload,
            success: function(res) {
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                btn.prop('disabled', false).html('<i class="fa-solid fa-print me-1"></i> Finalize & Print Bill');

                $('#resInvoiceNum').text(res.invoice_number);
                $('#linkThermalPrint').attr('href', res.print_url + '?format=thermal');
                $('#linkA4Print').attr('href', res.print_url);
                $('#linkPdfDownload').attr('href', res.pdf_url);

                const sModal = new bootstrap.Modal(document.getElementById('invoiceSuccessModal'));
                sModal.show();
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-print me-1"></i> Finalize & Print Bill');
                const errMsg = xhr.responseJSON?.message || (xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors).flat().join('\n') : 'Billing transaction failed.');
                showStockWarningToast(errMsg, true);
                // Also show inline notice if payment modal is open
                $('#modalErrorAlert').remove();
                $('#paymentModal .modal-body').prepend(`
                    <div id="modalErrorAlert" class="alert alert-danger py-2 px-3 small mb-2 fw-semibold">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> ${errMsg}
                    </div>
                `);
            }
        });
    }

    function resetDeskForNextCustomer() {
        bootstrap.Modal.getInstance(document.getElementById('invoiceSuccessModal')).hide();
        cart = {};
        lastCalculation = null;
        renderCart();
        syncCalculate();
        $('#billNotesInput').val('');
        $('#paymentReferenceInput').val('');
        $('#barcodeSearch').val('').focus();
    }

    // Quick Add Customer
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

    // Keyboard Shortcuts
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
            onTierToggle();
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

    $(document).ready(function() {
        $('#barcodeSearch').focus();
        onCustomerSelect();
    });
</script>
</body>
</html>
