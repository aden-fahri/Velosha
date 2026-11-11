# Prompt-SaaS.md — Peta langkah dan semua prompt agen untuk Velosha SaaS

| | |
|---|---|
| **Versi** | 1.0 — 3 November 2026 |
| **Pasangan dokumen** | `Plan-SaaS.md` (rencana, keputusan, arsitektur). Berkas ini = **apa yang dikerjakan, dalam urutan apa, dengan prompt apa** |
| **Pelaku** | **[ANDA]** dikerjakan manusia · **[AGEN]** dikerjakan agen AI dengan prompt di berkas ini |

---

## 0. Cara memakai (supaya tidak tersesat)

**Satu langkah = satu sesi agen baru = satu cabang Git = satu Pull Request.** Jangan menggabungkan dua langkah, dan jangan melompati urutan di Peta Navigasi (bagian 1).

### 0.1 Sebelum setiap langkah [ANDA]
```powershell
git status --short          # harus bersih (selain berkas ?? yang sudah di-.gitignore)
git switch main
git pull
git switch -c <fase>/<langkah>-<nama>     # contoh: fase1/p1-03-ci
php vendor/bin/phpunit                    # catat jumlah tes sebagai baseline
```
Buka sesi agen **baru**. Tempel **A0** (aturan umum), lalu prompt langkahnya (dan **A1** untuk langkah yang menyentuh Midtrans). Agen membalas **rencana**; baca daftar berkas yang akan disentuh; kalau ada berkas di luar tugas, tolak. Baru balas **"go"**.

### 0.2 Memeriksa hasil agen [ANDA] (wajib, 5–10 menit)
1. `git log --oneline -10` dan `git diff --stat main...HEAD`: hanya berkas yang diharapkan.
2. `git status --short`: tidak ada berkas baru yang belum dilacak di dalam repositori (skrip bantu, tangkapan layar, dll.).
3. Jalankan `php vendor/bin/phpunit`: semua lulus; jumlah tes **naik** sesuai tugas (bukan turun).
4. Coba fiturnya sendiri di peramban (bukan hanya membaca laporan).
5. Cari rahasia yang tak sengaja ikut: `git diff main...HEAD | Select-String -Pattern "secret|password|api_key|token|server_key"` (sebut nama variabel itu wajar; nilai sungguhan tidak boleh).
6. Baca semua tulisan **NOT VERIFIED** di laporan agen dan putuskan satu per satu.
7. Baru **Squash and merge** lewat GitHub, hapus cabangnya, lalu mulai langkah berikutnya dari `main` yang baru.

### 0.3 Kalau tersesat atau ada yang aneh
- Jalankan **G2 (Cek posisi)** di bagian 5: agen melaporkan langkah mana yang sudah ada buktinya di repositori.
- Kirim ke saya keluaran: `git branch --show-current`, `git log --oneline -10`, `git status --short`, `git stash list`, `git reflog -15`.
- **Jangan menyelesaikan masalah Git dengan perintah destruktif** (`reset --hard`, `clean -fd`, `push --force`). Pekerjaan hampir selalu masih ada di `git reflog`.
- Kalau agen mengubah sesuatu di luar rencana: hentikan, jangan dilanjutkan, kirim `git diff --stat` ke saya.

### 0.4 Aturan emas
1. Repositori **privat**, kunci hanya di `.env`, tidak pernah di chat.
2. Tidak ada uang nyata sebelum Fase 2 selesai dan teruji di sandbox.
3. Tidak menerima uang penyelenggara sebelum Fase 5 dan konsultasi hukum selesai.
4. Setiap fase ditutup dengan **Gerbang** (audit ulang) sebelum fase berikutnya dimulai.

---

## 1. Peta Navigasi

> Cara baca: kerjakan dari atas ke bawah. Kolom **Selesai bila** adalah bukti yang bisa Anda periksa sendiri. Baris **GERBANG** wajib lulus sebelum lanjut.

### FASE 0 — Tutup fondasi

| ID | Pelaku | Pekerjaan | Rujukan | Selesai bila |
|---|---|---|---|---|
| L0.1 | [ANDA] | Squash and merge Pull Request `chore/phase0-hardening` ke `main` | GitHub | `git log origin/main` memuat komit hardening; 346 tes lulus di `main` |
| L0.2 | [ANDA] | `git rebase origin/main` pada `feature/google-login`, uji, Squash and merge | Bagian 0 | Tes Google dan folder `Security` lulus; sekitar 358 tes total |
| L0.3 | [ANDA] | Repositori **privat**; jalankan gitleaks; pindahkan `AUDIT*.md` ke luar proyek; periksa dan hapus berkas `an` | Percakapan | gitleaks tanpa temuan (atau kunci sudah diganti); `git status` bersih |
| L0.4 | [ANDA] | Uji manual Google dengan kredensial asli (akun baru, akun lokal tak terverifikasi ditolak, batal di layar Google) | Plan 4.2 | 4 skenario berjalan |
| **GERBANG 0** | | Semua L0.x selesai | | `main` hijau, repositori privat, tanpa rahasia bocor |

### FASE 1 — Fondasi teknis

| ID | Pelaku | Pekerjaan | Prompt | Selesai bila |
|---|---|---|---|---|
| L1.1 | [AGEN] | Laporan kesiapan upgrade (baca saja) | P1-01 | Laporan lengkap, tanpa berkas diubah |
| L1.2 | [AGEN] | Upgrade PHP 8.3/8.4 dan Laravel 13 | P1-02 | Semua tes lulus, `composer audit` bersih |
| L1.3 | [AGEN] + [ANDA] push | CI GitHub Actions | P1-03 | Pull Request menampilkan centang hijau |
| L1.4 | [AGEN] | Email transaksional sungguhan | P1-04 | Email uji tiba di kotak masuk |
| L1.5 | [AGEN] | Siap Redis, antrean, health check | P1-05 | `app:health` lulus; panduan worker ada |
| L1.6 | [AGEN] | Pemantauan galat dan log terstruktur | P1-06 | Galat uji muncul di dasbor Sentry (tanpa data sensitif) |
| L1.7 | [AGEN] + [ANDA] | Cadangan dan runbook pemulihan | P1-07 | Satu pemulihan percobaan berhasil di staging/lokal |
| L1.8 | [AGEN] | Kebersihan repositori, README, lisensi | P1-08 | Orang baru bisa menjalankan proyek dari README |
| L1.9 | [ANDA] | Daftar layanan: email, Sentry, hosting, domain, **akun sandbox Midtrans** | — | Semua akun siap, kunci hanya di `.env` |
| **GERBANG 1** | | Jalankan **G1** (audit gerbang) | G1 | CI hijau, email jalan, galat terpantau, cadangan teruji |

### FASE 2 — Pembayaran sungguhan

| ID | Pelaku | Pekerjaan | Prompt | Selesai bila |
|---|---|---|---|---|
| L2.1 | [AGEN] | Antarmuka gateway, FakeGateway, tabel pembayaran | P2-01 | Tes signature dan state machine lulus |
| L2.2 | [AGEN] | Reservasi slot, kedaluwarsa, daftar ulang | P2-02 | Uji konkurensi: tidak ada oversell |
| L2.3 | [AGEN] | Webhook Midtrans, idempoten, status hanya maju | P2-03 | Uji putar ulang dan urutan terbalik lulus |
| L2.4 | [AGEN] | Checkout memakai Snap, refund, penutupan simulasi | P2-04 | Alur fake end-to-end lulus |
| L2.5 | [AGEN] | Rekonsiliasi harian dan halaman sukses | P2-05 | Laporan rekonsiliasi berjalan terjadwal |
| L2.6 | [ANDA] | Daftar penerimaan sandbox | L2.6 | Semua skenario lulus di akun sandbox |
| L2.7 | [ANDA] | Satu transaksi nyata kecil di produksi + rekonsiliasi cocok | — | Dana tercatat benar di Midtrans dan ledger sementara |
| L2.8 | [ANDA] | **Mulai konsultasi hukum dan pajak** (berjalan paralel sampai Fase 5) | Plan 6.5 | Jadwal konsultasi ada |
| **GERBANG 2** | | **G1** + uji beban awal | G1 | Titik berhenti aman: tiket event sendiri sudah bisa dijual |

