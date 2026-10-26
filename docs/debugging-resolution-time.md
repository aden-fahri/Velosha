# Panduan Debugging: Perhitungan `resolution_time` Tiket Helpdesk

Dokumen ini menjelaskan prosedur verifikasi dan demonstrasi breakpoint Xdebug untuk perhitungan waktu penyelesaian tiket (`resolution_time`) dan stempel waktu penutupan (`closed_at`) pada modul Velosha Helpdesk.

---

## 1. Arsitektur & Prinsip Single Source of Truth

Seluruh alur bisnis yang mengubah status tiket menjadi **Closed** dipusatkan secara atomik di dalam satu metode layanan:
- **Kelas**: `App\Services\Helpdesk\TicketService`
- **Metode**: `changeStatus(HelpdeskTicket $ticket, TicketStatus $newStatus, User $actor, ?string $solutionSummary = null, ?CarbonInterface $now = null): HelpdeskTicket`

Tidak ada bagian kode lain (baik Controller, Livewire Component, Model Event, maupun Seeder) yang menulis kolom `closed_at` dan `resolution_time` secara langsung. Ini memastikan invariansi data konsisten dan mudah diaudit.

Parameter opsional `?CarbonInterface $now = null` disediakan khusus agar pengujian deterministik dan sesi debugging interaktif dapat mensimulasikan stempel waktu penutupan kapan saja tanpa bergantung pada jam riil server.

---

## 2. Pengaturan Lingkungan Debugging (Windows / XAMPP & VS Code)

### A. Pengaturan Xdebug pada Windows / XAMPP

1. **Periksa Versi PHP**:
   Buka terminal PowerShell dan jalankan:
   ```powershell
   php -v
   ```
   Catat versi PHP (misalnya `PHP 8.2.x`), arsitektur (`x64`), dan tipe thread safety (`TS` atau `NTS`).

