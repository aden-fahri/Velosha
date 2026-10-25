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

async function main() {
    console.log('Starting Operator UI & Reporter Timeline Evidence Capture...');
    const browser = await chromium.launch({ headless: true });

    // 1. Operator context
    const opContext = await browser.newContext();
    const opLogin = await opContext.newPage();
    await opLogin.goto('http://127.0.0.1:8000/login');
    await opLogin.fill('input[name="email"]', 'operator@velosha.id');
    await opLogin.fill('input[name="password"]', 'password');
    await Promise.all([
        opLogin.waitForNavigation({ waitUntil: 'networkidle' }),
        opLogin.click('button[type="submit"]'),
    ]);
    console.log('Operator logged in successfully.');
    await opLogin.close();

    // 2. Peserta context
    const pesContext = await browser.newContext();
    const pesLogin = await pesContext.newPage();
    await pesLogin.goto('http://127.0.0.1:8000/login');
    await pesLogin.fill('input[name="email"]', 'peserta@velosha.id');
    await pesLogin.fill('input[name="password"]', 'password');
    await Promise.all([
        pesLogin.waitForNavigation({ waitUntil: 'networkidle' }),
        pesLogin.click('button[type="submit"]'),
    ]);
    console.log('Peserta logged in successfully.');
    await pesLogin.close();

    const results = [];

    for (const vp of viewports) {
        console.log(`\nTesting Viewport ${vp.name} (${vp.width}x${vp.height})...`);

        // A. Queue page as operator
        const opPage = await opContext.newPage();
        await opPage.setViewportSize({ width: vp.width, height: vp.height });
        await opPage.goto('http://127.0.0.1:8000/helpdesk', { waitUntil: 'networkidle' });

        const queueOverflow = await checkOverflow(opPage);
        const queueFilename = `queue_${vp.name}.png`;
        await opPage.screenshot({ path: path.join(outDir, queueFilename), fullPage: true });
        console.log(`  Captured ${queueFilename} - Overflow: ${queueOverflow.hasOverflow ? `YES (+${queueOverflow.diff}px)` : 'NO (0px)'}`);

        // B. Detail page as operator
        await opPage.goto('http://127.0.0.1:8000/helpdesk/tickets/1', { waitUntil: 'networkidle' });
        const detailOverflow = await checkOverflow(opPage);
        const detailFilename = `detail_${vp.name}.png`;
        await opPage.screenshot({ path: path.join(outDir, detailFilename), fullPage: true });
        console.log(`  Captured ${detailFilename} - Overflow: ${detailOverflow.hasOverflow ? `YES (+${detailOverflow.diff}px)` : 'NO (0px)'}`);
        await opPage.close();

        // C. Reporter timeline as peserta
        const pesPage = await pesContext.newPage();
        await pesPage.setViewportSize({ width: vp.width, height: vp.height });
        await pesPage.goto('http://127.0.0.1:8000/dashboard/bantuan/1', { waitUntil: 'networkidle' });
        const reporterOverflow = await checkOverflow(pesPage);
        const reporterFilename = `reporter_detail_${vp.name}.png`;
        await pesPage.screenshot({ path: path.join(outDir, reporterFilename), fullPage: true });
        console.log(`  Captured ${reporterFilename} - Overflow: ${reporterOverflow.hasOverflow ? `YES (+${reporterOverflow.diff}px)` : 'NO (0px)'}`);
        await pesPage.close();

        results.push(
            { page: 'queue', vp: vp.name, overflow: queueOverflow },
            { page: 'detail', vp: vp.name, overflow: detailOverflow },
            { page: 'reporter_detail', vp: vp.name, overflow: reporterOverflow }
        );
    }

    await browser.close();

    const overflows = results.filter(r => r.overflow.hasOverflow);
    console.log(`\nEvidence capture complete. Overflows: ${overflows.length === 0 ? 'ALL PASSED (0px overflow across all viewports)' : `${overflows.length} FAILED`}`);
    if (overflows.length > 0) process.exit(1);
}

async function checkOverflow(page) {
    return await page.evaluate(() => {
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
}

main().catch(err => {
    console.error('Error during capture:', err);
    process.exit(1);
});
