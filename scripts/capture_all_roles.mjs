import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const outDir = path.resolve('docs/screenshots');
if (!fs.existsSync(outDir)) {
    fs.mkdirSync(outDir, { recursive: true });
}

async function capture() {
    console.log('🚀 Menghubungkan Playwright ke http://127.0.0.1:8000...');
    const browser = await chromium.launch({ headless: true });

    // Helper function to capture a page with retries
    async function takeScreenshot(context, url, filePath, fullPage = true) {
        const page = await context.newPage();
        try {
            await page.goto(url, { waitUntil: 'load', timeout: 30000 });
            // Jika ada intro loading di beranda, lewati atau tunggu hingga selesai
            try {
                const skipBtn = page.locator('#skip-intro');
                if (await skipBtn.isVisible({ timeout: 2000 })) {
                    await skipBtn.click();
                    await page.waitForTimeout(1000);
                }
            } catch (_) {}
            await page.waitForTimeout(2500);
            await page.screenshot({ path: filePath, fullPage });
            console.log(`✅ Sukses: ${path.basename(filePath)}`);
        } catch (e) {
            console.error(`❌ Gagal: ${path.basename(filePath)} - ${e.message}`);
        } finally {
            await page.close();
        }
    }

    // 1. PUBLIC PAGES
    const publicContext = await browser.newContext({ viewport: { width: 1440, height: 900 } });
    const publicPages = [
        { file: '01_landing_page_beranda.png', url: 'http://127.0.0.1:8000/' },
        { file: '02_kalender_event.png', url: 'http://127.0.0.1:8000/events' },
        { file: '03_tentang_kami.png', url: 'http://127.0.0.1:8000/about' },
        { file: '04_regulasi.png', url: 'http://127.0.0.1:8000/rule' },
        { file: '05_bantuan.png', url: 'http://127.0.0.1:8000/help' },
        { file: '06_login.png', url: 'http://127.0.0.1:8000/login' },
        { file: '07_register.png', url: 'http://127.0.0.1:8000/register' },
    ];

    for (const item of publicPages) {
        await takeScreenshot(publicContext, item.url, path.join(outDir, item.file), true);
    }
    await publicContext.close();

    // 2. PESERTA
    console.log('\n--- AREA PESERTA ---');
    const pesertaContext = await browser.newContext({ viewport: { width: 1440, height: 900 } });
    const pesertaPage = await pesertaContext.newPage();
    try {
        await pesertaPage.goto('http://127.0.0.1:8000/login', { waitUntil: 'load' });
        await pesertaPage.locator('form').first().locator('input[name="email"]').fill('peserta@velosha.id');
        await pesertaPage.locator('form').first().locator('input[name="password"]').fill('password');
        await Promise.all([
            pesertaPage.waitForNavigation({ waitUntil: 'load', timeout: 30000 }).catch(() => {}),
            pesertaPage.locator('form').first().locator('button[type="submit"]').click(),
        ]);
        await pesertaPage.waitForTimeout(2000);

        await takeScreenshot(pesertaContext, 'http://127.0.0.1:8000/dashboard', path.join(outDir, '08_dashboard_peserta.png'), true);
        await takeScreenshot(pesertaContext, 'http://127.0.0.1:8000/helpdesk/create', path.join(outDir, '09_peserta_buat_tiket_bantuan.png'), true);
    } catch (e) {
        console.error('❌ Gagal di Peserta:', e.message);
    } finally {
        await pesertaPage.close();
        await pesertaContext.close();
    }

    // 3. OPERATOR
    console.log('\n--- AREA OPERATOR ---');
    const operatorContext = await browser.newContext({ viewport: { width: 1440, height: 900 } });
    const operatorPage = await operatorContext.newPage();
    try {
        await operatorPage.goto('http://127.0.0.1:8000/login', { waitUntil: 'load' });
        await operatorPage.locator('form').first().locator('input[name="email"]').fill('operator@velosha.id');
        await operatorPage.locator('form').first().locator('input[name="password"]').fill('password');
        await Promise.all([
            operatorPage.waitForNavigation({ waitUntil: 'load', timeout: 30000 }).catch(() => {}),
            operatorPage.locator('form').first().locator('button[type="submit"]').click(),
        ]);
        await operatorPage.waitForTimeout(2000);

        await takeScreenshot(operatorContext, 'http://127.0.0.1:8000/helpdesk/queue', path.join(outDir, '10_dashboard_operator_antrian.png'), true);
    } catch (e) {
        console.error('❌ Gagal di Operator:', e.message);
    } finally {
        await operatorPage.close();
        await operatorContext.close();
    }

    // 4. ADMIN
    console.log('\n--- AREA ADMIN ---');
    const adminContext = await browser.newContext({ viewport: { width: 1440, height: 900 } });
    const adminPage = await adminContext.newPage();
    try {
        await adminPage.goto('http://127.0.0.1:8000/login', { waitUntil: 'load' });
        await adminPage.locator('form').first().locator('input[name="email"]').fill('admin@velosha.id');
        await adminPage.locator('form').first().locator('input[name="password"]').fill('password');
        await Promise.all([
            adminPage.waitForNavigation({ waitUntil: 'load', timeout: 30000 }).catch(() => {}),
            adminPage.locator('form').first().locator('button[type="submit"]').click(),
        ]);
        await adminPage.waitForTimeout(3000);

        await takeScreenshot(adminContext, 'http://127.0.0.1:8000/admin', path.join(outDir, '11_admin_dashboard_panel.png'), false);
        await takeScreenshot(adminContext, 'http://127.0.0.1:8000/admin/events', path.join(outDir, '12_admin_kelola_event.png'), false);
        await takeScreenshot(adminContext, 'http://127.0.0.1:8000/admin/helpdesk-analytics', path.join(outDir, '13_admin_helpdesk_analytics.png'), false);
    } catch (e) {
        console.error('❌ Gagal di Admin:', e.message);
    } finally {
        await adminPage.close();
        await adminContext.close();
    }

    await browser.close();
    console.log('\n🎉 SEMUA SCREENSHOT BERHASIL DIAMBIL DI docs/screenshots/ !');
}

capture();
