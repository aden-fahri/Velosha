<?php

namespace Tests\Feature\Helpdesk;

use App\Enums\Helpdesk\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\ReplyTemplate;
use App\Models\SatisfactionRating;
use App\Models\SlaPolicy;
use App\Models\TicketResponse;
use App\Models\TicketSuggestionFeedback;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class HelpdeskSchemaAndPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pastikan nilai ENUM di kolom tabel MySQL persis sama dengan case di enum PHP.
     */
    public function test_mysql_enum_columns_match_php_enums_exactly(): void
    {
        $statusColumn = DB::selectOne("SHOW COLUMNS FROM " . HelpdeskTicket::TABLE_NAME . " LIKE 'status'");
        $this->assertNotNull($statusColumn);
        // Format MySQL: enum('Open','In-Progress','Closed')
        $this->assertSame("enum('Open','In-Progress','Closed')", $statusColumn->Type);

        preg_match("/^enum\((.*)\)$/", $statusColumn->Type, $matches);
        $statusValues = array_map(fn ($val) => trim($val, "'"), explode(',', $matches[1]));
        $this->assertEquals(TicketStatus::values(), $statusValues);

        $priorityColumn = DB::selectOne("SHOW COLUMNS FROM " . HelpdeskTicket::TABLE_NAME . " LIKE 'priority'");
        $this->assertNotNull($priorityColumn);
        // Format MySQL: enum('Low','Mid','High')
        $this->assertSame("enum('Low','Mid','High')", $priorityColumn->Type);

        preg_match("/^enum\((.*)\)$/", $priorityColumn->Type, $matches);
        $priorityValues = array_map(fn ($val) => trim($val, "'"), explode(',', $matches[1]));
        $this->assertEquals(TicketPriority::values(), $priorityValues);

        // Format MySQL: enum('Tiket/QR','Pembayaran','Akun','Lokasi','Lainnya')
        $categoryColumn = DB::selectOne("SHOW COLUMNS FROM " . HelpdeskTicket::TABLE_NAME . " LIKE 'category'");
        $this->assertNotNull($categoryColumn);
        $this->assertSame("enum('Tiket/QR','Pembayaran','Akun','Lokasi','Lainnya')", $categoryColumn->Type);

        preg_match("/^enum\((.*)\)$/", $categoryColumn->Type, $matches);
        $categoryValues = array_map(fn ($val) => trim($val, "'"), explode(',', $matches[1]));
        $this->assertEquals(TicketCategory::values(), $categoryValues);

        // Format MySQL pada tabel reply_templates
        $tplCategoryColumn = DB::selectOne("SHOW COLUMNS FROM reply_templates LIKE 'category'");
        $this->assertNotNull($tplCategoryColumn);
        $this->assertSame("enum('Tiket/QR','Pembayaran','Akun','Lokasi','Lainnya')", $tplCategoryColumn->Type);

        preg_match("/^enum\((.*)\)$/", $tplCategoryColumn->Type, $matches);
        $tplCategoryValues = array_map(fn ($val) => trim($val, "'"), explode(',', $matches[1]));
        $this->assertEquals(TicketCategory::values(), $tplCategoryValues);
    }

    /**
     * Pastikan semua tabel dan kolom ekstensi helpdesk terbentuk lengkap di database.
     */
    public function test_helpdesk_tables_have_all_expected_columns(): void
    {
        $ticketColumns = DB::getSchemaBuilder()->getColumnListing(HelpdeskTicket::TABLE_NAME);
        $expectedColumns = [
            'id',
            'reporter_id',
            'operator_id',
            'category',
            'subject',
            'description',
            'priority',
            'status',
            'duplicate_of_id',
            'is_reference',
            'solution_summary',
            'first_response_at',
            'closed_at',
            'resolution_time',
            'response_due_at',
            'resolution_due_at',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $col) {
            $this->assertContains($col, $ticketColumns, "Kolom {$col} harus ada pada tabel " . HelpdeskTicket::TABLE_NAME);
        }

        $this->assertTrue(DB::getSchemaBuilder()->hasTable('ticket_responses'));
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('satisfaction_ratings'));
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('sla_policies'));
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('reply_templates'));
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('ticket_suggestion_feedback'));
    }

    /**
     * Pastikan sla_policies memiliki nilai default High, Mid, Low sesuai PRD.
     */
    public function test_sla_policies_seeded_with_correct_defaults(): void
    {
        $high = SlaPolicy::forPriority(TicketPriority::HIGH);
        $this->assertNotNull($high);
        $this->assertSame(15, $high->response_minutes);
        $this->assertSame(240, $high->resolution_minutes);

        $mid = SlaPolicy::forPriority(TicketPriority::MID);
        $this->assertNotNull($mid);
        $this->assertSame(60, $mid->response_minutes);
        $this->assertSame(1440, $mid->resolution_minutes);

        $low = SlaPolicy::forPriority(TicketPriority::LOW);
        $this->assertNotNull($low);
        $this->assertSame(240, $low->response_minutes);
        $this->assertSame(4320, $low->resolution_minutes);
    }

    /**
     * Pastikan constraint skor rating 1-5 ditegakkan di MySQL.
     */
    public function test_satisfaction_rating_score_check_constraint(): void
    {
        $ticket = HelpdeskTicket::factory()->closed()->create();

        // Skor 0 tidak diizinkan oleh check constraint
        $this->expectException(QueryException::class);
        DB::table('satisfaction_ratings')->insert([
            'ticket_id' => $ticket->id,
            'score' => 0,
            'feedback' => 'Invalid score 0',
            'created_at' => now(),
        ]);
    }

    /**
     * Pastikan semua factory berfungsi menghasilkan model yang valid dengan relasi lengkap.
     */
    public function test_factories_create_valid_models_and_relations(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertInstanceOf(User::class, $ticket->reporter);
        $this->assertSame(TicketStatus::OPEN, $ticket->status);

        $response = TicketResponse::factory()->create(['ticket_id' => $ticket->id]);
        $this->assertInstanceOf(TicketResponse::class, $response);
        $this->assertSame($ticket->id, $response->ticket->id);

        $closedTicket = HelpdeskTicket::factory()->closed()->create();
        $rating = SatisfactionRating::factory()->create(['ticket_id' => $closedTicket->id]);
        $this->assertInstanceOf(SatisfactionRating::class, $rating);
        $this->assertSame($closedTicket->id, $rating->ticket->id);

        $template = ReplyTemplate::factory()->create();
        $this->assertInstanceOf(ReplyTemplate::class, $template);

        $feedback = TicketSuggestionFeedback::factory()->create(['ticket_id' => $closedTicket->id]);
        $this->assertInstanceOf(TicketSuggestionFeedback::class, $feedback);
    }

    /**
     * Uji kebijakan akses (Policy):
     * - Reporter hanya melihat tiket miliknya.
     * - Operator dan Admin dapat melihat tiket apa pun.
     * - Rating hanya boleh diberikan oleh pelapor saat Closed dan hanya satu kali.
     * - Balasan tidak boleh dikirim pada tiket Closed.
     */
    public function test_helpdesk_ticket_policy_rules(): void
    {
        $reporter = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $otherUser = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $ticket = HelpdeskTicket::factory()->create([
            'reporter_id' => $reporter->id,
            'status' => TicketStatus::OPEN,
        ]);

        // View policy
        $this->assertTrue(Gate::forUser($reporter)->allows('view', $ticket));
        $this->assertFalse(Gate::forUser($otherUser)->allows('view', $ticket));
        $this->assertTrue(Gate::forUser($operator)->allows('view', $ticket));
        $this->assertTrue(Gate::forUser($admin)->allows('view', $ticket));

        // Reply policy (saat Open)
        $this->assertTrue(Gate::forUser($reporter)->allows('reply', $ticket));
        $this->assertFalse(Gate::forUser($otherUser)->allows('reply', $ticket));
        $this->assertTrue(Gate::forUser($operator)->allows('reply', $ticket));
        $this->assertTrue(Gate::forUser($admin)->allows('reply', $ticket));

        // Update status/priority policy
        $this->assertFalse(Gate::forUser($reporter)->allows('update', $ticket));
        $this->assertTrue(Gate::forUser($operator)->allows('update', $ticket));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $ticket));

        // Rate policy saat Open (harus false untuk semua)
        $this->assertFalse(Gate::forUser($reporter)->allows('rate', $ticket));
        $this->assertFalse(Gate::forUser($operator)->allows('rate', $ticket));
        $this->assertFalse(Gate::forUser($admin)->allows('rate', $ticket));

        // Ubah status ke Closed
        $ticket->status = TicketStatus::CLOSED;
        $ticket->save();

        // Reply policy saat Closed (harus false untuk semua)
        $this->assertFalse(Gate::forUser($reporter)->allows('reply', $ticket));
        $this->assertFalse(Gate::forUser($operator)->allows('reply', $ticket));
        $this->assertFalse(Gate::forUser($admin)->allows('reply', $ticket));

        // Rate policy saat Closed (hanya reporter pemilik)
        $this->assertTrue(Gate::forUser($reporter)->allows('rate', $ticket));
        $this->assertFalse(Gate::forUser($otherUser)->allows('rate', $ticket));
        $this->assertFalse(Gate::forUser($operator)->allows('rate', $ticket));
        $this->assertFalse(Gate::forUser($admin)->allows('rate', $ticket));

        // Setelah diberi rating, tidak boleh memberi rating lagi
        SatisfactionRating::factory()->create(['ticket_id' => $ticket->id, 'score' => 5]);
        $this->assertFalse(Gate::forUser($reporter)->allows('rate', $ticket->fresh()));
    }

    /**
     * Pastikan migrasi down() pada migrasi helpdesk berjalan aman dan membersihkan tabel secara tuntas.
     */
    public function test_migration_down_drops_all_helpdesk_tables_cleanly(): void
    {
        $migrationFile = database_path('migrations/2026_10_24_000002_create_helpdesk_tables.php');
        $migration = require $migrationFile;

        try {
            $migration->down();

            $this->assertFalse(DB::getSchemaBuilder()->hasTable('ticket_suggestion_feedback'));
            $this->assertFalse(DB::getSchemaBuilder()->hasTable('reply_templates'));
            $this->assertFalse(DB::getSchemaBuilder()->hasTable('sla_policies'));
            $this->assertFalse(DB::getSchemaBuilder()->hasTable('satisfaction_ratings'));
            $this->assertFalse(DB::getSchemaBuilder()->hasTable('ticket_responses'));
            $this->assertFalse(DB::getSchemaBuilder()->hasTable(HelpdeskTicket::TABLE_NAME));
        } finally {
            $migration->up();
        }
    }

    /**
     * Pastikan indeks FULLTEXT dan kolom duplicate_of_id telah ada sejak Phase 2.
     */
    public function test_fulltext_and_duplicate_columns_exist_from_phase_2(): void
    {
        $columns = DB::getSchemaBuilder()->getColumnListing(HelpdeskTicket::TABLE_NAME);
        $this->assertContains('duplicate_of_id', $columns);

        $indexes = DB::select("SHOW INDEX FROM " . HelpdeskTicket::TABLE_NAME . " WHERE Index_type = 'FULLTEXT'");
        $this->assertNotEmpty($indexes, 'Tabel helpdesk_tickets harus memiliki indeks FULLTEXT.');

        $indexedColumns = array_map(fn ($idx) => $idx->Column_name, $indexes);
        $this->assertContains('subject', $indexedColumns);
        $this->assertContains('description', $indexedColumns);
    }

    /**
     * Pastikan tabel ticket_suggestion_feedback memiliki unique constraint (ticket_id, reporter_id)
     * dan updateOrCreate memperbarui data tanpa menambah baris duplikat.
     */
    public function test_ticket_suggestion_feedback_has_unique_constraint_on_ticket_and_reporter(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $reporter = User::factory()->create();

        TicketSuggestionFeedback::create([
            'ticket_id' => $ticket->id,
            'reporter_id' => $reporter->id,
            'helpful' => true,
            'created_at' => now(),
        ]);

        $this->expectException(QueryException::class);
        DB::table('ticket_suggestion_feedback')->insert([
            'ticket_id' => $ticket->id,
            'reporter_id' => $reporter->id,
            'helpful' => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Pastikan updateOrCreate pada ticket_suggestion_feedback memperbarui nilai helpful tanpa menduplikasi baris.
     */
    public function test_ticket_suggestion_feedback_vote_updates_cleanly_via_update_or_create(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $reporter = User::factory()->create();

        $first = TicketSuggestionFeedback::updateOrCreate(
            ['ticket_id' => $ticket->id, 'reporter_id' => $reporter->id],
            ['helpful' => true, 'created_at' => now()]
        );
        $this->assertTrue($first->helpful);
        $this->assertSame(1, TicketSuggestionFeedback::where('ticket_id', $ticket->id)->count());

        $second = TicketSuggestionFeedback::updateOrCreate(
            ['ticket_id' => $ticket->id, 'reporter_id' => $reporter->id],
            ['helpful' => false, 'created_at' => now()]
        );
        $this->assertFalse($second->helpful);
        $this->assertSame(1, TicketSuggestionFeedback::where('ticket_id', $ticket->id)->count());
    }

    /**
     * Pastikan migrasi 06 down() dan up() berjalan mulus.
     */
    public function test_migration_06_unique_constraint_down_and_up_works_cleanly(): void
    {
        $migrationFile = database_path('migrations/2026_10_24_000006_add_unique_to_ticket_suggestion_feedback_table.php');
        $migration = require $migrationFile;

        $migration->down();

        $ticket = HelpdeskTicket::factory()->create();
        $reporter = User::factory()->create();

        DB::table('ticket_suggestion_feedback')->insert([
            'ticket_id' => $ticket->id,
            'reporter_id' => $reporter->id,
            'helpful' => true,
            'created_at' => now(),
        ]);

        // Setelah down, duplicate insert dibolehkan oleh database
        DB::table('ticket_suggestion_feedback')->insert([
            'ticket_id' => $ticket->id,
            'reporter_id' => $reporter->id,
            'helpful' => false,
            'created_at' => now(),
        ]);
        $this->assertSame(2, DB::table('ticket_suggestion_feedback')->where('ticket_id', $ticket->id)->count());

        // Bersihkan data sebelum up() kembali
        DB::table('ticket_suggestion_feedback')->where('ticket_id', $ticket->id)->delete();
        $migration->up();
    }
}

