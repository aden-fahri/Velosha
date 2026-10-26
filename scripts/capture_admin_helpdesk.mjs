import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const outDir = path.resolve('docs/evidence/helpdesk/admin_entry');
if (!fs.existsSync(outDir)) {
    fs.mkdirSync(outDir, { recursive: true });
}

const viewports = [
    { name: '1440', width: 1440, height: 900 },
    { name: '1024', width: 1024, height: 768 },
    { name: '768', width: 768, height: 1024 },
];

async function main() {
    console.log('Starting Playwright Admin Helpdesk evidence capture...');
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();

    // 1. Log in as admin
    const page = await context.newPage();
    console.log('Logging in as admin@velosha.id...');
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle' });
    await page.fill('input[name="email"]', 'admin@velosha.id');
    await page.fill('input[name="password"]', 'password');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle' }),
        page.click('button[type="submit"]'),
    ]);
    console.log('Current URL after login:', page.url());

    // If redirected somewhere else, navigate explicitly to /admin
    if (!page.url().includes('/admin')) {
        await page.goto('http://127.0.0.1:8000/admin', { waitUntil: 'networkidle' });
    }

    const overflowReports = [];

    for (const vp of viewports) {
        console.log(`\nCapturing viewport ${vp.name} (${vp.width}x${vp.height})...`);
        await page.setViewportSize({ width: vp.width, height: vp.height });
        await page.goto('http://127.0.0.1:8000/admin', { waitUntil: 'networkidle' });

        // Ensure helpdesk widget and group are visible
        await page.waitForTimeout(1000);

        // At 768, if sidebar is collapsed, open it or capture dashboard
        const overflow = await page.evaluate(() => {
            const docWidth = document.documentElement.clientWidth;
            const docScroll = document.documentElement.scrollWidth;
            const bodyWidth = document.body.clientWidth;
            const bodyScroll = document.body.scrollWidth;
            const maxScroll = Math.max(docScroll, bodyScroll);
            const maxClient = Math.max(docWidth, bodyWidth);
            const delta = maxScroll - maxClient;
            return {
                docWidth,
                docScroll,
                bodyWidth,
                bodyScroll,
                delta,
                hasOverflow: delta > 1,
            };
        });

        overflowReports.push({ viewport: vp.name, overflow });
        console.log(`Viewport ${vp.name} overflow delta: ${overflow.delta}px (hasOverflow: ${overflow.hasOverflow})`);

        // Screenshot dashboard (shows HelpdeskOverview widget)
        const dashboardScreenshot = path.join(outDir, `admin_dashboard_${vp.name}.png`);
        await page.screenshot({ path: dashboardScreenshot, fullPage: true });
        console.log(`Saved screenshot: ${dashboardScreenshot}`);

        // If sidebar toggle exists (e.g. mobile/tablet 768), open sidebar and take a screenshot of sidebar
        if (vp.width <= 1024) {
            try {
                const sidebarBtn = await page.$('button[aria-label*="sidebar" i], button[title*="sidebar" i], .fi-topbar button.fi-icon-btn');
                if (sidebarBtn) {
                    await sidebarBtn.click();
                    await page.waitForTimeout(500);
                    const sidebarScreenshot = path.join(outDir, `admin_sidebar_open_${vp.name}.png`);
                    await page.screenshot({ path: sidebarScreenshot });
                    console.log(`Saved sidebar open screenshot: ${sidebarScreenshot}`);
                    // Close sidebar again
                    await page.keyboard.press('Escape');
                }
            } catch (err) {
                console.log(`Sidebar toggle not needed or not found: ${err.message}`);
            }
        }
    }

    await browser.close();

    console.log('\n--- Overflow Check Summary ---');
    console.table(overflowReports);

    fs.writeFileSync(
        path.join(outDir, 'overflow_report.json'),
        JSON.stringify(overflowReports, null, 2)
    );
}

main().catch((err) => {
    console.error('Capture failed:', err);
    process.exit(1);
});
