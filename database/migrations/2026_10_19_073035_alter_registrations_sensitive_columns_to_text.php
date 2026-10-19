<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->text('identity_number')->change();
            $table->text('emergency_contact_phone')->change();
        });
        // medical_notes already text
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('identity_number', 50)->change();
            $table->string('emergency_contact_phone', 30)->change();
        });
    }
};