<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nama tabel sentral untuk HelpdeskTicket.
     */
    public const TABLE = HelpdeskTicket::TABLE_NAME;

    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject', 160);
            $table->text('description');
            $table->enum('priority', TicketPriority::values())->default(TicketPriority::LOW->value);
            $table->enum('status', TicketStatus::values())->default(TicketStatus::OPEN->value);
            $table->foreignId('duplicate_of_id')->nullable()->constrained(self::TABLE)->nullOnDelete();
            $table->boolean('is_reference')->default(false);
            $table->dateTime('first_response_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->unsignedInteger('resolution_time')->nullable(); // Menit antara created_at dan closed_at
            $table->dateTime('response_due_at')->nullable();
            $table->dateTime('resolution_due_at')->nullable();
            $table->timestamps();

            // Indeks pendukung performa antrian dan agregasi analitik
            $table->index(['status', 'priority']);
            $table->index('reporter_id');
            $table->index('operator_id');
            $table->fullText(['subject', 'description']);
        });

        Schema::create('ticket_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained(self::TABLE)->cascadeOnDelete();
            $table->foreignId('responder_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->boolean('is_auto_reply')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['ticket_id', 'created_at']);
        });

        Schema::create('satisfaction_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->unique()->constrained(self::TABLE)->cascadeOnDelete();
            $table->unsignedTinyInteger('score'); // CHECK 1..5
            $table->text('feedback')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Pastikan CHECK constraint untuk score 1-5 di MySQL
        DB::statement('ALTER TABLE satisfaction_ratings ADD CONSTRAINT chk_satisfaction_rating_score CHECK (score BETWEEN 1 AND 5)');

        Schema::create('sla_policies', function (Blueprint $table) {
            $table->id();
            $table->string('priority', 32)->unique();
            $table->unsignedInteger('response_minutes');
            $table->unsignedInteger('resolution_minutes');
            $table->timestamps();
        });

        // Default SLA policies per spesifikasi: High 15m/240m (4j), Mid 60m/1440m (24j), Low 240m/4320m (72j)
        DB::table('sla_policies')->insert([
            [
                'priority' => TicketPriority::HIGH->value,
                'response_minutes' => 15,
                'resolution_minutes' => 240,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'priority' => TicketPriority::MID->value,
                'response_minutes' => 60,
                'resolution_minutes' => 1440,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'priority' => TicketPriority::LOW->value,
                'response_minutes' => 240,
                'resolution_minutes' => 4320,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::create('reply_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->text('body');
            $table->text('keywords')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('use_count')->default(0);
            $table->timestamps();
        });

        Schema::create('ticket_suggestion_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained(self::TABLE)->cascadeOnDelete();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('helpful');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['ticket_id', 'helpful']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_suggestion_feedback');
        Schema::dropIfExists('reply_templates');
        Schema::dropIfExists('sla_policies');
        Schema::dropIfExists('satisfaction_ratings');
        Schema::dropIfExists('ticket_responses');
        Schema::dropIfExists(self::TABLE);
    }
};
