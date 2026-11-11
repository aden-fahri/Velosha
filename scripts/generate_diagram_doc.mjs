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

const diagramsDir = path.resolve('docs/diagrams');
const outputFile = path.resolve('docs/Dokumentasi_Diagram_Velosha.docx');

function getPngDimensions(buffer) {
    if (buffer.length >= 24 && buffer.toString('ascii', 1, 4) === 'PNG') {
        const width = buffer.readUInt32BE(16);
        const height = buffer.readUInt32BE(20);
        return { width, height };
    }
    return { width: 800, height: 600 };
}

const diagrams = [
    { title: '1. Use Case Diagram (Hak Akses 4 Role: Peserta, Penyelenggara, Operator, Admin)', file: '01_use_case_diagram.png' },
    { title: '2. Flowchart Alur Pendaftaran Event (Dari Beranda sampai E-Ticket)', file: '02_flowchart_pendaftaran.png' },
    { title: '3. Sequence Diagram Login Google (OAuth 2.0 Laravel Socialite)', file: '03_sequence_login_google.png' },
    { title: '4. Diagram Arsitektur Sistem Velosha', file: '04_arsitektur_sistem.png' },
];

async function generateDiagramDocx() {
    console.log('📄 Membuat dokumen Word Diagram Velosha...');

    const children = [
        new Paragraph({
            text: 'DOKUMENTASI DIAGRAM SISTEM VELOSHA',
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

    const MAX_WIDTH = 590;

    for (const d of diagrams) {
        const imgPath = path.join(diagramsDir, d.file);

        children.push(
            new Paragraph({
                children: [
                    new TextRun({
                        text: d.title,
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
            const dim = getPngDimensions(buffer);

            const scale = Math.min(1, MAX_WIDTH / dim.width);
            const finalWidth = Math.round(dim.width * scale);
            const finalHeight = Math.round(dim.height * scale);

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

    const docBuffer = await Packer.toBuffer(doc);
    fs.writeFileSync(outputFile, docBuffer);
    console.log(`✅ File Word Diagram tersimpan di: ${outputFile}`);
}

generateDiagramDocx();

