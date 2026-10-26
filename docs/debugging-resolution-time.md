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

## 2. Lokasi Breakpoint Xdebug

Pasang breakpoint di file berikut:

- **File**: `app/Services/Helpdesk/TicketService.php`
- **Blok Kode**: Blok `if ($newStatus === TicketStatus::CLOSED)` (~baris 300–315):

```php
if ($newStatus === TicketStatus::CLOSED) {
    $closedAt = $current;                                            // <-- BREAKPOINT 1 (Inisialisasi waktu tutup)
    $diff = (int) $lockedTicket->created_at->diffInMinutes($closedAt); // <-- BREAKPOINT 2 (Hitung selisih menit)
    $resolutionTime = max(0, $diff);                                 // <-- BREAKPOINT 3 (Clamping non-negatif)

    $lockedTicket->status = TicketStatus::CLOSED;
    $lockedTicket->closed_at = $closedAt;
    $lockedTicket->resolution_time = $resolutionTime;

    if ($solutionSummary !== null && trim($solutionSummary) !== '') {
        $lockedTicket->solution_summary = trim($solutionSummary);
    }

    $lockedTicket->save();

    return $lockedTicket;
}
```

---

## 3. Variabel yang Perlu Dimonitor (Watch Expressions)

Saat eksekusi berhenti pada breakpoint, tambahkan variabel berikut ke panel **Watch** IDE Anda:

| Variabel / Ekspresi | Tipe | Penjelasan & Nilai Ekspektasi |
| :--- | :--- | :--- |
| `$lockedTicket->created_at` | `Carbon` | Waktu awal tiket dibuat oleh pelapor (misal: `2026-10-25 10:00:00`). |
| `$current` | `Carbon` | Waktu evaluasi penutupan tiket (menggunakan `$now` jika diinjeksi, atau `Carbon::now()`). |
| `$closedAt` | `Carbon` | Stempel waktu yang akan disimpan ke database pada kolom `closed_at`. |
| `$diff` | `int` | Selisih waktu mentah dalam satuan menit penuh (`created_at->diffInMinutes($closedAt)`). Contoh: 10:00 ke 12:30 menghasilkan `150`. |
| `$resolutionTime` | `int` | Hasil proteksi `max(0, $diff)`. Menjamin nilai **tidak pernah negatif** (contoh jika terjadi clock skew atau waktu mundur, bernilai `0`). |
| `$newStatus` | `TicketStatus` | Enum status tujuan; bernilai `TicketStatus::CLOSED`. |
| `$actor` | `User` | Pengguna yang menutup tiket (wajib berstatus `operator` atau `admin`). |
| `$solutionSummary` | `string|null` | Catatan resolusi/solusi yang ditulis oleh staf sebelum menutup tiket. |

---

## 4. Cara Mereproduksi & Menjalankan Debugging

### Metode A: Melalui PHPUnit Test (Paling Cepat & Deterministik)

Jalankan test case unit yang menguji perhitungan tepat menit dan proteksi nilai bertanda:

```bash
php artisan test tests/Unit/Helpdesk/TicketServiceTest.php --filter=test_resolution_time_calculation_exact_minutes_and_signed_clamp
```

**Skenario yang diuji dalam test**:
1. **Kasus Standar (Tepat 150 Menit)**:
   - Dibuat: `2026-10-25 10:00:00`
   - Ditutup: `2026-10-25 12:30:00`
   - Evaluasi `$resolutionTime`: tepat `150` menit.
2. **Kasus Clock Skew / Waktu Mundur**:
   - Dibuat: `2026-10-25 10:00:00`
   - Ditutup (simulasi waktu mundur): `2026-10-25 09:30:00`
   - Evaluasi `$resolutionTime`: `max(0, -30)` terpotong aman menjadi `0` menit.

### Metode B: Melalui CLI / Laravel Tinker

Buka tinker di terminal:

```bash
php artisan tinker
```

Jalankan skrip demonstrasi berikut:

```php
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Enums\TicketStatus;
use App\Services\Helpdesk\TicketService;
use Carbon\Carbon;

// 1. Ambil operator dan buat tiket uji coba
$operator = User::where('role', 'operator')->first() ?? User::factory()->create(['role' => 'operator']);
$reporter = User::where('role', 'peserta')->first() ?? User::factory()->create(['role' => 'peserta']);

$ticket = HelpdeskTicket::factory()->create([
    'reporter_id' => $reporter->id,
    'status' => TicketStatus::IN_PROGRESS,
    'created_at' => Carbon::parse('2026-10-25 10:00:00'),
    'closed_at' => null,
    'resolution_time' => null,
]);

// 2. Eksekusi penutupan dengan simulasi jam 12:30 (150 menit)
$service = app(TicketService::class);
$closedTicket = $service->changeStatus(
    ticket: $ticket,
    newStatus: TicketStatus::CLOSED,
    actor: $operator,
    solutionSummary: 'Masalah berhasil diinvestigasi dan diselesaikan.',
    now: Carbon::parse('2026-10-25 12:30:00')
);

// 3. Verifikasi hasil
echo "Status: " . $closedTicket->status->value . "\n";
echo "Closed At: " . $closedTicket->closed_at->toDateTimeString() . "\n";
echo "Resolution Time: " . $closedTicket->resolution_time . " menit\n";
```

Output yang dihasilkan:
```text
Status: Closed
Closed At: 2026-10-25 12:30:00
Resolution Time: 150 menit
```

### Metode C: Melalui Tampilan Web (Browser UI)

1. Pastikan server dev berjalan: `php artisan serve`.
2. Buka browser dan login sebagai operator di `/login`:
   - Akun: `operator@velosha.id` (Password: `password`).
3. Akses antrian tiket di `/helpdesk` dan klik salah satu tiket aktif untuk masuk ke halaman detail `/helpdesk/tickets/{id}`.
4. Pada panel kendali operator di sisi kanan:
   - Pilih status **Closed** (Tutup Tiket).
   - Isi form ringkasan solusi (Solution Summary).
   - Klik tombol konfirmasi penutupan.
5. Permintaan HTTP/Livewire akan memanggil `TicketService::changeStatus()` dan breakpoint Xdebug akan langsung terpicu pada baris perhitungan `resolution_time`.

---

## 5. Invarian & Proteksi Integritas Data

1. **Terminal State**: Begitu tiket berstatus `Closed`, metode `changeStatus()` menolak perubahan status lanjutan dengan melempar `DomainException: Tiket yang sudah ditutup tidak dapat diubah statusnya lagi.`
2. **Clamping Mutlak**: Rumus `max(0, $diff)` menjamin bahwa kolom database `resolution_time` (unsigned integer) tidak akan pernah mengalami error *out of range* atau nilai negatif.
3. **Penyatuan Melalui Merge**: Pada fitur penggabungan tiket duplikat (`mergeTickets()`), penutupan tiket duplikat juga didelegasikan ke `changeStatus()`, sehingga kalkulasi `resolution_time` dan `closed_at` tetap seragam dan konsisten.
