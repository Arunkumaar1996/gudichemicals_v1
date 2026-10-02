@extends('layouts.app')

@section('title', 'POS Fast Billing')

@push('styles')
<style>
    .pos-container {
        height: calc(100vh - 120px);
    }
    .product-grid {
        max-height: calc(100vh - 220px);
        overflow-y: auto;
    }
    .cart-pane {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .cart-items-wrapper {
        flex-grow: 1;
        overflow-y: auto;
    }
    .product-card {
        cursor: pointer;
        transition: transform 0.15s, border-color 0.15s, box-shadow 0.15s;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }
    .product-card:hover {
        transform: translateY(-2px);
        border-color: #005a9c;
        box-shadow: 0 4px 12px rgba(0, 90, 156, 0.15);
    }
</style>
@endpush

@section('content')
<div class="row g-3 pos-container">
    <!-- Left: Product Catalog & Barcode Search -->
    <div class="col-lg-7 d-flex flex-column h-100">
        <!-- Search & Filter Bar -->
        <div class="card border-0 shadow-sm p-3 mb-3 bg-white">
            <div class="row g-2">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-light text-primary"><i class="fa-solid fa-barcode"></i></span>
                        <input type="text" id="barcodeSearch" class="form-control" placeholder="Scan Barcode or Search Chemical (Enter to add)..." autofocus>
                    </div>
                </div>
                <div class="col-md-5">
                    <select id="categoryFilter" class="form-select" onchange="filterProducts()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Product Cards Grid -->
        <div class="product-grid flex-grow-1 pe-1">
            <div class="row g-2" id="productGrid">
                @foreach($products as $prod)
                    <div class="col-md-4 col-sm-6 product-item" 
                         data-id="{{ $prod->id }}"
                         data-name="{{ strtolower($prod->name) }}"
                         data-sku="{{ strtolower($prod->sku) }}"
                         data-barcode="{{ $prod->barcode }}"
                         data-category="{{ $prod->category_id }}"
                         data-retail-price="{{ $prod->retail_price }}"
                         data-wholesale-price="{{ $prod->wholesale_price }}"
                         data-hsn="{{ $prod->hsn_code }}"
                         data-gst="{{ $prod->gst_rate }}"
                         data-unit="{{ $prod->unit?->code }}"
                         data-stock="{{ $prod->total_stock }}"
                         onclick="addToCart({{ $prod->id }})">
                        <div class="product-card p-3 bg-white h-100 position-relative">
                            <span class="badge {{ $prod->total_stock > 0 ? 'bg-success' : 'bg-danger' }} position-absolute top-0 end-0 m-2" style="font-size: 0.7rem;">
                                {{ number_format($prod->total_stock, 1) }} {{ $prod->unit?->code }}
                            </span>
                            <div class="fw-bold text-dark text-truncate mb-1" style="font-size: 0.9rem;" title="{{ $prod->name }}">
                                {{ $prod->name }}
                            </div>
                            <div class="text-muted small mb-2">{{ $prod->sku }}</div>
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Retail Price</small>
                                    <span class="fw-bold text-success">₹{{ number_format($prod->retail_price, 2) }}</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Wholesale</small>
                                    <span class="fw-semibold text-secondary">₹{{ number_format($prod->wholesale_price, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right: Cart & Billing Desk -->
    <div class="col-lg-5 h-100">
        <div class="cart-pane p-3">
            <!-- Customer & Tier Header -->
            <div class="row g-2 mb-3 pb-3 border-bottom">
                <div class="col-7">
                    <label class="form-label small fw-semibold mb-1">Customer / Walk-in</label>
                    <select id="cartCustomer" class="form-select form-select-sm" onchange="calculateCart()">
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" data-type="{{ $c->customer_type }}" {{ $c->id == $defaultCustomer->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ ucfirst($c->customer_type) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-5">
                    <label class="form-label small fw-semibold mb-1">Price Tier</label>
                    <div class="btn-group btn-group-sm w-100" role="group">
                        <input type="radio" class="btn-check" name="price_tier" id="tierRetail" value="retail" checked onchange="calculateCart()">
                        <label class="btn btn-outline-primary" for="tierRetail">Retail</label>
                        
                        <input type="radio" class="btn-check" name="price_tier" id="tierWholesale" value="wholesale" onchange="calculateCart()">
                        <label class="btn btn-outline-primary" for="tierWholesale">Wholesale</label>
                    </div>
                </div>
            </div>

            <!-- Cart Table -->
            <div class="cart-items-wrapper">
                <table class="table table-sm align-middle mb-0" id="cartTable">
                    <thead class="table-light sticky-top" style="font-size: 0.8rem;">
                        <tr>
                            <th style="width: 45%;">Item</th>
                            <th style="width: 25%;" class="text-center">Qty</th>
                            <th style="width: 20%;" class="text-end">Total</th>
                            <th style="width: 10%;" class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="cartBody">
                        <!-- Populated by JavaScript -->
                    </tbody>
                </table>
                <div id="cartEmpty" class="text-center text-muted py-5">
                    <i class="fa-solid fa-cart-shopping fa-3x mb-2 text-secondary opacity-50"></i>
                    <p class="small mb-0">Cart is empty. Click a product or scan barcode.</p>
                </div>
            </div>

            <!-- Active Promotions Alert -->
            <div id="promoAlert" class="alert alert-warning py-1 px-2 small mb-2 d-none">
                <i class="fa-solid fa-tag me-1"></i> <span id="promoText"></span>
            </div>

            <!-- Billing Totals Summary -->
            <div class="border-top pt-2 mt-auto" style="font-size: 0.88rem;">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Taxable Value:</span>
                    <span class="fw-semibold" id="lblTaxable">₹0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted" id="lblGstType">CGST + SGST (9%+9%):</span>
                    <span class="fw-semibold text-primary" id="lblGstTotal">₹0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-1 d-none" id="rowDiscount">
                    <span class="text-success">Discounts Applied:</span>
                    <span class="fw-bold text-success" id="lblDiscount">-₹0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Round Off:</span>
                    <span class="text-muted" id="lblRounding">₹0.00</span>
                </div>

                <div class="p-2 rounded bg-light border d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-5 fw-bold text-dark">Grand Total:</span>
                    <span class="fs-4 fw-bold text-success" id="lblGrandTotal">₹0.00</span>
                </div>

                <div class="row g-2">
                    <div class="col-4">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="clearCart()">
                            <i class="fa-solid fa-trash me-1"></i> Clear
                        </button>
                    </div>
                    <div class="col-8">
                        <button type="button" class="btn btn-success w-100 py-2 fw-bold fs-6 shadow-sm" onclick="openPaymentModal()" id="btnCheckout" disabled>
                            <i class="fa-solid fa-credit-card me-1"></i> Collect & Pay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Checkout Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-cash-register text-success me-2"></i> Complete Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center p-3 mb-3 bg-light rounded border">
                    <span class="text-muted small">Total Payable Amount</span>
                    <h2 class="fw-bold text-success mb-0" id="modalPayable">₹0.00</h2>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Payment Method</label>
                    <select id="paymentMethod" class="form-select" onchange="adjustPaymentMethod()">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI / QR Code</option>
                        <option value="card">Card (Debit / Credit)</option>
                        <option value="bank_transfer">Bank Transfer (NEFT/RTGS)</option>
                        <option value="credit">Customer Credit (Ledger Due)</option>
                    </select>
                </div>

                <div class="row g-2 mb-3" id="cashSection">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Cash Tendered (₹)</label>
                        <input type="number" step="1" id="cashTendered" class="form-control fw-bold" oninput="calculateChange()">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Change to Return (₹)</label>
                        <input type="text" id="cashChange" class="form-control bg-light fw-bold text-danger" value="₹0.00" disabled>
                    </div>
                </div>

                <div class="mb-3 d-none" id="referenceSection">
                    <label class="form-label small fw-semibold">Reference / UTR / Transaction #</label>
                    <input type="text" id="payReference" class="form-control" placeholder="e.g. UPI Ref / Bank UTR">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Bill Notes (Optional)</label>
                    <input type="text" id="invoiceNotes" class="form-control" placeholder="Vehicle number, PO reference, delivery notes...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success fw-bold px-4" onclick="submitInvoice()" id="btnSubmitInvoice">
                    <i class="fa-solid fa-print me-1"></i> Finalize & Print Bill
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let cart = {}; // { productId: { quantity: 1, discount: 0 } }
    let productsMap = {};

    @foreach($products as $prod)
        productsMap[{{ $prod->id }}] = {
            id: {{ $prod->id }},
            name: "{{ addslashes($prod->name) }}",
            sku: "{{ $prod->sku }}",
            barcode: "{{ $prod->barcode }}",
            unit: "{{ $prod->unit?->code }}",
            retail_price: {{ $prod->retail_price }},
            wholesale_price: {{ $prod->wholesale_price }},
            gst_rate: {{ $prod->gst_rate }},
            stock: {{ $prod->total_stock }}
        };
    @endforeach

    function addToCart(productId) {
        if (!cart[productId]) {
            cart[productId] = { quantity: 1, discount: 0 };
        } else {
            cart[productId].quantity += 1;
        }
        calculateCart();
    }

    function updateQty(productId, qty) {
        qty = parseFloat(qty);
        if (qty <= 0) {
            delete cart[productId];
        } else {
            cart[productId].quantity = qty;
        }
        calculateCart();
    }

    function removeFromCart(productId) {
        delete cart[productId];
        calculateCart();
    }

    function clearCart() {
        cart = {};
        calculateCart();
    }

    function calculateCart() {
        const productIds = Object.keys(cart);
        const cartEmpty = document.getElementById('cartEmpty');
        const cartBody = document.getElementById('cartBody');
        const btnCheckout = document.getElementById('btnCheckout');

        if (productIds.length === 0) {
            cartBody.innerHTML = '';
            cartEmpty.classList.remove('d-none');
            document.getElementById('lblTaxable').innerText = '₹0.00';
            document.getElementById('lblGstTotal').innerText = '₹0.00';
            document.getElementById('lblRounding').innerText = '₹0.00';
            document.getElementById('lblGrandTotal').innerText = '₹0.00';
            document.getElementById('promoAlert').classList.add('d-none');
            btnCheckout.disabled = true;
            return;
        }

        cartEmpty.classList.add('d-none');
        btnCheckout.disabled = false;

        const customerId = document.getElementById('cartCustomer').value;
        const priceTier = document.querySelector('input[name="price_tier"]:checked').value;

        const cartItems = productIds.map(id => ({
            product_id: id,
            quantity: cart[id].quantity,
            discount_amount: cart[id].discount || 0
        }));

        // Send AJAX calculation request to server
        $.ajax({
            url: "{{ route('pos.calculate') }}",
            type: "POST",
            data: {
                customer_id: customerId,
                price_tier: priceTier,
                cart_items: cartItems
            },
            success: function(resp) {
                renderCartTable(resp.items);
                document.getElementById('lblTaxable').innerText = '₹' + parseFloat(resp.taxable_amount).toFixed(2);
                
                const gstSum = parseFloat(resp.cgst_amount) + parseFloat(resp.sgst_amount) + parseFloat(resp.igst_amount);
                document.getElementById('lblGstTotal').innerText = '₹' + gstSum.toFixed(2);
                
                if (resp.is_interstate) {
                    document.getElementById('lblGstType').innerText = 'IGST (' + (resp.items[0]?.gst_rate || 18) + '%):';
                } else {
                    document.getElementById('lblGstType').innerText = 'CGST + SGST (9%+9%):';
                }

                document.getElementById('lblRounding').innerText = (resp.rounding_adjustment >= 0 ? '+' : '') + parseFloat(resp.rounding_adjustment).toFixed(2);
                document.getElementById('lblGrandTotal').innerText = '₹' + parseFloat(resp.grand_total).toFixed(2);
                document.getElementById('modalPayable').innerText = '₹' + parseFloat(resp.grand_total).toFixed(2);
                document.getElementById('cashTendered').value = Math.ceil(resp.grand_total);
                calculateChange();

                // Promotions banner
                if (resp.applied_promotions && resp.applied_promotions.length > 0) {
                    const promoText = resp.applied_promotions.map(p => p.name + ' (' + p.details + ')').join(' | ');
                    document.getElementById('promoText').innerText = promoText;
                    document.getElementById('promoAlert').classList.remove('d-none');
                } else {
                    document.getElementById('promoAlert').classList.add('d-none');
                }
            }
        });
    }

    function renderCartTable(items) {
        let html = '';
        items.forEach(item => {
            const isFree = item.is_free_item;
            html += `
                <tr class="${isFree ? 'table-warning' : ''}">
                    <td>
                        <strong class="d-block text-truncate" style="max-width: 170px;" title="${item.product_name}">${item.product_name}</strong>
                        <small class="text-muted">₹${parseFloat(item.unit_price).toFixed(2)} / unit</small>
                        ${isFree ? '<span class="badge bg-danger ms-1">FREE OFFER</span>' : ''}
                    </td>
                    <td class="text-center">
                        ${isFree ? `
                            <span class="fw-bold">${item.quantity}</span>
                        ` : `
                            <input type="number" step="1" min="1" class="form-control form-control-sm text-center px-1" 
                                   value="${item.quantity}" onchange="updateQty(${item.product_id}, this.value)" style="width: 65px; margin: 0 auto;">
                        `}
                    </td>
                    <td class="text-end fw-bold">
                        ₹${parseFloat(item.line_total).toFixed(2)}
                    </td>
                    <td class="text-center">
                        ${!isFree ? `
                            <button type="button" class="btn btn-sm text-danger p-0" onclick="removeFromCart(${item.product_id})">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `;
        });
        document.getElementById('cartBody').innerHTML = html;
    }

    function filterProducts() {
        const search = document.getElementById('barcodeSearch').value.toLowerCase();
        const cat = document.getElementById('categoryFilter').value;
        const items = document.querySelectorAll('.product-item');

        items.forEach(item => {
            const name = item.dataset.name;
            const sku = item.dataset.sku;
            const barcode = item.dataset.barcode;
            const itemCat = item.dataset.category;

            const matchesSearch = !search || name.includes(search) || sku.includes(search) || (barcode && barcode.includes(search));
            const matchesCat = !cat || itemCat === cat;

            if (matchesSearch && matchesCat) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Barcode auto-detection on Enter
    document.getElementById('barcodeSearch').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            const val = this.value.trim().toLowerCase();
            for (let id in productsMap) {
                if (productsMap[id].barcode === val || productsMap[id].sku.toLowerCase() === val) {
                    addToCart(id);
                    this.value = '';
                    filterProducts();
                    return;
                }
            }
        }
        filterProducts();
    });

    function openPaymentModal() {
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }

    function adjustPaymentMethod() {
        const method = document.getElementById('paymentMethod').value;
        const cashSection = document.getElementById('cashSection');
        const refSection = document.getElementById('referenceSection');

        if (method === 'cash') {
            cashSection.classList.remove('d-none');
            refSection.classList.add('d-none');
        } else {
            cashSection.classList.add('d-none');
            refSection.classList.remove('d-none');
        }
    }

    function calculateChange() {
        const grandTotal = parseFloat(document.getElementById('lblGrandTotal').innerText.replace('₹', '')) || 0;
        const tendered = parseFloat(document.getElementById('cashTendered').value) || 0;
        const change = Math.max(0, tendered - grandTotal);
        document.getElementById('cashChange').value = '₹' + change.toFixed(2);
    }

    function submitInvoice() {
        const btn = document.getElementById('btnSubmitInvoice');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Posting Bill...';

        const customerId = document.getElementById('cartCustomer').value;
        const priceTier = document.querySelector('input[name="price_tier"]:checked').value;
        const grandTotal = parseFloat(document.getElementById('lblGrandTotal').innerText.replace('₹', '')) || 0;
        const method = document.getElementById('paymentMethod').value;
        const ref = document.getElementById('payReference').value;
        const notes = document.getElementById('invoiceNotes').value;

        const cartItems = Object.keys(cart).map(id => ({
            product_id: id,
            quantity: cart[id].quantity,
            discount_amount: cart[id].discount || 0
        }));

        const payments = [{
            amount: grandTotal,
            payment_method: method,
            reference_no: ref
        }];

        $.ajax({
            url: "{{ route('pos.store') }}",
            type: "POST",
            data: {
                customer_id: customerId,
                warehouse_id: 1, // Main plant warehouse default
                price_tier: priceTier,
                cart_items: cartItems,
                payments: payments,
                notes: notes
            },
            success: function(resp) {
                if (resp.success) {
                    // Open print window
                    window.open(resp.print_url, '_blank');
                    // Reset cart
                    clearCart();
                    bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-print me-1"></i> Finalize & Print Bill';
                    alert('Invoice ' + resp.invoice_number + ' generated successfully!');
                }
            },
            error: function(xhr) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-print me-1"></i> Finalize & Print Bill';
                alert('Error creating invoice: ' + (xhr.responseJSON?.message || 'Check stock availability'));
            }
        });
    }
</script>
@endpush
@endsection
