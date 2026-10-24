<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter enum role on users table to include 'operator'
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('peserta', 'penyelenggara', 'operator', 'admin') NOT NULL DEFAULT 'peserta'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe rollback: update any user with 'operator' role to 'peserta' first
        DB::table('users')->where('role', 'operator')->update(['role' => 'peserta']);

        // Shrink enum back to original roles
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('peserta', 'penyelenggara', 'admin') NOT NULL DEFAULT 'peserta'");
    }
};
