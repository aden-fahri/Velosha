<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        Schema::create('organization_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['owner', 'manager', 'staf'])->default('owner');
            $table->timestamps();

            $table->unique(['organization_id', 'user_id']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('user_id')->constrained('organizations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
        });

        Schema::dropIfExists('organization_members');
        Schema::dropIfExists('organizations');
    }
};
