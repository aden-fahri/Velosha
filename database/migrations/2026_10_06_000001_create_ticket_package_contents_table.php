<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_package_contents', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('features');
            $table->string('benefits_title');
            $table->json('benefits');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_package_contents');
    }
};
