<?php

namespace Tests\Feature\Security;

use App\Livewire\MizuChatWidget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MizuRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_mizu_rate_limit_message(): void
    {
        $component = Livewire::test(MizuChatWidget::class);

        for ($i = 0; $i < 11; $i++) {
            $component->set('question', 'Halo')->call('ask');
        }

        $component->assertSee('Terlalu banyak permintaan');
    }
}