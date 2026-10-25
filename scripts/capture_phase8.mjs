import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const outDir = path.resolve('docs/evidence/helpdesk/phase8');
if (!fs.existsSync(outDir)) {
    fs.mkdirSync(outDir, { recursive: true });
}

const viewports = [
    { name: '360', width: 360, height: 740 },
    { name: '390', width: 390, height: 844 },
    { name: '768', width: 768, height: 1024 },
    { name: '1024', width: 1024, height: 768 },
    { name: '1440', width: 1440, height: 900 },
];

async function main() {
    console.log('Starting Phase 8 Admin Analytics Evidence Capture...');
    const browser = await chromium.launch({ headless: true });

    // 1. Admin login context
    const context = await browser.newContext();
    const loginPage = await context.newPage();
    await loginPage.goto('http://127.0.0.1:8000/login');
    await loginPage.fill('input[name="email"]', 'admin@velosha.id');
    await loginPage.fill('input[name="password"]', 'password');
    await Promise.all([
        loginPage.waitForNavigation({ waitUntil: 'networkidle' }),
        loginPage.click('button[type="submit"]'),
    ]);
    console.log('Admin logged in successfully. Current URL:', loginPage.url());
    await loginPage.close();

    const results = [];

    for (const vp of viewports) {
        console.log(`\nTesting Viewport ${vp.name} (${vp.width}x${vp.height})...`);
        const page = await context.newPage();
        await page.setViewportSize({ width: vp.width, height: vp.height });

        await page.goto('http://127.0.0.1:8000/helpdesk/analitik', { waitUntil: 'networkidle' });

        // Check for error banner
        const pageText = await page.content();
        const hasErrorBanner = pageText.includes('Kendala:') || pageText.includes('Halaman ini hanya untuk peran');

        // Check overflow
        const overflow = await page.evaluate(() => {
            const docWidth = document.documentElement.clientWidth;
            const docScroll = document.documentElement.scrollWidth;
            const bodyWidth = document.body.clientWidth;
            const bodyScroll = document.body.scrollWidth;
            const maxScroll = Math.max(docScroll, bodyScroll);
            const maxClient = Math.max(docWidth, bodyWidth);
            return {
                hasOverflow: maxScroll > maxClient,
                client: maxClient,
                scroll: maxScroll,
                diff: maxScroll - maxClient,
            };
        });

        const filename = `analytics_${vp.name}.png`;
        const filepath = path.join(outDir, filename);
        await page.screenshot({ path: filepath, fullPage: true });

        console.log(`  Captured ${filename}:`);
        console.log(`    - Overflow: ${overflow.hasOverflow ? `YES (+${overflow.diff}px)` : 'NO (0px overflow)'}`);
        console.log(`    - Error Banner: ${hasErrorBanner ? 'DETECTED (FAIL)' : 'None (PASS)'}`);

        results.push({
            viewport: vp.name,
            hasOverflow: overflow.hasOverflow,
            diff: overflow.diff,
            hasErrorBanner,
            file: filepath,
        });

        await page.close();
    }

    await browser.close();

    console.log('\n=======================================');
    console.log('SUMMARY OF PHASE 8 EVIDENCE CHECKS:');
    const overflows = results.filter(r => r.hasOverflow);
    const banners = results.filter(r => r.hasErrorBanner);

    console.log(`- Overflows: ${overflows.length === 0 ? 'ALL PASSED (0px overflow across all 5 viewports)' : `${overflows.length} FAILED`}`);
    console.log(`- Error Banners: ${banners.length === 0 ? 'ALL PASSED (No error banners)' : `${banners.length} FAILED`}`);

    if (overflows.length > 0 || banners.length > 0) {
        process.exit(1);
    }
}

main().catch(err => {
    console.error('Error during capture:', err);
    process.exit(1);
});
