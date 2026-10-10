<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 10);
            $table->decimal('distance_km', 6, 2);
            $table->decimal('elevation_gain_m', 6, 2);
            $table->decimal('price', 12, 2);
            $table->integer('quota');
            $table->integer('registered_count')->default(0);
            $table->string('wave_start_time', 50);
            $table->decimal('cut_off_hours', 4, 2);
            $table->timestamps();

            $table->index('event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_categories');
    }
};
