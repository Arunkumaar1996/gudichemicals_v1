# Gudi Chemicals ERP — Implementation Plan & System Invariants

## 1. System Invariants (Non-Negotiable Rules)

### 1.1 Inventory Invariants
1. **Append-Only Ledger**: All stock level alterations must pass through `InventoryService::recordMovement()`. Direct raw edits to stock balance columns without movement log entries are strictly prohibited.
2. **Pessimistic Concurrency**: Any inventory deduction must acquire a `lockForUpdate()` on the `stock_balances` row inside an atomic database transaction.
3. **Non-Negative Stock Default**: Inventory deductions that exceed available balance throw `InsufficientStockException`.
4. **Controlled Adjustments**: Administrative adjustments require a dedicated adjustment document with reason codes and supervisor audit identity.

### 1.2 Production Invariants
1. **Formula Immutability**: Production orders freeze a snapshot of the formula version at batch initiation time. Future edits to the formula do not distort historic batches.
2. **Batch Traceability**: Every output lot is directly linked to the specific consumed ingredient batches and supplier lot records.
3. **Double-Finalization Guard**: Production orders can only be finalized once, protected by database transactions and state check assertions.

### 1.3 Billing & GST Invariants
1. **Server-Side Authoritative Math**: The browser client displays real-time estimates via AJAX, but the server completely recalculates taxable values, discounts, HSN rates, CGST/SGST/IGST, and round-offs upon invoice posting.
2. **Sequential Gapless Invoice Numbers**: Number generation uses atomic database increments on `document_sequences` with row locking (`lockForUpdate()`) to guarantee uniqueness and sequential integrity across concurrent POS cashiers.
3. **Permanent Audit Trail**: Posted invoices cannot be modified or silently deleted. Reversals must be executed via controlled Sales Returns or Credit Notes.
4. **Decoupled Notification**: Invoice commitment must succeed even if WhatsApp/Email delivery fails. Deliveries are queued after transaction commit.

---

## 2. Phased Roadmap

### Phase 1: Foundation & Project Setup
- Install Laravel 12 with PHP 8.2 compatibility in current workspace.
- Setup SQLite for ultra-fast, robust automated testing and MySQL for production.
- Install Spatie Laravel Permission, Bootstrap 5 UI assets, and SweetAlert2 / Toastr.
- Create Company Settings, Warehouses, Units, and Categories master tables & seeders.
- Build clean layout with collapsible sidebar, top navigation, Gudi Chemicals branding, and reusable Blade components.
- Implement Authentication (login, logout, profile) and Role-Based Access Control.

### Phase 2: Product & Inventory Foundation
- Products/Item Master with dual pricing (Retail / Wholesale), HSN, GST rate, item type (raw chemical, semi-finished, finished, packaging, trading).
- Inventory Batches and Stock Balances.
- Dedicated `InventoryService` with atomic transactions and movement logs.
- Opening stock entry module.
- Low-stock alerts and inventory ledger views.

### Phase 3: Purchasing & Vendor Management
- Vendor master with GSTIN and payment terms.
- Purchase Orders (draft, approve, cancel).
- Goods Receipt Notes (GRN) with batch tracking (mfg date, expiry, lot numbers).
- Invariant verification: Stock increments only on GRN posting.
- Supplier invoices and vendor payment ledgers.

### Phase 4: Production & Formula Management
- Formulas / Bill of Materials (BOM) with versioning and expected yield / process loss.
- Production order planning with automatic ingredient requirement scaling.
- Batch execution, ingredient consumption tracking, and quality control (QC) checks.
- Finalization: atomic deduction of raw materials, addition of finished chemical stock, and unit production cost calculation.
- Packaging workflow (bulk liquid + bottles + caps + labels -> finished goods).

### Phase 5: High-Speed POS & GST Sales Billing
- POS desk with barcode scanner support, debounced instant search, keyboard shortcuts.
- Dynamic cart with Retail vs Wholesale price tier selection.
- Full Indian GST calculation service (CGST, SGST, IGST, tax rounding, HSN tracking).
- Multi-mode split payment collection (Cash, UPI, Card, Credit).
- Sequential invoice numbering engine with concurrency lock.
- Thermal receipt & standard A4 GST tax invoice print / PDF view.
- Sales returns and credit notes with saleable vs damaged stock classification.

### Phase 6: Promotional Engine & Notifications
- Promotion service supporting BOGO, B2G1, combo packs, and slab discounts.
- Asynchronous notification queue for email and WhatsApp sharing.
- Provider interface with mock provider for local/testing environments.

### Phase 7: Accounting, Ledgers & Reports
- Customer and vendor account ledgers.
- Operating expense tracker by category.
- Comprehensive reports: Daily Sales, GST Sales Summary (GSTR-1 format), Stock Ledger, Production Yield & Costing, Inventory Valuation.

### Phase 8: Hardening, Automated Testing & Verification
- Unit & Feature tests covering:
  - Inventory concurrency & non-negative guard.
  - GST math (intra-state vs inter-state, mixed tax rates).
  - Production consumption and stock deduction.
  - Sequential invoice numbering.
- Security and responsive UI verification.
