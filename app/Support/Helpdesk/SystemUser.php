<?php

namespace App\Support\Helpdesk;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @file SystemUser.php
 * @class SystemUser
 * @brief Helper penyedia akun bot sistem helpdesk secara lazy.
 */
class SystemUser
{
    /**
     * Alamat email unik untuk bot sistem otomatis.
     */
    public const BOT_EMAIL = 'system-bot@velosha.id';

    /**
     * Nama tampilan bot sistem.
     */
    public const BOT_NAME = 'Velosha Auto-Reply';

    /**
     * Mengambil atau membuat entitas bot sistem secara lazy (firstOrCreate).
     * Password dibuat acak dan tidak dapat ditebak/login (unusable).
     * Role bukan OPERATOR (menggunakan Peserta dengan is_system = true).
     *
     * @return User
     */
    public static function get(): User
    {
        return User::firstOrCreate(
            ['email' => self::BOT_EMAIL],
            [
                'name' => self::BOT_NAME,
                'password' => Hash::make('!*UNUSABLE_' . Str::random(64) . '*!'),
                'role' => User::ROLE_PESERTA,
                'is_system' => true,
            ]
        );
    }
}
