<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use App\Models\Registration;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class RegistrationSeeder extends Seeder
{
    /**
     * ±260 pendaftaran dummy tersebar di seluruh kategori event dengan
     * komposisi status campuran, lengkap dengan nomor BIB dan tiket QR.
     *
     * Idempoten: pasangan (pengguna, kategori) yang sudah terdaftar dilewati,
     * sedangkan pendaftaran lunas yang belum punya BIB/tiket diperbaiki lebih
     * dahulu supaya tidak menyisakan baris setengah jadi.
     */
    private const STATUS_CYCLE = [
        Registration::STATUS_PAID,
        Registration::STATUS_CHECKED_IN,
        Registration::STATUS_PENDING,
        Registration::STATUS_PAID,
        Registration::STATUS_CHECKED_IN,
        Registration::STATUS_PENDING,
        Registration::STATUS_PAID,
        Registration::STATUS_CHECKED_IN,
        Registration::STATUS_PENDING,
        Registration::STATUS_CANCELLED,
        Registration::STATUS_PAID,
        Registration::STATUS_CHECKED_IN,
        Registration::STATUS_PENDING,
        Registration::STATUS_PAID,
        Registration::STATUS_CHECKED_IN,
        Registration::STATUS_PENDING,
        Registration::STATUS_CANCELLED,
        Registration::STATUS_PAID,
        Registration::STATUS_CHECKED_IN,
        Registration::STATUS_PENDING,
    ];

    private const KELUARGA = ['Istri', 'Suami', 'Orang Tua', 'Saudara', 'Teman'];

    private const KLUB = [
        'Pesepeda Nusantara', 'Jakarta Cycling Community', 'Bandung Road Warriors',
        'Bali Endurance Squad', 'Surabaya Spin Force', 'Yogyakarta Night Riders',
        'Solo Sepeda Mania', 'Semarang Grupetto', 'Independent Rider',
    ];

    private const CATATAN_MEDIS = [
        null, null, null,
        'Alergi udang — bawa antihistamin.',
        'Asma ringan, inhaler di pouch.',
        'Pernah cedera lutut kanan (2024).',
        'Hipertensi terkontrol, minum pagi sebelum start.',
    ];

    public function run(): void
    {
        /** @var Collection<int, User> $users */
        $users = User::where('role', User::ROLE_PESERTA)->orderBy('id')->get();

        if ($users->isEmpty()) {
            return;
        }

        /** @var Collection<int, EventCategory> $categories */
        $categories = EventCategory::query()->with('event')->orderBy('event_id')->orderBy('id')->get();

        if ($categories->isEmpty()) {
            return;
        }

        $adminName = User::where('role', User::ROLE_ADMIN)->value('name') ?? 'Admin Velosha';

        $this->perbaikiSisa($adminName);

        $sudahTerdaftar = [];

        Registration::query()->get(['user_id', 'event_category_id'])
            ->each(function (Registration $registration) use (&$sudahTerdaftar): void {
                $sudahTerdaftar[$registration->user_id][$registration->event_category_id] = true;
            });

        $cursor = 0;
        $urutan = 0;

        foreach ($categories->values() as $indeks => $category) {
            $target = 10 + ($indeks % 4);

            for ($n = 0; $n < $target; $n++) {
                $user = $this->ambilPengguna($users, $cursor, $category, $sudahTerdaftar);

                if ($user === null) {
                    continue;
                }

                $sudahTerdaftar[$user->id][$category->id] = true;

                $status = self::STATUS_CYCLE[$urutan % count(self::STATUS_CYCLE)];
                $urutan++;

                $this->buatPendaftaran($user, $category, $status, $adminName);
            }
        }

        $this->sinkronkanRegisteredCount();
    }

    /**
     * Ambil pengguna peserta berikutnya yang belum terdaftar di kategori ini.
     *
     * @param  Collection<int, User>  $users
     * @param  array<int, array<int, bool>>  $sudahTerdaftar
     */
    private function ambilPengguna(Collection $users, int &$cursor, EventCategory $category, array $sudahTerdaftar): ?User
    {
        $total = $users->count();

        for ($coba = 0; $coba < $total; $coba++) {
            $user = $users[($cursor + $coba) % $total];

            if (! isset($sudahTerdaftar[$user->id][$category->id])) {
                $cursor = ($cursor + $coba + 1) % $total;

                return $user;
            }
        }

        return null;
    }

    /**
     * Selesaikan pendaftaran lunas yang tertinggal tanpa BIB/tiket
     * (mis. sisa proses yang gagal sebelumnya) supaya datanya utuh kembali.
     */
    private function perbaikiSisa(string $adminName): void
    {
        Registration::query()
            ->with('category')
            ->get()
            ->each(function (Registration $registration) use ($adminName): void {
                $category = $registration->category;

                if ($category === null) {
                    return;
                }

                if (! in_array($registration->status, [Registration::STATUS_PAID, Registration::STATUS_CHECKED_IN], true)) {
                    return;
                }

                if ($registration->bib_number === null) {
                    $registration->bib_number = Registration::generateBibNumber($category);
                    $registration->save();
                }

                $ticket = Ticket::issueFor($registration);

                if ($registration->status === Registration::STATUS_CHECKED_IN && ! $ticket->is_checked_in) {
                    $ticket->update([
                        'is_checked_in' => true,
                        'checked_in_at' => now()->subDays(fake()->numberBetween(1, 30)),
                        'checked_in_by_admin' => $adminName,
                    ]);
                }
            });
    }

    private function buatPendaftaran(User $user, EventCategory $category, string $status, string $adminName): void
    {
        $registration = Registration::create([
            'user_id' => $user->id,
            'event_category_id' => $category->id,
            'bib_number' => null,
            'participant_name' => $user->name,
            'identity_number' => fake()->numerify('################'),
            'birth_date' => $user->birth_date?->format('Y-m-d')
                ?? fake()->dateTimeBetween('-48 years', '-18 years')->format('Y-m-d'),
            'gender' => $user->gender ?? fake()->randomElement(['male', 'female']),
            'blood_type' => $user->blood_type ?? fake()->randomElement(['A', 'B', 'AB', 'O']),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_relation' => fake()->randomElement(self::KELUARGA),
            'emergency_contact_phone' => '08'.fake()->numerify('##########'),
            'medical_notes' => fake()->randomElement(self::CATATAN_MEDIS),
            'club' => fake()->randomElement(self::KLUB),
            'status' => $status,
        ]);

        // Sebar tanggal pendaftaran 5–120 hari ke belakang agar grafik
        // "pendaftaran per bulan" pada dashboard punya data bervariasi.
        $registration->forceFill([
            'created_at' => fake()->dateTimeBetween('-120 days', '-5 days'),
            'updated_at' => now(),
        ])->save();

        if (! in_array($status, [Registration::STATUS_PAID, Registration::STATUS_CHECKED_IN], true)) {
            return;
        }

        $registration->bib_number = Registration::generateBibNumber($category);
        $registration->save();

        $ticket = Ticket::issueFor($registration);

        if ($status === Registration::STATUS_CHECKED_IN) {
            $checkInAt = $registration->created_at->addDays(fake()->numberBetween(1, 7));

            $ticket->update([
                'is_checked_in' => true,
                'checked_in_at' => $checkInAt->isFuture() ? now() : $checkInAt,
                'checked_in_by_admin' => $adminName,
            ]);
        }
    }

    /**
     * Samakan `registered_count` dengan jumlah baris pendaftaran lunas,
     * supaya angka keterisian kuota di katalog selalu konsisten dengan
     * tabel pendaftar di panel admin.
     */
    private function sinkronkanRegisteredCount(): void
    {
        EventCategory::query()->get()->each(function (EventCategory $category): void {
            $terisi = Registration::query()
                ->where('event_category_id', $category->id)
                ->whereIn('status', [Registration::STATUS_PAID, Registration::STATUS_CHECKED_IN])
                ->count();

            $category->update(['registered_count' => $terisi]);
        });
    }
}
