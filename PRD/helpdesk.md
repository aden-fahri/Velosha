# helpdesk.md — Desain Modul Helpdesk Terpadu (Velosha)

Khusus modul helpdesk. Acuan visual: 6 board di mockup (Pelapor: Buat tiket, Tiket saya. Operator: Antrian tiket, Detail tiket. Admin: Dashboard performa, Kebijakan SLA) dan token di `PRD/desain-revisi.md` §3–4. Database MySQL 100%.

Status keputusan pemilik: peran baru `operator` (opsi A); pelapor memakai portal peserta; helpdesk tidak ditaruh di landing page.

---

## 1. Tujuan

Modul helpdesk dua arah antara pengguna (pelapor) dan pengelola, dengan: anti duplikasi aduan (full text search yang belajar dari aduan sebelumnya), SLA (respon time dan resolution time), saran balasan otomatis, dan dashboard analitik (kecepatan respons rata-rata dalam menit atau jam, tingkat kepuasan).

## 2. Peran dan hak akses (RBAC)

| Peran | Padanan di Velosha | Hak |
|---|---|---|
| Pelapor | `peserta` | Buat tiket, lihat progres tiket miliknya, membalas, memberi rating setelah Closed |
| Operator Helpdesk | `operator` (**baru**) | Kelola antrian, ambil tiket, membalas, mengubah status dan prioritas |
| Admin/Manager | `admin` | Dashboard performa operator dan analitik kendala, Kebijakan SLA, template balasan, lihat semua tiket |

`penyelenggara` tidak punya akses helpdesk (hanya mengelola event).

Implementasi: tambah nilai `operator` pada enum `UserRole` dan kolom `users.role` (migration MySQL `ALTER` untuk kolom enum, dengan `down()` yang aman), method `isOperator()`, daftar `ROLES`, dan `home()` operator mengarah ke antrian. Tidak mengubah `canAccessPanel` Filament (operator tidak masuk panel Filament). Rute memakai middleware `role` yang sudah ada.

Aturan akses tegas (wajib dites):
- Pelapor hanya melihat tiket miliknya. Tiket orang lain → 404 (bukan 403, agar ID tidak bisa ditebak).
- Rating hanya oleh pelapor, hanya setelah status Closed, satu kali per tiket.
- Pelapor tidak bisa membalas tiket Closed.
- Operator tidak bisa membuka dashboard admin dan halaman SLA.
- Admin boleh melihat semua tiket; membalas tiket opsional (default: bisa).
- ID tiket pada komponen Livewire memakai `#[Locked]` dan query selalu dibatasi pemilik atau peran (pola yang sama dengan registrationId).

## 3. Menu dan halaman

| Halaman | Peran | Rute usulan | Isi |
|---|---|---|---|
| Buat tiket | Pelapor | `/dashboard/bantuan/buat` | Form subjek, deskripsi, prioritas; panel aduan serupa; saran jawaban otomatis |
| Tiket saya | Pelapor | `/dashboard/bantuan` | Daftar tiket (filter Semua/Open/Closed), detail percakapan, rating setelah Closed |
| Antrian tiket | Operator, Admin | `/helpdesk` | Kartu ringkas (Open, In-Progress, melewati SLA, selesai hari ini), filter, tabel dengan hitung mundur SLA, pencarian |
| Detail tiket | Operator, Admin | `/helpdesk/tiket/{ticket}` | Percakapan, saran balasan, ubah status/prioritas, tenggat SLA |
| Dashboard performa | Admin | `/helpdesk/dashboard` | KPI, grafik volume, waktu respons per prioritas, sebaran kepuasan, kendala paling sering, performa operator |
| Kebijakan SLA | Admin | `/helpdesk/sla` | Target per prioritas, template balasan otomatis |

Landing page: halaman Bantuan publik hanya memuat kartu "Belum ketemu jawabannya? Buat tiket bantuan" yang mengarah ke login lalu ke Buat tiket. Portal peserta mendapat satu item sidebar "Tiket bantuan" dengan lencana jumlah tiket aktif.

Tidak ada: menu Users baru (pengelolaan pengguna tetap di Filament, tambah pilihan peran `operator` di sana), menu Pengaturan umum.

## 4. Skema database

### 4.1 Skema dari soal (tetap)

- `tickets`: id, reporter_id (FK users), operator_id (FK users, nullable), subject (varchar), description (text), priority (enum Low, Mid, High), status (enum Open, In-Progress, Closed), created_at.
- `ticket_responses`: id, ticket_id (FK), responder_id (FK users), message (text), is_auto_reply (boolean).
- `satisfaction_ratings`: ticket_id (FK), score (int 1–5), feedback (text).

### 4.2 Kolom dan tabel tambahan (diperlukan agar SLA, debugging, dan analitik jalan)

