import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const outDir = path.resolve('docs/evidence/restore_portal_auth');
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

async function checkOverflow(page) {
    return await page.evaluate(() => {
        const doc = document.documentElement;
        const scrollW = doc.scrollWidth;
        const clientW = doc.clientWidth;
        const diff = Math.max(0, scrollW - clientW);
        return {
            scrollWidth: scrollW,
            clientWidth: clientW,
            overflow: diff,
            hasOverflow: diff > 1, // allow 1px subpixel
        };
    });
}

async function main() {
    console.log('Starting Restored Portal & Auth Redesign Evidence Capture with reduced-motion...');
    const browser = await chromium.launch({ headless: true });

    // 1. Guest Context with reduced-motion: 'reduce'
    const guestContext = await browser.newContext({
        reducedMotion: 'reduce',
    });

    // 2. Peserta Context with reduced-motion: 'reduce'
    const pesContext = await browser.newContext({
        reducedMotion: 'reduce',
    });
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

        // A. /login (Guest)
        const loginPage = await guestContext.newPage();
        await loginPage.setViewportSize({ width: vp.width, height: vp.height });
        await loginPage.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle' });
        const loginOverflow = await checkOverflow(loginPage);
        const loginFilename = `login_${vp.name}.png`;
        await loginPage.screenshot({ path: path.join(outDir, loginFilename), fullPage: true });
        await loginPage.close();

        // B. /register (Guest)
        const regPage = await guestContext.newPage();
        await regPage.setViewportSize({ width: vp.width, height: vp.height });
        await regPage.goto('http://127.0.0.1:8000/register', { waitUntil: 'networkidle' });
        const regOverflow = await checkOverflow(regPage);
        const regFilename = `register_${vp.name}.png`;
        await regPage.screenshot({ path: path.join(outDir, regFilename), fullPage: true });
        await regPage.close();

        // C. /dashboard (Peserta)
        const dashPage = await pesContext.newPage();
        await dashPage.setViewportSize({ width: vp.width, height: vp.height });
        await dashPage.goto('http://127.0.0.1:8000/dashboard', { waitUntil: 'networkidle' });
        const dashOverflow = await checkOverflow(dashPage);
        const dashFilename = `dashboard_${vp.name}.png`;
        await dashPage.screenshot({ path: path.join(outDir, dashFilename), fullPage: true });
        await dashPage.close();

        // D. /dashboard/bantuan (Peserta)
        const bantuanPage = await pesContext.newPage();
        await bantuanPage.setViewportSize({ width: vp.width, height: vp.height });
        await bantuanPage.goto('http://127.0.0.1:8000/dashboard/bantuan', { waitUntil: 'networkidle' });
        const bantuanOverflow = await checkOverflow(bantuanPage);
        const bantuanFilename = `bantuan_${vp.name}.png`;
        await bantuanPage.screenshot({ path: path.join(outDir, bantuanFilename), fullPage: true });
        await bantuanPage.close();

        results.push({
            viewport: vp.name,
            size: `${vp.width}x${vp.height}`,
            login: loginOverflow,
            register: regOverflow,
            dashboard: dashOverflow,
            bantuan: bantuanOverflow,
        });

        console.log(`  Login: ${loginOverflow.scrollWidth}px / ${loginOverflow.clientWidth}px (overflow: ${loginOverflow.overflow}px)`);
        console.log(`  Register: ${regOverflow.scrollWidth}px / ${regOverflow.clientWidth}px (overflow: ${regOverflow.overflow}px)`);
        console.log(`  Dashboard: ${dashOverflow.scrollWidth}px / ${dashOverflow.clientWidth}px (overflow: ${dashOverflow.overflow}px)`);
        console.log(`  Bantuan: ${bantuanOverflow.scrollWidth}px / ${bantuanOverflow.clientWidth}px (overflow: ${bantuanOverflow.overflow}px)`);
    }

    await browser.close();

    console.log('\n--- OVERFLOW SUMMARY ---');
    console.table(results.map(r => ({
        Viewport: r.viewport,
        LoginOverflow: `${r.login.overflow}px`,
        RegisterOverflow: `${r.register.overflow}px`,
        DashboardOverflow: `${r.dashboard.overflow}px`,
        BantuanOverflow: `${r.bantuan.overflow}px`,
    })));

    const anyOverflow = results.some(r =>
        r.login.hasOverflow || r.register.hasOverflow || r.dashboard.hasOverflow || r.bantuan.hasOverflow
    );

    if (anyOverflow) {
        console.error('FAILED: Horizontal overflow detected!');
        process.exit(1);
    } else {
        console.log('SUCCESS: All pages have 0px horizontal overflow across all 5 viewports with reduced-motion!');
    }
}

main().catch(err => {
    console.error('Execution error:', err);
    process.exit(1);
});
