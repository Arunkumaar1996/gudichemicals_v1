const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

async function run() {
    const edgePath = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
    const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
    const executablePath = fs.existsSync(edgePath) ? edgePath : chromePath;

    const screenshotsDir = path.resolve(__dirname, '../screenshots');

    const browser = await puppeteer.launch({
        executablePath,
        headless: true,
        defaultViewport: { width: 1366, height: 768 },
        args: ['--no-sandbox', '--disable-gpu']
    });

    const page = await browser.newPage();

    // Login
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle2' });
    await page.evaluate(() => {
        document.querySelector('input[name="email"]').value = 'admin@gudichemicals.com';
        document.querySelector('input[name="password"]').value = 'Admin@12345';
        document.querySelector('form').submit();
    });
    await new Promise(r => setTimeout(r, 2000));

    // POS Desk
    await page.goto('http://127.0.0.1:8000/pos', { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 1000));

    // Add item to cart
    await page.evaluate(() => {
        const firstAddBtn = document.querySelector('button[onclick*="addToCart"], .product-card');
        if (firstAddBtn) firstAddBtn.click();
    });
    await new Promise(r => setTimeout(r, 800));

    // Click Checkout to open payment modal
    await page.evaluate(() => {
        if (typeof openPaymentModal === 'function') {
            openPaymentModal();
        } else {
            const btn = document.getElementById('btnCheckout');
            if (btn) btn.click();
        }
    });
    await new Promise(r => setTimeout(r, 800));

    // Ensure Cash is selected and enter tendered amount
    await page.evaluate(() => {
        const cashRadio = document.querySelector('input[value="cash"]');
        if (cashRadio) {
            cashRadio.checked = true;
            cashRadio.dispatchEvent(new Event('change'));
        }
        const tenderedInput = document.getElementById('cashTendered') || document.querySelector('input[name="cash_tendered"]');
        if (tenderedInput) {
            tenderedInput.value = '1000';
            tenderedInput.dispatchEvent(new Event('input'));
        }
    });
    await new Promise(r => setTimeout(r, 600));

    await page.screenshot({ path: path.join(screenshotsDir, '03b_pos_cash_payment_modal.png'), fullPage: true });
    console.log('Captured 03b_pos_cash_payment_modal.png successfully!');

    await browser.close();
}

run().catch(err => {
    console.error('Error:', err);
    process.exit(1);
});