### FASE 3 — Organisasi dan multi-tenant

| ID | Pelaku | Pekerjaan | Prompt | Selesai bila |
|---|---|---|---|---|
| L3.1 | [AGEN] | Laporan desain multi-tenant (baca saja) | P3-01 | Laporan dengan file:line lengkap |
| L3.2 | [AGEN] | Tabel organisasi dan anggota, isi ulang data lama | P3-02 | Semua event punya organisasi; hitungan cocok |
| L3.3 | [AGEN] | Global scope, Policy, uji isolasi tenant | P3-03 | Isolasi lulus untuk semua sumber daya |
| L3.4 | [AGEN] | Panel penyelenggara (tenancy) dan panel admin terpisah | P3-04 | Matriks akses lulus |
| L3.5 | [AGEN] | Undangan anggota, semua pengguna boleh mendaftar, helpdesk per event | P3-05 | Alur undang-terima dan aduan per event teruji |
| L3.6 | [AGEN] | Tahap penutup migrasi (NOT NULL, hapus cascade, soft delete) | P3-06 | Menghapus pengguna tidak menghapus event |
| **GERBANG 3** | | **G1** + **G3** pada kode tenancy | G1, G3 | Tidak ada kebocoran data antar organisasi |

### FASE 4 — Kuota, verifikasi, moderasi

| ID | Pelaku | Pekerjaan | Prompt | Selesai bila |
|---|---|---|---|---|
| L4.1 | [AGEN] | Paket kuota dan buku kuota | P4-01 | Saldo kuota benar; uji konkurensi lulus |
| L4.2 | [AGEN] | Beli paket kuota (memakai alur pembayaran) | P4-02 | Pembelian sandbox menambah kuota satu kali |
| L4.3 | [AGEN] | Disk privat, unggah dokumen, antrean verifikasi admin | P4-03 | Dokumen tak bisa dibuka lewat URL publik |
| L4.4 | [AGEN] | Moderasi event, status, lapor event | P4-04 | Alur draf→tayang teruji |
| L4.5 | [AGEN] | Log audit | P4-05 | Tindakan sensitif tercatat |
| L4.6 | [ANDA] | Tulis Syarat Penyelenggara, Kebijakan Refund, Kebijakan Privasi (bersama ahli) | — | Tiga dokumen terbit di situs |
| **GERBANG 4** | | **G1** + **G3** pada verifikasi dan moderasi | G1, G3 | Siap menerima penyelenggara pilot (tanpa dana dulu) |

### FASE 5 — Uang penyelenggara (hanya setelah keputusan hukum)

| ID | Pelaku | Pekerjaan | Prompt | Selesai bila |
|---|---|---|---|---|
| L5.0 | [ANDA] | Putuskan model dana (Plan D2), persentase dana ditahan (D3), siapa menanggung biaya gateway | Plan 3, 6 | Tiga keputusan tertulis |
| L5.1 | [AGEN] | Ledger append-only dan aturan tahan/lepas | P5-01 | Saldo cocok dengan uji sintetis |
| L5.2 | [AGEN] | Rekening pencairan dan alur pencairan | P5-02 | Alur ajukan→setujui→bayar teruji |
| L5.3 | [AGEN] | Laporan penyelesaian dan ekspor | P5-03 | Laporan cocok dengan ledger |
| **GERBANG 5** | | **G1** + **G3** + tinjauan ahli | G1, G3 | Satu siklus uang diuji end-to-end |

### FASE 6 — Hari-H

| ID | Pelaku | Pekerjaan | Prompt | Selesai bila |
|---|---|---|---|---|
| L6.1 | [AGEN] | Check-in sungguhan dan pemindai QR | P6-01 | Check-in tercatat; check-in ganda ditolak |
| L6.2 | [AGEN] | Petugas lapangan terbatas waktu dan daftar start | P6-02 | Akses berakhir otomatis |
| L6.3 | [ANDA] | Uji lapangan 100+ peserta simulasi | — | Waktu per check-in memenuhi target |
| **GERBANG 6** | | **G1** | G1 | Hari-H siap |

### FASE 7 — Pilot

| ID | Pelaku | Pekerjaan | Prompt | Selesai bila |
|---|---|---|---|---|
| L7.1 | [ANDA] | Wawancara 5–10 penyelenggara; tetapkan harga | Plan 11.4 | Harga kuota dan biaya layanan ditetapkan |
| L7.2 | [AGEN] | Skrip uji beban | P7-01 | Skrip siap dijalankan di staging |
| L7.3 | [ANDA] | Pilot 1–3 penyelenggara dengan pendampingan penuh | Plan 11.4 | Kriteria pilot terpenuhi |
| L7.4 | [AGEN] | Audit akhir sebelum peluncuran | G1 + G3 | Tidak ada temuan kritis terbuka |
| **GERBANG 7** | | Daftar gerbang Plan 15.3 lengkap | | Siap peluncuran terbatas |

### FASE 8 — Peluncuran terbatas
Buka pendaftaran penyelenggara lewat undangan atau daftar tunggu, pantau metrik (Plan 11.3), perbaiki, lalu tambah fitur lanjutan (domain sendiri, impor hasil timing, sertifikat, WhatsApp, dasbor HP, API).

---

## 2. Blok acuan (tempel sesuai keperluan)

### A0 — Aturan umum (tempel di awal SETIAP sesi agen)
```
GROUND RULES — apply to everything in this session.

GIT: Run `git status --short` first and show it. If anything other than untracked files (??) appears, stop and tell me. Do NOT run checkout, switch, restore, reset, clean, stash, rebase or merge. Never use `git add .`, `git add -A` or `git commit -a`: stage only the files you changed, by name, and show `git diff --cached --name-only` before each commit. One commit per numbered item, clear message. Never touch untracked files.

SECRETS: Never open .env. Never print, log, hardcode or paraphrase any secret value (API keys, server keys, passwords, tokens, APP_KEY). Use only variable NAMES in .env.example. In tests use obviously fake values such as "test-server-key". Never put real keys in code, tests, fixtures or logs.

DATABASE: Use only the dedicated test database (abort if DB_DATABASE does not end with "_test"). Never run migrate:fresh, db:wipe or db:seed on the development database. Migrations must be reversible and safe on existing data.

DEPENDENCIES: Do not add any composer or npm package unless the task says so. Prefer Laravel's built-in tools.

HONESTY: Do not invent routes, columns, class names, line numbers or API fields. If you did not open or verify something, write NOT VERIFIED. When a test passes, show the real command and its output summary (counts). A test is only meaningful if its positive control passes.

WORKFLOW: (1) Reply FIRST with a plan: files you will read, files you will create or change, and the order. Do not edit anything in this reply. (2) Wait for my "go". (3) Implement. (4) Run the relevant tests, then the full suite. (5) Final report: commits (hash, message, staged files), real test counts, anything NOT VERIFIED, anything you changed outside the files named in the plan.

CODE STYLE: follow the existing project conventions (namespaces, Pint). Money is always an integer number of rupiah, never float. Add no marketing text or emoji to code.
```

