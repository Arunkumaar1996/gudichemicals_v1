# Gudi Chemicals ERP — Chemical Manufacturing, Inventory, Sales POS & GST System

**Gudi Chemicals ERP** is a production-grade enterprise web application built on **Laravel 12**, **PHP 8.2**, **Bootstrap 5**, and **MySQL/SQLite**, tailored specifically for chemical manufacturing, batch processing, recipe formulation, bulk liquid bottling, GST-compliant POS billing, multi-tier inventory, and accounts ledgers.

---

## 1. Key Application Capabilities

1. **Chemical Production & Formulation (BOM)**:
   - Recipe formulation with version control (v1, v2...) and expected yield / process loss percentage.
   - Production orders automatically scale ingredient requirements based on planned batch output size.
   - Lab Quality Control (QC) check parameter recording (pH, Viscosity, Specific Gravity, Active Matter %, Appearance).
   - Atomic finalization: consumes raw chemicals/packaging, adds finished products to inventory, creates lots, and calculates unit production costs.

2. **Multi-tier Inventory Ledger**:
   - Supports Raw Materials (acids, solvents, water, LABSA), Semi-finished Intermediates (bulk compounding liquid), Packaging (HDPE bottles, jerry cans, caps, cartons), and Finished Chemical Products.
   - Strict append-only stock movement ledger (`stock_movements`) and real-time balances (`stock_balances`) protected by pessimistic row locks (`lockForUpdate`).
   - Non-negative stock protection with `InsufficientStockException`.
   - Batch tracking (`inventory_batches`) with supplier lot references, manufacturing date, and expiry/retest dates.
   - Controlled physical stock adjustments with supervisor audit logging.

3. **High-Speed POS & GST Sales Billing Desk**:
   - Keyboard-friendly POS with barcode scanning and instant debounced search.
   - Retail Selling Price (RSP) vs Wholesale Selling Price (WSP) tiering.
   - Server-side Indian GST calculation engine:
     - Intra-state (equal split into CGST and SGST).
     - Inter-state (IGST).
     - HSN classification and configurable GST rates (0%, 5%, 12%, 18%, 28%).
     - Rounding adjustment and sequential gapless invoice numbering (`GC/2026-27/0001`).
   - Split payments (Cash, UPI, Card, Bank Transfer, Customer Credit) with instant change calculator.
   - Print options: Standard A4 GST Tax Invoice, 80mm compact thermal receipt, and downloadable PDF.

4. **Promotional Offers Engine**:
   - Buy 1 Get 1 Free (BOGO), Buy 2 Get 1 Free (B2G1), Buy X Get Y (BXGY).
   - Percentage discounts, flat invoice discounts, combo bundles, and quantity slab tiers.
   - Automatic cart reward item injection.

5. **Purchasing & Accounts Payable**:
   - Purchase Orders (Draft -> Approval).
   - Goods Receipt Notes (GRN) with batch creation. Stock increases strictly upon GRN posting.
   - Supplier bills and vendor payment allocations with live payable balance reduction.

6. **Customer & Vendor Ledgers**:
   - Complete transaction and payment histories.
   - Customer credit limits and outstanding receivables.
   - Sales returns & Credit Notes with condition sorting (saleable re-enters stock; damaged is quarantined).

7. **Reports & Audit Trail**:
   - GSTR-1 compliant periodic sales register with taxable, CGST, SGST, IGST breakdown.
   - Real-time stock valuation report (asset cost value).
   - Production yield, wastage, and batch unit costing report.
   - Daily payment collections breakdown.

---

## 2. Default Credentials & Initial Setup

### Administrator Account
- **URL**: `/login`
- **Email**: `admin@gudichemicals.com`
- **Password**: `Admin@12345`
- **Role**: `Super Admin`

### Cashier Counter Account
- **Email**: `cashier@gudichemicals.com`
- **Password**: `Cashier@12345`
- **Role**: `Cashier`

---

## 3. Technology Stack & Architecture

- **Backend**: Laravel 12.69.3 / PHP 8.2+
- **Database**: SQLite (default test/local) & MySQL (production InnoDB utf8mb4)
- **Authorization**: `spatie/laravel-permission` (RBAC)
- **PDF Generation**: `barryvdh/laravel-dompdf` (DomPDF v3.1)
- **Frontend**: Laravel Blade, Bootstrap 5.3.3, FontAwesome 6, jQuery 3.7.1
- **Testing**: PHPUnit / Laravel Test Suite (14 passed, 68 assertions)

---

## 4. Quickstart Commands

```bash
# 1. Install Composer dependencies
composer install

# 2. Run migrations and seed default company, warehouses, units, and products
php artisan migrate:fresh --seed

# 3. Execute automated test suite
php artisan test

# 4. Start local development server
php artisan serve
```

---

## 5. Architectural Directory Layout

```
app/
├── Exceptions/
│   ├── InsufficientStockException.php
│   ├── ProductionException.php
│   └── BillingException.php
├── Http/Controllers/
│   ├── Auth/AuthController.php
│   ├── DashboardController.php
│   ├── Finance/ExpenseController.php
│   ├── Inventory/InventoryController.php
│   ├── Inventory/StockAdjustmentController.php
│   ├── Masters/
│   │   ├── CategoryController.php
│   │   ├── CustomerController.php
│   │   ├── ProductController.php
│   │   ├── PromotionController.php
│   │   ├── UnitController.php
│   │   └── VendorController.php
│   ├── Production/
│   │   ├── FormulaController.php
│   │   └── ProductionOrderController.php
│   ├── Purchasing/
│   │   ├── GoodsReceiptController.php
│   │   ├── PurchaseOrderController.php
│   │   └── VendorPaymentController.php
│   ├── Reports/ReportController.php
│   ├── Sales/
│   │   ├── PosController.php
│   │   ├── SalesInvoiceController.php
│   │   └── SalesReturnController.php
│   └── Settings/
│       ├── SettingsController.php
│       └── UserController.php
├── Models/ [29 Eloquent Models covering all domains]
└── Services/
    ├── Inventory/InventoryService.php
    ├── Notifications/NotificationService.php
    ├── Production/ProductionService.php
    ├── Purchasing/PurchasingService.php
    ├── Sales/
    │   ├── InvoicePostingService.php
    │   └── PromotionCalculationService.php
    └── Tax/GstCalculationService.php

docs/
├── requirements.md
├── database-design.md
└── implementation-plan.md
```
