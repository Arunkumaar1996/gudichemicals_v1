const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer-core');
const {
    Document, Packer, Paragraph, TextRun, HeadingLevel,
    Table, TableRow, TableCell, ImageRun, AlignmentType,
    WidthType, ShadingType, Header, Footer, PageNumber
} = require('docx');

const logoPath = path.resolve(__dirname, '../../public/assets/images/ak_logo.png');
const screenshotsDir = path.resolve(__dirname, '../screenshots');

// Read Logo
const logoBase64 = fs.existsSync(logoPath) ? `data:image/png;base64,${fs.readFileSync(logoPath).toString('base64')}` : '';
const logoBuffer = fs.existsSync(logoPath) ? fs.readFileSync(logoPath) : null;

// Screenshot Map
const screenshotFiles = {
    '__SCREENSHOT_01__': '01_login_screen.png',
    '__SCREENSHOT_02__': '02_dashboard.png',
    '__SCREENSHOT_03__': '03_pos_billing.png',
    '__SCREENSHOT_04__': '04_sales_invoices.png',
    '__SCREENSHOT_05__': '05_invoice_details.png',
    '__SCREENSHOT_06__': '06_tax_invoice_print.png',
    '__SCREENSHOT_07__': '07_products_master.png',
    '__SCREENSHOT_08__': '08_inventory_status.png',
    '__SCREENSHOT_09__': '09_production_formulas.png',
    '__SCREENSHOT_10__': '10_production_orders.png',
    '__SCREENSHOT_11__': '11_purchase_orders.png',
    '__SCREENSHOT_12__': '12_sales_returns.png',
    '__SCREENSHOT_13__': '13_sales_report.png',
    '__SCREENSHOT_14__': '14_system_settings.png',
};

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

function processHtmlTemplate(templatePath) {
    let content = fs.readFileSync(templatePath, 'utf8');

    // Replace Logo
    const logoTag = logoBase64 ? `<img src="${logoBase64}" class="cover-logo" alt="ARUN CODEX Logo">` : '';
    content = content.replace('__LOGO_IMG__', logoTag);

    // Replace Screenshots
    for (const [placeholder, filename] of Object.entries(screenshotFiles)) {
        const b64 = getScreenshotBase64(filename);
        const imgTag = b64 ? `<img src="${b64}" class="screenshot-img" alt="${filename}">` : '';
        content = content.split(placeholder).join(imgTag);
    }

    return content;
}

async function renderPdf(browser, htmlString, outputPath) {
    console.log(`Rendering PDF to: ${outputPath}...`);
    const page = await browser.newPage();
    await page.setContent(htmlString, { waitUntil: 'networkidle0' });
    await page.pdf({
        path: outputPath,
        format: 'A4',
        printBackground: true,
        margin: { top: '16mm', bottom: '16mm', left: '12mm', right: '12mm' }
    });
    await page.close();
    console.log(`PDF created: ${outputPath} (${fs.statSync(outputPath).size} bytes)`);
}