### A1 — Spesifikasi Midtrans (diverifikasi dari dokumentasi resmi, 3 Nov 2026)
Tempel blok ini bersama prompt Fase 2 mana pun yang menyentuh Midtrans.
```
MIDTRANS SPEC (verified against official Midtrans documentation; if the code needs anything not listed here, write NOT VERIFIED instead of guessing):
- Snap popup script: https://app.sandbox.midtrans.com/snap/snap.js for sandbox (production uses the host without ".sandbox"), with the attribute data-client-key.
- Snap transaction creation: POST /snap/v1/transactions with transaction_details { order_id, gross_amount } and optional customer_details/item_details; the response contains a token and a redirect_url. Authentication is HTTP Basic with the SERVER KEY as username and an empty password. (Exact sandbox/production hostnames and auth header: confirm in the official docs and mark NOT VERIFIED if you cannot.)
- Get Status API: GET https://api.sandbox.midtrans.com/v2/{order_id}/status (production host without ".sandbox").
- order_id must be unique per transaction attempt. A retry after expiry or cancellation needs a NEW order_id (suggested format VLS-{registration_id}-{attempt}).
- Notification (webhook) fields used: order_id, status_code, gross_amount (a STRING such as "100000.00"), signature_key, transaction_status, fraud_status (cards), payment_type, transaction_id, transaction_time.
- Signature: signature_key must equal SHA512(order_id + status_code + gross_amount + SERVER_KEY), using gross_amount exactly as received (string). Compare with hash_equals. Always verify it before processing.
- transaction_status values: capture, settlement, pending, deny, expire, cancel. A payment counts as SUCCESSFUL only when status_code is 200 AND transaction_status is settlement, or capture with fraud_status accept.
- Notifications can arrive OUT OF ORDER (for example settlement before pending). Status may only move forward; a late pending must never downgrade a paid payment.
- If a notification is doubtful or missing, query the Get Status API (this is also the recommended way to challenge a notification).
- Refunds: only settled transactions can be refunded, and not every payment channel supports refund through the API. A refund that cannot be done through the API must be recorded as "manual refund required".
- The Notification URL is configured in the Midtrans dashboard (sandbox and production separately) and must be reachable from the internet.
```

---

## 3. Prompt per fase

### FASE 1

### P1-01 — Kesiapan upgrade PHP 8.3/8.4 dan Laravel 13 (baca saja)
```
TASK (READ-ONLY, change nothing): Prepare an upgrade readiness report for moving from PHP 8.2 / Laravel 12 to PHP 8.3 or 8.4 and Laravel 13. Your reply is the report; do NOT create files in the repo. Do not run composer update/require, npm install, migrations or anything that changes files or the database.

REPORT
1. Current versions from composer.json and composer.lock for every direct dependency (name, constraint, locked version).
2. Output of `composer outdated --direct` and `composer audit` (read-only), summarized without secrets.
3. For each direct dependency: does its latest release declare support for Laravel 13 and PHP 8.3/8.4? Use `composer show <package> --all` or metadata already in vendor. If you cannot verify, write NOT VERIFIED.
4. Items from the official Laravel upgrade guide that could apply here: list each and search the code for it (file:line). Say clearly if none apply.
5. Code patterns that behave differently or are deprecated in PHP 8.3/8.4 (search; file:line).
6. Local environment: PHP version and extensions in use, and the steps needed to run PHP 8.3/8.4 on this machine (list only).
7. A proposed order of small commits with one test command after each step and a non-destructive rollback plan.
8. Ranked risks and the questions you need me to answer.
```

### P1-02 — Eksekusi upgrade PHP dan Laravel
```
[Paste A0 first. Paste the readiness report from P1-01 under this line.]
TASK: Execute the upgrade in small, reversible commits on the current branch (created by me from a clean main). Reply with the plan first and wait for "go".

ITEMS (one commit each; run the full test suite after each; stop and report if a step fails and cannot be fixed within the files of that step)
1. composer.json: set the PHP constraint to the target in the report (^8.3 or ^8.4). Do not install PHP.
2. Upgrade the dependencies that block Laravel 13 first, exactly as the report lists, one commit per package family.
3. Upgrade laravel/framework to ^13.0 and the companion packages the report lists (livewire, filament, socialite and others). Apply only the upgrade-guide changes the report found. Do not refactor unrelated code.
4. Run: composer audit, php artisan route:cache then route:clear, php artisan config:cache then config:clear, php artisan about (summarize without secrets), the full test suite. Report real outputs and counts.
5. Frontend (vite/tailwind): touch it only if the report says it is required.
6. Write docs/upgrade-notes.md: versions before and after, steps taken, known issues, and a rollback procedure using non-destructive git commands only.
```

### P1-03 — CI GitHub Actions
```
[Paste A0 first.]
TASK: Continuous integration. Create .github/workflows/ci.yml (and nothing else outside docs/). Reply with the plan first.

REQUIREMENTS
- Triggers: pull_request and push to main.
- Jobs: (1) lint: ./vendor/bin/pint --test; (2) static analysis ONLY if larastan/phpstan is already a dependency, otherwise skip and say so; (3) tests: PHP version = the project's target from composer.json; database exactly as phpunit.xml and tests/TestCase.php require (the safety guard needs sqlite :memory: or a database whose name ends with _test; use a MySQL service container if needed); copy .env.example to .env, php artisan key:generate, install dependencies with cache, build assets only if tests need them; (4) secret scan with gitleaks: use the official gitleaks GitHub Action pinned to a full version tag; if it needs a license key or token, tell me and use the CLI binary download instead.
- The workflow must contain NO secrets. Use only GitHub-provided variables and fake values.
- Do not modify application code or tests. If a test fails in CI for an environment reason, report it; do not weaken the test.
- If a README exists, add a CI status badge line at its top.
ACCEPTANCE: the YAML parses (validate with actionlint if installed, otherwise describe how you checked); list every step with its purpose. I will push the branch and watch the first run.
```

### P1-04 — Email transaksional sungguhan
```
[Paste A0 first.]
TASK: Real transactional email. STEP 0 (read-only, then wait for "go"): list every Mailable and Notification, where each is sent (verification, password reset, ticket, others), and the current MAIL_* configuration keys in config/mail.php (names only).

ITEMS
1. Config: mailer stays "log" in local/testing. Add a production guard in AppServiceProvider: throw a RuntimeException at boot if app()->isProduction() and the default mailer is log or array. .env.example gets the exact MAIL_* variable NAMES that config/mail.php reads in this Laravel version (no values).
2. Mailables implementing ShouldQueue: TicketIssuedMail (event name, date, BIB, link to the ticket page; NEVER NIK, medical data, emergency contact or full address) and PaymentReceiptMail (order id, amount, date). Templates in resources/views/emails using inline CSS with the brand colors (#10282E ink, #DE7119 orange, #F8E3B5 butter, #FFFDF8 cream), Inter/system font stack, plain-text alternative included, mobile-friendly.
3. Trigger: send TicketIssuedMail and PaymentReceiptMail when a registration becomes paid. If a PaymentPaid event already exists, use a listener; otherwise dispatch after commit from CompleteRegistrationAction using DB::afterCommit. A mail failure must never break payment completion or show an error to the user (queue retries with backoff).
4. TESTS with Mail::fake: sent exactly once on paid; not sent twice when the completion runs twice; password reset mail still works; rendered content contains no NIK or medical data (assert on fixture values).
5. docs/ops/email.md: what to configure at the provider (SPF, DKIM, DMARC), how to send a test mail with a one-off artisan tinker command that prints no secrets.
```

### P1-05 — Siap Redis, antrean, health check
```
[Paste A0 first.]
TASK: Make queue, cache and session Redis-ready WITHOUT forcing Redis locally. Reply with the plan first.

ITEMS
1. Keep database drivers as the local default. Document in .env.example (names only) the switches QUEUE_CONNECTION, CACHE_STORE, SESSION_DRIVER and REDIS_* that production will use. Add no package (Redis support ships with Laravel).
2. Queue robustness: set sensible tries, backoff and timeout on queued jobs and listeners that exist today; confirm the failed-jobs table exists (report if missing, do not create outside a migration).
3. Command `app:health`: checks database connection, cache read/write, queue connection, storage writability, and that the scheduler ran recently if a heartbeat exists; prints a table of OK/FAIL, never prints secrets, returns a non-zero exit code on failure.
4. docs/ops/queue-worker.md: how to run `queue:work` in production under Supervisor or systemd (give example unit files with placeholders), the scheduler cron line, and `queue:restart` on deploy.
5. TESTS: app:health passes in the testing environment; fails (exit code non-zero) when a check is forced to fail.
```

