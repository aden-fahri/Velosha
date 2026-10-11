<?php

namespace Tests\Feature;

use App\Livewire\MizuChatWidget;
use App\Support\MizuAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class MizuChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Default suite: Mizu aktif dengan kuncidummy (tidak pernah memanggil API asli).
        config()->set('services.gemini.key', 'test-key');
        config()->set('services.gemini.model', 'gemini-3.1-flash-lite');
    }

    protected function fakeGemini(string $text = 'Race pack diambil H-2 sampai H-1 di race village.'): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => $text]]]],
                ],
            ]),
        ]);
    }

    public function test_widget_renders_on_public_pages(): void
    {
        $this->fakeGemini();

        $this->get('/')
            ->assertOk()
            ->assertSee('Tanya Mizu');
    }

    public function test_widget_renders_on_help_page(): void
    {
        $this->fakeGemini();

        $this->get('/help')
            ->assertOk()
            ->assertSee('Asisten Balap Velosha');
    }

    public function test_widget_is_hidden_without_api_key(): void
    {
        config()->set('services.gemini.key', null);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Tanya Mizu');
    }

    public function test_asking_a_question_sends_history_and_shows_answer(): void
    {
        $this->fakeGemini('Bawa E-Ticket QR dan kartu identitas asli.');

        Livewire::test(MizuChatWidget::class)
            ->set('question', 'Kapan ambil race pack?')
            ->call('ask')
            ->assertSet('question', '')
            ->assertSee('Kapan ambil race pack?')
            ->assertSee('Bawa E-Ticket QR dan kartu identitas asli.');

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return str_contains($request->url(), 'gemini-3.1-flash-lite:generateContent')
                && str_contains($request->url(), 'key=test-key')
                && $payload['contents'][0]['role'] === 'user'
                && $payload['contents'][0]['parts'][0]['text'] === 'Kapan ambil race pack?';
        });
    }

    public function test_system_prompt_carries_live_site_knowledge(): void
    {
        $this->fakeGemini();

        Livewire::test(MizuChatWidget::class)
            ->set('question', 'Apa itu Cut-Off Time?')
            ->call('ask');

        Http::assertSent(function ($request) {
            $systemPrompt = $request->data()['systemInstruction']['parts'][0]['text'] ?? '';

            return str_contains($systemPrompt, 'Mizu')
                && str_contains($systemPrompt, 'Cut-Off Time')
                && str_contains($systemPrompt, 'Regulasi Balapan');
        });
    }

    public function test_quick_prompt_submits_question(): void
    {
        $this->fakeGemini();

        Livewire::test(MizuChatWidget::class)
            ->call('askQuick', 'Apa itu Cut-Off Time (COT)?')
            ->assertSee('Apa itu Cut-Off Time (COT)?');
    }

    public function test_panel_is_anchored_so_trigger_button_never_moves(): void
    {
        $this->fakeGemini();

        // Panel harus absolute (keluar dari aliran layout) agar tombol pemicu diam.
        Livewire::test(MizuChatWidget::class)
            ->assertSee('absolute bottom-full right-0', false)
            ->assertSee('flex flex-col items-end w-fit', false);
    }

    public function test_panel_state_is_client_side_only(): void
    {
        $this->fakeGemini();

        $component = Livewire::test(MizuChatWidget::class);

        // Tidak ada lagi state $open di server: buka/tutup sepenuhnya Alpine.
        $component->assertDontSee('@entangle', false);
        $this->assertFalse($component->instance()->hasProperty('open'));
    }

    public function test_component_no_longer_exposes_toggle_action(): void
    {
        $this->assertFalse(method_exists(\App\Livewire\MizuChatWidget::class, 'toggle'));
    }

    public function test_answering_dispatches_scroll_event(): void
    {
        $this->fakeGemini('Bawa E-Ticket QR dan identitas asli.');

        Livewire::test(MizuChatWidget::class)
            ->set('question', 'Kapan ambil race pack?')
            ->call('ask')
            ->assertDispatched('mizu-answered');
    }

    public function test_reset_chat_clears_messages(): void
    {
        $this->fakeGemini();

        Livewire::test(MizuChatWidget::class)
            ->set('question', 'Halo Mizu')
            ->call('ask')
            ->assertCount('messages', 2)
            ->call('resetChat')
            ->assertSet('messages', []);
    }

    public function test_overlong_input_is_rejected_before_calling_api(): void
    {
        $this->fakeGemini();

        Livewire::test(MizuChatWidget::class)
            ->set('question', str_repeat('a', 900))
            ->call('ask')
            ->assertHasErrors('question');

        Http::assertNothingSent();
    }

    public function test_service_truncates_overlong_user_message_defensively(): void
    {
        $this->fakeGemini();

        // Lapisan kedua: walau pesan lolos validasi, service tetap memotong.
        app(MizuAiService::class)->ask([
            ['role' => 'user', 'text' => str_repeat('b', 900)],
        ]);

        Http::assertSent(function ($request) {
            $sent = $request->data()['contents'][0]['parts'][0]['text'] ?? '';

            return mb_strlen($sent) === MizuAiService::INPUT_LIMIT;
        });
    }

    public function test_blank_question_is_ignored(): void
    {
        $this->fakeGemini();

        Livewire::test(MizuChatWidget::class)
            ->set('question', '   ')
            ->call('ask')
            ->assertSet('messages', []);

        Http::assertNothingSent();
    }

    public function test_gemini_error_returns_friendly_message(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['error' => 'quota exceeded'], 429),
        ]);

        Livewire::test(MizuChatWidget::class)
            ->set('question', 'Halo')
            ->call('ask')
            ->assertSee('Layanan AI sedang sibuk');
    }

    public function test_empty_model_response_returns_friendly_message(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['candidates' => []]),
        ]);

        Livewire::test(MizuChatWidget::class)
            ->set('question', 'Halo')
            ->call('ask')
            ->assertSee('belum bisa menjawab');
    }

    public function test_service_reports_configured_state(): void
    {
        config()->set('services.gemini.key', 'test-key');
        $this->assertTrue(app(MizuAiService::class)->isConfigured());

        config()->set('services.gemini.key', '   ');
        $this->assertFalse(app(MizuAiService::class)->isConfigured());
    }
}