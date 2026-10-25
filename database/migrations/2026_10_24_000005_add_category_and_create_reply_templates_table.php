<?php

use App\Enums\Helpdesk\TicketCategory;
use App\Models\HelpdeskTicket;
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
        if (! Schema::hasColumn(HelpdeskTicket::TABLE_NAME, 'category')) {
            Schema::table(HelpdeskTicket::TABLE_NAME, function (Blueprint $table) {
                $table->enum('category', TicketCategory::values())
                    ->default(TicketCategory::LAINNYA->value)
                    ->after('operator_id');
                $table->index('category');
            });
        }

        if (! Schema::hasColumn('reply_templates', 'category')) {
            Schema::table('reply_templates', function (Blueprint $table) {
                $table->enum('category', TicketCategory::values())
                    ->default(TicketCategory::LAINNYA->value)
                    ->after('id');
                $table->index('category');
            });
        }

        if (! Schema::hasColumn('reply_templates', 'keyword_triggers')) {
            Schema::table('reply_templates', function (Blueprint $table) {
                $table->json('keyword_triggers')->nullable()->after('keywords');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('reply_templates', 'keyword_triggers')) {
            Schema::table('reply_templates', function (Blueprint $table) {
                $table->dropColumn('keyword_triggers');
            });
        }

        if (Schema::hasColumn('reply_templates', 'category')) {
            Schema::table('reply_templates', function (Blueprint $table) {
                $table->dropIndex(['category']);
                $table->dropColumn('category');
            });
        }

        if (Schema::hasColumn(HelpdeskTicket::TABLE_NAME, 'category')) {
            Schema::table(HelpdeskTicket::TABLE_NAME, function (Blueprint $table) {
                $table->dropIndex(['category']);
                $table->dropColumn('category');
            });
        }
    }
};