### P1-06 — Pemantauan galat dan log terstruktur
```
[Paste A0 first.]
TASK: Error monitoring and logging. The ONLY dependency you may add is sentry/sentry-laravel. First verify it supports the installed Laravel/PHP versions; if you cannot verify, stop and report NOT VERIFIED. Reply with the plan first.

ITEMS
1. Install per the official Laravel integration. The DSN comes only from env SENTRY_LARAVEL_DSN; empty means disabled and the app must boot normally.
2. PRIVACY: send_default_pii=false; strip request bodies, cookies and Authorization headers; add a denylist that masks the fields password, identity_number, nik, emergency_contact_phone, medical_notes, token, signature_key, server_key, client_secret, api_key wherever they appear. Add a unit test that asserts the denylist contains each of these names and that a sample payload is scrubbed.
3. Tag every event with environment and release (release from an env var such as APP_RELEASE).
4. Logging: a JSON-formatted channel with daily rotation and retention days from env; a middleware that adds an X-Request-Id header and puts the id in every log line. No personal data in log context.
5. docs/ops/monitoring.md: the alerts to create in the monitoring tool (payment webhook failures, failed jobs, 5xx rate, queue delay) and what each alert means.
6. TESTS: the app boots with an empty DSN; the request id middleware sets the header; the scrubbing test above.
```

### P1-07 — Cadangan dan runbook pemulihan
```
[Paste A0 first.]
TASK: Backups and recovery procedures. You may add spatie/laravel-backup ONLY if it supports the installed Laravel/PHP versions; otherwise implement an artisan command around mysqldump. Reply with the plan first and wait for "go".

ITEMS
1. Backup configuration: database dump plus the PRIVATE storage disk (not publicly reachable files). Destination disk from env BACKUP_DISK; daily schedule at 02:30 and a weekly cleanup; failure notifications to an email address from env.
2. A check that fails the app:health command (if it exists) when the newest backup is older than a configurable number of hours.
3. docs/ops/backup-restore.md: step-by-step restore on a staging or local database; a monthly restore-test checklist; WHAT MUST BE STORED SEPARATELY AND OFFLINE (APP_KEY and the production .env in a password manager, never in the repository) with the consequence of losing APP_KEY (encrypted columns cannot be recovered).
4. TESTS: the schedule registers the backup and cleanup commands; the freshness check passes with a recent backup and fails with an old one (use fakes, no real dumps).
Do not run a real backup or restore yourself; I will do the first restore test.
```

### P1-08 — Kebersihan repositori dan dokumentasi
```
[Paste A0 first.]
TASK: Repository hygiene. No behavior changes. Reply with the plan first.

ITEMS (one commit each)
1. composer.json: name "velosha/velosha", an accurate description, license "proprietary". Do not touch require/require-dev.
2. LICENSE file: "All rights reserved" notice with the project name and the current year.
3. README.md: replace the default text with: 2-sentence summary; the REAL stack and versions read from composer.json; local setup (composer install, copy .env, key:generate, migrate, npm install and build, queue worker, running tests, notes for XAMPP on Windows); a table of environment variable NAMES with one-line meanings from .env.example; links to docs/. No marketing claims, no unverified numbers.
4. Move root-level images and prototype HTML into docs/assets/ with git mv, but FIRST search views, CSS and JS for any reference and report; do not delete.
5. Report every file under public/ larger than 300 KB with its size and where it is referenced. Convert hero images to WebP ONLY if a conversion tool is already available on this machine; keep originals; update references; otherwise leave the conversion for me and list the commands.
6. Remove unused mockup assets under public/images/mockup ONLY if a repo-wide search proves they are unreferenced; list what you removed and what you kept.
7. .gitignore review: report what is missing; add only names, never untrack silently.
```

### FASE 2

### P2-01 — Fondasi gateway: antarmuka, Fake, konfigurasi, tabel
```
[Paste A0 and A1 first.]
TASK: Payment foundation (no UI changes). Branch: use the current branch.

ITEMS
1. CONFIG: In config/payments.php keep the existing `simulation` guard unchanged. Add `gateway` (env PAYMENT_GATEWAY; values "fake" or "midtrans"; default "fake" only in local/testing, otherwise null), `reservation_ttl_minutes` (default 60) and a `midtrans` block reading env MIDTRANS_ENV (sandbox|production), MIDTRANS_SERVER_KEY, MIDTRANS_CLIENT_KEY, MIDTRANS_MERCHANT_ID. Read env() only inside config files. Add only the NAMES (no values) to .env.example.
2. BOOT GUARD: in AppServiceProvider throw a RuntimeException at boot when app()->isProduction() and (gateway is not "midtrans" OR server key OR client key is empty). Test it by calling the guard method with config overrides.
3. INTERFACE: App\Payments\Contracts\PaymentGateway with: createTransaction(PaymentRequest $r): PaymentSession (token, redirectUrl); fetchStatus(string $orderId): GatewayNotification; parseNotification(array $payload): GatewayNotification; verifyNotification(array $payload): bool. DTOs are immutable. GatewayNotification normalizes: orderId, statusCode, grossAmountRaw (string), grossAmountIdr (int, rejecting any non-zero fraction), transactionStatus, fraudStatus, paymentType, transactionId, transactionTime. Bind the implementation from config('payments.gateway') in a service provider.
4. IMPLEMENTATIONS: FakeGateway (deterministic, no network, computes signatures with a fake server key from config) and MidtransGateway using Laravel's Http client (no SDK). MidtransGateway must follow the MIDTRANS SPEC; anything not in the spec: NOT VERIFIED + a clearly named constant. Hosts and keys come from config only.
5. SIGNATURE: App\Payments\PaymentSignature::verify(orderId, statusCode, grossAmount, serverKey, signature): bool using sha512 and hash_equals, case-insensitive on the provided hex.
6. MIGRATIONS (reversible): `payments` (id, registration_id nullable FK restrict, quota_purchase_id nullable unsigned bigint WITHOUT FK for now, provider, order_id unique, amount_idr unsigned bigint, status string, snap_token nullable, expires_at nullable, paid_at nullable, refund_status nullable, raw_status nullable, timestamps) and `payment_events` (id, payment_id nullable FK restrict, provider, provider_event_key unique, payload json, signature_valid boolean, processed_at nullable, error text nullable, timestamps). No cascade deletes. Models Payment and PaymentEvent with status constants and a `canTransitionTo()` that only allows forward moves: created→pending→paid|expired|cancelled|denied, paid→refunded.
7. TESTS: signature valid / invalid / tampered amount / tampered status_code; boot guard; gateway binding by config; state machine (no downgrade); MidtransGateway with Http::fake (request path, Basic auth header, body fields, response parsing) and zero real network calls.
Do NOT touch Checkout, CompleteRegistrationAction, routes or views in this task.
```