// -------------------------------------------------------------------------
// DOCX BUILDER FOR SRS
// -------------------------------------------------------------------------
async function buildSrsDocx(outputPath) {
    console.log(`Building SRS DOCX to: ${outputPath}...`);

    const doc = new Document({
        styles: {
            default: {
                document: {
                    run: { font: 'Segoe UI', size: 21, color: '1e293b' },
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
                properties: { page: { margin: { top: 1200, bottom: 1200, left: 1200, right: 1200 } } },
                children: [
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 300, after: 200 },
                        children: logoBuffer ? [
                            new ImageRun({ data: logoBuffer, transformation: { width: 140, height: 140 } })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 60 },
                        children: [
                            new TextRun({ text: 'ARUN CODEX', bold: true, size: 48, color: '0f172a' })
                        ]
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 500 },
                        children: [
                            new TextRun({ text: 'WEB DEVELOPER | DESIGNER | INNOVATOR', bold: true, size: 22, color: '005a9c' })
                        ]
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 400, after: 120 },
                        children: [
                            new TextRun({ text: 'SOFTWARE REQUIREMENTS SPECIFICATION (SRS)', bold: true, size: 36, color: '003e6b' })
                        ]
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 800 },
                        children: [
                            new TextRun({ text: 'Gudi Chemicals ERP — Production, Inventory, GST & High-Speed POS System', size: 24, color: '475569', italics: true })
                        ]
                    }),
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
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Document Standard:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun('IEEE 830-1998 Format')] })]
                                    }),
                                    new TableCell({
                                        shading: { fill: 'f1f5f9', type: ShadingType.CLEAR },
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Core Focus:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun('High-Speed POS & Billing Module')] })]
                                    })
                                ]
                            })
                        ]
                    })
                ]
            },

            // BODY SECTION
            {
                properties: { page: { margin: { top: 1200, bottom: 1200, left: 1200, right: 1200 } } },
                headers: {
                    default: new Header({
                        children: [
                            new Paragraph({
                                alignment: AlignmentType.RIGHT,
                                children: [new TextRun({ text: 'ARUN CODEX | Gudi Chemicals ERP SRS', size: 16, color: '94a3b8' })]
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
                                    new TextRun({ children: [PageNumber.CURRENT], size: 18, color: '64748b' })
                                ]
                            })
                        ]
                    })
                },
                children: [
                    new Paragraph({ text: '1. Executive Summary & Project Context', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Gudi Chemicals ERP is an enterprise-level chemical resource planning and sales automation suite engineered by '),
                            new TextRun({ text: 'ARUN CODEX (Web Developer | Designer | Innovator)', bold: true }),
                            new TextRun('. The software addresses specific challenges in industrial chemical manufacturing: recipe/BOM versioning, evaporation loss calculations, density/viscosity quality control tests, multi-tier raw material and packaging inventories, moving average inventory valuations, and statutory Indian Goods and Services Tax (GST) compliance.')
                        ]
                    }),

                    new Paragraph({ text: '2. High-Speed POS & GST Billing Module (Deep Dive)', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun({ text: 'The Billing Desk is the primary operational heart of the system. During counter rush hours, cashier speed and absolute tax accuracy are paramount.', bold: true })
                        ]
                    }),
                    new Paragraph({ text: 'Core Billing Capabilities:', heading: HeadingLevel.HEADING_2 }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Keyboard-First Single-Screen POS: ', bold: true }),
                            new TextRun('Full functionality accessible via keyboard shortcuts (F2 for Search, F4 for Customer, F7 for Price Tier, F8 for Checkout). Eliminates mouse-dependent delays.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Barcode Hardware Scanner Workflow: ', bold: true }),
                            new TextRun('Instant barcode scan recognition in under 80 milliseconds. Automatically adds scanned product to cart or increments quantity if already present.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Dual-Tier Pricing Engine: ', bold: true }),
                            new TextRun('Dynamic switching between Retail Selling Price (RSP) for walk-in retail counter buyers and Wholesale Selling Price (WSP) for registered B2B distributors.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Comprehensive Indian GST Tax Engine: ', bold: true }),
                            new TextRun('Automated partitioning into CGST 50% + SGST 50% for Intra-State sales (Maharashtra Code 27) and IGST 100% for Inter-State sales. Freezes exact tax snapshots per line item.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Multi-Mode Split Payment Handling: ', bold: true }),
                            new TextRun('Enables dividing invoice totals across Cash, UPI QR, Card, Bank Transfer, and Customer Credit with live change-due computation.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Sequential Gapless Invoice Numbering: ', bold: true }),
                            new TextRun('Generates sequential invoice numbers (e.g. GC/2026-27/0001) using atomic database row-locking (lockForUpdate) to eliminate race conditions.')
                        ]
                    }),

                    // Embed Screenshot 03 (POS Billing)
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 200, after: 60 },
                        children: getScreenshotBuffer('03_pos_billing.png') ? [
                            new ImageRun({ data: getScreenshotBuffer('03_pos_billing.png'), transformation: { width: 540, height: 300 } })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 200 },
                        children: [new TextRun({ text: 'Figure 2.1: High-Speed POS Fast Billing Interface with Live Tax Calculation', italics: true, size: 18 })]
                    }),

                    new Paragraph({ text: '3. Dual-Format Invoicing & Print Engine', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('The system supports dual print outputs to cater to different operational scenarios:')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: '80mm Thermal Receipt (POS Slip): ', bold: true }),
                            new TextRun('Ultra-fast slip printing for counter walk-in retail sales.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Standard A4 GST Tax Invoice: ', bold: true }),
                            new TextRun('Formal 3-copy commercial tax invoice compliant with GST Rule 46. Features full seller and buyer GSTIN, HSN tax distribution table, and bank RTGS details.')
                        ]
                    }),

                    // Embed Screenshot 06 (Tax Invoice Print)
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 200, after: 60 },
                        children: getScreenshotBuffer('06_tax_invoice_print.png') ? [
                            new ImageRun({ data: getScreenshotBuffer('06_tax_invoice_print.png'), transformation: { width: 500, height: 340 } })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 200 },
                        children: [new TextRun({ text: 'Figure 3.1: Standard A4 GST Tax Invoice Print Preview with Complete HSN & Tax Distribution', italics: true, size: 18 })]
                    }),

                    new Paragraph({ text: '4. Chemical Manufacturing & Inventory Management', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Chemical products are organized into 5 categories: Raw Chemicals, Semi-Finished Intermediates, Finished Bottled Goods, Packaging Materials, and Trading Goods. The manufacturing workflow supports versioned recipes (BOM), automatic ingredient scaling, QC testing (pH, density, viscosity), and atomic stock deduction.')
                        ]
                    }),

                    // Embed Screenshot 02 (Dashboard)
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 200, after: 60 },
                        children: getScreenshotBuffer('02_dashboard.png') ? [
                            new ImageRun({ data: getScreenshotBuffer('02_dashboard.png'), transformation: { width: 540, height: 300 } })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 200 },
                        children: [new TextRun({ text: 'Figure 4.1: Real-Time Operational Dashboard with KPIs and Stock Alerts', italics: true, size: 18 })]
                    }),

                    new Paragraph({ text: '5. Technical Certification & Author Sign-Off', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('This Software Requirements Specification has been officially certified and authored by '),
                            new TextRun({ text: 'ARUN CODEX (Web Developer | Designer | Innovator)', bold: true }),
                            new TextRun(' as the technical baseline for Gudi Chemicals ERP.')
                        ]
                    })
                ]
            }
        ]
    });

    const buffer = await Packer.toBuffer(doc);
    fs.writeFileSync(outputPath, buffer);
    console.log(`SRS DOCX created: ${outputPath} (${buffer.length} bytes)`);
}

