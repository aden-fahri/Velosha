import fs from 'fs';
import path from 'path';
import {
    Document,
    Packer,
    Paragraph,
    TextRun,
    HeadingLevel,
    ImageRun,
    AlignmentType
} from 'docx';

const screenshotsDir = path.resolve('docs/screenshots');
const outputFile = path.resolve('docs/Dokumentasi_Tampilan_Velosha_Natural.docx');

// Fungsi membaca ukuran asli PNG secara native dari buffer header IHDR
function getPngDimensions(buffer) {
    if (buffer.length >= 24 && buffer.toString('ascii', 1, 4) === 'PNG') {
        const width = buffer.readUInt32BE(16);
        const height = buffer.readUInt32BE(20);
        return { width, height };
    }
    return { width: 1440, height: 900 }; // fallback
}

const items = [
    { title: '01_landing page', file: '01_landing_page_beranda.png' },
    { title: '02_kalender event', file: '02_kalender_event.png' },
    { title: '03_tentang kami', file: '03_tentang_kami.png' },
    { title: '04_regulasi', file: '04_regulasi.png' },
    { title: '05_bantuan', file: '05_bantuan.png' },
    { title: '06_login', file: '06_login.png' },
    { title: '07_register', file: '07_register.png' },
    { title: '08_dashboard peserta', file: '08_dashboard_peserta.png' },
    { title: '09_peserta buat tiket bantuan', file: '09_peserta_buat_tiket_bantuan.png' },
    { title: '10_dashboard operator antrian', file: '10_dashboard_operator_antrian.png' },
    { title: '11_admin dashboard panel', file: '11_admin_dashboard_panel.png' },
    { title: '12_admin kelola event', file: '12_admin_kelola_event.png' },
    { title: '13_admin helpdesk analytics', file: '13_admin_helpdesk_analytics.png' },
];

async function generateDocx() {
    console.log('📄 Membuat dokumen dengan proporsi gambar natural (aspek rasio 100% asli)...');

    const children = [
        new Paragraph({
            text: 'DOKUMENTASI TAMPILAN APLIKASI VELOSHA',
            heading: HeadingLevel.TITLE,
            alignment: AlignmentType.CENTER,
            spacing: { after: 300 },
        }),
        new Paragraph({
            children: [
                new TextRun({ text: 'Nama  : ', bold: true, size: 24 }),
                new TextRun({ text: '..........................................................', size: 24 }),
            ],
            spacing: { after: 120 },
        }),
        new Paragraph({
            children: [
                new TextRun({ text: 'Kelas : ', bold: true, size: 24 }),
                new TextRun({ text: '..........................................................', size: 24 }),
            ],
            spacing: { after: 400 },
        }),
        new Paragraph({
            children: [
                new TextRun({
                    text: '------------------------------------------------------------------------------------------------------------------',
                    color: '999999',
                }),
            ],
            spacing: { after: 300 },
        }),
    ];

    const MAX_WIDTH = 590; // Lebar halaman yang bisa ditampung dokumen Word

    for (const item of items) {
        const imgPath = path.join(screenshotsDir, item.file);

        children.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: item.title,
                        bold: true,
                        size: 26,
                        color: '1A1A1A',
                    }),
                ],
                spacing: { before: 280, after: 140 },
            })
        );

        if (fs.existsSync(imgPath)) {
            const imageBuffer = fs.readFileSync(imgPath);
            const originalDim = getPngDimensions(imageBuffer);

            // Hitung skala aspect ratio murni agar TIDAK GEPENG
            const scale = MAX_WIDTH / originalDim.width;
            const finalWidth = Math.round(originalDim.width * scale);
            const finalHeight = Math.round(originalDim.height * scale);

            console.log(`Dimensi ${item.file}: Asli (${originalDim.width}x${originalDim.height}) -> Skala Proporsional (${finalWidth}x${finalHeight})`);

            children.push(
                new Paragraph({
                    children: [
                        new ImageRun({
                            data: imageBuffer,
                            transformation: {
                                width: finalWidth,
                                height: finalHeight,
                            },
                        }),
                    ],
                    alignment: AlignmentType.CENTER,
                    spacing: { after: 400 },
                })
            );
        } else {
            children.push(
                new Paragraph({
                    children: [
                        new TextRun({
                            text: `[File gambar ${item.file} tidak ditemukan di docs/screenshots]`,
                            italics: true,
                            color: 'FF0000',
                        }),
                    ],
                    spacing: { after: 300 },
                })
            );
        }
    }

    const doc = new Document({
        sections: [
            {
                properties: {
                    page: {
                        margin: {
                            top: 720,
                            right: 720,
                            bottom: 720,
                            left: 720,
                        },
                    },
                },
                children: children,
            },
        ],
    });

    const buffer = await Packer.toBuffer(doc);
    fs.writeFileSync(outputFile, buffer);
    console.log(`\n🎉 SELESAI! Dokumen tersimpan: ${outputFile}`);
}

generateDocx();
