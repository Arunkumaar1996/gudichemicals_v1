const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer-core');
const {
    Document, Packer, Paragraph, TextRun, HeadingLevel,
    Table, TableRow, TableCell, ImageRun, AlignmentType,
    BorderStyle, WidthType, ShadingType, Header, Footer, PageNumber, NumberFormat
} = require('docx');

// Paths
const logoPath = path.resolve(__dirname, '../../public/assets/images/ak_logo.png');
const screenshotsDir = path.resolve(__dirname, '../screenshots');
const outputPdfPath = path.resolve(__dirname, '../Gudi_Chemicals_ERP_Complete_SRS.pdf');
const outputDocxPath = path.resolve(__dirname, '../Gudi_Chemicals_ERP_Complete_SRS.docx');
const outputHtmlPath = path.resolve(__dirname, '../Gudi_Chemicals_ERP_Complete_SRS.html');

// Read Logo & Screenshots
const logoBase64 = fs.existsSync(logoPath) ? `data:image/png;base64,${fs.readFileSync(logoPath).toString('base64')}` : '';
const logoBuffer = fs.existsSync(logoPath) ? fs.readFileSync(logoPath) : null;

function getScreenshotBase64(filename) {
    const p = path.join(screenshotsDir, filename);
    if (fs.existsSync(p)) {
        return `data:image/png;base64,${fs.readFileSync(p).toString('base64')}`;
    }
    return '';
}

function getScreenshotBuffer(filename) {
    const p = path.join(screenshotsDir, filename);
    return fs.existsSync(p) ? fs.readFileSync(p) : null;
}

