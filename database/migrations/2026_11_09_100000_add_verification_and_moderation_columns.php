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
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('verification_status', 20)->default('unverified')->after('logo_path');
            $table->string('verification_document_path')->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('verification_document_path');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            $table->text('rejection_note')->nullable()->after('verified_by');

            $table->index('verification_status');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('status', 20)->default('draft')->change();
            $table->text('rejection_note')->nullable()->after('status');
            $table->foreignId('reviewed_by')->nullable()->after('rejection_note')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['rejection_note', 'reviewed_at']);
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropIndex(['verification_status']);
            $table->dropColumn([
                'verification_status',
                'verification_document_path',
                'verified_at',
                'rejection_note',
            ]);
        });
    }
};

