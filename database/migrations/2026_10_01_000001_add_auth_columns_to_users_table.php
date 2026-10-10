<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('password');
            $table->date('birth_date')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female'])->nullable()->after('birth_date');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable()->after('gender');
            $table->string('avatar')->nullable()->after('blood_type');
            $table->string('provider', 50)->nullable()->after('avatar');
            $table->string('provider_id')->nullable()->after('provider');
            $table->enum('role', ['peserta', 'penyelenggara', 'admin'])
                ->default('peserta')
                ->after('provider_id');
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'birth_date',
                'gender',
                'blood_type',
                'avatar',
                'provider',
                'provider_id',
                'role',
            ]);
            $table->string('password')->nullable(false)->change();
        });
    }
};
