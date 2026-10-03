const fs = require('fs');
const path = require('path');
const puppeteer = require('../generator/node_modules/puppeteer-core');
const {
    Document, Packer, Paragraph, TextRun, HeadingLevel,
    Table, TableRow, TableCell, ImageRun, AlignmentType,
    WidthType, ShadingType, Header, Footer, PageNumber
} = require('../generator/node_modules/docx');

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
    '__SCREENSHOT_03B__': '03b_pos_cash_payment_modal.png',
    '__SCREENSHOT_04__': '04_sales_invoices.png',
    '__SCREENSHOT_05__': '05_invoice_details.png',
    '__SCREENSHOT_06__': '06_tax_invoice_print.png',
    '__SCREENSHOT_07__': '07_products_master.png',
    '__SCREENSHOT_08__': '08_inventory_status.png',
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
    let target = outputPath;
    try {
        await page.pdf({
            path: target,
            format: 'A4',
            printBackground: true,
            margin: { top: '16mm', bottom: '16mm', left: '12mm', right: '12mm' }
        });
    } catch (err) {
        if (err.code === 'EBUSY') {
            target = outputPath.replace('.pdf', '_Updated.pdf');
            console.log(`Initial path locked by viewer, saving to fallback: ${target}...`);
            await page.pdf({
                path: target,
                format: 'A4',
                printBackground: true,
                margin: { top: '16mm', bottom: '16mm', left: '12mm', right: '12mm' }
            });
        } else {
            throw err;
        }
    }
    await page.close();
    console.log(`PDF created: ${target} (${fs.statSync(target).size} bytes)`);
}

