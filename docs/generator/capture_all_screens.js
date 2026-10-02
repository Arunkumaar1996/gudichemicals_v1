const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

async function run() {
    const edgePath = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
    const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
    const executablePath = fs.existsSync(edgePath) ? edgePath : chromePath;

    console.log(`Using browser executable: ${executablePath}`);

    const screenshotsDir = path.resolve(__dirname, '../screenshots');
    if (!fs.existsSync(screenshotsDir)) {
        fs.mkdirSync(screenshotsDir, { recursive: true });
    }

    const browser = await puppeteer.launch({
        executablePath,
        headless: true,
        defaultViewport: { width: 1366, height: 768 },
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu']
    });

    const page = await browser.newPage();

    // 1. Login Page
    console.log('Capturing Login...');
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(screenshotsDir, '01_login_screen.png'), fullPage: true });

    // Perform Login by setting input values directly and submitting
    console.log('Logging in as Admin...');
    await page.evaluate(() => {
        document.querySelector('input[name="email"]').value = 'admin@gudichemicals.com';
        document.querySelector('input[name="password"]').value = 'Admin@12345';
        document.querySelector('form').submit();
    });

    await page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 15000 }).catch(() => {});
    await new Promise(r => setTimeout(r, 1500));

    // 2. Dashboard
    console.log('Capturing Dashboard...');
    await page.goto('http://127.0.0.1:8000/dashboard', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(screenshotsDir, '02_dashboard.png'), fullPage: true });

    // 3. POS Fast Billing Desk
    console.log('Capturing POS Billing...');
    await page.goto('http://127.0.0.1:8000/pos', { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 1000));
    try {
        await page.evaluate(() => {
            const firstAddBtn = document.querySelector('button[onclick*="addToCart"], .product-card');
            if (firstAddBtn) firstAddBtn.click();
        });
        await new Promise(r => setTimeout(r, 800));
    } catch (e) {
        console.log('Cart add note:', e.message);
    }
    await page.screenshot({ path: path.join(screenshotsDir, '03_pos_billing.png'), fullPage: true });

    // 4. Sales Invoices List
    console.log('Capturing Sales Invoices List...');
    await page.goto('http://127.0.0.1:8000/invoices', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(screenshotsDir, '04_sales_invoices.png'), fullPage: true });

    // 5. Invoice Detailed View
    console.log('Capturing Invoice Details...');
    await page.goto('http://127.0.0.1:8000/invoices/7', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(screenshotsDir, '05_invoice_details.png'), fullPage: true });

    // 6. Tax Invoice Print
    console.log('Capturing Tax Invoice Print Preview...');
    await page.goto('http://127.0.0.1:8000/invoices/7/print', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(screenshotsDir, '06_tax_invoice_print.png'), fullPage: true });

    // 7. Products Master
    console.log('Capturing Products Master...');
    await page.goto('http://127.0.0.1:8000/masters/products', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(screenshotsDir, '07_products_master.png'), fullPage: true });

    // 8. Inventory Stock Ledger
    console.log('Capturing Inventory Status & Ledger...');
    await page.goto('http://127.0.0.1:8000/inventory', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(screenshotsDir, '08_inventory_status.png'), fullPage: true });

    // 9. Production Formulas (BOM)
    console.log('Capturing Production Formulas...');
    await page.goto('http://127.0.0.1:8000/production/formulas', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(screenshotsDir, '09_production_formulas.png'), fullPage: true });

    // 10. Production Orders
    console.log('Capturing Production Orders...');
    await page.goto('http://127.0.0.1:8000/production/orders', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(screenshotsDir, '10_production_orders.png'), fullPage: true });

    // 11. Purchasing (Purchase Orders)
    console.log('Capturing Purchase Orders...');
    await page.goto('http://127.0.0.1:8000/purchases/orders', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(screenshotsDir, '11_purchase_orders.png'), fullPage: true });

    // 12. Sales Returns
    console.log('Capturing Sales Returns...');
    await page.goto('http://127.0.0.1:8000/returns', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(screenshotsDir, '12_sales_returns.png'), fullPage: true });

    // 13. Reports (Sales Report)
    console.log('Capturing Sales Report...');
    await page.goto('http://127.0.0.1:8000/reports/sales', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(screenshotsDir, '13_sales_report.png'), fullPage: true });

    // 14. System Settings
    console.log('Capturing System Settings...');
    await page.goto('http://127.0.0.1:8000/settings', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(screenshotsDir, '14_system_settings.png'), fullPage: true });

    await browser.close();
    console.log('ALL SCREENSHOTS CAPTURED SUCCESSFULLY!');
}

run().catch(err => {
    console.error('Error capturing screenshots:', err);
    process.exit(1);
});
