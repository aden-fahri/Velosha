<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_contents', function (Blueprint $table) {
            $table->dropColumn(['circuit_label', 'circuit_title', 'checkpoints', 'paket_items']);
        });
    }

    public function down(): void
    {
        Schema::table('about_contents', function (Blueprint $table) {
            $table->string('circuit_label')->nullable();
            $table->string('circuit_title')->nullable();
            $table->json('checkpoints')->nullable();
            $table->json('paket_items')->nullable();
        });
    }
};
