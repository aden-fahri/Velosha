import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const outDir = path.resolve('docs/evidence/helpdesk/fix_operator_ui');
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

const urls = [
    { label: 'queue', path: '/helpdesk' },
    { label: 'detail', path: '/helpdesk/tickets/1' },
];

async function main() {
    console.log('Starting Operator UI Evidence Capture...');
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();

    // 1. Log in as operator
    const loginPage = await context.newPage();
    await loginPage.goto('http://127.0.0.1:8000/login');
    await loginPage.fill('input[name="email"]', 'operator@velosha.id');
    await loginPage.fill('input[name="password"]', 'password');
    await Promise.all([
        loginPage.waitForNavigation({ waitUntil: 'networkidle' }),
        loginPage.click('button[type="submit"]'),
    ]);
    console.log('Logged in successfully as operator. URL:', loginPage.url());
    await loginPage.close();

    const results = [];

    for (const vp of viewports) {
        console.log(`\nTesting Viewport ${vp.name} (${vp.width}x${vp.height})...`);
        const page = await context.newPage();
        await page.setViewportSize({ width: vp.width, height: vp.height });

        for (const item of urls) {
            const targetUrl = `http://127.0.0.1:8000${item.path}`;
            await page.goto(targetUrl, { waitUntil: 'networkidle' });

            // Check for error banner or "Halaman ini hanya untuk peran peserta"
            const pageText = await page.content();
            const hasErrorBanner = pageText.includes('Halaman ini hanya untuk peran peserta') ||
                                   pageText.includes('Kendala:');

            // Overflow check
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

            // Check for Portal Rider link
            const hasPortalRider = await page.evaluate(() => {
                const links = Array.from(document.querySelectorAll('a'));
                return links.some(a => a.textContent.includes('Portal Rider') || a.getAttribute('href')?.includes('/dashboard'));
            });

            // Check for Mizu chat widget
            const hasMizuWidget = await page.evaluate(() => {
                return !!document.querySelector('[aria-label="Tanya Mizu"]') || !!document.querySelector('.mizu-chat-container');
            });

            const filename = `${item.label}_${vp.name}.png`;
            const filepath = path.join(outDir, filename);
            await page.screenshot({ path: filepath, fullPage: true });

            console.log(`  Captured ${filename}:`);
            console.log(`    - Overflow: ${overflow.hasOverflow ? `YES (+${overflow.diff}px)` : 'NO (0px overflow)'}`);
            console.log(`    - Error Banner: ${hasErrorBanner ? 'DETECTED (FAIL)' : 'None (PASS)'}`);
            console.log(`    - Portal Rider Link: ${hasPortalRider ? 'DETECTED (FAIL)' : 'None (PASS)'}`);
            console.log(`    - Mizu Widget: ${hasMizuWidget ? 'DETECTED (FAIL)' : 'None (PASS)'}`);

            results.push({
                page: item.label,
                viewport: vp.name,
                hasOverflow: overflow.hasOverflow,
                hasErrorBanner,
                hasPortalRider,
                hasMizuWidget,
                diff: overflow.diff,
                file: filepath,
            });
        }

        await page.close();
    }

    await browser.close();

    console.log('\n=======================================');
    console.log('SUMMARY OF CHECKS:');
    const overflows = results.filter(r => r.hasOverflow);
    const banners = results.filter(r => r.hasErrorBanner);
    const portalLinks = results.filter(r => r.hasPortalRider);
    const mizuWidgets = results.filter(r => r.hasMizuWidget);

    console.log(`- Overflows: ${overflows.length === 0 ? 'ALL PASSED (0px overflow across all viewports)' : `${overflows.length} FAILED`}`);
    console.log(`- Error Banners: ${banners.length === 0 ? 'ALL PASSED (No error banner found)' : `${banners.length} FAILED`}`);
    console.log(`- Portal Rider Links: ${portalLinks.length === 0 ? 'ALL PASSED (No portal rider links)' : `${portalLinks.length} FAILED`}`);
    console.log(`- Mizu Widget: ${mizuWidgets.length === 0 ? 'ALL PASSED (Not rendered)' : `${mizuWidgets.length} FAILED`}`);

    if (overflows.length > 0 || banners.length > 0 || portalLinks.length > 0 || mizuWidgets.length > 0) {
        process.exit(1);
    }
}

main().catch(err => {
    console.error('Error during capture:', err);
    process.exit(1);
});
