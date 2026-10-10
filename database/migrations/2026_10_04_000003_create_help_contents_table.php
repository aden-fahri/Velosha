<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('help_contents', function (Blueprint $table) {
            $table->id();
            $table->string('header_eyebrow')->nullable();
            $table->string('header_title');
            $table->text('header_description')->nullable();
            $table->string('header_meta')->nullable();
            $table->json('contacts')->nullable();
            $table->json('faq_groups')->nullable();
            $table->string('cta_title');
            $table->text('cta_desc')->nullable();
            $table->string('cta_label');
            $table->string('cta_url');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('help_contents');
    }
};
