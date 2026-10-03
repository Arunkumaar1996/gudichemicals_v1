const fs = require('fs');
const path = require('path');
const puppeteer = require('./node_modules/puppeteer-core');
const {
    Document, Packer, Paragraph, TextRun, HeadingLevel,
    Table, TableRow, TableCell, ImageRun, AlignmentType,
    WidthType, ShadingType, Header, Footer, PageNumber
} = require('./node_modules/docx');

const logoPath = path.resolve(__dirname, '../../public/assets/images/ak_logo.png');
const screenshotsDir = path.resolve(__dirname, '../screenshots');

// Read Logo
const logoBase64 = fs.existsSync(logoPath) ? `data:image/png;base64,${fs.readFileSync(logoPath).toString('base64')}` : '';
const logoBuffer = fs.existsSync(logoPath) ? fs.readFileSync(logoPath) : null;

// All 15 Screenshot Mappings
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

    // Replace All Screenshots
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
            target = outputPath.replace('.pdf', '_v2.pdf');
            console.log(`Path locked, saving to: ${target}...`);
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
// DOCX BUILDER FOR COMPREHENSIVE PAGE-WISE USER MANUAL
// -------------------------------------------------------------------------
async function buildDocx(outputPath) {
    console.log(`Building Comprehensive User Manual DOCX to: ${outputPath}...`);

    function createImgPara(filename, caption, width = 520, height = 290) {
        const buf = getScreenshotBuffer(filename);
        if (!buf) return [new Paragraph({ text: `[Image: ${filename}]` })];
        return [
            new Paragraph({
                alignment: AlignmentType.CENTER,
                spacing: { before: 180, after: 60 },
                children: [
                    new ImageRun({
                        data: buf,
                        transformation: { width, height }
                    })
                ]
            }),
            new Paragraph({
                alignment: AlignmentType.CENTER,
                spacing: { after: 160 },
                children: [
                    new TextRun({ text: caption, italics: true, size: 18, color: '64748b' })
                ]
            })
        ];
    }

    const doc = new Document({
        styles: {
            default: {
                document: {
                    run: { font: 'Segoe UI', size: 21, color: '1e293b' },
                    paragraph: { spacing: { line: 290, after: 120 } }
                },
                heading1: {
                    run: { font: 'Segoe UI', size: 32, bold: true, color: '003e6b' },
                    paragraph: { spacing: { before: 320, after: 140 } }
                },
                heading2: {
                    run: { font: 'Segoe UI', size: 24, bold: true, color: '0f172a' },
                    paragraph: { spacing: { before: 220, after: 100 } }
                },
                heading3: {
                    run: { font: 'Segoe UI', size: 21, bold: true, color: '1e293b' },
                    paragraph: { spacing: { before: 180, after: 80 } }
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
                        spacing: { before: 350, after: 120 },
                        children: [
                            new TextRun({ text: 'GUDI CHEMICALS ERP USER MANUAL', bold: true, size: 36, color: '003e6b' })
                        ]
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { after: 700 },
                        children: [
                            new TextRun({ text: 'Complete Step-by-Step Operations Guide with Live Screen Visuals for All Modules', size: 23, color: '475569', italics: true })
                        ]
                    }),
                    new Table({
                        width: { size: 100, type: WidthType.PERCENTAGE },
                        rows: [
                            new TableRow({
                                children: [
                                    new TableCell({
                                        shading: { fill: 'f1f5f9', type: ShadingType.CLEAR },
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Target Enterprise:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun('Gudi Chemicals (Coimbatore, Tamil Nadu)')] })]
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
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Address:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun('Spic, Thenkarai, Tamil Nadu 641010')] })]
                                    }),
                                    new TableCell({
                                        shading: { fill: 'f1f5f9', type: ShadingType.CLEAR },
                                        children: [new Paragraph({ children: [new TextRun({ text: 'Hours & Tax:', bold: true })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun('9:00am–6:30pm | TN Code 33')] })]
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
                                children: [new TextRun({ text: 'ARUN CODEX | Gudi Chemicals ERP Complete User Manual', size: 16, color: '94a3b8' })]
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
                    // CHAPTER 1
                    new Paragraph({ text: 'Chapter 1: User Login & Session Security', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('To log in, open your browser and go to your ERP address (http://127.0.0.1:8000/login). Enter your assigned email address and password to sign in safely.')
                        ]
                    }),
                    ...createImgPara('01_login_screen.png', 'Figure 1.1: Secure User Login Screen with Input Credentials', 500, 280),

                    // CHAPTER 2
                    new Paragraph({ text: 'Chapter 2: Executive Operational Dashboard', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('The Dashboard displays your daily turnover, total invoices generated, low stock alerts, and interactive revenue velocity charts.')
                        ]
                    }),
                    ...createImgPara('02_dashboard.png', 'Figure 2.1: Operational Dashboard with Live Revenue KPIs and Low Stock Alerts', 530, 290),

                    // CHAPTER 3
                    new Paragraph({ text: 'Chapter 3: High-Speed POS Fast Billing Desk', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('The single-screen POS desk lets cashiers scan barcodes, search items (F2), select retail or wholesale customers (F4), switch price tiers (F7), and checkout with cash (F8).')
                        ]
                    }),
                    ...createImgPara('03_pos_billing.png', 'Figure 3.1: POS Billing Screen with Product Catalog Grid and Live Order Cart', 530, 290),

                    // CHAPTER 4
                    new Paragraph({ text: 'Chapter 4: POS Cash Checkout & Change Due', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('In the payment modal, entering Cash Tendered automatically computes exact Change Due for the customer in bold green numerals. Clicking Complete opens the cash drawer and prints the receipt.')
                        ]
                    }),
                    ...createImgPara('03b_pos_cash_payment_modal.png', 'Figure 4.1: Cash Payment Modal showing Net Payable, Cash Tendered, and Change Due', 500, 310),

                    // CHAPTER 5
                    new Paragraph({ text: 'Chapter 5: Sales Invoices Register & Shift Log', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('The Invoices screen maintains a searchable register of all posted tax invoices with one-click actions for Thermal Print, A4 Tax Print, and PDF download.')
                        ]
                    }),
                    ...createImgPara('04_sales_invoices.png', 'Figure 5.1: Invoices Register with Real-Time Filters and Printing Actions', 530, 290),

                    // CHAPTER 6
                    new Paragraph({ text: 'Chapter 6: Detailed Invoice View & Action Center', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Displays line-by-line itemization, CGST/SGST tax breakdown, customer address, payment settlement records, and linked inventory movement logs.')
                        ]
                    }),
                    ...createImgPara('05_invoice_details.png', 'Figure 6.1: Detailed Invoice Management Screen with Audit Records', 530, 290),

                    // CHAPTER 7
                    new Paragraph({ text: 'Chapter 7: Dual-Format Print Engine (A4 & Thermal)', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Supports 80mm high-speed thermal counter slips and formal 3-copy commercial A4 GST tax invoices compliant with Rule 46 of the CGST Rules.')
                        ]
                    }),
                    ...createImgPara('06_tax_invoice_print.png', 'Figure 7.1: Standard A4 GST Tax Invoice Print Preview with Full HSN Distribution', 500, 340),

                    // CHAPTER 8
                    new Paragraph({ text: 'Chapter 8: Chemical Products Master Catalog', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Manage Floor Cleaners, Toilet Cleaners, Degreasers, Packaging Jerry Cans, and Raw Chemicals with dual pricing (Retail RSP vs Wholesale WSP).')
                        ]
                    }),
                    ...createImgPara('07_products_master.png', 'Figure 8.1: Products Master Catalog with Dual Pricing and Stock Quantities', 530, 290),

                    // CHAPTER 9
                    new Paragraph({ text: 'Chapter 9: Multi-Tier Chemical Inventory & Stock Ledgers', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Real-time warehouse inventory balances, batch lot numbers, manufacturing and expiry dates, and an immutable movement ledger.')
                        ]
                    }),
                    ...createImgPara('08_inventory_status.png', 'Figure 9.1: Multi-Tier Inventory Status and Batch Valuation Screen', 530, 290),

                    // CHAPTER 10
                    new Paragraph({ text: 'Chapter 10: Chemical Formulas & Bill of Materials (BOM)', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Plant chemists define versioned master recipes specifying standard batch sizes, expected yields, process evaporation losses, and ingredient mixing ratios.')
                        ]
                    }),
                    ...createImgPara('09_production_formulas.png', 'Figure 10.1: Master Chemical Recipes & Bill of Materials (BOM) Management', 530, 290),

                    // CHAPTER 11
                    new Paragraph({ text: 'Chapter 11: Production Orders & Laboratory QC Gates', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Manage the 4-stage batch manufacturing lifecycle: Draft planning, ingredient reservation, laboratory QC testing (pH, density, viscosity), and atomic finalization.')
                        ]
                    }),
                    ...createImgPara('10_production_orders.png', 'Figure 11.1: Production Orders Lifecycle Desk with Laboratory QC Verification', 530, 290),

                    // CHAPTER 12
                    new Paragraph({ text: 'Chapter 12: Purchasing & Goods Receipt Notes (GRN)', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Create supplier purchase orders, inspect incoming chemical deliveries at the depot gate, and post Goods Receipt Notes (GRN) to update warehouse stock.')
                        ]
                    }),
                    ...createImgPara('11_purchase_orders.png', 'Figure 12.1: Purchasing and Goods Receipt (GRN) Management Desk', 530, 290),

                    // CHAPTER 13
                    new Paragraph({ text: 'Chapter 13: Sales Returns & GST Credit Notes', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Process customer returns with mandatory original invoice linking, segregating returned containers into Saleable Stock vs Quarantined Damaged Chemical Waste.')
                        ]
                    }),
                    ...createImgPara('12_sales_returns.png', 'Figure 13.1: Sales Returns and Credit Note Management Desk', 530, 290),

                    // CHAPTER 14
                    new Paragraph({ text: 'Chapter 14: Sales & Statutory GSTR-1 Tax Analytics', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Generate daily sales registers, cash collection audits, inventory valuation reports, and monthly GSTR-1 outward supply tax summaries for filing.')
                        ]
                    }),
                    ...createImgPara('13_sales_report.png', 'Figure 14.1: Sales & Statutory Tax Analytics Report with Date Filtering', 530, 290),

                    // CHAPTER 15
                    new Paragraph({ text: 'Chapter 15: System Settings & User Administration', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Configure company profile, GSTIN credentials (33AAACG1234D1Z5), Coimbatore Thenkarai address, 80mm thermal receipt margins, and staff login permissions.')
                        ]
                    }),
                    ...createImgPara('14_system_settings.png', 'Figure 15.1: System Configuration Desk — Company Profile, Tax & Hardware Setup', 530, 290),

                    // CHAPTER 16
                    new Paragraph({ text: 'Chapter 16: Keyboard Shortcuts & Technical Support', heading: HeadingLevel.HEADING_1 }),
                    new Paragraph({
                        children: [
                            new TextRun('Function keys for counter cashiers: F2 (Search/Scan), F4 (Customer), F7 (Price Tier), F8 (Cash Checkout), Enter (Complete Bill), Esc (Clear/Close).')
                        ]
                    }),
                    new Paragraph({
                        children: [
                            new TextRun({ text: 'Prepared & Engineered By: ', bold: true }),
                            new TextRun({ text: 'ARUN CODEX (Web Developer | Designer | Innovator)', bold: true, color: '005a9c' }),
                            new TextRun(' for Gudi Chemicals (Spic, Thenkarai, Coimbatore, Tamil Nadu 641010). All rights reserved.')
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
    console.log('=== STARTING COMPLETE PAGE-WISE USER MANUAL GENERATION ===');

    const htmlPath = path.resolve(__dirname, '../Gudi_Chemicals_ERP_PageWise_User_Manual.html');
    const pdfPath = path.resolve(__dirname, '../Gudi_Chemicals_ERP_PageWise_User_Manual.pdf');
    const docxPath = path.resolve(__dirname, '../Gudi_Chemicals_ERP_PageWise_User_Manual.docx');

    // Also prepare copies inside new-srs folder
    const pdfPathNewSrs = path.resolve(__dirname, '../new-srs/Gudi_Chemicals_ERP_PageWise_User_Manual.pdf');
    const docxPathNewSrs = path.resolve(__dirname, '../new-srs/Gudi_Chemicals_ERP_PageWise_User_Manual.docx');

    // 1. Process HTML template
    console.log('1. Processing HTML template...');
    const html = processHtmlTemplate(path.resolve(__dirname, 'complete_user_manual_template.html'));
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

    // Copy PDF to new-srs folder
    try {
        fs.copyFileSync(pdfPath, pdfPathNewSrs);
        console.log(`Copied PDF to: ${pdfPathNewSrs}`);
    } catch (e) {
        console.log('Note on copy:', e.message);
    }

    // 3. Build DOCX File
    console.log('3. Building native DOCX file...');
    await buildDocx(docxPath);

    // Copy DOCX to new-srs folder
    try {
        fs.copyFileSync(docxPath, docxPathNewSrs);
        console.log(`Copied DOCX to: ${docxPathNewSrs}`);
    } catch (e) {
        console.log('Note on copy:', e.message);
    }

    console.log('=== COMPLETE PAGE-WISE USER MANUAL GENERATED SUCCESSFULLY! ===');
}

main().catch(err => {
    console.error('Fatal error during user manual build:', err);
    process.exit(1);
});