2. **Unduh DLL Xdebug yang Tepat**:
   - Jalankan `php -i > phpinfo.txt` di terminal.
   - Buka laman wizard resmi: [https://xdebug.org/wizard](https://xdebug.org/wizard).
   - Tempel (paste) seluruh isi `phpinfo.txt` ke dalam kolom analisis wizard, lalu klik tombol **Analyse my phpinfo() output**.
   - Wizard akan merekomendasikan dan memberikan tautan unduhan file DLL yang persis sesuai (misal: `php_xdebug-3.x.x-8.2-ts-vs16-x86_64.dll`).
   - Unduh file DLL tersebut dan simpan di direktori ekstensi PHP XAMPP:
     ```text
     C:\xampp\php\ext\php_xdebug.dll
     ```

3. **Konfigurasi `php.ini`**:
   Buka file konfigurasi PHP XAMPP (biasanya di `C:\xampp\php\php.ini`) dan tambahkan baris konfigurasi berikut di baris paling bawah:
   ```ini
   [xdebug]
   zend_extension=xdebug
   xdebug.mode=debug
   xdebug.start_with_request=yes
   xdebug.client_port=9003
   ```

4. **Restart Web Server**:
   Buka **XAMPP Control Panel**, hentikan service **Apache** (Stop), lalu nyalakan kembali (Start).

---

### B. Konfigurasi Debugger pada VS Code

1. **Pasang Ekstensi**:
   Buka tab Extensions di VS Code (`Ctrl+Shift+X`), cari dan pasang ekstensi **PHP Debug** (oleh Xdebug / Felix Becker, id: `xdebug.php-debug`).

2. **Konfigurasi `launch.json`**:
   Buka atau buat file `.vscode/launch.json` di akar proyek:
   ```json
   {
       "version": "0.2.0",
       "configurations": [
           {
               "name": "Listen for Xdebug",
               "type": "php",
               "request": "launch",
               "port": 9003
           }
       ]
   }
   ```

3. **Mulai Sesi Debugging**:
   Buka panel **Run and Debug** (`Ctrl+Shift+D`), pilih konfigurasi **"Listen for Xdebug"**, lalu tekan tombol **Start Debugging** (`F5`).

---

## 3. Lokasi Breakpoint Xdebug

Pasang breakpoint di file layanan tiket:

- **File**: `app/Services/Helpdesk/TicketService.php`
- **Baris Kunci Perhitungan `$diff`**: **Baris 315** (di dalam blok status `CLOSED`):

```php
313:             if ($newStatus === TicketStatus::CLOSED) {
314:                 $closedAt = $current;
315:                 $diff = (int) $lockedTicket->created_at->diffInMinutes($closedAt); // <-- BREAKPOINT UTAMA (Baris 315)
316:                 $resolutionTime = max(0, $diff);
317: 
318:                 $lockedTicket->status = TicketStatus::CLOSED;
319:                 $lockedTicket->closed_at = $closedAt;
320:                 $lockedTicket->resolution_time = $resolutionTime;
321: 
322:                 if ($solutionSummary !== null && trim($solutionSummary) !== '') {
323:                     $lockedTicket->solution_summary = trim($solutionSummary);
324:                 }
325: 
326:                 $lockedTicket->save();
327: 
328:                 return $lockedTicket;
329:             }
```

---

## 4. Demonstrasi Evaluasi pada VS Code Debug Console

Ketika eksekusi program berhenti pada breakpoint di **baris 315**, buka tab **Debug Console** di VS Code dan lakukan evaluasi perbandingan kedua ekspresi berikut:

### Evaluasi 1 (Bentuk Terbalik / Mundur):
```text
$closedAt->diffInMinutes($lockedTicket->created_at)
```
- **Hasil Evaluasi**: `-150` (bernilai **negatif**).
- **Penyebab**: Pada Carbon 3 (versi Carbon modern), metode `diffInMinutes()` mengembalikan nilai bertanda (*signed integer*). Objek `$closedAt` (misal pukul 12:30) membandingkan dirinya ke waktu masa lalu `$lockedTicket->created_at` (pukul 10:00). Karena pergerakannya berarah mundur dari titik acuan, Carbon menghasilkan angka bertanda negatif `-150`.

### Evaluasi 2 (Bentuk Maju yang Digunakan Kode):
```text
$lockedTicket->created_at->diffInMinutes($closedAt)
```
- **Hasil Evaluasi**: `150` (bernilai **positif**).
- **Penyebab**: Objek waktu pembuatan (`created_at`, pukul 10:00) dievaluasi maju menuju waktu penyelesaian (`closedAt`, pukul 12:30), sehingga menghasilkan selisih positif yang merepresentasikan durasi waktu riil yang telah berlalu.

### Mengapa Kode Menggunakan Bentuk Kedua Ditambah `max(0, $diff)`?
1. **Representasi Durasi Logis**: Menghitung dari `created_at` ke `closedAt` adalah urutan kronologis alami yang menghasilkan bilangan positif tanpa perlu manipulasi tanda manual.
2. **Defensive Clamping Guard**: Fungsi `max(0, $diff)` ditambahkan secara eksplisit untuk melindungi database. Jika terjadi anomali jam sistem (*clock skew*) atau jika staf/pengujian secara tidak sengaja memajukan `created_at` melampaui `closedAt`, nilai `$diff` yang negatif akan dipaksa (*clamped*) menjadi `0`. Kolom basis data `resolution_time` bertipe integer non-negatif sehingga tidak akan pernah mengalami error *out of range* atau integritas data yang rusak.

---

## 5. Variabel yang Perlu Dimonitor (Watch Expressions)

Tambahkan variabel-variabel berikut ke panel **Watch** di panel Run & Debug IDE:

| Variabel / Ekspresi | Tipe | Penjelasan & Nilai Ekspektasi |
| :--- | :--- | :--- |
| `$lockedTicket->created_at` | `Carbon` | Waktu awal tiket dibuat oleh pelapor (misal: `2026-10-25 10:00:00`). |
| `$current` | `Carbon` | Waktu evaluasi penutupan tiket (menggunakan `$now` jika diinjeksi, atau `Carbon::now()`). |
| `$closedAt` | `Carbon` | Stempel waktu yang akan disimpan ke database pada kolom `closed_at`. |
| `$diff` | `int` | Selisih waktu dalam satuan menit via `diffInMinutes` (Baris 315: bernilai `150`). |
| `$resolutionTime` | `int` | Durasi final terproteksi via `max(0, $diff)` (Baris 316: bernilai `150`). |
| `$newStatus` | `TicketStatus` | Enum status tujuan; bernilai `TicketStatus::CLOSED`. |
| `$actor` | `User` | Pengguna yang menutup tiket (wajib berstatus `operator` atau `admin`). |
| `$solutionSummary` | `string|null` | Catatan solusi yang ditulis staf sebelum menutup tiket. |

---

## 6. Cara Menjalankan Debugging

### Metode A: Melalui PHPUnit Test di PowerShell (Alternatif Cepat)

Untuk mengaktifkan Xdebug saat menjalankan test case otomatis di terminal PowerShell:

```powershell
# 1. Aktifkan mode debug Xdebug untuk sesi terminal saat ini
$env:XDEBUG_MODE="debug"

# 2. Jalankan test unit kalkulasi resolution_time
php artisan test tests/Unit/Helpdesk/TicketServiceTest.php --filter=test_resolution_time_calculation_exact_minutes_and_signed_clamp

# 3. (Opsional) Kembalikan mode Xdebug setelah selesai
$env:XDEBUG_MODE="off"
```

Jika listener VS Code aktif (`F5`), test runner PHPUnit akan langsung memicu breakpoint pada baris 315.

### Metode B: Melalui Tampilan Browser (Livewire Operator Interface)

1. Pastikan server dev berjalan:
   ```powershell
   php artisan serve
   ```
2. Pastikan VS Code sedang mendengarkan Xdebug (`F5` pada "Listen for Xdebug").
3. Buka browser dan login sebagai operator di `/login`:
   - Akun: `operator@velosha.id` (Password: `password`).
4. Buka antrian tiket `/helpdesk` dan klik salah satu tiket aktif untuk membuka `/helpdesk/tickets/{id}`.
5. Pada panel status operator di sisi kanan:
   - Pilih opsi **Closed** (Tutup Tiket).
   - Masukkan ringkasan solusi.
   - Klik tombol konfirmasi penutupan.
6. Permintaan HTTP Livewire akan memanggil `TicketService::changeStatus()` dan VS Code akan langsung berhenti di breakpoint **baris 315**.

### Metode C: Melalui Interactive CLI (Laravel Tinker)

Buka tinker di terminal:
```powershell
php artisan tinker
```
Jalankan snippet berikut untuk memicu breakpoint:
```php
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Enums\TicketStatus;
use App\Services\Helpdesk\TicketService;
use Carbon\Carbon;

$operator = User::where('role', 'operator')->first() ?? User::factory()->create(['role' => 'operator']);
$reporter = User::where('role', 'peserta')->first() ?? User::factory()->create(['role' => 'peserta']);

$ticket = HelpdeskTicket::factory()->create([
    'reporter_id' => $reporter->id,
    'status' => TicketStatus::IN_PROGRESS,
    'created_at' => Carbon::parse('2026-10-25 10:00:00'),
    'closed_at' => null,
    'resolution_time' => null,
]);

$service = app(TicketService::class);
$closedTicket = $service->changeStatus(
    ticket: $ticket,
    newStatus: TicketStatus::CLOSED,
    actor: $operator,
    solutionSummary: 'Kendala sinkronisasi berhasil ditangani.',
    now: Carbon::parse('2026-10-25 12:30:00')
);
```

---

## 7. Invarian & Proteksi Integritas Data

1. **Terminal State**: Begitu tiket berstatus `Closed`, metode `changeStatus()` menolak perubahan status lanjutan dengan melempar `DomainException: Tiket yang sudah ditutup tidak dapat diubah statusnya lagi.`
2. **Clamping Mutlak**: Rumus `max(0, $diff)` menjamin bahwa kolom database `resolution_time` (unsigned integer) tidak akan pernah mengalami error *out of range* atau nilai negatif.
3. **Penyatuan Melalui Merge**: Pada fitur penggabungan tiket duplikat (`mergeTickets()`), penutupan tiket duplikat juga didelegasikan ke `changeStatus()`, sehingga kalkulasi `resolution_time` dan `closed_at` tetap seragam dan konsisten.
