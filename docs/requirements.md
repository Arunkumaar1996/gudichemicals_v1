# Gudi Chemicals ERP — Requirements Specification

## 1. System Overview
**Gudi Chemicals ERP** is a dedicated enterprise resource planning system tailored for chemical manufacturing, batch-wise processing, bulk liquid preparation, packaging, trading (purchase & resale), multi-tier inventory, GST-compliant point of sale (POS) & B2B sales billing, formula/BOM management, quality control, customer/vendor account ledgers, operating expenses, and reporting.

The system is designed for single-company, single-location operations initially, structured cleanly with warehouse and location schemas to scale to multi-warehouse and multi-branch operations seamlessly in future releases.

---

## 2. Core Business Workflows

### 2.1 Manufacturing & Formula Management
1. **Bill of Materials (BOM) / Formulas**:
   - Master formulas with version control (v1, v2, etc.).
   - Standard output quantity, unit, expected yield, and process loss / wastage tolerance.
   - Ingredients list with specific item requirements and scaling factors.
   - Immutable snapshot linked to each production order/batch.
2. **Production Lifecycle**:
   - Planning: Order creation, scale ingredient requirements, check stock availability, warn on deficit.
   - Batch Initiation: Assign internal batch number, record start time, assign operator.
   - Consumption & Execution: Record actual ingredient consumption (FEFO/FIFO batch picking), record process losses.
   - Quality Control (QC): Test against predefined quality parameters (pH, specific gravity, viscosity, purity), accept/quarantine/reject.
   - Packaging / Bottling: Either direct preparation & pack or intermediate bulk liquid -> separate bottling (consuming bulk liquid, bottles, caps, labels, cartons) -> finished goods.
   - Finalization: Atomic stock deduction of raw materials & packaging, atomic credit of finished goods, production cost computation.

### 2.2 Inventory Management & Invariants
1. **Multi-Type Item Master**:
   - Raw Materials (acids, solvents, water, pigments).
   - Semi-Finished Products (bulk unbottled liquid/intermediates).
   - Finished Products (bottled & packaged chemical products).
   - Packaging Materials (bottles, caps, seals, labels, cartons).
   - Trading Products (finished goods bought and resold directly).
2. **Stock Invariants**:
   - Immutable stock movement ledger (`stock_movements`) tracking every debit and credit with source document references.
   - Transactional balance projection (`stock_balances`) updated strictly within atomic database transactions with row-level locks.
   - Batch-level tracking (`inventory_batches`) with manufacturing date, expiry date, retest date, and supplier lot numbers.
   - No negative stock by default. Controlled adjustments require reason codes and supervisor authorization.
   - Moving average / FIFO inventory valuation.

### 2.3 Purchasing & Vendor Management
1. Vendor registration with GSTIN, PAN, payment terms, and opening balance.
2. Purchase Workflow:
   - Purchase Order (PO) -> Approval -> Goods Receipt Note (GRN) -> Supplier Invoice -> Payment.
   - Direct purchase option when formal PO is not required.
3. Invariant: Stock increases **only** upon Goods Receipt posting, never upon PO creation. Supplier invoice entries reconcile against GRNs without double-counting inventory.

### 2.4 High-Speed POS & GST Sales Billing
1. **POS & Billing Desk**:
   - Barcode scanning & instant SKU search.
   - Walk-in / Retail customers and registered B2B wholesale accounts.
   - Retail Selling Price (RSP) and Wholesale Selling Price (WSP) tiers with minimum selling price safety guards.
   - Single-screen keyboard-friendly billing with split payments (Cash, UPI, Card, Bank Transfer, Customer Credit).
2. **Indian GST Compliance Engine**:
   - HSN classification per item with configurable GST rates (0%, 5%, 12%, 18%, 28%).
   - Intra-state transactions: equal split into CGST and SGST.
   - Inter-state transactions: IGST.
   - Documented rounding rules (half-up at invoice total or line item level as configured).
   - Safe sequential financial year invoice numbering (`GC/2026-27/0001`) with database locks preventing gaps and concurrency collision.
   - Full tax breakdown snapshots preserved immutably per line item.
3. **Sales Returns & Credit Notes**:
   - Original invoice reference validation.
   - Segregation of returned stock into saleable vs. damaged/quarantined.
   - Adjustment to customer ledger or refund.

### 2.5 Promotional Engine
1. Server-side authoritative evaluation.
2. Supported types:
   - Buy 1 Get 1 Free (BOGO), Buy 2 Get 1 Free (B2G1), Buy X Get Y.
   - Percentage discounts and flat-rate invoice discounts.
   - Pre-packaged Combo SKUs vs. dynamic cart promotion rules.
   - Quantity/slab discounts for wholesale volumes.

### 2.6 Customer & Vendor Ledgers & Accounting
1. Customer balance tracking: invoices increase receivables, payments/credit notes reduce receivables.
2. Vendor balance tracking: supplier bills increase payables, payments/debit notes reduce payables.
3. Operating expenses by category (utilities, freight, factory overheads, salaries).
4. Real gross margin calculation based on actual Cost of Goods Sold (COGS) rather than raw cash flows.

### 2.7 Notifications & Invoicing Sharing
1. WhatsApp invoice delivery via official Cloud API / approved webhook provider with mock fallback.
2. Email invoice PDF delivery via Laravel Mail (configurable SMTP).
3. Delivery logs and retry mechanics queued asynchronously after invoice commit.

---

## 3. Non-Functional Requirements
- **Security**: Robust CSRF protection, SQL injection prevention via Eloquent/PDO bindings, session fixation protection, strict RBAC authorization policies.
- **Performance**: Sub-200ms POS response time, server-side pagination on all lists, debounced searches, eager loading of relations to prevent N+1 queries.
- **Reliability & Concurrency**: Pessimistic row locking (`lockForUpdate`) on inventory balances and document sequence generators.
- **Auditability**: Complete audit trails for stock adjustments, price overrides, invoice cancellations, and user activity.
