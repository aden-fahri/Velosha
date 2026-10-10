<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_contents', function (Blueprint $table) {
            $table->id();
            $table->text('brand_description');
            $table->string('kategori_title');
            $table->json('kategori_links');
            $table->string('bantuan_title');
            $table->json('bantuan_links');
            $table->string('payment_title');
            $table->json('payment_methods');
            $table->string('copyright');
            $table->json('bottom_links');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_contents');
    }
};