// -------------------------------------------------------------------------
// DOCX BUILDER FOR USER MANUAL
// -------------------------------------------------------------------------
async function buildUserManualDocx(outputPath) {
    console.log(`Building User Manual DOCX to: ${outputPath}...`);

    const doc = new Document({
        styles: {
            default: {
                document: {
                    run: { font: 'Segoe UI', size: 21, color: '1e293b' },
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
                properties: { page: { margin: { top: 1200, bottom: 1200, left: 1200, right: 1200 } } },
                children: [
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 300, after: 200 },
                        children: logoBuffer ? [
                            new ImageRun({ data: logoBuffer, transformation: { width: 140, height: 140 } })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 60 },
                        children: [
                            new TextRun({ text: 'ARUN CODEX', bold: true, size: 48, color: '0f172a' })
                        ]
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 500 },
                        children: [
                            new TextRun({ text: 'WEB DEVELOPER | DESIGNER | INNOVATOR', bold: true, size: 22, color: '005a9c' })
                        ]
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 400, after: 120 },
                        children: [
                            new TextRun({ text: 'GUDI CHEMICALS ERP USER MANUAL', bold: true, size: 36, color: '003e6b' })
                        ]
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 800 },
                        children: [
                            new TextRun({ text: 'Complete Operations & Training Guide with Exhaustive Billing Masterclass', size: 24, color: '475569', italics: true })
                        ]
                    }),
                    new Table({
                        width: { size: 100, type: WidthType.PERCENTAGE },
                        rows: [
                            new TableRow({
                                children: [
                                    new TableCell({
                                        shading: { fill: 'f1f5f9', type: ShadingType.CLEAR },
                                        children: [new Paragraph({ children: [new TextRun({ text: 'System:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun('Gudi Chemicals ERP (Release 1.0)')] })]
                                    }),
                                    new TableCell({
                                        shading: { fill: 'f1f5f9', type: ShadingType.CLEAR },
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Author & Designer:', bold: true })] })]
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
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Target Users:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun('Cashiers, Storekeepers, Chemists, Managers')] })]
                                    }),
                                    new TableCell({
                                        shading: { fill: 'f1f5f9', type: ShadingType.CLEAR },
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Language:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun('Simple, Lucid & Practical English')] })]
                                    })
                                ]
                            })
                        ]
                    })
                ]
            },

            // BODY SECTION
            {
                properties: { page: { margin: { top: 1200, bottom: 1200, left: 1200, right: 1200 } } },
                headers: {
                    default: new Header({
                        children: [
                            new Paragraph({
                                alignment: AlignmentType.RIGHT,
                                children: [new TextRun({ text: 'ARUN CODEX | Gudi Chemicals ERP User Manual', size: 16, color: '94a3b8' })]
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
                                    new TextRun({ children: [PageNumber.CURRENT], size: 18, color: '64748b' })
                                ]
                            })
                        ]
                    })
                },
                children: [
                    new Paragraph({ text: 'Chapter 1: Getting Started & System Access', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Welcome to the official Gudi Chemicals ERP Operations Manual, authored by '),
                            new TextRun({ text: 'ARUN CODEX (Web Developer | Designer | Innovator)', bold: true }),
                            new TextRun('. To log in, open your browser and navigate to the application URL. Enter your corporate email and password to access the system.')
                        ]
                    }),

                    // Embed Screenshot 01 (Login)
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 200, after: 60 },
                        children: getScreenshotBuffer('01_login_screen.png') ? [
                            new ImageRun({ data: getScreenshotBuffer('01_login_screen.png'), transformation: { width: 500, height: 280 } })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 200 },
                        children: [new TextRun({ text: 'Figure 1.1: Gudi Chemicals ERP Secure Login Screen', italics: true, size: 18 })]
                    }),

                    new Paragraph({ text: 'Chapter 2: High-Speed POS Billing Masterclass', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun({ text: 'The Billing Desk is where you serve customers and record sales. Gudi Chemicals ERP makes this process fast and simple.', bold: true })
                        ]
                    }),
                    new Paragraph({ text: 'Key POS Operations Guide:', heading: HeadingLevel.HEADING_2 }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Scanning Products: ', bold: true }),
                            new TextRun('Point your barcode scanner at any chemical container. The item is automatically added to the cart.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Quick SKU Search: ', bold: true }),
                            new TextRun('Press F2 on your keyboard to instantly focus the search box. Type product name and press Enter.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Customer Selection: ', bold: true }),
                            new TextRun('Press F4 to choose between Walk-in Retail Customer or registered B2B Wholesale Accounts.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Price Tier (Retail vs Wholesale): ', bold: true }),
                            new TextRun('Press F7 to toggle between Retail Price (RSP) and Wholesale Price (WSP). Wholesale price automatically applies for B2B buyers.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Payment & Checkout: ', bold: true }),
                            new TextRun('Press F8 to open checkout. Enter Cash Tendered to see exact Change Due, or choose UPI QR, Card, or Customer Credit.')
                        ]
                    }),

                    // Embed Screenshot 03 (POS Billing)
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 200, after: 60 },
                        children: getScreenshotBuffer('03_pos_billing.png') ? [
                            new ImageRun({ data: getScreenshotBuffer('03_pos_billing.png'), transformation: { width: 540, height: 300 } })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 200 },
                        children: [new TextRun({ text: 'Figure 2.1: The POS Fast Billing Screen with Live Cart and Real-Time Totals', italics: true, size: 18 })]
                    }),

                    new Paragraph({ text: 'Chapter 3: Invoicing, Printing & Document Dispatch', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('From the Invoices screen, cashiers can click "Print Thermal" for fast 80mm counter receipts, or "Print A4" for formal commercial GST tax invoices. PDFs can also be downloaded directly or shared with customers via WhatsApp.')
                        ]
                    }),

                    // Embed Screenshot 06 (Tax Invoice Print)
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 200, after: 60 },
                        children: getScreenshotBuffer('06_tax_invoice_print.png') ? [
                            new ImageRun({ data: getScreenshotBuffer('06_tax_invoice_print.png'), transformation: { width: 500, height: 340 } })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 200 },
                        children: [new TextRun({ text: 'Figure 3.1: Standard A4 GST Tax Invoice Print Preview', italics: true, size: 18 })]
                    }),

                    new Paragraph({ text: 'Chapter 4: Inventory, Manufacturing & Support', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('The manual covers master data creation, chemical formula BOM recipes, production order execution, goods receipt notes (GRN), and daily sales reporting.')
                        ]
                    }),
                    new Paragraph({
                        children: [
                            new TextRun('For technical support, custom features, or questions, please contact '),
                            new TextRun({ text: 'ARUN CODEX (Web Developer | Designer | Innovator)', bold: true }),
                            new TextRun('. All rights reserved.')
                        ]
                    })
                ]
            }
        ]
    });

    const buffer = await Packer.toBuffer(doc);
    fs.writeFileSync(outputPath, buffer);
    console.log(`User Manual DOCX created: ${outputPath} (${buffer.length} bytes)`);
}

