<?php

namespace Tests\Feature\Helpdesk;

use App\Enums\Helpdesk\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Requests\Auth\LoginRequest;
use App\Livewire\Helpdesk\CreateTicket;
use App\Models\HelpdeskTicket;
use App\Models\ReplyTemplate;
use App\Models\TicketResponse;
use App\Models\User;
use App\Services\Helpdesk\AutoReplyService;
use App\Services\Helpdesk\TicketService;
use App\Support\Helpdesk\SystemUser;
use Database\Seeders\DatabaseSeeder;
use DomainException;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class SystemBotAndAutoReplyIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        putenv('ADMIN_EMAIL');
        putenv('ADMIN_PASSWORD');
        parent::tearDown();
    }

    public function test_system_bot_is_lazy_and_not_created_by_production_seed(): void
    {
        putenv('ADMIN_EMAIL=prod-owner@velosha.id');
        putenv('ADMIN_PASSWORD=ProductionSecretPass123!');
        app()->detectEnvironment(fn () => 'production');
        config(['app.env' => 'production']);

        (new DatabaseSeeder())->run();

        // Pastikan tidak ada akun demo atau bot yang di-seed otomatis di production
        $this->assertDatabaseMissing('users', ['email' => 'operator@velosha.id']);
        $this->assertDatabaseMissing('users', ['email' => SystemUser::BOT_EMAIL]);

        // Pemanggilan lazy SystemUser::get() membuat bot
        $bot = SystemUser::get();
        $this->assertInstanceOf(User::class, $bot);
        $this->assertTrue($bot->is_system);
        $this->assertNotSame(User::ROLE_OPERATOR, $bot->role);
        $this->assertDatabaseHas('users', ['email' => SystemUser::BOT_EMAIL, 'is_system' => true]);
    }

    public function test_system_bot_cannot_log_in(): void
    {
        $bot = User::factory()->create([
            'email' => 'bot-login-test@velosha.id',
            'password' => \Illuminate\Support\Facades\Hash::make('known-password'),
            'is_system' => true,
        ]);

        $request = new LoginRequest();
        $request->merge([
            'email' => $bot->email,
            'password' => 'known-password',
        ]);

        $this->expectException(ValidationException::class);
        $request->authenticate();
    }

    public function test_system_bot_is_excluded_from_assignable_operators(): void
    {
        $bot = SystemUser::get();
        $botOperator = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'is_system' => true,
        ]);

        $operator = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'is_system' => false,
        ]);

        $assignable = User::assignableOperators()->get();

        $this->assertTrue($assignable->contains('id', $operator->id));
        $this->assertFalse($assignable->contains('id', $bot->id));
        $this->assertFalse($assignable->contains('id', $botOperator->id));
    }

    public function test_system_bot_cannot_claim_or_be_assigned_ticket(): void
    {
        $bot = SystemUser::get();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_system' => false]);

        $ticket = HelpdeskTicket::factory()->create([
            'status' => TicketStatus::OPEN,
            'operator_id' => null,
        ]);

        $service = app(TicketService::class);

        // 1. Bot tidak bisa klaim tiket
        try {
            $service->claimTicket($ticket, $bot);
            $this->fail('Bot tidak boleh dapat mengklaim tiket');
        } catch (DomainException $e) {
            $this->assertStringContainsString('bot', $e->getMessage());
        }

        // 2. Bot tidak bisa ditugaskan tiket
        try {
            $service->assignOperator($ticket, $bot, $admin);
            $this->fail('Bot tidak boleh dapat ditugaskan tiket');
        } catch (DomainException $e) {
            $this->assertStringContainsString('bot', $e->getMessage());
        }
    }

    public function test_create_ticket_triggers_exactly_one_auto_reply_and_ticket_stays_open(): void
    {
        $reporter = User::factory()->create(['role' => User::ROLE_PESERTA]);

        // Buat template aktif untuk kategori Pembayaran
        ReplyTemplate::create([
            'title' => 'Auto-Reply Pembayaran Event',
            'category' => TicketCategory::PEMBAYARAN,
            'body' => 'Halo {{reporter_name}}, kendala pembayaran pada tiket {{ticket_id}} telah tercatat.',
            'keyword_triggers' => ['bayar', 'qris'],
            'is_active' => true,
        ]);

        $service = app(TicketService::class);
        $ticket = $service->createTicket($reporter, [
            'category' => TicketCategory::PEMBAYARAN,
            'subject' => 'Pembayaran QRIS belum lunas',
            'description' => 'Saya sudah bayar via qris tapi status tiket masih pending.',
            'priority' => TicketPriority::HIGH,
        ]);

        $ticket->refresh();

        // 1. Status tetap Open
        $this->assertSame(TicketStatus::OPEN, $ticket->status);

        // 2. first_response_at tetap null (auto-reply tidak dihitung response SLA manusia)
        $this->assertNull($ticket->first_response_at);

        // 3. operator_id tetap null
        $this->assertNull($ticket->operator_id);

        // 4. Tepat 1 tanggapan auto-reply dibuat
        $responses = TicketResponse::where('ticket_id', $ticket->id)->get();
        $this->assertCount(1, $responses);
        $this->assertTrue($responses->first()->is_auto_reply);
        $this->assertSame(SystemUser::get()->id, $responses->first()->responder_id);
    }

    public function test_blank_template_creates_zero_responses_and_ticket_still_created(): void
    {
        $reporter = User::factory()->create(['role' => User::ROLE_PESERTA]);

        // Tidak ada template yang cocok atau template hanya spasi/kosong
        ReplyTemplate::query()->delete();
        ReplyTemplate::create([
            'title' => 'Template Kosong',
            'category' => TicketCategory::PEMBAYARAN,
            'body' => "  \n\t  {{unknown_foo}}  ",
            'is_active' => true,
        ]);

        $service = app(TicketService::class);
        $ticket = $service->createTicket($reporter, [
            'category' => TicketCategory::PEMBAYARAN,
            'subject' => 'Kendala pembayaran transfer',
            'description' => 'Uraian detail kendala pembayaran.',
            'priority' => TicketPriority::MID,
        ]);

        $ticket->refresh();

        // Tiket berhasil dibuat
        $this->assertNotNull($ticket->id);
        $this->assertSame(TicketStatus::OPEN, $ticket->status);

        // 0 tanggapan (non-empty guarantee)
        $this->assertSame(0, TicketResponse::where('ticket_id', $ticket->id)->count());
    }

    public function test_exception_in_auto_reply_is_logged_and_does_not_block_ticket_creation(): void
    {
        $reporter = User::factory()->create(['role' => User::ROLE_PESERTA]);

        // Mock AutoReplyService agar melempar exception tak terduga
        $mockAutoReply = $this->createMock(AutoReplyService::class);
        $mockAutoReply->method('send')->willThrowException(new Exception('Simulated AutoReply Crash'));
        $this->app->instance(AutoReplyService::class, $mockAutoReply);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'Gagal mengirim auto-reply helpdesk')
                    && isset($context['ticket_id']);
            });

        $service = app(TicketService::class);
        $ticket = $service->createTicket($reporter, [
            'category' => TicketCategory::TIKET_QR,
            'subject' => 'Kendala cetak nomor tiket',
            'description' => 'Uraian deskripsi tiket.',
            'priority' => TicketPriority::LOW,
        ]);

        // Tiket tetap berhasil tersimpan
        $this->assertNotNull($ticket->id);
        $this->assertDatabaseHas('helpdesk_tickets', ['id' => $ticket->id, 'subject' => 'Kendala cetak nomor tiket']);
    }

    public function test_create_ticket_livewire_component_validates_and_persists_category(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $this->actingAs($user);

        // 1. Kategori tidak valid ditolak
        Livewire::test(CreateTicket::class)
            ->set('category', 'InvalidCategory')
            ->set('subject', 'Subjek tiket yang valid')
            ->set('description', 'Deskripsi tiket minimal sepuluh karakter.')
            ->call('submit')
            ->assertHasErrors(['category']);

        // 2. Kategori valid diterima dan tiket tersimpan dengan kategori tersebut
        Livewire::test(CreateTicket::class)
            ->set('category', TicketCategory::PEMBAYARAN->value)
            ->set('subject', 'Pertanyaan tagihan virtual account')
            ->set('description', 'Deskripsi kendala pembayaran tagihan bank.')
            ->call('submit')
            ->assertHasNoErrors();

        $ticket = HelpdeskTicket::where('reporter_id', $user->id)->latest('id')->first();
        $this->assertNotNull($ticket);
        $this->assertSame(TicketCategory::PEMBAYARAN, $ticket->category);
    }

    public function test_system_bot_cannot_be_updated_or_deleted(): void
    {
        $bot = SystemUser::get();

        // 1. Coba update harus gagal dengan DomainException
        try {
            $bot->update(['name' => 'Nama Baru']);
            $this->fail('Akun bot sistem tidak boleh dapat diubah');
        } catch (\DomainException $e) {
            $this->assertStringContainsString('tidak dapat diubah', $e->getMessage());
        }

        // 2. Coba delete harus gagal dengan DomainException
        try {
            $bot->delete();
            $this->fail('Akun bot sistem tidak boleh dapat dihapus');
        } catch (\DomainException $e) {
            $this->assertStringContainsString('tidak dapat dihapus', $e->getMessage());
        }

        // Pastikan bot tetap utuh di database
        $this->assertDatabaseHas('users', ['id' => $bot->id, 'email' => SystemUser::BOT_EMAIL]);
    }

    public function test_system_user_helper_get_is_idempotent_and_survives_protection(): void
    {
        $botFirst = SystemUser::get();
        $botSecond = SystemUser::get();

        $this->assertSame($botFirst->id, $botSecond->id);
        $this->assertSame(SystemUser::BOT_EMAIL, $botSecond->email);
        $this->assertTrue($botSecond->is_system);
    }

    public function test_system_bot_is_excluded_from_filament_user_queries_and_stats_overview(): void
    {
        $bot = SystemUser::get();
        $regularUser = User::factory()->create(['role' => User::ROLE_PESERTA, 'is_system' => false]);

        // 1. Filament UserResource::getEloquentQuery() mengecualikan is_system
        $filamentUsers = \App\Filament\Resources\Users\UserResource::getEloquentQuery()->get();
        $this->assertTrue($filamentUsers->contains('id', $regularUser->id));
        $this->assertFalse($filamentUsers->contains('id', $bot->id));

        // 2. canEdit dan canDelete bernilai false untuk bot
        $this->assertFalse(\App\Filament\Resources\Users\UserResource::canEdit($bot));
        $this->assertFalse(\App\Filament\Resources\Users\UserResource::canDelete($bot));
        $this->assertTrue(\App\Filament\Resources\Users\UserResource::canEdit($regularUser));
        $this->assertTrue(\App\Filament\Resources\Users\UserResource::canDelete($regularUser));

        // 3. VeloshaStatsOverview mengecualikan bot dari jumlah peserta
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_system' => false]);
        $this->actingAs($admin);

        $widget = new class extends \App\Filament\Widgets\VeloshaStatsOverview {
            public function exposeJumlahPeserta(): int
            {
                return $this->jumlahPeserta();
            }
        };

        // Hanya regularUser yang dihitung, bot peserta diabaikan
        $this->assertSame(1, $widget->exposeJumlahPeserta());
    }
}
