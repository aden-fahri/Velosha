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
        Schema::create('payout_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('bank_name', 50);
            $table->text('account_number'); // Disimpan terenkripsi
            $table->string('account_name', 150);
            $table->boolean('is_primary')->default(true);
            $table->timestamps();

            $table->index('organization_id');
        });

        Schema::create('financial_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('registration_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 30); // ticket_sale, platform_fee, refund, payout, adjustment
            $table->enum('direction', ['credit', 'debit']);
            $table->unsignedInteger('amount'); // Nominal Rupiah bulat
            $table->string('note', 255)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['organization_id', 'created_at']);
            $table->index(['organization_id', 'direction']);
        });

        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payout_account_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount');
            $table->string('status', 20)->default('pending'); // pending, approved, rejected
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->text('rejection_note')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
        Schema::dropIfExists('financial_ledger');
        Schema::dropIfExists('payout_accounts');
    }
};