### P2-02 — Reservasi slot, kedaluwarsa, daftar ulang
```
[Paste A0 first.]
TASK: Slot reservation with expiry. Read first, report, then wait for my "go".

STEP 0 (read-only report): (a) MySQL/MariaDB version in use (from a read-only query on the TEST connection), (b) the exact name of the existing unique index on registrations [user_id, event_id], (c) the real status constants in Registration.php, (d) where quota is checked and incremented today (file:line), (e) how CompleteRegistrationAction uses it, (f) whether any scheduler is registered (routes/console.php or equivalent).

ITEMS (after "go")
1. event_categories: add `reserved_count` unsigned int default 0. registrations: add `reserved_until` nullable datetime and any missing statuses from this list, using the REAL constant style found in step 0: expired, cancelled, refunded.
2. REPLACE THE UNIQUE KEY so a user can register again after an expired or cancelled registration: drop the old unique index; add a VIRTUAL generated column `active_slot_key` = IF(status IN (<the real active statuses: pending, paid, checked_in>), CONCAT(user_id,'-',event_id), NULL) with a UNIQUE index on it. If step 0 shows the server version cannot do this, stop and tell me instead of improvising. Check for existing duplicates first and report them instead of failing.
3. App\Services\ReserveSlot: inside one transaction, lock the category row (lockForUpdate), require quota - registered_count - reserved_count > 0, increment reserved_count, create the registration as pending with reserved_until = now + config('payments.reservation_ttl_minutes'). Return it. No gateway call here.
4. CompleteRegistrationAction: when payment succeeds, inside a transaction with the category row locked, convert the reservation (reserved_count--, registered_count++). If the reservation had already expired and been released, re-check availability: if a slot exists, take it; if not, throw a domain exception that the caller turns into "refund required". Keep BIB and ticket logic unchanged.
5. Command `payments:release-expired` (chunked, each registration handled in its own transaction with locks): pending registrations with reserved_until < now become expired, reserved_count--, linked pending payment becomes expired. Register it in the scheduler to run every minute (without overlapping).
6. TESTS (use Carbon::setTestNow): two buyers race for the last slot; reservation expires and releases; paying after expiry with a free slot succeeds; paying after expiry with no slot throws the refund-required exception and takes no slot; re-registering after expiry works; two ACTIVE registrations for the same user and event are impossible; existing quota concurrency tests still pass (adapt only if their assumptions changed, and explain each adaptation).
```

### P2-03 — Webhook Midtrans
```
[Paste A0 and A1 first. Requires P2-01 and P2-02 merged.]
TASK: Midtrans notification endpoint, idempotent processing, and status reconciliation.

ITEMS
1. ROUTE: POST /payments/midtrans/notification, name payments.midtrans.notify. No auth middleware, excluded from CSRF verification (configure it in bootstrap/app.php the way this Laravel version expects), throttle:120,1. Accept JSON.
2. CONTROLLER (thin): validate required keys; build provider_event_key = sha1(order_id|transaction_status|status_code|gross_amount|transaction_time|transaction_id); insert a payment_events row; on unique violation (duplicate) return 200 and stop; verify the signature through the PaymentGateway contract; if invalid, store signature_valid=false and return 403 WITHOUT processing; if valid dispatch ProcessPaymentNotification and return 200 quickly. For an unknown order_id store the event and return 200 without touching anything.
3. JOB ProcessPaymentNotification (idempotent, safe to run twice): lock the payment row; compare grossAmountIdr to payments.amount_idr (mismatch: record error, change nothing); map settlement, or capture with fraud accept → paid; pending → pending; expire → expired; cancel → cancelled; deny → denied; capture with fraud challenge → keep pending and flag for manual review. Apply status ONLY through Payment::canTransitionTo() (forward only). On paid: call CompleteRegistrationAction; if it throws the refund-required exception, mark payment.refund_status = "required"; dispatch a `PaymentPaid` domain event (listeners come later). Set processed_at.
4. COMMAND `payments:reconcile-pending` (every 5 minutes, no overlap): for payments pending for more than 5 minutes call gateway->fetchStatus and feed the result through the SAME processing code path as the webhook (share one service; do not duplicate logic).
5. LOGGING: never log the server key or the full payload at info level; store the payload only in payment_events.
6. TESTS (use FakeGateway or a test server key; compute signatures in the tests): valid paid notification; same notification twice has one effect; settlement then a late pending is ignored; wrong signature returns 403 and changes nothing; wrong amount changes nothing; unknown order returns 200 and changes nothing; expire after paid is ignored; capture with fraud challenge stays pending; request without a CSRF token is accepted (not 419); throttle returns 429; running the job twice is harmless; reconcile command updates a stuck pending payment.
```

### P2-04 — Checkout memakai Snap, refund, penutupan simulasi
```
[Paste A0 and A1 first. Requires P2-01, P2-02, P2-03 merged.]
TASK: Wire the checkout UI to the real gateway flow, keep fake mode for local/testing, add refund handling.

ITEMS
1. Checkout::pay(): call ReserveSlot, then gateway->createTransaction, store the snap token on the payment. With PAYMENT_GATEWAY=midtrans render snap.js (host by MIDTRANS_ENV) with data-client-key from config('payments.midtrans.client_key') and open the popup with the token. NEVER treat a client-side callback as proof of payment: the page only polls the server.
2. STATUS POLLING: an owner-only endpoint /registration/{registration}/payment-status returning only the status (authorize with the existing ownership check; add a paired authorization test). The checkout page polls every 3 seconds until paid, expired, cancelled or denied, then redirects to the ticket page or shows a clear message with the expiry countdown.
3. RETRY: after expired, cancelled or denied, a new attempt creates a NEW payment with a NEW order_id (VLS-{registration_id}-{attempt}); if the registration itself has expired, start from ReserveSlot again.
4. FAKE MODE: simulatePaid stays available ONLY when gateway=fake AND the environment is local or testing; keep the existing guard tests green and extend them so production-like config can never reach simulatePaid.
5. REFUND SERVICE: App\Payments\RefundPayment: for paid payments with refund_status required (or an admin action), call gateway->refund(...) if the contract supports it (add the method to the contract and FakeGateway; MidtransGateway returns "unsupported" unless the MIDTRANS SPEC covers it: NOT VERIFIED otherwise). When unsupported, set refund_status = manual_required. Never mark a payment refunded unless the gateway confirmed it or a human marked it manually.
6. TESTS: Livewire checkout with FakeGateway (happy path, expiry, retry with new order_id, denied); polling endpoint authorization (owner 200, other user 403/404, guest redirect); production-like config cannot call simulatePaid; refund service branches.
7. Do not change the mockup design beyond what is needed to show the payment states; no new packages.
```

### P2-05 — Rekonsiliasi harian dan halaman pembayaran berhasil
```
[Paste A0 and A1 first. Requires P2-01 to P2-04 merged.]
TASK: Daily reconciliation and the payment success page. Reply with the plan first.

ITEMS
1. Command `payments:reconcile-report {--date=}`: for the payments of that day (default yesterday) compare the database status with gateway->fetchStatus (batched, with a small sleep between calls to stay polite). Output only counts and the order_ids of mismatches. Exit code 1 if there is any mismatch. Store a summary row in a new table payment_reconciliations (date, total, matched, mismatched, created_at). Schedule it daily at 02:00 without overlapping.
2. Success page matching the mockup "Pembayaran berhasil": title, order summary (event, date, category, BIB, amount, payment method), next steps, buttons to the ticket page and the calendar. Show only to the owner of a paid registration; otherwise redirect. Use existing components and brand tokens; check 390 px width.
3. TESTS: reconcile report (all matched, one mismatch, gateway error handled); success page (owner sees it, another user does not, guest is redirected, unpaid registration redirects).
```

### L2.6 — Daftar penerimaan sandbox [ANDA] (setelah kunci ada)
1. Buat akun sandbox Midtrans; isi `PAYMENT_GATEWAY=midtrans`, `MIDTRANS_ENV=sandbox`, dan kedua kunci di `.env` (bukan di chat, bukan di repositori).
2. Jalankan terowongan ke aplikasi lokal; daftarkan alamat `…/payments/midtrans/notification` sebagai Notification URL di dasbor sandbox.
3. Uji berurutan dengan simulator pembayaran Midtrans: **sukses** (tiket dan BIB terbit), **pending lalu sukses**, **kedaluwarsa** (slot kembali), **batal**, **kirim ulang notifikasi yang sama** (tidak ada efek ganda), **ubah nominal** (ditolak), **signature salah** (403), dan **pembayaran sesudah kedaluwarsa** (pulih atau refund diperlukan).
4. Periksa tabel `payment_events` dan `payments` untuk setiap skenario; catat selisih antara dokumentasi dan perilaku nyata, lalu perbaiki.
5. Fase 2 baru selesai jika semua skenario lulus **dan** satu transaksi nyata kecil (produksi) berhasil dengan rekonsiliasi cocok.