// -------------------------------------------------------------------------
// DOCX BUILDER FOR REVISED SRS (CASH ONLY, PRODUCTION SKIPPED)
// -------------------------------------------------------------------------
async function buildDocx(outputPath) {
    console.log(`Building DOCX to: ${outputPath}...`);

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
                            new TextRun({ text: 'Gudi Chemicals ERP — Commercial Trading, Inventory, GST & Cash-Only POS System', size: 24, color: '475569', italics: true })
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
                                        children: [new Paragraph({ children: [new TextRun('Gudi Chemicals ERP (v1.0 - Commercial Edition)')] })]
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
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Payment Method:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Cash-Only Settlement (Fast Counter)', bold: true, color: '166534' })] })]
                                    }),
                                    new TableCell({
                                        shading: { fill: 'f1f5f9', type: ShadingType.CLEAR },
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Scope Focus:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun('Trading, Inventory & Sales (Production Skipped)')] })]
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
                                children: [new TextRun({ text: 'ARUN CODEX | Gudi Chemicals ERP SRS (Commercial Edition)', size: 16, color: '94a3b8' })]
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
                    new Paragraph({ text: '1. Executive Summary & Revised Scope', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('This revised Software Requirements Specification is authored by '),
                            new TextRun({ text: 'ARUN CODEX (Web Developer | Designer | Innovator)', bold: true }),
                            new TextRun(' for the commercial trading, inventory management, and high-speed counter checkout operations of Gudi Chemicals.')
                        ]
                    }),
                    new Paragraph({
                        children: [
                            new TextRun('Key adjustments incorporated into this edition:'),
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Cash-Only Billing Architecture: ', bold: true }),
                            new TextRun('Counter checkout transactions are streamlined strictly to Cash payment. Cashiers input Cash Tendered, the system instantly computes Change Due, and transactions are posted with zero gateway latency.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Production Module Skipped: ', bold: true }),
                            new TextRun('The chemical manufacturing menu (BOM recipes, intermediate compounding vats, production order execution, and laboratory QC gates) is completely excluded from this specification to focus squarely on merchandising and warehouse sales.')
                        ]
                    }),

                    new Paragraph({ text: '2. High-Speed POS & Billing Module (Cash-Only Focus)', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun({ text: 'The billing desk is the core commercial interface. It is designed for maximum speed and simplicity.', bold: true })
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Keyboard-Driven Cockpit: ', bold: true }),
                            new TextRun('F2 for Search/Barcode, F4 for Customer, F7 for Price Tier (Retail vs Wholesale), F8 for Cash Checkout.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Hardware Barcode Scanner Support: ', bold: true }),
                            new TextRun('Automatic detection of 1D/2D container barcodes with sub-80ms cart addition and auto-incrementing quantity.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Cash Tendered & Change Due Mechanism: ', bold: true }),
                            new TextRun('Cashiers input the physical currency handed over by the customer (e.g. ₹1,000 for an ₹885 bill). The system calculates and displays Change Due (₹115.00) in bold green numerals.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Indian GST Engine: ', bold: true }),
                            new TextRun('Automatic 50:50 equal split into CGST and SGST for Intra-State sales in Tamil Nadu (State Code 33) and 100% IGST for Inter-State sales.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Sequential Gapless Invoice Numbers: ', bold: true }),
                            new TextRun('Atomic numbering (e.g. GC/2026-27/0001) using lockForUpdate concurrency locks to guarantee legal compliance.')
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

                    // Embed Screenshot 03B (Cash Modal)
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 200, after: 60 },
                        children: getScreenshotBuffer('03b_pos_cash_payment_modal.png') ? [
                            new ImageRun({ data: getScreenshotBuffer('03b_pos_cash_payment_modal.png'), transformation: { width: 500, height: 320 } })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 200 },
                        children: [new TextRun({ text: 'Figure 2.2: POS Cash Payment Checkout Modal with Cash Tendered and Change Due Calculation', italics: true, size: 18 })]
                    }),

                    new Paragraph({ text: '3. Dual-Format Invoicing & Document Printing', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Once a cash transaction is committed, the cashier can print in either of two standard formats:')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: '80mm Thermal Receipt (POS Slip): ', bold: true }),
                            new TextRun('Instant slip with store header, HSN line items, GST breakdown, cash tendered, and change returned.')
                        ]
                    }),
                    new Paragraph({
                        bullet: { level: 0 },
                        children: [
                            new TextRun({ text: 'Standard A4 GST Tax Invoice: ', bold: true }),
                            new TextRun('Official 3-copy commercial tax invoice compliant with GST Rule 46 with complete HSN tax summary table.')
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

                    new Paragraph({ text: '4. Inventory, Purchasing & Reporting Suite', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('The system provides real-time stock balances with batch tracking, goods receipt notes (GRN) from suppliers, sales returns with stock isolation, operating expense logs, and statutory GSTR-1 summaries.')
                        ]
                    }),

                    // Embed Screenshot 08 (Inventory Status)
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 200, after: 60 },
                        children: getScreenshotBuffer('08_inventory_status.png') ? [
                            new ImageRun({ data: getScreenshotBuffer('08_inventory_status.png'), transformation: { width: 540, height: 300 } })
                        ] : []
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 200 },
                        children: [new TextRun({ text: 'Figure 4.1: Real-Time Multi-Tier Inventory Status and Batch Valuation Screen', italics: true, size: 18 })]
                    }),

                    new Paragraph({ text: '5. Technical Certification & Author Sign-Off', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('This revised Software Requirements Specification (Commercial & Cash-Only Edition) has been officially prepared and certified by '),
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
    console.log(`DOCX created: ${outputPath} (${buffer.length} bytes)`);
}

// -------------------------------------------------------------------------
// MAIN EXECUTOR
// -------------------------------------------------------------------------
async function main() {
    console.log('=== STARTING REVISED SRS GENERATION (CASH-ONLY EDITION) ===');

    const htmlPath = path.resolve(__dirname, 'Gudi_Chemicals_ERP_New_SRS.html');
    const pdfPath = path.resolve(__dirname, 'Gudi_Chemicals_ERP_New_SRS.pdf');
    const docxPath = path.resolve(__dirname, 'Gudi_Chemicals_ERP_New_SRS.docx');

    // 1. Process HTML
    console.log('1. Processing HTML template...');
    const html = processHtmlTemplate(path.resolve(__dirname, 'new_srs_template.html'));
    fs.writeFileSync(htmlPath, html);
    console.log(`Saved: ${htmlPath}`);

    // 2. Launch Puppeteer for PDF
    console.log('2. Launching headless browser for PDF generation...');
    const edgePath = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
    const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
    const executablePath = fs.existsSync(edgePath) ? edgePath : chromePath;

    const browser = await puppeteer.launch({
        executablePath,
        headless: true,
        args: ['--no-sandbox', '--disable-gpu']
    });

    await renderPdf(browser, html, pdfPath);
    await browser.close();
    console.log('Browser closed.');

    // 3. Build DOCX File
    console.log('3. Building native DOCX file...');
    await buildDocx(docxPath);

    console.log('=== REVISED SRS GENERATED SUCCESSFULLY IN docs/new-srs/ ===');
}

main().catch(err => {
    console.error('Fatal error during revised SRS build:', err);
    process.exit(1);
});
