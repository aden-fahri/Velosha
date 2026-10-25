<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_suggestion_feedback', function (Blueprint $table) {
            $table->unique(['ticket_id', 'reporter_id'], 'ticket_suggestion_feedback_ticket_reporter_unique');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_suggestion_feedback', function (Blueprint $table) {
            $table->dropUnique('ticket_suggestion_feedback_ticket_reporter_unique');
        });
    }
};