`tickets`: `first_response_at` (datetime null), `closed_at` (datetime null), `resolution_time` (unsigned int null, menit; diisi saat Closed), `response_due_at` (datetime), `resolution_due_at` (datetime), `updated_at`. Indeks: FULLTEXT (`subject`,`description`), (`status`,`priority`), (`reporter_id`), (`operator_id`).

`ticket_responses`: `created_at`; indeks (`ticket_id`,`created_at`).

`satisfaction_ratings`: `id` (PK), `created_at`; unik `ticket_id`; skor dibatasi 1–5 (CHECK constraint dan validasi aplikasi).

Tabel baru:
- `sla_policies`: `priority` (unik), `response_minutes`, `resolution_minutes`. Nilai awal usulan (diubah admin): High 15 m / 4 j, Mid 60 m / 24 j, Low 240 m / 72 j.
- `reply_templates`: `id`, `title`, `body`, `keywords` (text), `is_active`, `use_count`.
- `ticket_suggestion_feedback`: `id`, `ticket_id` (tiket rujukan), `reporter_id`, `helpful` (boolean), `created_at`. Dasar pembelajaran peringkat.

Semua waktu disimpan UTC, ditampilkan Asia/Jakarta. Enum status dan prioritas diwujudkan sebagai enum PHP (`TicketStatus`, `TicketPriority`).

## 5. Alur tiket dan status

```
Pelapor buat tiket (Open) → balasan otomatis → operator ambil & balas (In-Progress, first_response_at tercatat)
→ saling balas → operator tutup (Closed, closed_at & resolution_time tercatat) → pelapor beri rating → admin lihat statistik
```

Transisi yang diizinkan: Open → In-Progress → Closed. Open → Closed (ditutup langsung, mis. duplikat). Closed tidak dibuka lagi (pelapor membuat tiket baru). Transisi lain melempar `DomainException`.

Aturan waktu:
- Saat dibuat: `response_due_at = created_at + response_minutes`, `resolution_due_at = created_at + resolution_minutes` menurut prioritas.
- `first_response_at` terisi pada balasan **manusia** pertama dari operator/admin (`is_auto_reply = false`). Balasan otomatis dan pesan pelapor tidak dihitung.
- `resolution_time = menit antara created_at dan closed_at`, dibulatkan, tidak boleh negatif. Waspada Carbon 3: `diffInMinutes` bertanda dan mengembalikan float; urutan argumen penting.
- Perubahan prioritas menghitung ulang tenggat dari `created_at`.
- Jam kerja SLA: default 24 jam (keputusan pemilik bila ingin jam kantor).

## 6. Anti duplikasi aduan

1. Saat pelapor mengetik subjek (debounce 400 ms, minimal 3 kata), Livewire memanggil pencarian.
2. Query: `MATCH(subject, description) AGAINST (? IN NATURAL LANGUAGE MODE)` pada tiket Closed yang ditandai rujukan atau tiket In-Progress; ambil 3 teratas di atas ambang skor.
3. Pembelajaran: skor akhir = skor FULLTEXT × (1 + ln(1 + jumlah "helpful" dari `ticket_suggestion_feedback`)) − penalti kecil untuk yang sering ditolak.
4. Pelapor memilih "Ini menjawab" (tiket tidak dibuat; feedback dicatat) atau "Tetap kirim" (feedback negatif dicatat; tiket dibuat).
5. Privasi: hanya subjek yang sudah dibersihkan dan ringkasan solusi yang ditampilkan, tanpa nama atau data pribadi pelapor lain. Hanya tiket yang punya `is_reference = true` (ditandai operator saat menutup) yang muncul ke pelapor lain. Tambahkan kolom `is_reference` (boolean default false) di `tickets`.
6. Catatan MySQL: FULLTEXT bawaan tidak ber-stemming Indonesia; mulai dengan normalisasi huruf kecil dan daftar stopword sendiri.

## 7. Balasan otomatis dan saran balasan

- Balasan otomatis ke pelapor: dikirim satu kali saat tiket dibuat, `is_auto_reply = true`, responder = akun sistem khusus (bukan akun operator), tampil dengan label "Otomatis". Tidak mengubah status.
- **Aturan wajib: tidak pernah mengirim pesan kosong.** `AutoReplyService::build()` memangkas spasi/placeholder yang kosong; bila hasilnya kosong, mengembalikan `null` dan tidak ada baris `ticket_responses` yang dibuat.
- Saran balasan untuk operator: 3 template teratas berdasarkan kemiripan kata kunci. Operator menyunting lalu mengirim; `use_count` bertambah. Balasan tersebut tetap `is_auto_reply = false`.

## 8. Dashboard analitik (admin)

KPI: rata-rata waktu respons (menit atau jam, satuan otomatis), rata-rata waktu penyelesaian, tingkat kepuasan (rata-rata skor + sebaran 1–5), kepatuhan SLA (respons dan penyelesaian terpisah). Grafik: volume dibuka vs Closed 14 hari. Tabel: kinerja operator (jumlah, waktu respons, waktu selesai, kepuasan), kendala paling sering (dikelompokkan dari kemiripan teks). Periode: 7/30/90 hari.

