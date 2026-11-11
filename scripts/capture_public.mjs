import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const outDir = path.resolve('docs/screenshots');
if (!fs.existsSync(outDir)) {
    fs.mkdirSync(outDir, { recursive: true });
}

const pages = [
    { name: '01_beranda', url: 'http://127.0.0.1:8000/' },
    { name: '02_kalender_event', url: 'http://127.0.0.1:8000/events' },
    { name: '03_tentang_kami', url: 'http://127.0.0.1:8000/about' },
    { name: '04_regulasi', url: 'http://127.0.0.1:8000/rule' },
    { name: '05_bantuan', url: 'http://127.0.0.1:8000/help' },
    { name: '06_login_masuk', url: 'http://127.0.0.1:8000/login' },
    { name: '07_daftar_akun', url: 'http://127.0.0.1:8000/register' },
];

async function run() {
    console.log('🚀 Membuka browser Playwright...');
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage({
        viewport: { width: 1440, height: 900 }
    });

    for (const p of pages) {
        console.log(`Mengambil ${p.name}...`);
        try {
            await page.goto(p.url, { waitUntil: 'domcontentloaded', timeout: 30000 });
            await page.waitForTimeout(2000);
            await page.screenshot({ path: path.join(outDir, `${p.name}.png`), fullPage: true });
            console.log(`✅ Berhasil: ${p.name}.png`);
        } catch (err) {
            console.error(`❌ Gagal di ${p.name}:`, err.message);
        }
    }

    await browser.close();
    console.log('Semua halaman publik selesai diambil!');
}

run();

