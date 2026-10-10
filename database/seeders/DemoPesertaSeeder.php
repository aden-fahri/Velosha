<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoPesertaSeeder extends Seeder
{
    /**
     * 60 akun peserta demo (sandi sama: "password") supaya pendaftaran
     * dummy menempel ke banyak pengguna berbeda, bukan satu akun saja.
     */
    public const JUMLAH = 60;

    public function run(): void
    {
        foreach (range(1, self::JUMLAH) as $i) {
            User::firstOrCreate(
                ['email' => sprintf('peserta%02d@velosha.id', $i)],
                [
                    'name' => fake()->unique()->name(),
                    'phone' => '08'.fake()->unique()->numerify('##########'),
                    'role' => User::ROLE_PESERTA,
                    'password' => 'password',
                ],
            );
        }

        // Profil pendukung (usia, golongan darah) diisi acak agar panel lebih hidup.
        User::where('role', User::ROLE_PESERTA)
            ->where('email', 'like', 'peserta%@velosha.id')
            ->orderBy('id')
            ->get()
            ->each(function (User $user): void {
                if ($user->birth_date !== null) {
                    return;
                }

                $user->forceFill([
                    'birth_date' => fake()->dateTimeBetween('-45 years', '-18 years')->format('Y-m-d'),
                    'gender' => fake()->randomElement(['male', 'female']),
                    'blood_type' => fake()->randomElement(['A', 'B', 'AB', 'O']),
                ])->save();
            });
    }
}
