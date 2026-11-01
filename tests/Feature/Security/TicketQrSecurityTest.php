<?php

namespace Tests\Feature\Security;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Registration;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketQrSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function createCategory(Event $event): EventCategory
    {
        return EventCategory::create([
            'event_id' => $event->id,
            'name' => 'Category Test',
            'code' => 'C' . rand(100, 999),
            'distance_km' => 50,
            'elevation_gain_m' => 500,
            'price' => 100000,
            'quota' => 100,
            'registered_count' => 0,
            'wave_start_time' => 'Wave 1 - 06:00 WIB',
            'cut_off_hours' => 5.0,
        ]);
    }

    protected function createRegistration(User $user, EventCategory $category, string $bibNumber): Registration
    {
        return Registration::create([
            'user_id' => $user->id,
            'event_category_id' => $category->id,
            'participant_name' => 'Test Rider ' . $bibNumber,
            'identity_number' => '3301010101900001',
            'birth_date' => '1995-05-15',
            'gender' => 'male',
            'blood_type' => 'O',
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_relation' => 'Family',
            'emergency_contact_phone' => '081234567890',
            'medical_notes' => null,
            'club' => 'Test Club',
            'status' => Registration::STATUS_PAID,
            'bib_number' => $bibNumber,
        ]);
    }

    public function test_ticket_issue_for_uses_cryptographically_secure_hash(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $category = $this->createCategory($event);

        $registration = $this->createRegistration($user, $category, 'BIB-101');
        $ticket = Ticket::issueFor($registration);

        $this->assertNotNull($ticket);
        $this->assertNotEmpty($ticket->qr_code_data);
        // Pattern: VELOSHA-VERIFY-[64 HEX CHARS]-[BIB]
        $this->assertMatchesRegularExpression(
            '/^VELOSHA-VERIFY-[A-F0-9]{64}-BIB-101$/',
            $ticket->qr_code_data
        );
    }

    public function test_ticket_qr_generation_produces_no_collisions_across_multiple_runs(): void
    {
        $event = Event::factory()->create();
        $category = $this->createCategory($event);

        $generatedTokens = [];
        for ($i = 1; $i <= 50; $i++) {
            $user = User::factory()->create();
            $reg = $this->createRegistration($user, $category, "BIB-{$i}");
            $ticket = Ticket::issueFor($reg);
            $this->assertArrayNotHasKey($ticket->qr_code_data, $generatedTokens);
            $generatedTokens[$ticket->qr_code_data] = true;
        }

        $this->assertCount(50, $generatedTokens);
    }

    public function test_qr_code_data_column_enforces_unique_constraint(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $event = Event::factory()->create();
        $category = $this->createCategory($event);

        $regA = $this->createRegistration($userA, $category, 'BIB-A');
        $regB = $this->createRegistration($userB, $category, 'BIB-B');

        $ticketA = Ticket::issueFor($regA);

        $this->expectException(QueryException::class);

        // Attempt to insert duplicate qr_code_data
        Ticket::create([
            'registration_id' => $regB->id,
            'qr_code_data' => $ticketA->qr_code_data,
        ]);
    }
}