Implementasi: query agregasi MySQL dengan indeks, cache singkat (60 dtk), tanpa memuat semua tiket ke memori. Angka di mockup adalah data contoh; implementasi harus dari data nyata dan menampilkan keadaan kosong yang jelas.

## 9. Desain visual

Mengikuti `desain-revisi.md` (token, komponen `.btn`, `.card`, `.chip`, `.input`, fokus mustard, kontras: teks di atas oranye wajib `--ink`). Komponen khusus helpdesk: lencana status (Open=mustard, In-Progress=berry, Closed=pine-l), lencana prioritas (Low=pine-l, Mid=mustard, High=red-l dengan teks ink), pil SLA (ok/warn/bad), gelembung pesan (pelapor=oranye-l rata kanan, operator=card, otomatis=pine-l garis putus), bar SLA, bintang rating (pakai radio tersembunyi yang bisa diakses keyboard, bukan div klik). Shell operator/admin memakai sidebar ink 260px dengan item sesuai peran. Responsif: di mobile sidebar pengelola menjadi menu tombol; tabel antrian menjadi kartu bertumpuk. Reduced-motion dihormati.

## 10. Keamanan dan kualitas

- Validasi server-side semua input (subjek ≤ 160, deskripsi ≤ 5.000, pesan ≤ 5.000, skor 1–5). Output di-escape (`{{ }}`).
- Rate limit: pembuatan tiket dan pesan per pengguna.
- Tidak ada lampiran file di versi ini (di luar skema).
- Notifikasi surel ke pelapor saat ada balasan operator dan saat tiket ditutup (antre via queue; gagal kirim tidak menggagalkan balasan).
- Pembaruan dua arah: awal `wire:poll.10s` pada thread; websocket belum diperlukan.
- Seeder produksi tidak membuat tiket atau pengguna demo. Data contoh hanya untuk lingkungan lokal dan dijaga flag.

## 11. Pengujian (PHPUnit, MySQL)

Wajib: RBAC tiap peran dan rute; isolasi pelapor (404); rating hanya setelah Closed dan sekali; transisi status valid/tidak valid; `resolution_time` benar (tiket 10.00 ditutup 12.30 → 150 menit; tidak pernah negatif); `first_response_at` hanya dari balasan manusia; **auto-reply tidak mengirim pesan kosong** (data provider: `''`, `'   '`, `"\n\t"`; tidak ada baris `ticket_responses`); tenggat SLA menurut prioritas dan perhitungan ulang; pencarian duplikat mengembalikan tiket serupa dan menghormati `is_reference`; pembelajaran peringkat dari feedback; agregasi dashboard (nilai pasti dari data fixture); rate limit. Lakukan mutation check untuk tes penting (matikan perilakunya, tes harus gagal).

## 12. Dokumentasi (Doxygen) dan debugging

- Kelas dan metode fitur ini memakai docblock `/** */` lengkap dengan `@param`, `@return`, `@throws`: `TicketService` (buat, ubah status, balas, tutup), `AutoReplyService`, `SlaCalculator`, `DuplicateTicketFinder`, model `Ticket`.
- `Doxyfile` di akar proyek: `INPUT` ke folder helpdesk, `RECURSIVE = YES`, `FILE_PATTERNS = *.php`, `EXTRACT_ALL = YES`, `GENERATE_HTML = YES`, `GENERATE_LATEX = NO`, `OUTPUT_DIRECTORY = docs/doxygen`. Hasil HTML tidak di-commit (tambahkan ke `.gitignore`), kecuali pemilik meminta.
- Fungsi `TicketService::changeStatus()` harus mudah di-debug: satu tempat menghitung `closed_at` dan `resolution_time`, menerima parameter waktu opsional (`?CarbonInterface $now = null`) agar dapat diuji dan diperiksa dengan breakpoint Xdebug.

## 13. Fase kerja

1. Peran `operator` + migration + tes RBAC.
2. Skema, model, enum, kebijakan akses, factory.
3. Pelapor: buat tiket, Tiket saya, thread dua arah.
4. Operator: antrian, detail, ubah status (`TicketService`), SLA.
5. Auto-reply + saran balasan + template.
6. Anti duplikasi + feedback pembelajaran.
7. Rating kepuasan.
8. Dashboard admin + Kebijakan SLA.
9. Notifikasi, aksesibilitas, responsif, Doxygen, finalisasi tes.

## 14. Keputusan terbuka untuk pemilik

Jam kerja SLA (24 jam atau jam kantor); nilai target SLA; apakah admin boleh membalas tiket; kunci kemiripan tiket rujukan (`is_reference`) perlu persetujuan operator atau otomatis; lampiran file.