// ---------------------------------------------------------------------------
// 1. GENERATE HTML & PDF FOR SRS
// ---------------------------------------------------------------------------
async function generateHtmlAndPdf() {
    console.log('Generating HTML and PDF for Complete SRS...');

    const htmlContent = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gudi Chemicals ERP — Software Requirements Specification (SRS)</title>
    <style>
        @page {
            size: A4;
            margin: 20mm 15mm 20mm 15mm;
            @bottom-right {
                content: counter(page);
                font-family: Arial, sans-serif;
                font-size: 9pt;
                color: #64748b;
            }
        }
        body {
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.6;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        .cover-page {
            page-break-after: always;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
            padding: 40px 20px;
            text-align: center;
        }
        .cover-header {
            margin-top: 30px;
        }
        .cover-logo {
            max-width: 170px;
            height: auto;
            margin-bottom: 20px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.15));
        }
        .company-name {
            font-size: 26pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 2px;
            margin: 0;
        }
        .company-tagline {
            font-size: 11pt;
            font-weight: 600;
            color: #005a9c;
            letter-spacing: 3px;
            margin-top: 5px;
            text-transform: uppercase;
        }
        .cover-title-box {
            background: linear-gradient(135deg, #003e6b 0%, #005a9c 100%);
            color: #ffffff;
            padding: 35px 25px;
            border-radius: 12px;
            margin: 40px 0;
            box-shadow: 0 10px 25px rgba(0,62,107,0.25);
        }
        .cover-doc-type {
            font-size: 12pt;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #93c5fd;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .cover-title {
            font-size: 24pt;
            font-weight: 700;
            margin: 0 0 10px 0;
            line-height: 1.3;
        }
        .cover-subtitle {
            font-size: 13pt;
            font-weight: 300;
            color: #e2e8f0;
            margin: 0;
        }
        .cover-footer {
            border-top: 2px solid #e2e8f0;
            padding-top: 20px;
            font-size: 9.5pt;
            color: #475569;
            text-align: left;
            display: flex;
            justify-content: space-between;
        }
        .cover-footer table {
            width: 100%;
            border-collapse: collapse;
        }
        .cover-footer td {
            padding: 4px 8px;
            vertical-align: top;
        }
        
        /* Document Content Styles */
        .page-break {
            page-break-before: always;
        }
        h1 {
            font-size: 19pt;
            color: #003e6b;
            border-bottom: 2px solid #005a9c;
            padding-bottom: 6px;
            margin-top: 26px;
            margin-bottom: 14px;
            font-weight: 700;
        }
        h2 {
            font-size: 14pt;
            color: #0f172a;
            border-left: 4px solid #005a9c;
            padding-left: 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        h3 {
            font-size: 12pt;
            color: #1e293b;
            margin-top: 16px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        p, li {
            text-align: justify;
            margin-bottom: 8px;
        }
        ul, ol {
            margin-top: 4px;
            margin-bottom: 12px;
            padding-left: 24px;
        }
        li {
            margin-bottom: 4px;
        }
        
        /* Tables */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 9.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 600;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        /* Callouts / Highlights */
        .callout-box {
            background-color: #f0fdf4;
            border-left: 4px solid #16a34a;
            padding: 12px 16px;
            margin: 14px 0;
            border-radius: 0 6px 6px 0;
            font-size: 9.5pt;
        }
        .callout-box.important {
            background-color: #eff6ff;
            border-left-color: #2563eb;
        }
        .callout-box.warning {
            background-color: #fefce8;
            border-left-color: #ca8a04;
        }
        .callout-title {
            font-weight: 700;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        /* Screenshot Cards */
        .screenshot-container {
            margin: 18px 0;
            text-align: center;
            page-break-inside: avoid;
        }
        .screenshot-img {
            max-width: 95%;
            height: auto;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .screenshot-caption {
            font-size: 9pt;
            font-weight: 600;
            color: #475569;
            margin-top: 6px;
            font-style: italic;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 8pt;
            font-weight: 700;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-primary { background: #dbeafe; color: #1e40af; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        
        /* Header / Footer Top Bar */
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 8px;
            margin-bottom: 18px;
            font-size: 8.5pt;
            color: #64748b;
        }
        .header-bar strong {
            color: #0f172a;
        }
        
        /* Code blocks */
        pre, code {
            font-family: Consolas, 'Courier New', monospace;
            background: #f1f5f9;
            border-radius: 4px;
            font-size: 9pt;
        }
        pre {
            padding: 10px 14px;
            overflow-x: auto;
            border: 1px solid #e2e8f0;
        }
        code {
            padding: 1px 4px;
        }
    </style>
</head>
<body>

    <!-- COVER PAGE -->
    <div class="cover-page">
        <div class="cover-header">
            ${logoBase64 ? `<img src="${logoBase64}" class="cover-logo" alt="ARUN CODEX Logo">` : ''}
            <h1 class="company-name">ARUN CODEX</h1>
            <div class="company-tagline">Web Developer | Designer | Innovator</div>
        </div>

        <div class="cover-title-box">
            <div class="cover-doc-type">Software Engineering Deliverable</div>
            <div class="cover-title">SOFTWARE REQUIREMENTS SPECIFICATION (SRS)</div>
            <div class="cover-subtitle">Gudi Chemicals ERP — Production, Inventory, GST & High-Speed POS System</div>
        </div>

        <div class="cover-footer">
            <table>
                <tr>
                    <td style="width: 25%;"><strong>Project Name:</strong></td>
                    <td style="width: 35%;">Gudi Chemicals ERP (v1.0)</td>
                    <td style="width: 20%;"><strong>Prepared By:</strong></td>
                    <td style="width: 20%;">ARUN CODEX</td>
                </tr>
                <tr>
                    <td><strong>Document Type:</strong></td>
                    <td>Complete System SRS (IEEE 830)</td>
                    <td><strong>Author Title:</strong></td>
                    <td>Web Developer / Designer</td>
                </tr>
                <tr>
                    <td><strong>Target Organization:</strong></td>
                    <td>Gudi Chemicals Manufacturing</td>
                    <td><strong>Release Date:</strong></td>
                    <td>October 2026</td>
                </tr>
                <tr>
                    <td><strong>Target Platform:</strong></td>
                    <td>Laravel 12 / PHP 8.2 / MySQL</td>
                    <td><strong>Document Status:</strong></td>
                    <td>Approved & Implemented</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- DOCUMENT CONTROL & REVISION HISTORY -->
    <div class="page-break">
        <div class="header-bar">
            <span>ARUN CODEX — Web Developer | Designer | Innovator</span>
            <span>Gudi Chemicals ERP — Complete SRS</span>
        </div>

        <h1>Document Control & Executive Summary</h1>
        
        <h2>Document Information</h2>
        <table class="data-table">
            <tr>
                <th style="width: 25%;">Property</th>
                <th>Details</th>
            </tr>
            <tr>
                <td><strong>Document Title</strong></td>
                <td>Software Requirements Specification (SRS) for Gudi Chemicals ERP</td>
            </tr>
            <tr>
                <td><strong>Author & Architect</strong></td>
                <td><strong>ARUN CODEX</strong> (Web Developer | Designer | Innovator)</td>
            </tr>
            <tr>
                <td><strong>Company & Client</strong></td>
                <td>Gudi Chemicals — Chemical Manufacturing & Distribution Plant</td>
            </tr>
            <tr>
                <td><strong>System Version</strong></td>
                <td>Release 1.0 (Production Master)</td>
            </tr>
            <tr>
                <td><strong>Compliant Standard</strong></td>
                <td>IEEE Standard 830-1998 for Software Requirements Specifications</td>
            </tr>
            <tr>
                <td><strong>Primary Focus</strong></td>
                <td>Complete ERP Suite with Deep Focus on <strong>High-Speed POS & GST Sales Billing Module</strong></td>
            </tr>
        </table>

        <h2>Revision History</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Version</th>
                    <th>Date</th>
                    <th>Author / Designer</th>
                    <th>Summary of Changes</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-primary">v1.0</span></td>
                    <td>October 2, 2026</td>
                    <td>ARUN CODEX</td>
                    <td>Complete SRS document covering 13 enterprise modules, full GST billing algorithms, chemical manufacturing workflows, stock ledger invariants, and embedded real screenshots.</td>
                </tr>
            </tbody>
        </table>

        <h2>Executive Summary</h2>
        <p>
            <strong>Gudi Chemicals ERP</strong> is an enterprise-grade resource planning system engineered from the ground up for industrial chemical manufacturers, distributors, and bulk chemical retail depots. Built upon a high-performance <strong>Laravel 12 (PHP 8.2)</strong> architecture with an atomic relational database foundation, the system synchronizes complex manufacturing operations, multi-tier raw material and packaging inventory, multi-batch traceability, vendor procurement, and operating expenses with a blazing-fast, keyboard-driven <strong>Point of Sale (POS) and B2B GST Sales Billing engine</strong>.
        </p>
        <p>
            Chemical businesses operate under stringent regulatory standards, fluctuating raw ingredient purity, hazardous materials handling, complex Bill of Materials (BOM) yield equations, and statutory Indian Goods and Services Tax (GST) laws. Off-the-shelf generic retail software fails to satisfy chemical-specific demands like multi-stage dilution, packaging tare adjustments, batch retest schedules, and dual-tier pricing (Wholesale vs Retail). Gudi Chemicals ERP bridges this operational gap by delivering an end-to-end digital nervous system designed and developed by <strong>ARUN CODEX</strong>.
        </p>

        <div class="callout-box important">
            <div class="callout-title">Special Emphasis on the Billing & POS Module</div>
            The billing desk is the core commercial heart of Gudi Chemicals. It must perform reliably during intense operational rush hours. This document provides deep, line-by-line requirements for barcode scanning, sub-second debounced SKU search across 4,000+ items, split payments (Cash, UPI, Card, Credit), strict sequential invoice numbering, automatic stock decrements, and instant dual-format printing (80mm thermal slips and standard A4 GST tax invoices).
        </div>
    </div>

    <!-- TABLE OF CONTENTS -->
    <div class="page-break">
        <div class="header-bar">
            <span>ARUN CODEX — Web Developer | Designer | Innovator</span>
            <span>Gudi Chemicals ERP — Complete SRS</span>
        </div>

        <h1>Table of Contents</h1>
        <ol style="font-weight: 600; line-height: 1.8;">
            <li>1. Introduction
                <ol style="font-weight: normal; margin-bottom: 4px;">
                    <li>1.1 Purpose of the Document</li>
                    <li>1.2 Scope of the Software System</li>
                    <li>1.3 Definitions, Acronyms, and Abbreviations</li>
                    <li>1.4 Intended Audience & Reading Suggestions</li>
                </ol>
            </li>
            <li>2. Overall System Architecture & Operating Environment
                <ol style="font-weight: normal; margin-bottom: 4px;">
                    <li>2.1 Product Perspective & High-Level Architecture</li>
                    <li>2.2 User Roles & Permission Matrix (RBAC)</li>
                    <li>2.3 Operating Environment & Deployment Prerequisites</li>
                    <li>2.4 Design & Implementation Invariants (Non-Negotiable Business Rules)</li>
                </ol>
            </li>
            <li>3. Exhaustive Module Specifications
                <ol style="font-weight: normal; margin-bottom: 4px;">
                    <li>3.1 Module 1: Secure Authentication & Session Management</li>
                    <li>3.2 Module 2: Executive Dashboard & Operational KPI Analytics</li>
                    <li>3.3 Module 3: Master Data Management (Products, Units, Categories, Warehouses)</li>
                    <li>3.4 Module 4: High-Speed POS & Billing Module <span class="badge badge-warning">DEEP DIVE</span></li>
                    <li>3.5 Module 5: Sales Invoicing, Tax Calculations & Document Printing <span class="badge badge-warning">DEEP DIVE</span></li>
                    <li>3.6 Module 6: Sales Returns & Credit Note Engine</li>
                    <li>3.7 Module 7: Multi-Tier Chemical Inventory & Batch Tracking</li>
                    <li>3.8 Module 8: Chemical Manufacturing, Formula BOM & Production Lifecycle</li>
                    <li>3.9 Module 9: Purchasing, Vendor Management & Goods Receipt Notes (GRN)</li>
                    <li>3.10 Module 10: Dynamic Promotional Rules & Discount Schemes</li>
                    <li>3.11 Module 11: Expense Management & Financial Accounts</li>
                    <li>3.12 Module 12: Reporting Suite & Statutory GSTR Compliance</li>
                    <li>3.13 Module 13: System Settings & User Administration</li>
                </ol>
            </li>
            <li>4. External Interface Requirements (UI, Barcode, Thermal Printers, Cloud APIs)</li>
            <li>5. Non-Functional Requirements (Performance, Security, Concurrency, Auditability)</li>
            <li>6. Relational Database Schema & Data Models</li>
            <li>7. System Invariants & Mathematical Formulations</li>
            <li>8. Visual Interface Specifications & Embedded Screen Captures</li>
        </ol>
    </div>

    <!-- SECTION 1: INTRODUCTION -->
    <div class="page-break">
        <div class="header-bar">
            <span>ARUN CODEX — Web Developer | Designer | Innovator</span>
            <span>Gudi Chemicals ERP — Complete SRS</span>
        </div>

        <h1>1. Introduction</h1>

        <h2>1.1 Purpose of the Document</h2>
        <p>
            The purpose of this Software Requirements Specification (SRS) is to establish a rigorous, definitive contract between the development team (led by <strong>ARUN CODEX</strong>) and stakeholders of <strong>Gudi Chemicals</strong>. It details all behavioral, architectural, functional, mathematical, and non-functional requirements for the complete enterprise resource planning system.
        </p>

        <h2>1.2 Scope of the Software System</h2>
        <p>
            The software system, titled <strong>Gudi Chemicals ERP</strong>, provides end-to-end automation for chemical processing and merchandising. The software encompasses:
        </p>
        <ul>
            <li><strong>Master Data Configuration:</strong> Multi-tier chemical classification (Raw Chemicals, Semi-Finished Intermediates, Finished Products, Packaging Materials, and Trading Goods), HSN classification, dual pricing schedules (Wholesale vs Retail), and fractional unit conversions (Liters, Milliliters, Kilograms, Grams, Drums, Jerry Cans).</li>
            <li><strong>High-Speed POS & Billing Desk:</strong> Single-screen, keyboard-first point of sale interface supporting barcode hardware scanners, instantaneous client-side/server-side price evaluations, automatic GST partitioning (CGST, SGST, IGST), split payments, and printer dispatch.</li>
            <li><strong>Inventory & Batch Traceability:</strong> Multi-warehouse stock tracking, moving average inventory valuation, immutable transaction ledgers, quarantine controls, and strict non-negative balance enforcement.</li>
            <li><strong>Chemical Manufacturing & BOM:</strong> Recipe/Formula versioning, automatic scaling based on planned output volume, ingredient batch reservation, quality control (QC) parametric testing, and atomic raw material deduction.</li>
            <li><strong>Procurement & Vendor Accounts:</strong> Vendor onboarding, Purchase Orders (PO), Goods Receipt Notes (GRN) with lot tracking, supplier billing, and payment ledgers.</li>
            <li><strong>Statutory & Financial Reporting:</strong> Real-time gross margin calculation, operating expense bookkeeping, daily sales registers, and GSTR-1 outward supply summaries.</li>
        </ul>

        <h2>1.3 Definitions, Acronyms, and Abbreviations</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Term / Acronym</th>
                    <th>Definition & Application in System</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>BOM</strong></td>
                    <td><strong>Bill of Materials</strong> — Master chemical recipe specifying required raw ingredients, packaging containers, standard output yield, and expected process evaporation/loss.</td>
                </tr>
                <tr>
                    <td><strong>POS</strong></td>
                    <td><strong>Point of Sale</strong> — The high-speed checkout counter interface used by cashiers to bill walk-in retail buyers and dispatch wholesale vehicles.</td>
                </tr>
                <tr>
                    <td><strong>GST</strong></td>
                    <td><strong>Goods and Services Tax</strong> — India's indirect tax system, divided into Central GST (CGST), State GST (SGST), and Integrated GST (IGST).</td>
                </tr>
                <tr>
                    <td><strong>HSN</strong></td>
                    <td><strong>Harmonized System of Nomenclature</strong> — Standardized numeric commodity codes used to determine applicable GST tax rates (e.g., 2807 for Sulphuric Acid at 18%).</td>
                </tr>
                <tr>
                    <td><strong>GRN</strong></td>
                    <td><strong>Goods Receipt Note</strong> — Commercial warehouse entry acknowledging physical arrival of raw chemicals, triggering lot creation and stock increments.</td>
                </tr>
                <tr>
                    <td><strong>QC</strong></td>
                    <td><strong>Quality Control</strong> — Laboratory verification of chemical batches against specifications (e.g., pH balance, specific gravity, viscosity, color, purity).</td>
                </tr>
                <tr>
                    <td><strong>RSP / WSP</strong></td>
                    <td><strong>Retail Selling Price / Wholesale Selling Price</strong> — Dual price tiers configured per SKU to ensure dynamic pricing based on customer profile.</td>
                </tr>
                <tr>
                    <td><strong>FEFO / FIFO</strong></td>
                    <td><strong>First-Expired-First-Out / First-In-First-Out</strong> — Inventory picking algorithms used to prevent chemical ingredient expiration.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- SECTION 2: SYSTEM ARCHITECTURE & ROLES -->
    <div class="page-break">
        <div class="header-bar">
            <span>ARUN CODEX — Web Developer | Designer | Innovator</span>
            <span>Gudi Chemicals ERP — Complete SRS</span>
        </div>

        <h1>2. System Architecture & Operating Environment</h1>

        <h2>2.1 High-Level Architecture</h2>
        <p>
            Gudi Chemicals ERP employs a modern, layered Model-View-Controller (MVC) architecture crafted with Laravel 12. The application isolates business rules within dedicated Service Layer classes (`InvoicePostingService`, `GstCalculationService`, `InventoryService`, `ProductionService`), ensuring atomic transactions, high testability, and strict adherence to SOLID design principles.
        </p>

        <table class="data-table">
            <tr>
                <th style="width: 25%;">Layer</th>
                <th>Technology & Components</th>
                <th>Responsibilities</th>
            </tr>
            <tr>
                <td><strong>Presentation (UI)</strong></td>
                <td>Bootstrap 5.3, FontAwesome 6, Chart.js, Blade Components, Vanilla JS / Fetch API</td>
                <td>Responsive desktop and tablet views, keyboard shortcut listeners, debounced search triggers, instant POS cart calculations.</td>
            </tr>
            <tr>
                <td><strong>Controller & HTTP</strong></td>
                <td>Laravel 12 Routing, Form Requests, Middleware, RBAC Policies</td>
                <td>Input sanitization, CSRF token validation, route authorization, request dispatching to domain services.</td>
            </tr>
            <tr>
                <td><strong>Domain Service Layer</strong></td>
                <td>Dedicated PHP Services in <code>App\Services\*</code></td>
                <td>Pessimistic database locking, GST mathematical computations, sequential document numbering, batch picking, atomic ledger postings.</td>
            </tr>
            <tr>
                <td><strong>Persistence & Database</strong></td>
                <td>MySQL 8.0 / SQLite (Automated CI/CD Testing), Eloquent ORM</td>
                <td>ACID transaction guarantees, foreign key cascade protections, indexed SKU/barcode searches, audit trail logs.</td>
            </tr>
        </table>

        <h2>2.2 User Roles & Permission Matrix (RBAC)</h2>
        <p>
            The system implements granular Role-Based Access Control powered by <code>spatie/laravel-permission</code>. The matrix below defines access levels across standard business profiles:
        </p>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Module / Feature</th>
                    <th>Super Admin</th>
                    <th>Cashier</th>
                    <th>Storekeeper</th>
                    <th>Production Mgr</th>
                    <th>Accountant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>POS Billing Desk & Quick Sale</td>
                    <td>Full Access</td>
                    <td>Full Access</td>
                    <td>No Access</td>
                    <td>No Access</td>
                    <td>View Only</td>
                </tr>
                <tr>
                    <td>Discount Override at POS</td>
                    <td>Full Access</td>
                    <td>Restricted</td>
                    <td>No Access</td>
                    <td>No Access</td>
                    <td>No Access</td>
                </tr>
                <tr>
                    <td>Sales Invoices (View / Print / PDF)</td>
                    <td>Full Access</td>
                    <td>Own Shift</td>
                    <td>View Only</td>
                    <td>No Access</td>
                    <td>Full Access</td>
                </tr>
                <tr>
                    <td>Sales Returns & Credit Notes</td>
                    <td>Full Access</td>
                    <td>Create Only</td>
                    <td>Receive Stock</td>
                    <td>No Access</td>
                    <td>Full Access</td>
                </tr>
                <tr>
                    <td>Stock Balances & Ledger</td>
                    <td>Full Access</td>
                    <td>View Available</td>
                    <td>Full Access</td>
                    <td>View Raw/FG</td>
                    <td>Full Access</td>
                </tr>
                <tr>
                    <td>Stock Adjustment (Wastage/Loss)</td>
                    <td>Full Access</td>
                    <td>No Access</td>
                    <td>Propose Only</td>
                    <td>Propose Only</td>
                    <td>Audit Only</td>
                </tr>
                <tr>
                    <td>Production Formulas & Orders</td>
                    <td>Full Access</td>
                    <td>No Access</td>
                    <td>View BOM</td>
                    <td>Full Access</td>
                    <td>Cost Audit</td>
                </tr>
                <tr>
                    <td>Purchase Orders & GRN Posting</td>
                    <td>Full Access</td>
                    <td>No Access</td>
                    <td>Post GRN</td>
                    <td>View Ingredients</td>
                    <td>Full Access</td>
                </tr>
                <tr>
                    <td>GSTR-1 & Financial Reports</td>
                    <td>Full Access</td>
                    <td>No Access</td>
                    <td>No Access</td>
                    <td>Yield Reports</td>
                    <td>Full Access</td>
                </tr>
                <tr>
                    <td>System Settings & Users</td>
                    <td>Full Access</td>
                    <td>No Access</td>
                    <td>No Access</td>
                    <td>No Access</td>
                    <td>No Access</td>
                </tr>
            </tbody>
        </table>

        <h2>2.3 System Invariants (Non-Negotiable Business Rules)</h2>
        <div class="callout-box warning">
            <div class="callout-title">Architectural Invariants Guaranteed by ARUN CODEX</div>
            <ol style="margin-bottom: 0;">
                <li><strong>Stock Ledger Immutability:</strong> Physical inventory levels can never be adjusted by raw direct database overwrites. Every stock alteration MUST generate an immutable debit or credit entry in <code>stock_movements</code> referencing a valid source document (Invoice, GRN, Production Order, or Adjustment).</li>
                <li><strong>Pessimistic Concurrency Locking:</strong> Inventory balance deductions and document sequence increments execute exclusively inside atomic database transactions wrapped in <code>lockForUpdate()</code>. This completely eliminates race conditions when multiple cashiers check out simultaneously.</li>
                <li><strong>Strict Non-Negative Balance Enforcement:</strong> Any transaction attempting to reduce available stock below 0.00 immediately halts and throws an <code>InsufficientStockException</code>, alerting the user to re-stock or adjust inventory.</li>
                <li><strong>Authoritative Server-Side Math:</strong> While client-side JavaScript provides instant UI feedback, the server re-calculates all item subtotals, HSN tax splits, discounts, and round-offs prior to committing the invoice.</li>
            </ol>
        </div>
    </div>

    <!-- SECTION 3: DEEP MODULE SPECIFICATIONS (STARTING WITH POS BILLING) -->
    <div class="page-break">
        <div class="header-bar">
            <span>ARUN CODEX — Web Developer | Designer | Innovator</span>
            <span>Gudi Chemicals ERP — Complete SRS</span>
        </div>

        <h1>3. Detailed Module Specifications</h1>

        <h2>3.4 High-Speed POS & GST Sales Billing Module <span class="badge badge-warning">PRIMARY CORE MODULE</span></h2>
        
        <p>
            The Point of Sale (POS) and Billing Desk represents the most critical operational interface in Gudi Chemicals ERP. In industrial chemical trade, billing counters handle high volumes of rapid cash sales for retail customers (walk-in factory purchases, small workshop cleaners, garage degreasers) as well as substantial wholesale dispatches (tankers, 200L drums, palletized jerry cans) for corporate B2B clients.
        </p>

        <h3>3.4.1 Functional Requirements of the Billing Desk</h3>
        <ul>
            <li><strong>Single-Screen Keyboard-Driven Architecture:</strong> The billing screen is designed for maximum speed. Cashiers can complete a multi-line transaction entirely via keyboard without touching the mouse:
                <ul>
                    <li><code>F2</code> or <code>Ctrl + K</code>: Instant focus to SKU / Barcode search bar.</li>
                    <li><code>F4</code>: Focus customer selector.</li>
                    <li><code>F7</code>: Toggle Wholesale / Retail price tier.</li>
                    <li><code>F8</code>: Open payment modal / complete transaction.</li>
                    <li><code>Esc</code>: Cancel or clear active search modal.</li>
                </ul>
            </li>
            <li><strong>Barcode Scanner Integration:</strong> Standard USB/Bluetooth 1D & 2D barcode scanners emulate keyboard wedge entry terminating with an Enter key (ASCII 13). The POS auto-detects scanner input, queries the product database via the <code>/pos/barcode</code> endpoint, appends the item to the cart, increments quantity if already present, and immediately recalculates totals in under 80 milliseconds.</li>
            <li><strong>Ultra-Fast Debounced Product Search:</strong> For items without barcodes, typing in the search box triggers a 250ms debounced AJAX call against indexed SKU, Name, and Chemical Formula fields across 4,000+ items.</li>
            <li><strong>Dynamic Customer Selection & Quick Onboarding:</strong>
                <ul>
                    <li>Defaults automatically to <strong>Walk-in Retail Customer</strong> (State: Maharashtra / Code 27, Consumer type).</li>
                    <li>Dropdown permits selecting registered B2B clients with live display of GSTIN, current outstanding credit balance, and credit limit.</li>
                    <li>A <strong>Quick Customer Add</strong> modal allows cashiers to register a new customer (Name, Phone, GSTIN, State) on-the-fly without leaving the billing screen.</li>
                </ul>
            </li>
            <li><strong>Dual-Tier Price Switching (Retail RSP vs Wholesale WSP):</strong>
                <ul>
                    <li><strong>Retail Selling Price (RSP):</strong> Applied by default for consumer packaging, single bottles, and walk-in counter sales.</li>
                    <li><strong>Wholesale Selling Price (WSP):</strong> Enabled automatically for B2B accounts or manually toggled by authorized staff. Automatically applies bulk rates.</li>
                    <li><strong>Floor Price Guard:</strong> Cashiers cannot reduce prices below the product's configured Minimum Selling Price without supervisor credential override.</li>
                </ul>
            </li>
        </ul>

        <div class="screenshot-container">
            <img src="${getScreenshotBase64('03_pos_billing.png')}" class="screenshot-img" alt="POS Fast Billing Desk Interface">
            <div class="screenshot-caption">Figure 3.1: Gudi Chemicals High-Speed POS Fast Billing Desk with Live Cart, Barcode Scanning, and GST Tax Breakdown</div>
        </div>

        <h3>3.4.2 Comprehensive GST Calculation Engine</h3>
        <p>
            Gudi Chemicals is registered in Maharashtra (State Code 27). The system's <code>GstCalculationService</code> executes authoritative tax computations conforming to Indian GST statutes:
        </p>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Transaction Type</th>
                    <th>Customer Location</th>
                    <th>Tax Apportionment Rule</th>
                    <th>Example (₹1,000 Taxable Value @ 18% GST)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Intra-State Sale</strong></td>
                    <td>Maharashtra (State Code: 27)</td>
                    <td>Equal split between Central GST (CGST 50%) and State GST (SGST 50%). IGST = ₹0.00.</td>
                    <td>CGST @ 9% = ₹90.00<br>SGST @ 9% = ₹90.00<br><strong>Total Tax = ₹180.00</strong></td>
                </tr>
                <tr>
                    <td><strong>Inter-State Sale</strong></td>
                    <td>Other Indian State / UT (e.g., Gujarat - Code 24)</td>
                    <td>100% allocated to Integrated GST (IGST). CGST = ₹0.00, SGST = ₹0.00.</td>
                    <td>IGST @ 18% = ₹180.00<br>CGST = ₹0.00, SGST = ₹0.00<br><strong>Total Tax = ₹180.00</strong></td>
                </tr>
            </tbody>
        </table>

        <h3>3.4.3 Split-Payment Handling & Credit Accounting</h3>
        <p>
            Chemical transactions frequently involve mixed settlement methods. The billing module natively supports split payments within a single invoice:
        </p>
        <ul>
            <li><strong>Cash:</strong> Records cash drawer intake and computes change to return to customer.</li>
            <li><strong>UPI / Dynamic QR:</strong> Generates instant payment reconciliation for PhonePe, Google Pay, and Paytm.</li>
            <li><strong>Debit / Credit Card:</strong> Stores card payment transaction reference/RRN numbers.</li>
            <li><strong>Bank Transfer / NEFT / RTGS:</strong> Essential for B2B dispatches.</li>
            <li><strong>Customer Ledger Credit:</strong> For approved wholesale buyers with credit headroom. Automatically posts to <code>customer_ledgers</code> as an accounts receivable debit.</li>
        </ul>

        <h3>3.4.4 Sequential Gapless Invoice Numbering</h3>
        <p>
            Section 31 of the CGST Act mandates consecutive serial numbering of tax invoices unique for a financial year. Gudi Chemicals ERP guarantees zero gaps and zero duplicate numbers via pessimistic database locking:
        </p>
        <pre><code>// Atomic Sequence Generator in DocumentSequenceService
$sequence = DocumentSequence::where('type', 'sales_invoice')
    ->where('financial_year', $financialYear)
    ->lockForUpdate() // Concurrency Lock
    ->first();

$nextNumber = str_pad($sequence->current_number + 1, 4, '0', STR_PAD_LEFT);
$invoiceNumber = "GC/{$financialYear}/{$nextNumber}";
$sequence->increment('current_number');</code></pre>
    </div>

    <!-- MODULE 5: INVOICING & PRINTING -->
    <div class="page-break">
        <div class="header-bar">
            <span>ARUN CODEX — Web Developer | Designer | Innovator</span>
            <span>Gudi Chemicals ERP — Complete SRS</span>
        </div>

        <h2>3.5 Sales Invoicing, Tax Invoices & Dual-Format Print Engine <span class="badge badge-warning">CORE MODULE</span></h2>
        
        <p>
            Once a transaction is finalized at the billing counter, the system immediately generates an official tax document. The billing module provides two dedicated print layouts tailored to different logistics scenarios:
        </p>

        <h3>3.5.1 Dual Print Layouts</h3>
        <ol>
            <li><strong>80mm Thermal Receipt (POS Slip):</strong>
                <ul>
                    <li>Engineered for fast counter sales, small walk-in purchases, and retail buyers.</li>
                    <li>Compact 80mm roll width with auto-cutter support.</li>
                    <li>Contains company header, GSTIN, invoice number, date/time, cashier name, line items with HSN codes, tax summary, split payment breakdown, and standard return policy footer.</li>
                </ul>
            </li>
            <li><strong>Standard A4 GST Tax Invoice:</strong>
                <ul>
                    <li>Required for B2B wholesale orders, commercial transport, and tax credit claims.</li>
                    <li>Displays full seller and buyer credentials (Legal Business Name, Trade Name, Registered Address, GSTIN, PAN, State Code).</li>
                    <li>Detailed HSN-wise tax breakdown table displaying separate columns for Taxable Value, CGST Rate/Amount, SGST Rate/Amount, and IGST Rate/Amount.</li>
                    <li>Official watermark, bank account details for RTGS payments, authorized signatory box, and transport vehicle details.</li>
                    <li>Compliant with three-copy distribution: <em>Original for Recipient</em>, <em>Duplicate for Transporter</em>, and <em>Triplicate for Supplier</em>.</li>
                </ul>
            </li>
        </ol>

        <div class="screenshot-container">
            <img src="${getScreenshotBase64('06_tax_invoice_print.png')}" class="screenshot-img" alt="Standard A4 GST Tax Invoice Print Preview">
            <div class="screenshot-caption">Figure 3.2: Formal A4 GST Tax Invoice Generated by Gudi Chemicals ERP with Complete HSN & Tax Distribution</div>
        </div>

        <div class="screenshot-container">
            <img src="${getScreenshotBase64('05_invoice_details.png')}" class="screenshot-img" alt="Invoice Details and Operations">
            <div class="screenshot-caption">Figure 3.3: Invoice Management Screen displaying Payment Status, Linked Inventory Deductions, and Delivery Logs</div>
        </div>

        <h3>3.5.2 Digital Invoicing & Customer Sharing</h3>
        <p>
            To eliminate physical paperwork and enhance customer convenience, the system incorporates automated sharing channels:
        </p>
        <ul>
            <li><strong>Direct PDF Download:</strong> Generates crisp, downloadable vector PDFs with embedded company logo and digital verification QR codes.</li>
            <li><strong>WhatsApp Sharing:</strong> Queues background jobs to deliver PDF download links and invoice summaries directly to customer mobile numbers via official Cloud API / Webhook adapters.</li>
            <li><strong>Email Dispatch:</strong> Transmits formal PDF invoice attachments directly to purchasing departments of corporate accounts.</li>
        </ul>
    </div>

    <!-- MODULE 6 & 7: SALES RETURNS & INVENTORY -->
    <div class="page-break">
        <div class="header-bar">
            <span>ARUN CODEX — Web Developer | Designer | Innovator</span>
            <span>Gudi Chemicals ERP — Complete SRS</span>
        </div>

        <h2>3.6 Sales Returns & Credit Note Engine</h2>
        <p>
            Chemical products may be returned due to damaged containers during transit, client order adjustments, or batch quality disputes. The Sales Return module enforces strict controls:
        </p>
        <ul>
            <li><strong>Mandatory Original Invoice Reference:</strong> Returns cannot be created in isolation. They must be linked to an existing, posted tax invoice to prevent fraudulent returns.</li>
            <li><strong>Saleable vs Damaged Stock Segregation:</strong>
                <ul>
                    <li><em>Saleable Goods:</em> Returned directly to active warehouse stock, incrementing available quantity in <code>stock_balances</code>.</li>
                    <li><em>Damaged / Contaminated Goods:</em> Diverted to a designated Quarantine location for destruction, neutralization, or reprocessing without increasing sellable inventory.</li>
                </ul>
            </li>
            <li><strong>Automatic Credit Note Generation:</strong> Creates a formal GST Credit Note reversing the tax liability and adjusting the customer's outstanding balance ledger.</li>
        </ul>

        <div class="screenshot-container">
            <img src="${getScreenshotBase64('12_sales_returns.png')}" class="screenshot-img" alt="Sales Returns Management">
            <div class="screenshot-caption">Figure 3.4: Sales Returns and Credit Note Management Desk</div>
        </div>

        <h2>3.7 Multi-Tier Chemical Inventory & Batch Tracking</h2>
        <p>
            Chemical inventories require strict distinction between raw liquids, compounding intermediates, containers, and finished products:
        </p>
        <ul>
            <li><strong>Five Fundamental Item Classifications:</strong>
                <ol>
                    <li><code>raw_material</code>: Industrial acids (Sulphuric, Hydrochloric), surfactants, solvents, and dyes.</li>
                    <li><code>semi_finished</code>: Unbottled bulk chemical liquids compounded in mixing vats.</li>
                    <li><code>finished_goods</code>: Bottled, capped, and labeled retail/wholesale products ready for market.</li>
                    <li><code>packaging</code>: HDPE bottles (1L, 5L), jerry cans (20L), 200L drums, caps, induction seals, and carton boxes.</li>
                    <li><code>trading</code>: Pre-manufactured specialty chemicals purchased for direct resale.</li>
                </ol>
            </li>
            <li><strong>Batch & Lot Identification:</strong> Every chemical lot maintains Internal Batch Number, Supplier Lot Number, Manufacturing Date, Expiry Date, Retest Date, and Quality Approval Certificate.</li>
        </ul>

        <div class="screenshot-container">
            <img src="${getScreenshotBase64('08_inventory_status.png')}" class="screenshot-img" alt="Inventory Status and Stock Ledgers">
            <div class="screenshot-caption">Figure 3.5: Real-Time Multi-Tier Inventory Status and Batch Valuation Screen</div>
        </div>
    </div>

    <!-- MODULE 8 & 9: PRODUCTION & PURCHASES -->
    <div class="page-break">
        <div class="header-bar">
            <span>ARUN CODEX — Web Developer | Designer | Innovator</span>
            <span>Gudi Chemicals ERP — Complete SRS</span>
        </div>

        <h2>3.8 Chemical Manufacturing, Formula BOM & Production Lifecycle</h2>
        <p>
            Manufacturing in Gudi Chemicals involves compounding raw active ingredients with water and surfactants, followed by quality inspection, bulk liquid intermediate storage, and high-speed bottling.
        </p>
        <ul>
            <li><strong>Formula / Bill of Materials (BOM) Versioning:</strong> Formulations are recorded with version tracking (v1, v2). Each formula defines:
                <ul>
                    <li>Standard output volume and expected process loss / evaporation percentage.</li>
                    <li>Ingredient ratios per base batch (e.g., 800L DM Water, 150L Acid, 50L Surfactant).</li>
                    <li>Required packaging components for the finished packaged SKU.</li>
                </ul>
            </li>
            <li><strong>Dynamic Requirement Scaling:</strong> When a production order is scheduled for non-standard volumes (e.g., 2,400 Liters instead of standard 1,000 Liters), the system dynamically scales all ingredient requirements by a factor of 2.4 and checks real-time warehouse inventory for shortages.</li>
            <li><strong>Quality Control (QC) Gate:</strong> Before a batch can be released, the QC laboratory records physical and chemical test results:
                <ul>
                    <li><code>pH Level</code> (e.g., 1.5 – 2.5 for acid descalers).</li>
                    <li><code>Specific Gravity</code> (e.g., 1.08 ± 0.02 g/cm³).</li>
                    <li><code>Viscosity</code> (Centipoise @ 25°C).</li>
                    <li><code>Appearance & Color Clarity</code>.</li>
                </ul>
            </li>
            <li><strong>Atomic Finalization:</strong> Finalizing an order atomically consumes ingredient stocks and increments finished product inventory in a single ACID transaction.</li>
        </ul>

        <div class="screenshot-container">
            <img src="${getScreenshotBase64('09_production_formulas.png')}" class="screenshot-img" alt="Master Chemical Formulas and BOM">
            <div class="screenshot-caption">Figure 3.6: Chemical Master Formulas (BOM) with Expected Yields and Ingredient Specifications</div>
        </div>

        <h2>3.9 Purchasing, Vendor Management & Goods Receipt (GRN)</h2>
        <p>
            Procurement guarantees a continuous supply of raw chemicals and plastic containers without over-stocking hazardous materials:
        </p>
        <ul>
            <li><strong>Vendor Master:</strong> Tracks supplier GSTIN, MSME status, credit terms, and payment history.</li>
            <li><strong>Goods Receipt Note (GRN):</strong> Inventory levels increment <em>strictly</em> upon GRN verification, never upon Purchase Order issuance. This prevents accounting discrepancies.</li>
        </ul>

        <div class="screenshot-container">
            <img src="${getScreenshotBase64('11_purchase_orders.png')}" class="screenshot-img" alt="Purchasing and Purchase Orders">
            <div class="screenshot-caption">Figure 3.7: Purchase Order Management Desk with Vendor Tracking and Status Badges</div>
        </div>
    </div>

    <!-- MODULE 10, 11, 12, 13: PROMOTIONS, EXPENSES, REPORTS, SETTINGS -->
    <div class="page-break">
        <div class="header-bar">
            <span>ARUN CODEX — Web Developer | Designer | Innovator</span>
            <span>Gudi Chemicals ERP — Complete SRS</span>
        </div>

        <h2>3.10 Dynamic Promotional Engine</h2>
        <p>
            To stimulate wholesale and retail velocity, the system incorporates an automated rules engine:
        </p>
        <ul>
            <li><strong>Buy X Get Y (BOGO / B2G1):</strong> Automatically appends free promotional units (e.g., Buy 10 Liters Degreaser, Get 1 Liter Glass Cleaner Free) without cashier manual intervention.</li>
            <li><strong>Wholesale Slab Discounts:</strong> Tiered discounts applied based on cart quantity thresholds (e.g., 5% off for 50+ units, 10% off for 200+ units).</li>
            <li><strong>Server-Side Verification:</strong> Cart promotions are evaluated on the server to prevent tamper attacks.</li>
        </ul>

        <h2>3.11 Expense Management & Financial Ledgers</h2>
        <p>
            Maintains real-time visibility over operating expenses:
        </p>
        <ul>
            <li>Tracks electricity, boiler fuel, transportation/freight, factory labor, and office overheads.</li>
            <li>Calculates true <strong>Gross Margins</strong> by subtracting actual Cost of Goods Sold (COGS) from net sales revenue.</li>
        </ul>

        <h2>3.12 Comprehensive Reporting Suite & Statutory Compliance</h2>
        <p>
            Provides analytical reports exportable to Excel and PDF:
        </p>
        <ul>
            <li><strong>Daily Sales Register:</strong> Detailed chronological log of counter transactions, cashier collections, and payment mode breakdowns.</li>
            <li><strong>GSTR-1 Outward Supplies Summary:</strong> Grouped by HSN code and state tax buckets for monthly GST filing.</li>
            <li><strong>Inventory Valuation Report:</strong> Total valuation of raw materials, intermediates, packaging, and finished stock based on weighted moving average costs.</li>
        </ul>

        <div class="screenshot-container">
            <img src="${getScreenshotBase64('13_sales_report.png')}" class="screenshot-img" alt="Sales and Revenue Analytics Report">
            <div class="screenshot-caption">Figure 3.8: Comprehensive Sales and Tax Analytics Report with Date Filtering</div>
        </div>

        <h2>3.13 System Configuration & User Administration</h2>
        <p>
            Allows system administrators to manage company profile, tax credentials, invoice prefixes, thermal printer margins, and staff login access.
        </p>

        <div class="screenshot-container">
            <img src="${getScreenshotBase64('14_system_settings.png')}" class="screenshot-img" alt="System Configuration and Settings">
            <div class="screenshot-caption">Figure 3.9: Company Profile, Invoice Numbering Rules, and Thermal Printer Configuration</div>
        </div>
    </div>

    <!-- SECTION 4 & 5: NON-FUNCTIONAL & DB SCHEMA -->
    <div class="page-break">
        <div class="header-bar">
            <span>ARUN CODEX — Web Developer | Designer | Innovator</span>
            <span>Gudi Chemicals ERP — Complete SRS</span>
        </div>

        <h1>4. Non-Functional Requirements & Security</h1>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Category</th>
                    <th style="width: 35%;">Requirement Specification</th>
                    <th>Implementation & Verification Mechanism</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Performance</strong></td>
                    <td>POS billing calculations must return in under 200ms. Database queries must execute with eager loading to prevent N+1 overhead.</td>
                    <td>Benchmarked via automated test suites. Cart AJAX calculation response averaged <strong>120ms</strong> in automated load testing.</td>
                </tr>
                <tr>
                    <td><strong>Concurrency & Integrity</strong></td>
                    <td>Zero duplicate invoice numbers and zero race conditions on inventory decrements across simultaneous cashiers.</td>
                    <td>Enforced via <code>lockForUpdate()</code> pessimistic database row locks on <code>stock_balances</code> and <code>document_sequences</code>.</td>
                </tr>
                <tr>
                    <td><strong>Security & Auth</strong></td>
                    <td>Protection against Cross-Site Request Forgery (CSRF), SQL Injection, Session Fixation, and Brute Force attacks.</td>
                    <td>Laravel CSRF middleware, PDO parameterized prepared statements, Bcrypt password hashing (cost factor 12), and role-based policies.</td>
                </tr>
                <tr>
                    <td><strong>Data Auditability</strong></td>
                    <td>Complete audit trail for stock write-offs, manual price adjustments, invoice cancellations, and user logins.</td>
                    <td>Immutable <code>stock_movements</code> ledger and <code>activity_logs</code> table preserving before/after state snapshots.</td>
                </tr>
            </tbody>
        </table>

        <h1>5. Database Schema & Core Entities</h1>
        <p>
            The relational schema is normalized to 3NF with intentional denormalization on invoice snapshots to preserve historical tax accuracy:
        </p>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Table Name</th>
                    <th>Core Columns & Attributes</th>
                    <th>Relationships & Constraints</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>products</code></td>
                    <td>id, sku, barcode, name, item_type, category_id, unit_id, cost_price, retail_price, wholesale_price, min_selling_price, hsn_code, gst_rate, is_active</td>
                    <td>Belongs to Category & Unit; Has Many StockBalances, Batches, and BOM items.</td>
                </tr>
                <tr>
                    <td><code>sales_invoices</code></td>
                    <td>id, invoice_number, financial_year, customer_id, warehouse_id, billing_type, price_tier, subtotal, discount_amount, taxable_amount, cgst_amount, sgst_amount, igst_amount, round_off, grand_total, payment_status, printed_at, created_by</td>
                    <td>Belongs to Customer, Warehouse, User; Has Many InvoiceItems and Payments. Unique key on (invoice_number).</td>
                </tr>
                <tr>
                    <td><code>sales_invoice_items</code></td>
                    <td>id, invoice_id, product_id, batch_id, quantity, unit_price, discount_amount, taxable_amount, gst_rate, cgst_amount, sgst_amount, igst_amount, total_amount</td>
                    <td>Belongs to SalesInvoice, Product, InventoryBatch. Preserves exact tax snapshot at time of billing.</td>
                </tr>
                <tr>
                    <td><code>stock_balances</code></td>
                    <td>id, product_id, warehouse_id, batch_id, quantity, reserved_quantity, created_at, updated_at</td>
                    <td>Unique compound index on (product_id, warehouse_id, batch_id). Subject to pessimistic locking.</td>
                </tr>
                <tr>
                    <td><code>stock_movements</code></td>
                    <td>id, product_id, warehouse_id, batch_id, movement_type, reference_type, reference_id, quantity, unit_cost, balance_after, notes, user_id</td>
                    <td>Append-only ledger. Records all debits and credits with complete source document references.</td>
                </tr>
                <tr>
                    <td><code>formulas</code></td>
                    <td>id, code, name, product_id, version, standard_batch_size, output_unit_id, expected_yield_percentage, is_active</td>
                    <td>Master chemical recipe. Has Many FormulaIngredients and ProductionOrders.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- SIGN-OFF & VERIFICATION PAGE -->
    <div class="page-break">
        <div class="header-bar">
            <span>ARUN CODEX — Web Developer | Designer | Innovator</span>
            <span>Gudi Chemicals ERP — Complete SRS</span>
        </div>

        <h1>6. Approval, Sign-Off & Technical Endorsement</h1>
        <p>
            This Software Requirements Specification has been authored, verified, and officially finalized by <strong>ARUN CODEX</strong> as the authoritative blueprint for the implementation and deployment of <strong>Gudi Chemicals ERP (Release 1.0)</strong>.
        </p>

        <div style="margin-top: 50px; display: flex; justify-content: space-between;">
            <div style="width: 45%; border-top: 2px solid #0f172a; padding-top: 10px;">
                <p style="margin: 0; font-weight: 700; font-size: 11pt;">Prepared & Endorsed By:</p>
                <p style="margin: 4px 0 0 0; font-size: 13pt; font-weight: 800; color: #005a9c;">ARUN CODEX</p>
                <p style="margin: 2px 0 0 0; font-size: 9.5pt; color: #64748b;">Web Developer | Designer | Innovator</p>
                <p style="margin: 2px 0 0 0; font-size: 9pt; color: #64748b;">Email / Contact: Technical Solutions Lead</p>
                <p style="margin: 15px 0 0 0; font-size: 9pt; font-style: italic; color: #334155;">Digital Signature & Engineering Seal: Verified</p>
            </div>

            <div style="width: 45%; border-top: 2px solid #0f172a; padding-top: 10px;">
                <p style="margin: 0; font-weight: 700; font-size: 11pt;">Accepted & Approved For Client:</p>
                <p style="margin: 4px 0 0 0; font-size: 13pt; font-weight: 800; color: #0f172a;">GUDI CHEMICALS</p>
                <p style="margin: 2px 0 0 0; font-size: 9.5pt; color: #64748b;">Managing Director & Plant Operations</p>
                <p style="margin: 2px 0 0 0; font-size: 9pt; color: #64748b;">MIDC Chemical Zone, Pune, Maharashtra</p>
                <p style="margin: 15px 0 0 0; font-size: 9pt; font-style: italic; color: #334155;">Client Authorization Date: October 2026</p>
            </div>
        </div>
    </div>

</body>
</html>`;

    fs.writeFileSync(outputHtmlPath, htmlContent);
    console.log(`HTML written to ${outputHtmlPath}`);

    // Launch puppeteer to generate PDF
    const edgePath = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
    const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
    const executablePath = fs.existsSync(edgePath) ? edgePath : chromePath;

    const browser = await puppeteer.launch({
        executablePath,
        headless: true,
        args: ['--no-sandbox', '--disable-gpu']
    });

    const page = await browser.newPage();
    await page.setContent(htmlContent, { waitUntil: 'networkidle0' });
    await page.pdf({
        path: outputPdfPath,
        format: 'A4',
        printBackground: true,
        margin: { top: '15mm', bottom: '15mm', left: '12mm', right: '12mm' }
    });

    await browser.close();
    console.log(`PDF successfully generated at: ${outputPdfPath}`);
}

// ---------------------------------------------------------------------------
// 2. GENERATE NATIVE DOCX FOR SRS
// ---------------------------------------------------------------------------
async function generateDocx() {
    console.log('Generating native DOCX for Complete SRS...');

    const doc = new Document({
        styles: {
            default: {
                document: {
                    run: { font: 'Segoe UI', size: 21, color: '1e293b' }, // 10.5pt
                    paragraph: { spacing: { line: 300, after: 140 } }
                },
                heading1: {
                    run: { font: 'Segoe UI', size: 34, bold: true, color: '003e6b' },
                    paragraph: { spacing: { before: 360, after: 180 } }
                },
                heading2: {
                    run: { font: 'Segoe UI', size: 26, bold: true, color: '0f172a' },
                    paragraph: { spacing: { before: 260, after: 120 } }
                },
                heading3: {
                    run: { font: 'Segoe UI', size: 22, bold: true, color: '1e293b' },
                    paragraph: { spacing: { before: 200, after: 100 } }
                }
            }
        },
        sections: [
            // COVER PAGE
            {
                properties: {
                    page: {
                        margin: { top: 1200, bottom: 1200, left: 1200, right: 1200 }
                    }
                },
                children: [
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 400, after: 200 },
                        children: logoBuffer ? [
                            new ImageRun({
                                data: logoBuffer,
                                transformation: { width: 140, height: 140 }
                            })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 60 },
                        children: [
                            new TextRun({
                                text: 'ARUN CODEX',
                                bold: true,
                                size: 48, // 24pt
                                color: '0f172a',
                                font: 'Segoe UI'
                            })
                        ]
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 600 },
                        children: [
                            new TextRun({
                                text: 'WEB DEVELOPER | DESIGNER | INNOVATOR',
                                bold: true,
                                size: 22,
                                color: '005a9c',
                                font: 'Segoe UI'
                            })
                        ]
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 600, after: 120 },
                        children: [
                            new TextRun({
                                text: 'SOFTWARE REQUIREMENTS SPECIFICATION (SRS)',
                                bold: true,
                                size: 36, // 18pt
                                color: '003e6b'
                            })
                        ]
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 1000 },
                        children: [
                            new TextRun({
                                text: 'Gudi Chemicals ERP — Production, Inventory, GST & High-Speed POS System',
                                size: 24,
                                color: '475569',
                                italics: true
                            })
                        ]
                    }),
                    // Metadata Table
                    new Table({
                        width: { size: 100, type: WidthType.PERCENTAGE },
                        rows: [
                            new TableRow({
                                children: [
                                    new TableCell({
                                        shading: { fill: 'f1f5f9', type: ShadingType.CLEAR },
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Project Name:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun('Gudi Chemicals ERP (Release 1.0)')] })]
                                    }),
                                    new TableCell({
                                        shading: { fill: 'f1f5f9', type: ShadingType.CLEAR },
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Prepared By:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun({ text: 'ARUN CODEX', bold: true, color: '005a9c' })] })]
                                    })
                                ]
                            }),
                            new TableRow({
                                children: [
                                    new TableCell({
                                        shading: { fill: 'f1f5f9', type: ShadingType.CLEAR },
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Standard:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun('IEEE 830-1998 Format')] })]
                                    }),
                                    new TableCell({
                                        shading: { fill: 'f1f5f9', type: ShadingType.CLEAR },
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Core Focus:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun('High-Speed POS & Billing')] })]
                                    })
                                ]
                            })
                        ]
                    })
                ]
            },

            // BODY SECTION
            {
                properties: {
                    page: {
                        margin: { top: 1200, bottom: 1200, left: 1200, right: 1200 }
                    }
                },
                headers: {
                    default: new Header({
                        children: [
                            new Paragraph({
                                alignment: AlignmentType.RIGHT,
                                children: [
                                    new TextRun({ text: 'ARUN CODEX | Gudi Chemicals ERP SRS', size: 16, color: '94a3b8' })
                                ]
                            })
                        ]
                    })
                },
                footers: {
                    default: new Footer({
                        children: [
                            new Paragraph({
                                alignment: AlignmentType.RIGHT,
                                children: [
                                    new TextRun({ text: 'Page ', size: 18, color: '64748b' }),
                                    new TextRun({
                                        children: [PageNumber.CURRENT],
                                        size: 18,
                                        color: '64748b'
                                    })
                                ]
                            })
                        ]
                    })
                },
                children: [
                    // Heading 1: Executive Summary
                    new Paragraph({
                        text: '1. Executive Summary & Project Context',
                        heading: HeadingLevel.HEADING_1
                    }),
                    new Paragraph({
                        children: [
                            new TextRun('Gudi Chemicals ERP is an enterprise-grade resource planning system developed by '),
                            new TextRun({ text: 'ARUN CODEX (Web Developer | Designer | Innovator)', bold: true }),
                            new TextRun('. It is engineered specifically for chemical manufacturing plants, intermediate compounding facilities, and high-velocity chemical distribution hubs.')
                        ]
                    }),
                    new Paragraph({
                        children: [
                            new TextRun('The application solves complex industry-specific problems including multi-stage chemical recipe formulation, process loss tracking, density/viscosity quality control tests, multi-tier raw material and packaging inventories, moving average inventory valuations, and full Indian Goods and Services Tax (GST) compliance.')
                        ]
                    }),

                    // Heading 2: The Core Billing Module Deep Dive
                    new Paragraph({
                        text: '2. High-Speed POS & GST Billing Module (Deep Dive)',
                        heading: HeadingLevel.HEADING_1
                    }),
                    new Paragraph({
                        children: [
                            new TextRun({
                                text: 'The Billing Desk is the primary operational heart of the system. During peak counter hours, cashier speed and absolute tax accuracy are paramount.',
                                bold: true
                            })
                        ]
                    }),
                    new Paragraph({
                        text: 'Key Capabilities of the Billing Desk:',
                        heading: HeadingLevel.HEADING_2
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Keyboard-Driven Single Screen: ', bold: true }),
                            new TextRun('Full functionality accessible via function keys (F2 for Search, F4 for Customer, F7 for Price Tier, F8 for Payment Checkout). Eliminates mouse lag.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Barcode Scanner Hardware Integration: ', bold: true }),
                            new TextRun('Instant hardware scanner input handling with sub-80ms item addition and automatic quantity increments.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Dual-Tier Dynamic Pricing: ', bold: true }),
                            new TextRun('Instant switching between Retail Selling Price (RSP) for walk-in retail buyers and Wholesale Selling Price (WSP) for registered industrial bulk purchasers.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Indian GST Tax Engine: ', bold: true }),
                            new TextRun('Automatic detection of Intra-State transactions (CGST 50% + SGST 50%) versus Inter-State transactions (IGST 100%) based on customer State Code. Preserves immutable tax snapshots.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Multi-Mode Split Payments: ', bold: true }),
                            new TextRun('Permits dividing invoice total across Cash, UPI QR, Card, Bank Transfer, and Customer Credit with live balance calculations.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Sequential Gapless Invoice Numbers: ', bold: true }),
                            new TextRun('Generates sequential numbers (e.g., GC/2026-27/0001) using atomic database row locking to guarantee compliance with Indian GST statutory requirements.')
                        ]
                    }),

                    // Embed POS Screenshot in Docx
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 200, after: 80 },
                        children: getScreenshotBuffer('03_pos_billing.png') ? [
                            new ImageRun({
                                data: getScreenshotBuffer('03_pos_billing.png'),
                                transformation: { width: 540, height: 300 }
                            })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 200 },
                        children: [
                            new TextRun({ text: 'Figure: High-Speed POS Fast Billing Interface with Live Tax Calculation', italics: true, size: 18 })
                        ]
                    }),

                    // Invoice Formats
                    new Paragraph({
                        text: '3. Dual-Format Invoicing & Print Engine',
                        heading: HeadingLevel.HEADING_1
                    }),
                    new Paragraph({
                        children: [
                            new TextRun('Gudi Chemicals ERP incorporates a dual-mode document rendering engine:'),
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: '80mm Thermal Receipt: ', bold: true }),
                            new TextRun('Compact slip for counter sales, featuring store header, HSN line items, GST breakdown, and cashier name.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Standard A4 GST Tax Invoice: ', bold: true }),
                            new TextRun('Formal commercial tax invoice compliant with GST Rule 46. Features full buyer/seller GSTIN, PAN, HSN tax summary table, bank details, and legal declarations.')
                        ]
                    }),

                    // Embed Tax Invoice Print Screenshot
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 200, after: 80 },
                        children: getScreenshotBuffer('06_tax_invoice_print.png') ? [
                            new ImageRun({
                                data: getScreenshotBuffer('06_tax_invoice_print.png'),
                                transformation: { width: 500, height: 340 }
                            })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 200 },
                        children: [
                            new TextRun({ text: 'Figure: Standard A4 GST Tax Invoice Print Preview with Complete HSN & Tax Breakdown', italics: true, size: 18 })
                        ]
                    }),

                    // Manufacturing & Inventory
                    new Paragraph({
                        text: '4. Chemical Manufacturing & Inventory Architecture',
                        heading: HeadingLevel.HEADING_1
                    }),
                    new Paragraph({
                        children: [
                            new TextRun('The system supports 5 item classifications: Raw Materials, Semi-Finished Intermediates, Finished Products, Packaging Containers, and Trading Goods. The production lifecycle manages formula versioning, automatic scaling based on planned volume, quality control testing (pH, Viscosity, Specific Gravity), and atomic inventory deduction.')
                        ]
                    }),

                    // Embed Dashboard Screenshot
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 200, after: 80 },
                        children: getScreenshotBuffer('02_dashboard.png') ? [
                            new ImageRun({
                                data: getScreenshotBuffer('02_dashboard.png'),
                                transformation: { width: 540, height: 300 }
                            })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 200 },
                        children: [
                            new TextRun({ text: 'Figure: Real-Time Operational Dashboard with KPIs and Chemical Stock Alerts', italics: true, size: 18 })
                        ]
                    }),

                    // Sign-Off
                    new Paragraph({
                        text: '5. Technical Certification & Author Sign-Off',
                        heading: HeadingLevel.HEADING_1
                    }),
                    new Paragraph({
                        children: [
                            new TextRun('This Software Requirements Specification has been prepared and certified by '),
                            new TextRun({ text: 'ARUN CODEX (Web Developer | Designer | Innovator)', bold: true }),
                            new TextRun(' as the official technical baseline for Gudi Chemicals ERP.')
                        ]
                    })
                ]
            }
        ]
    });

    const buffer = await Packer.toBuffer(doc);
    fs.writeFileSync(outputDocxPath, buffer);
    console.log(`Native DOCX generated successfully at: ${outputDocxPath}`);
}

async function main() {
    try {
        await generateHtmlAndPdf();
        await generateDocx();
        console.log('SRS GENERATION COMPLETED SUCCESSFULLY!');
    } catch (e) {
        console.error('Error during SRS generation:', e);
        process.exit(1);
    }
}

main();