// -------------------------------------------------------------------------
// MAIN EXECUTOR
// -------------------------------------------------------------------------
async function main() {
    console.log('=== STARTING COMPLETE DOCUMENT GENERATION ===');

    const srsHtmlPath = path.resolve(__dirname, '../Gudi_Chemicals_ERP_Complete_SRS.html');
    const srsPdfPath = path.resolve(__dirname, '../Gudi_Chemicals_ERP_Complete_SRS.pdf');
    const srsDocxPath = path.resolve(__dirname, '../Gudi_Chemicals_ERP_Complete_SRS.docx');

    const umHtmlPath = path.resolve(__dirname, '../Gudi_Chemicals_ERP_User_Manual.html');
    const umPdfPath = path.resolve(__dirname, '../Gudi_Chemicals_ERP_User_Manual.pdf');
    const umDocxPath = path.resolve(__dirname, '../Gudi_Chemicals_ERP_User_Manual.docx');

    // 1. Process HTMLs
    console.log('1. Processing HTML templates...');
    const srsHtml = processHtmlTemplate(path.resolve(__dirname, 'srs_template.html'));
    fs.writeFileSync(srsHtmlPath, srsHtml);
    console.log(`Saved: ${srsHtmlPath}`);

    const umHtml = processHtmlTemplate(path.resolve(__dirname, 'user_manual_template.html'));
    fs.writeFileSync(umHtmlPath, umHtml);
    console.log(`Saved: ${umHtmlPath}`);

    // 2. Launch Puppeteer for PDFs
    console.log('2. Launching headless browser for PDF generation...');
    const edgePath = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
    const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
    const executablePath = fs.existsSync(edgePath) ? edgePath : chromePath;

    const browser = await puppeteer.launch({
        executablePath,
        headless: true,
        args: ['--no-sandbox', '--disable-gpu']
    });

    await renderPdf(browser, srsHtml, srsPdfPath);
    await renderPdf(browser, umHtml, umPdfPath);

    await browser.close();
    console.log('Browser closed.');

    // 3. Build DOCX Files
    console.log('3. Building native DOCX files...');
    await buildSrsDocx(srsDocxPath);
    await buildUserManualDocx(umDocxPath);

    console.log('=== ALL 4 DOCUMENTS (.DOCX & .PDF) GENERATED SUCCESSFULLY! ===');
}

main().catch(err => {
    console.error('Fatal error during document build:', err);
    process.exit(1);
});
