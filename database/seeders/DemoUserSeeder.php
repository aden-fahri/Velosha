<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    /**
     * Akun demo per role. Sandi sama: "password".
     */
    public function run(): void
    {
        $akun = [
            [
                'name' => 'Admin Velosha',
                'email' => 'admin@velosha.id',
                'phone' => '081100000001',
                'role' => User::ROLE_ADMIN,
            ],
            [
                'name' => 'Race Director',
                'email' => 'penyelenggara@velosha.id',
                'phone' => '081100000002',
                'role' => User::ROLE_PENYELENGGARA,
            ],
            [
                'name' => 'Budi Pratama',
                'email' => 'peserta@velosha.id',
                'phone' => '081100000003',
                'role' => User::ROLE_PESERTA,
            ],
            [
                'name' => 'Raka Perkasa',
                'email' => 'operator@velosha.id',
                'phone' => '081100000004',
                'role' => User::ROLE_OPERATOR,
            ],
        ];

        foreach ($akun as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                $data + ['password' => 'password'],
            );
        }
    }
}