### FASE 3

### P3-01 — Laporan desain multi-tenant (baca saja)
```
[Paste A0 first.]
TASK (READ-ONLY): Reply with a design report only; create no files. Do not change anything.

REPORT, with file:line for every claim and NOT VERIFIED where you did not open it
1. Every table, model, Filament resource, Livewire component and route that touches events, categories, registrations, tickets, payments or helpdesk, and which of them would need an organization_id.
2. Every place users.role is used: middleware aliases, gates, policies, Filament canAccessPanel, tests.
3. What the INSTALLED Filament version provides for panels and tenancy: read the vendor source (look for HasTenants and tenant-related methods) and report exactly what exists.
4. A proposed expand → backfill → switch → contract plan with the exact tables, columns, ordering, and the checks to run between steps.
5. A proposed role model (platform roles, organization roles, per-event staff) mapped to the current code, and what must change in routes that use role:peserta.
6. The list of isolation tests that would be needed (resource by resource).
7. Risks and your questions for me.
```

### P3-02 — Organisasi, anggota, dan isi ulang data lama
```
[Paste A0 first. Requires P3-01 report reviewed by me.]
TASK: Create the organization model and backfill existing data without changing behavior. Reply with the plan first. Each migration reversible; remind me to take a database backup before running migrations anywhere except the test database.

ITEMS (one commit each)
1. Tables organizations (id, name, slug unique, status pending|verified|suspended default pending, verified_at nullable, contact_email, settings json nullable, timestamps, soft deletes) and organization_members (organization_id, user_id, role owner|manager|staf, invited_at, accepted_at, unique [organization_id,user_id]). No cascade deletes on financial or event data.
2. events.organization_id nullable with an index. Command `org:backfill-from-events {--dry-run}`: idempotent; for every user who owns events create an organization (name from the user, unique slug) with that user as owner, then set events.organization_id; print only counts.
3. Add nullable organization_id to event_categories, registrations, tickets and payments with indexes such as [organization_id, event_id]; backfill from events inside the same command or a second idempotent command.
4. Do NOT drop events.user_id, do NOT change any foreign key or cascade yet. Creating events and registrations must now set BOTH user_id and organization_id, resolved from the owner's organization.
5. TESTS: backfill is idempotent (running twice changes nothing the second time); counts match; new events and registrations get an organization_id; migrations go up and down.
```

### P3-03 — Global scope, Policy, dan uji isolasi tenant
```
[Paste A0 first. Requires P3-02 merged.]
TASK: Enforce tenant isolation in code and prove it with tests. Reply with the plan first.

ITEMS
1. Trait BelongsToOrganization with a global scope that applies ONLY when a current organization is bound in the container (set by the organizer panel or explicitly). The public site and participant pages must behave exactly as before.
2. Policies, organization-aware, for Event, EventCategory, Registration, Ticket, Payment and HelpdeskTicket: roles owner/manager/staf per the Plan-SaaS permission matrix; platform admin bypass written explicitly; deny by default.
3. Isolation suite tests/Feature/Tenancy: for EVERY Filament resource and Livewire component that touches organization data, a paired test: member of organization A gets success; member of organization B gets 403 or 404; a guest is redirected. Every pair needs the positive control. Include direct URL access with another organization's record id.
4. Existing flows keep working: until the contract step, policies fall back to user_id when organization_id is null.
5. Report which resources you found that are NOT yet covered and why.
```

### P3-04 — Panel penyelenggara (tenancy) dan panel admin terpisah
```
[Paste A0 first. Requires P3-03 merged.]
TASK: Split the Filament panels. Reply with the plan first and quote what the installed Filament version supports for tenancy (from vendor source); write NOT VERIFIED if unsure and wait.

ITEMS
1. A new panel `organizer` with tenancy on Organization; the existing panel stays as `admin` for platform staff only. Resources that are organization-scoped (Event, Registration) are served in the organizer panel with the tenant scope; platform-wide resources (users, helpdesk, content) stay in admin.
2. canAccessPanel for both panels per the role model: admin panel = platform admin/operator only; organizer panel = organization members. After login redirect by membership; a user in several organizations can switch.
3. Navigation as in the mockup "Penyelenggara — dasbor": Ringkasan, Event saya, Peserta, Pembayaran, Verifikasi, Pengaturan. Pages not built yet appear disabled with the label "Segera" (no dead links).
4. The sidebar quota card ("Kuota event 2 dari 3") appears as a placeholder showing "—" until the quota phase.
5. TESTS: access matrix (admin, operator, owner, manager, staf, peserta, guest) for both panels; tenant isolation tests from P3-03 still pass; no organization data visible across tenants in the organizer panel.
```

### P3-05 — Undangan anggota, pendaftaran untuk semua pengguna, helpdesk per event
```
[Paste A0 first. Requires P3-04 merged.]
TASK: Reply with the plan first.

ITEMS (one commit each)
1. Remove the role:peserta restriction on registration and ticket routes: any authenticated, verified user may register as a participant. Keep ownership checks. Update RoleAccessTest and other tests only where their assumption changed, and explain each change.
2. Invitations: table organization_invitations (organization_id, email, role, token_hash, expires_at, accepted_at, created_by). Owner invites by email (queued mail, link with a random token; store only the hash). Accepting requires being logged in with the SAME verified email; expired or reused tokens are rejected. Pairs of tests: valid, expired, reused, wrong account, wrong role.
3. Helpdesk: add nullable event_id and organization_id to helpdesk_tickets; the reporter may pick one of their own registered events; organization members can READ tickets about their events (no assignment rights), platform operators and admins keep full access; privacy rule: a reporter's personal data beyond name and the ticket text is not shown to organization members. Tests per role.
```

### P3-06 — Tahap penutup migrasi (contract)
```
[Paste A0 first. Requires P3-05 merged and a fresh database backup taken by me.]
TASK: Reply with the plan first and wait for "go". Before changing anything run read-only checks and report: counts of NULL organization_id per table (must be zero), orphan rows, and foreign keys that currently cascade.

ITEMS
1. Make organization_id NOT NULL on events, event_categories, registrations, tickets and payments (only if zero NULLs).
2. Rename events.user_id to created_by, nullable, foreign key nullOnDelete. Remove cascade deletes from events, categories, registrations, tickets and payments; use restrict. Add soft deletes to events.
3. Remove the user_id fallback from policies and scopes.
4. TESTS: deleting a user does not delete events or registrations; deleting an organization with events is refused; every isolation test from P3-03 still passes; migrations are reversible.
```

### FASE 4

### P4-01 — Paket kuota dan buku kuota
```
[Paste A0 first. Requires Phase 3 merged.]
TASK: Reply with the plan first.

ITEMS
1. Tables quota_packages (name, events_count, price_idr, is_active), quota_purchases (organization_id, package_id, amount_idr, status, payment_id nullable) and quota_ledger (organization_id, delta int, reason purchase|consume|refund|adjust, event_id nullable, created_by nullable, created_at). The ledger is APPEND-ONLY: the model refuses update and delete, and a test proves it.
2. Seeder with three packages (1, 3 and 10 events). Prices are PLACEHOLDERS read from config/quota.php and clearly marked as examples; no hardcoded business numbers elsewhere.
3. App\Services\QuotaService: balance(org), grant(org, n, reason), consume(org, event), refund(org, event). Every write inside a transaction with the organization row locked.
4. Rules, each with a test (from Plan-SaaS 5.5): draft uses nothing; publish consumes 1; rejected by moderation refunds; cancelled before any ticket sold refunds; cancelled after tickets sold does NOT refund; editing a published event consumes nothing; many categories or days still 1.
5. Concurrency test: two simultaneous publishes with a balance of 1 → exactly one succeeds.
6. Wire the sidebar quota card to the real balance.
```

