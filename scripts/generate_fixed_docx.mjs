import fs from 'fs';
import path from 'path';
import { imageSize } from 'image-size';
import {
    Document,
    Packer,
    Paragraph,
    TextRun,
    HeadingLevel,
    ImageRun,
    AlignmentType
} from 'docx';

const diagramsDir = path.resolve('docs/diagrams');
const screenshotsDir = path.resolve('docs/screenshots');

// Kertas A4: lebar 8.27 inch = 11906 dxa, margin 720 dxa (0.5 inch) di kiri dan kanan -> Lebar konten = 10466 dxa = 523 pt
const MAX_DOCX_WIDTH = 500; 

function createDocx(title, items, baseDir, outputFilename) {
    console.log(`\n📄 Membuat dokumen: ${outputFilename}`);
    
    const children = [
        new Paragraph({
            text: title.toUpperCase(),
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

    for (const item of items) {
        const imgPath = path.join(baseDir, item.file);

        children.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: item.title,
                        bold: true,
                        size: 26,
                        color: '10282E',
                    }),
                ],
                spacing: { before: 280, after: 140 },
            })
        );

        if (fs.existsSync(imgPath)) {
            const buffer = fs.readFileSync(imgPath);
            const dim = imageSize(buffer);

            // RUMUS ASPEK RASIO MURNI 100%:
            // ratio = dim.height / dim.width
            // finalHeight = finalWidth * ratio
            const scale = Math.min(1, MAX_DOCX_WIDTH / dim.width);
            const finalWidth = Math.round(dim.width * scale);
            const finalHeight = Math.round(dim.height * (finalWidth / dim.width));

            console.log(`[${item.file}] Asli: ${dim.width}x${dim.height} | Di Word: ${finalWidth}x${finalHeight} (Rasio Asli: ${(dim.width/dim.height).toFixed(2)})`);

            children.push(
                new Paragraph({
                    children: [
                        new ImageRun({
                            data: buffer,
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

    return Packer.toBuffer(doc).then(buf => {
        fs.writeFileSync(outputFilename, buf);
        console.log(`✅ Selesai: ${outputFilename}`);
    });
}

async function run() {
    // 1. Dokumen Diagram
    const diagramItems = [
        { title: '1. Use Case Diagram (Hak Akses 4 Role: Peserta, Penyelenggara, Operator, Admin)', file: '01_use_case_diagram.png' },
        { title: '2. Flowchart Alur Pendaftaran Event (Dari Beranda sampai E-Ticket)', file: '02_flowchart_pendaftaran.png' },
        { title: '3. Sequence Diagram Login Google (OAuth 2.0 Laravel Socialite)', file: '03_sequence_login_google.png' },
        { title: '4. Diagram Arsitektur Sistem Velosha', file: '04_arsitektur_sistem.png' },
    ];
    await createDocx('Dokumentasi Diagram Sistem Velosha', diagramItems, diagramsDir, path.resolve('docs/Dokumentasi_Diagram_Velosha_Fixed.docx'));

    // 2. Dokumen Screenshot Tampilan
    const screenshotItems = [
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
    await createDocx('Dokumentasi Tampilan Aplikasi Velosha', screenshotItems, screenshotsDir, path.resolve('docs/Dokumentasi_Tampilan_Velosha_Fixed.docx'));
}

run();

