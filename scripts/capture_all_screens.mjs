import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const outDir = path.resolve('docs/screenshots');
if (!fs.existsSync(outDir)) {
    fs.mkdirSync(outDir, { recursive: true });
}

const publicPages = [
    { name: '01_beranda', url: 'http://127.0.0.1:8000/' },
    { name: '02_kalender_event', url: 'http://127.0.0.1:8000/events' },
    { name: '03_tentang_kami', url: 'http://127.0.0.1:8000/about' },
    { name: '04_regulasi', url: 'http://127.0.0.1:8000/rule' },
    { name: '05_bantuan', url: 'http://127.0.0.1:8000/help' },
    { name: '06_login_masuk', url: 'http://127.0.0.1:8000/login' },
    { name: '07_daftar_akun', url: 'http://127.0.0.1:8000/register' },
];

async function captureAll() {
    console.log('🚀 Memulai proses pengambilan screenshot seluruh tampilan...');
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1440, height: 900 },
        deviceScaleFactor: 1,
    });
    const page = await context.newPage();

    // 1. Capture public pages
    for (const p of publicPages) {
        console.log(`📸 Menangkap halaman: ${p.name}`);
        try {
            await page.goto(p.url, { waitUntil: 'load', timeout: 15000 });
            await page.waitForTimeout(1000);
            await page.screenshot({
                path: path.join(outDir, `${p.name}.png`),
                fullPage: true,
            });
            console.log(`✅ Berhasil: ${p.name}.png`);
        } catch (e) {
            console.error(`❌ Gagal di ${p.name}:`, e.message);
        }
    }

    // 2. Auth captures (login as admin)
    console.log('🔑 Login sebagai admin...');
    try {
        await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'load', timeout: 15000 });
        await page.fill('input[name="email"]', 'admin@velosha.id');
        await page.fill('input[name="password"]', 'password');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'load', timeout: 15000 }),
            page.click('button[type="submit"]'),
        ]);

        console.log('📸 Menangkap: 08_admin_dashboard');
        await page.goto('http://127.0.0.1:8000/admin', { waitUntil: 'load', timeout: 15000 });
        await page.waitForTimeout(1500);
        await page.screenshot({
            path: path.join(outDir, '08_admin_dashboard.png'),
            fullPage: false,
        });
        console.log('✅ Berhasil: 08_admin_dashboard.png');

        console.log('📸 Menangkap: 09_peserta_dashboard');
        await page.goto('http://127.0.0.1:8000/dashboard', { waitUntil: 'load', timeout: 15000 });
        await page.waitForTimeout(1500);
        await page.screenshot({
            path: path.join(outDir, '09_peserta_dashboard.png'),
            fullPage: true,
        });
        console.log('✅ Berhasil: 09_peserta_dashboard.png');

        console.log('📸 Menangkap: 10_helpdesk_antrian');
        await page.goto('http://127.0.0.1:8000/helpdesk/queue', { waitUntil: 'load', timeout: 15000 });
        await page.waitForTimeout(1500);
        await page.screenshot({
            path: path.join(outDir, '10_helpdesk_antrian.png'),
            fullPage: true,
        });
        console.log('✅ Berhasil: 10_helpdesk_antrian.png');
    } catch (e) {
        console.error('❌ Error saat auth captures:', e.message);
    }

    await browser.close();
    console.log('🎉 Selesai! Semua screenshot tersimpan.');
}

captureAll();