### P4-02 — Beli paket kuota
```
[Paste A0 and A1 first. Requires P4-01 merged and the payment flow (Phase 2).]
TASK: Reply with the plan first.

ITEMS
1. payments.quota_purchase_id gets its foreign key now. Extend the payment-notification processing so that a payment linked to a quota purchase grants the quota exactly once (idempotent, even if the webhook is replayed).
2. Public entry "Jadi penyelenggara" → create organization (the user becomes owner) → page "Pilih paket kuota" → page "Bayar paket" → Snap payment → return to the organizer dashboard. Follow the mockups "Penyelenggara — pilih paket kuota" and "Penyelenggara — bayar paket"; use existing components; check 390 px.
3. Email receipt for the purchase (reuse the payment receipt mail with a different subject).
4. TESTS: purchase paid once grants once; replayed webhook grants once; failed or expired payment grants nothing; a user who is not owner cannot buy; amount comes from the package at purchase time (store it on quota_purchases) so later price changes do not rewrite history.
```

### P4-03 — Disk privat, dokumen verifikasi, antrean admin
```
[Paste A0 first. Requires P4-01 merged.]
TASK: Reply with the plan first.

ITEMS
1. A private filesystem disk (local now, S3-compatible later by env). Documents must NEVER be reachable by a public URL.
2. verification_documents (organization_id, type, path, original_name, size, mime, status pending|approved|rejected, reviewed_by, note, timestamps). Upload validation: pdf, jpg, png only, max 5 MB, random stored filename, extension and MIME both checked. Download only through a controller with a policy check and a short-lived signed URL.
3. Organization verification steps as in the mockup "Penyelenggara — verifikasi": email and phone, KTP of the board member, community document, payout account details. The payout account number uses an encrypted cast; its table is created here but the payout flow comes in Phase 5.
4. Admin panel queue: list pending organizations with their documents, approve or reject with a required reason; each decision sends an email and is written to the audit log if it exists yet (otherwise leave a TODO test marker).
5. Rule: an event can be submitted for moderation only when the organization is verified. The first event of an organization is ALWAYS reviewed.
6. TESTS: no public URL serves a document; owner of another organization cannot download; admin can; wrong type, oversize and double-extension uploads are rejected; reject requires a reason; status transitions.
```

### P4-04 — Moderasi event, status, dan lapor event
```
[Paste A0 first. Requires P4-03 merged.]
TASK: Reply with the plan first.

ITEMS
1. events.status values: draft, submitted, in_review, approved, rejected, published, closed, cancelled, implemented as an action-class state machine following the diagram in Plan-SaaS 5.5. Tables event_reviews (event_id, status, reviewer_id, note, created_at).
2. Publishing consumes one quota (QuotaService) in the same transaction as the state change; rejection and cancellation follow the refund rules from P4-01.
3. Admin queue in the admin panel: approve, or reject with structured reasons plus a note. Emails to the organizer on every decision.
4. Public catalog and event pages show only published events; a cancelled event shows a clear notice and triggers the refund-required flag on paid registrations (reuse the payment refund service).
5. Public "Laporkan event" button creating a row in event_reports (event_id, reporter_id nullable, reason, details, status); admin list; rate-limited.
6. TESTS: every legal and illegal transition; quota effects; catalog visibility per status; reports (guest and logged-in, rate limit).
```

### P4-05 — Log audit
```
[Paste A0 first.]
TASK: Reply with the plan first.

ITEMS
1. Table audit_logs (id, actor_id nullable, organization_id nullable, action, subject_type, subject_id, meta json, ip, user_agent, created_at). Append-only: no update or delete through the model (a test proves it).
2. Service Audit::record(action, subject, meta). The meta must never contain personal data or secrets: add a guard that rejects keys such as identity_number, nik, password, token, medical_notes, account_number, and test it.
3. Record: verification decisions, moderation decisions, quota grants and consumption, price changes on categories, membership and role changes, participant exports, payout requests (when they exist), logins of platform staff.
4. A read-only Filament resource in the admin panel with filters by organization, action and date.
5. TESTS: each recorded action produces one entry with the actor; no personal data in meta; immutability.
```

### FASE 5

### P5-01 — Ledger append-only
```
[Paste A0 first. Requires Phase 4 merged and my written decisions L5.0 pasted below this line: money model (A or B), hold percentage, who pays gateway fees, platform fee rule.]
TASK: Reply with the plan first.

ITEMS
1. Table ledger_entries (organization_id, event_id, registration_id nullable, payout_id nullable, type sale|gateway_fee|platform_fee|hold|release|refund|payout|adjustment, amount_idr unsigned bigint, direction credit|debit, available_at nullable, created_at). Append-only with model guards and a test. Money is always integer rupiah.
2. LedgerService writes the entries when a payment becomes paid (listener on the PaymentPaid event), idempotent per payment. All percentages, fixed fees, hold percentage and release delay come from config/ledger.php; NO business numbers hardcoded. Rounding rule: documented, integer, deterministic, tested.
3. Release job: moves held amounts to available after the event ends plus the configured delay, only if there is no open dispute or refund-required flag.
4. Refunds create debit entries and consume the held amount first.
5. Balances (available, held, paid out) are computed from the ledger with SQL sums, never stored as separate numbers.
6. TESTS with fixed numbers: one sale, many sales, refund before release, refund after release, rounding edge cases, replayed event, release blocked by a dispute flag. Include a property-style test: the sum of all entries equals the sum of payments minus refunds minus fees.
```

### P5-02 — Rekening pencairan dan alur pencairan
```
[Paste A0 first. Requires P5-01 merged.]
TASK: Reply with the plan first.

ITEMS
1. payout_accounts (organization_id, bank, account_number with encrypted cast, account_name, verified_at). Only the last 4 digits are ever displayed. Verification: the account name must match the organization or a verified board member (manual check by an admin in this version).
2. payouts (organization_id, amount_idr, status requested|approved|paid|rejected, requested_by, approved_by, paid_at, bank_reference, note). Rules: only an owner can request; the organization must be fully verified and the account verified; the amount cannot exceed the available balance computed at approval time (re-check inside a transaction with the organization row locked); amounts above a configurable threshold require approval by TWO different admins; the requester and the approver can never be the same person.
3. On paid: write the payout debit entry to the ledger. Money is transferred manually in this version; record the bank reference.
4. UI per the mockup "Penyelenggara — pembayaran & pencairan" (organizer) and a payout queue (admin). The "Ajukan pencairan" button is disabled until the preconditions are met, with the reason shown.
5. Every step writes an audit log entry. No secrets or full account numbers in logs.
6. TESTS: preconditions, concurrency (two simultaneous requests cannot exceed the balance), two-admin rule, self-approval refused, account number never appears in HTML or logs beyond the last 4 digits.
```

### P5-03 — Laporan penyelesaian dan ekspor
```
[Paste A0 first. Requires P5-02 merged.]
TASK: Reply with the plan first.

ITEMS
1. Per-event settlement page for organizers: gross sales, gateway fees, platform fees, refunds, held, available, paid out; computed from the ledger.
2. CSV export for the organizer's bookkeeping. Columns are minimal: date, order id, category, amount, fee, net. NO national ID, phone, address or health data. Every export writes an audit log entry.
3. Admin reconciliation view comparing ledger totals with payments totals per event; a mismatch is highlighted.
4. TESTS: numbers match the ledger on fixed fixtures; the CSV contains no sensitive columns; organization isolation; export is audited.
```

### FASE 6

