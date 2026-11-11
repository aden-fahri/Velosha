import fs from 'fs';
import path from 'path';

const outDir = path.resolve('docs/diagrams');

const diagrams = [
    {
        filename: '01_use_case_diagram.png',
        code: `flowchart LR
    subgraph Aktor
        P((Peserta / Publik))
        O((Operator))
        Y((Penyelenggara))
        A((Administrator))
    end

    subgraph Velosha App
        UC1[Jelajah Kalender & Detail Event]
        UC2[Login Email / Akun Google]
        UC3[Daftar & Checkout Tiket Event]
        UC4[Akses E-Ticket & QR Code]
        UC5[Buat Tiket Tanya Bantuan / Mizu AI]
        UC6[Kelola Antrian & Balas Tiket Helpdesk]
        UC7[Check-in Peserta Hari-H]
        UC8[Kelola Event & Kategori Balap]
        UC9[Pantau Pendaftar & Kuota Event]
        UC10[Kelola Konten CMS Situs]
        UC11[Pantau Analitik & SLA Helpdesk]
    end

    P --> UC1
    P --> UC2
    P --> UC3
    P --> UC4
    P --> UC5

    O --> UC2
    O --> UC6
    O --> UC7

    Y --> UC2
    Y --> UC7
    Y --> UC8
    Y --> UC9

    A --> UC2
    A --> UC6
    A --> UC7
    A --> UC8
    A --> UC9
    A --> UC10
    A --> UC11`
    },
    {
        filename: '02_flowchart_pendaftaran.png',
        code: `flowchart TD
  A[Buka kalender event] --> B[Detail event]
  B --> C{Sudah login?}
  C -- Belum --> D[Masuk / Daftar / Google]
  D --> E[Isi identitas dan pilih kategori]
  C -- Sudah --> E
  E --> F[Pembayaran]
  F --> G{Berhasil?}
  G -- Ya --> H[E-ticket QR dan nomor BIB]
  G -- Tidak --> I[Menunggu atau ulangi]`
    },
    {
        filename: '03_sequence_login_google.png',
        code: `sequenceDiagram
  autonumber
  actor U as Peserta
  participant V as Velosha (Laravel)
  participant G as Google OAuth
  participant D as Database
  U->>V: Klik "Masuk dengan Google"
  V->>G: Redirect (client_id, scope, state)
  G->>U: Minta izin akun
  U->>G: Setuju
  G->>V: Callback /auth/google/callback?code=...
  V->>G: Tukar code, ambil profil
  G-->>V: email, nama, google_id, foto
  V->>D: Cari pengguna (google_id / email)
  alt Belum ada
    V->>D: Buat akun peran peserta
  else Sudah ada
    V->>D: Tautkan google_id
  end
  V-->>U: Sesi dimulai, ke /dashboard`
    },
    {
        filename: '04_arsitektur_sistem.png',
        code: `flowchart TB
    subgraph ClientLayer [Client & Device Layer]
        Browser[Peramban Web: Chrome, Edge, Safari Desktop & Mobile]
    end

    subgraph AppLayer [Aplikasi Velosha - Laravel 12 Backend]
        Router[Routing & Middleware Auth / Role Guard]
        Livewire[Livewire 4 Components - Reaktif]
        Socialite[Laravel Socialite - Google OAuth]
        Filament[Filament 5 Admin Panel]
        QRGen[PHP QR Code Generator]
        GeminiClient[Google Gemini AI - Tanya Mizu]
    end

    subgraph ServiceLayer [Third Party Services]
        GoogleOAuth[Google Cloud OAuth 2.0 API]
        GoogleGemini[Google Gemini 3.1 Flash AI Studio]
    end

    subgraph DataLayer [Storage & Persistence]
        MySQL[(Basis Data MySQL - Tabel Users, Events, Registrations, Tickets, Helpdesk)]
    end

    Browser <--> Router
    Router --> Livewire
    Router --> Socialite
    Router --> Filament
    Socialite <--> GoogleOAuth
    GeminiClient <--> GoogleGemini
    Livewire --> QRGen
    Livewire <--> MySQL
    Filament <--> MySQL`
    }
];

async function downloadDiagrams() {
    console.log('🚀 Mengunduh gambar diagram resolusi tinggi dari Mermaid Ink...');
    for (const d of diagrams) {
        console.log(`Mengunduh ${d.filename}...`);
        try {
            const encoded = Buffer.from(d.code).toString('base64');
            const url = `https://mermaid.ink/img/${encoded}?bgColor=FFFFFF`;
            
            const res = await fetch(url);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const arrayBuffer = await res.arrayBuffer();
            const buffer = Buffer.from(arrayBuffer);
            
            const outPath = path.join(outDir, d.filename);
            fs.writeFileSync(outPath, buffer);
            console.log(`✅ Tersimpan: ${d.filename}`);
        } catch (e) {
            console.error(`❌ Gagal di ${d.filename}:`, e.message);
        }
    }
    console.log('🎉 Selesai mengunduh semua diagram ke docs/diagrams/ !');
}

downloadDiagrams();

