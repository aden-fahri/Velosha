<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_category_id')->constrained()->cascadeOnDelete();
            $table->string('bib_number', 30)->nullable()->unique();
            $table->string('participant_name', 150);
            $table->string('identity_number', 50);
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->enum('blood_type', ['A', 'B', 'AB', 'O']);
            $table->string('emergency_contact_name', 150);
            $table->string('emergency_contact_relation', 50);
            $table->string('emergency_contact_phone', 30);
            $table->text('medical_notes')->nullable();
            $table->string('club', 150)->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();

            $table->unique(['user_id', 'event_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