### P6-01 — Check-in sungguhan dan pemindai QR
```
[Paste A0 first. Requires Phase 3 merged.]
TASK: Reply with the plan first. You may add ONE npm package for camera QR scanning only if the browser's native BarcodeDetector is not enough: report the package name, license and size and wait for my OK before installing.

ITEMS
1. Table checkins (ticket_id, event_id, organization_id, scanned_by, scanned_at, method qr|manual, result, device nullable). Append-only.
2. Endpoint POST /checkin (auth, staff-only): look up the ticket by qr_code_data (the QR carries only the random token; no signing), verify it belongs to the staff member's event, then in a transaction with the ticket row locked write tickets.is_checked_in, checked_in_at, the checkin row and the registration status. A second scan returns "sudah check-in" with the time and who did it; it never writes a second check-in.
3. Mobile-friendly scanner page (390 px) with camera scanning where supported and a manual fallback (search by BIB or name) that records method=manual. Show only minimum data: name, BIB, category, and a warning flag; emergency contact only after an explicit tap and the tap is audited.
4. Undo within a short window by an organizer/manager with a required reason, recorded as a new row, never an edit.
5. Rate limit the endpoint. Organization isolation: staff of one event cannot check in tickets of another.
6. Fix the old Filament "Check-in RPC" action so it writes through the same service (no second code path).
7. TESTS: first scan succeeds; second scan is reported and not duplicated; two simultaneous scans produce one check-in; another event's ticket is refused; manual fallback is logged; undo requires reason and role; the Filament action uses the service.
```

### P6-02 — Petugas lapangan terbatas waktu dan daftar start
```
[Paste A0 first. Requires P6-01 merged.]
TASK: Reply with the plan first.

ITEMS
1. Table event_staff (event_id, user_id, role petugas_lapangan, valid_from, valid_until, created_by). Invitation by link or one-time PIN (store only hashes). Access automatically ends at valid_until and is checked on every request.
2. A restricted UI for field staff: only the scanner and the start list. They cannot open the organizer or admin panels, ticket details beyond the minimum, payments or exports.
3. Start list page and CSV for staff with minimal columns (BIB, name, category, wave), no sensitive fields; exports are audited.
4. Organizers and managers manage staff for their events; platform admins can see all.
5. TESTS: access works inside the window and fails outside; expired PIN rejected; staff cannot reach any other route; isolation across events and organizations; start list has no sensitive columns.
```

### FASE 7

### P7-01 — Skrip uji beban
```
[Paste A0 first.]
TASK: Prepare load-test scripts. Do NOT run them against production. Reply with the plan first.

ITEMS
1. docs/loadtest/ with k6 scripts for: browsing the catalog, the event page, the checkout start (reservation), and the payment webhook with FakeGateway-style signed payloads (generated in the script with a TEST key, never a real one).
2. A non-production-only seeder/command that creates synthetic events, categories and users for staging, refusing to run when app()->isProduction().
3. docs/loadtest/README.md: how to run, how to read results, and a table of thresholds to fill in after the first staging run (p95 latency, error rate, reservations per second, oversell must be exactly zero).
4. TESTS: the seeder refuses to run in production.
```

---

## 4. Prompt gerbang dan bantu

### G1 — Audit gerbang fase (baca saja; jalankan di akhir setiap fase)
```
[Paste A0 first.]
TASK (READ-ONLY): Gate audit for the phase that just finished. Reply only; create no files; run no migrations; touch no database except the dedicated test database for running tests.

REPORT (file:line for every claim; NOT VERIFIED when you did not open it)
1. Versions: php, laravel/framework and direct dependencies from composer.lock; output of `composer audit`.
2. Routes: real `php artisan route:list --except-vendor --json` summary: count, and every route without auth middleware.
3. Isolation: list every test in tests/Feature/Tenancy and tests/Feature/Security; run them and show the real counts.
4. Money: payments, webhook and ledger code paths: signature verification present? idempotency key? amount check? forward-only status? any code path that can mark a payment paid without a verified notification?
5. Secrets: any hardcoded key-like strings in tracked files (describe by file and kind, never print values); .env.example contains names only?
6. Data protection: which columns hold personal data, which are encrypted, which are shown in views, exports or logs.
7. Files: any upload handler, its validation and disk; any document reachable by a public URL.
8. Full test suite: real command, counts, duration, failures.
9. Repo hygiene: untracked files, large tracked files, files that should not be tracked.
10. Open items from the previous gate: for each, fixed or still open.
Finish with a table of the top 10 risks (facts only, no fixes) and a list of NOT VERIFIED items.
```

### G2 — Cek posisi (baca saja; jalankan kapan pun Anda merasa tersesat)
```
[Paste A0 first.]
TASK (READ-ONLY): Tell me where the project stands against the navigation map. Do not change anything.

STEPS
1. Show `git branch --show-current`, `git status --short`, `git log --oneline -15`, `git stash list` and `git branch -vv`.
2. For each step id below, report DONE / PARTIAL / NOT STARTED with the evidence (migration names, classes, routes, test names, commits). Do not guess; if you cannot find evidence, say NOT STARTED.
   L1.2 upgrade to Laravel 13 · L1.3 CI workflow · L1.4 real mail config and mailables · L1.5 app:health command · L1.6 Sentry and request id · L1.7 backup config · L1.8 README/license · L2.1 PaymentGateway contract and payments tables · L2.2 reserved_count and reserved_until · L2.3 Midtrans notification route · L2.4 Snap checkout and polling endpoint · L2.5 reconcile report · L3.2 organizations tables · L3.3 BelongsToOrganization · L3.4 organizer panel · L3.5 invitations · L3.6 NOT NULL organization_id · L4.1 quota tables · L4.2 quota purchase · L4.3 verification documents · L4.4 moderation statuses · L4.5 audit_logs · L5.1 ledger_entries · L5.2 payouts · L6.1 checkins · L6.2 event_staff.
3. Run the full test suite and report the real counts.
4. State the single next step from the map and any blocker.
```

### G3 — Tinjauan kode fitur sensitif (baca saja; sebelum menggabungkan login, pembayaran, tenancy, ledger)
```
[Paste A0 first. Replace <FEATURE> with the branch or feature name.]
TASK (READ-ONLY): Security review of <FEATURE>. Change nothing. Report with file:line.

Answer YES / NO / PARTIAL with evidence for each point that applies:
1. Authorization: every route and Livewire action checks ownership or organization membership; any record fetched by id without a check?
2. Isolation: a user of organization A can never read or change data of organization B (name the tests that prove it, with positive controls).
3. Input: validation rules, mass assignment (fillable/guarded), file uploads, HTML output (any raw {!! !!}).
4. Money: amounts are integers; status only moves forward; payments change state only after a verified notification; replay safe.
5. Secrets and personal data: nothing sensitive in logs, exceptions, emails, exports or HTML; encrypted casts used where expected.
6. Abuse: rate limits on login, registration, webhook, scanner and export endpoints.
7. Errors: friendly messages, no stack traces or exception messages shown to users.
8. Redirects: no open redirect from request parameters.
9. Tests: list test names covering success, denial, replay, concurrency and tampering.
Finish with a table: point, answer, file:line, and a ranked list of risks. Do not propose code.
```

---

## 5. Catatan penutup
- Prompt di atas sengaja menyuruh agen **membaca dulu** sebelum menulis dan **melapor rencana** sebelum bekerja. Itu melindungi Anda dari agen yang mengarang nama berkas atau kolom.
- Kalau suatu prompt terasa tidak cocok dengan keadaan kode Anda yang sebenarnya, jangan dipaksakan: kirim ke saya laporan baca-saja dari agen, dan saya sesuaikan prompt berikutnya.
- Perbarui `Plan-SaaS.md` bila keputusan berubah (gateway, model dana, harga, retensi data). Prompt Fase 5 bergantung pada keputusan L5.0.
