<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quota_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->unsignedInteger('events_count');
            $table->unsignedInteger('price');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('quota_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quota_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_id', 100)->unique();
            $table->unsignedInteger('amount');
            $table->string('status', 20)->default('pending'); // pending, paid, cancelled, expire
            $table->string('snap_token')->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
        });

        Schema::create('quota_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->integer('delta'); // +N untuk pembelian, -1 untuk publish
            $table->string('reason', 50); // purchase, publish_event, refund_event, manual_adjustment
            $table->foreignId('quota_purchase_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index(['organization_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quota_ledger');
        Schema::dropIfExists('quota_purchases');
        Schema::dropIfExists('quota_packages');
    }
};

