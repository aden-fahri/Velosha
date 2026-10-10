<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->text('description');
            $table->string('location_name', 200);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->date('event_date');
            $table->time('start_time');
            $table->enum('type', ['gran_fondo', 'medio_fondo', 'fun_ride', 'climb'])->default('gran_fondo');
            $table->enum('status', ['draft', 'published', 'completed', 'cancelled'])->default('draft');
            $table->string('youtube_video_id', 50)->nullable();
            $table->string('gpx_file_path')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('event_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
