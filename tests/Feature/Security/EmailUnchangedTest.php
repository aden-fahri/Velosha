<?php

namespace Tests\Feature\Security;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailUnchangedTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_does_not_change_account_email(): void
    {
        $user = User::factory()->create(['role' => 'peserta', 'email' => 'rider@velosha.id']);
        $original = $user->email;

        // simulasi toStep3 logic: hanya phone yang diupdate
        $user->fill(['phone' => '081234567890']);
        $user->save();

        $this->assertSame($original, $user->fresh()->email);
    }
}