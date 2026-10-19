<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (blank($email) || blank($password)) {
            throw new \RuntimeException('ADMIN_EMAIL dan ADMIN_PASSWORD wajib diisi untuk seed production.');
        }
        if (strlen($password) < 12 || strtolower($password) === 'password') {
            throw new \RuntimeException('ADMIN_PASSWORD terlalu lemah.');
        }

        User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin Velosha',
                'password' => bcrypt($password),
                'role' => User::ROLE_ADMIN ?? 'admin',
            ]
        );
    }
}