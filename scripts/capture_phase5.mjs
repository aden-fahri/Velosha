import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const outDir = path.resolve('docs/evidence/helpdesk/phase5');
if (!fs.existsSync(outDir)) {
    fs.mkdirSync(outDir, { recursive: true });
}

const viewports = [
    { name: '1920', width: 1920, height: 1080 },
    { name: '1440', width: 1440, height: 900 },
    { name: '1024', width: 1024, height: 768 },
    { name: '768', width: 768, height: 1024 },
    { name: '375', width: 375, height: 667 },
];

async function capture() {
    console.log('Launching Playwright for Phase 5 evidence capture...');
    const browser = await chromium.launch({ headless: true });

    // --- 1. Capture Create Form (as Peserta) ---
    console.log('\n--- 1. Capturing Create Ticket Form (Peserta) ---');
    const pesertaContext = await browser.newContext();
    const pesertaPage = await pesertaContext.newPage();

    await pesertaPage.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle' });
    await pesertaPage.fill('input[name="email"]', 'peserta@velosha.id');
    await pesertaPage.fill('input[name="password"]', 'password');
    await Promise.all([
        pesertaPage.waitForNavigation({ waitUntil: 'networkidle' }),
        pesertaPage.click('button[type="submit"]'),
    ]);
    console.log('Logged in as peserta@velosha.id. Current URL:', pesertaPage.url());

    for (const vp of viewports) {
        await pesertaPage.setViewportSize({ width: vp.width, height: vp.height });
        await pesertaPage.goto('http://127.0.0.1:8000/dashboard/bantuan/buat', { waitUntil: 'networkidle' });

        // Scroll to form view
        await pesertaPage.evaluate(() => window.scrollTo(0, 0));

        const filename = `create_ticket_form_${vp.name}.png`;
        const filepath = path.join(outDir, filename);
        await pesertaPage.screenshot({ path: filepath, fullPage: true });
        console.log(`  Captured ${filename} (${vp.width}x${vp.height})`);
    }
    await pesertaPage.close();
    await pesertaContext.close();

    // --- 2. Capture Operator Ticket Detail with Suggestions (as Operator) ---
    console.log('\n--- 2. Capturing Operator Ticket Detail with Suggestions (Operator) ---');
    const operatorContext = await browser.newContext();
    const operatorPage = await operatorContext.newPage();

    await operatorPage.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle' });
    await operatorPage.fill('input[name="email"]', 'operator@velosha.id');
    await operatorPage.fill('input[name="password"]', 'password');
    await Promise.all([
        operatorPage.waitForNavigation({ waitUntil: 'networkidle' }),
        operatorPage.click('button[type="submit"]'),
    ]);
    console.log('Logged in as operator@velosha.id. Current URL:', operatorPage.url());

    for (const vp of viewports) {
        await operatorPage.setViewportSize({ width: vp.width, height: vp.height });
        await operatorPage.goto('http://127.0.0.1:8000/helpdesk/tickets/1', { waitUntil: 'networkidle' });

        const filename = `operator_ticket_detail_${vp.name}.png`;
        const filepath = path.join(outDir, filename);
        await operatorPage.screenshot({ path: filepath, fullPage: true });
        console.log(`  Captured ${filename} (${vp.width}x${vp.height})`);
    }
    await operatorPage.close();
    await operatorContext.close();

    await browser.close();
    console.log('\nAll 10 Phase 5 screenshots captured successfully!');
}

capture().catch((err) => {
    console.error('Capture error:', err);
    process.exit(1);
});
