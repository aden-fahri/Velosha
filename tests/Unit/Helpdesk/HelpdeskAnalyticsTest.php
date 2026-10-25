<?php

namespace Tests\Unit\Helpdesk;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\SatisfactionRating;
use App\Models\User;
use App\Services\Helpdesk\HelpdeskAnalytics;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpdeskAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected HelpdeskAnalytics $analytics;

    protected function setUp(): void
    {
        parent::setUp();
        $this->analytics = new HelpdeskAnalytics();
    }

    public function test_kpi_metrics_calculation(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        HelpdeskTicket::factory()->create(['reporter_id' => $peserta->id, 'status' => TicketStatus::OPEN]);
        HelpdeskTicket::factory()->create(['reporter_id' => $peserta->id, 'status' => TicketStatus::OPEN]);
        HelpdeskTicket::factory()->create(['reporter_id' => $peserta->id, 'status' => TicketStatus::IN_PROGRESS]);
        HelpdeskTicket::factory()->create(['reporter_id' => $peserta->id, 'status' => TicketStatus::CLOSED]);

        $kpi = $this->analytics->getKpiMetrics();

        $this->assertSame(4, $kpi['total']);
        $this->assertSame(2, $kpi['open']);
        $this->assertSame(1, $kpi['in_progress']);
        $this->assertSame(1, $kpi['closed']);
    }

    public function test_average_response_time_per_operator_exact_numbers(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        // Operator A: 10m & 20m -> avg 15m
        $opA = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'name' => 'Operator A',
            'is_system' => false,
        ]);

        HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $opA->id,
            'created_at' => Carbon::parse('2026-10-25 10:00:00'),
            'first_response_at' => Carbon::parse('2026-10-25 10:10:00'), // 10 min
        ]);

        HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $opA->id,
            'created_at' => Carbon::parse('2026-10-25 10:00:00'),
            'first_response_at' => Carbon::parse('2026-10-25 10:20:00'), // 20 min
        ]);

        // Ticket without human response (auto-reply only, first_response_at remains null)
        HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $opA->id,
            'created_at' => Carbon::parse('2026-10-25 10:00:00'),
            'first_response_at' => null,
        ]);

        // Operator B: 150m -> >= 120m formatted as 2.5h
        $opB = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'name' => 'Operator B',
            'is_system' => false,
        ]);

        HelpdeskTicket::factory()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $opB->id,
            'created_at' => Carbon::parse('2026-10-25 08:00:00'),
            'first_response_at' => Carbon::parse('2026-10-25 10:30:00'), // 150 min = 2.5h
        ]);

        // Operator C: No response data -> '-'
        $opC = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'name' => 'Operator C',
            'is_system' => false,
        ]);

        $results = $this->analytics->getAverageResponseTimesPerOperator()->keyBy('operator_id');

        // Assert Operator A
        $this->assertEquals(15.0, $results[$opA->id]->avg_response_minutes);
        $this->assertSame('15m', $results[$opA->id]->formatted_response_time);

        // Assert Operator B
        $this->assertEquals(150.0, $results[$opB->id]->avg_response_minutes);
        $this->assertSame('2.5h', $results[$opB->id]->formatted_response_time);

        // Assert Operator C
        $this->assertNull($results[$opC->id]->avg_response_minutes);
        $this->assertSame('-', $results[$opC->id]->formatted_response_time);
    }

    public function test_satisfaction_ratings_exact_numbers_and_distribution(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $operator = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'name' => 'Operator Star',
            'is_system' => false,
        ]);

        // Create 3 tickets for Operator Star with ratings 5, 4, 3 -> avg 4.0
        $t1 = HelpdeskTicket::factory()->closed()->create(['reporter_id' => $peserta->id, 'operator_id' => $operator->id]);
        $t2 = HelpdeskTicket::factory()->closed()->create(['reporter_id' => $peserta->id, 'operator_id' => $operator->id]);
        $t3 = HelpdeskTicket::factory()->closed()->create(['reporter_id' => $peserta->id, 'operator_id' => $operator->id]);

        SatisfactionRating::create(['ticket_id' => $t1->id, 'score' => 5, 'created_at' => now()]);
        SatisfactionRating::create(['ticket_id' => $t2->id, 'score' => 4, 'created_at' => now()]);
        SatisfactionRating::create(['ticket_id' => $t3->id, 'score' => 3, 'created_at' => now()]);

        $satisfaction = $this->analytics->getSatisfactionRatings();

        // 1. Overall
        $this->assertSame(3, $satisfaction['overall']['total']);
        $this->assertSame(4.0, $satisfaction['overall']['average']);
        $this->assertSame('4.0', $satisfaction['overall']['formatted_average']);
        $this->assertSame([1 => 0, 2 => 0, 3 => 1, 4 => 1, 5 => 1], $satisfaction['overall']['distribution']);

        // 2. By Operator
        $opData = $satisfaction['by_operator']->firstWhere('operator_id', $operator->id);
        $this->assertNotNull($opData);
        $this->assertSame(3, $opData->total_ratings);
        $this->assertSame(4.0, $opData->avg_score);
        $this->assertSame('4.0', $opData->formatted_average);
        $this->assertSame([1 => 0, 2 => 0, 3 => 1, 4 => 1, 5 => 1], $opData->distribution);
    }

    public function test_is_system_users_are_strictly_excluded_from_analytics(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        // Human operator
        $human = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'name' => 'Human Operator',
            'is_system' => false,
        ]);

        // System user (bot)
        $systemUser = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'name' => 'System Bot',
            'is_system' => true,
        ]);

        // Ticket handled by system user
        $botTicket = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $systemUser->id,
            'created_at' => Carbon::parse('2026-10-25 10:00:00'),
            'first_response_at' => Carbon::parse('2026-10-25 10:01:00'),
        ]);
        SatisfactionRating::create(['ticket_id' => $botTicket->id, 'score' => 1, 'created_at' => now()]);

        // Ticket handled by human
        $humanTicket = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $human->id,
            'created_at' => Carbon::parse('2026-10-25 10:00:00'),
            'first_response_at' => Carbon::parse('2026-10-25 10:20:00'),
        ]);
        SatisfactionRating::create(['ticket_id' => $humanTicket->id, 'score' => 5, 'created_at' => now()]);

        // Verify response times exclude system bot
        $responseTimes = $this->analytics->getAverageResponseTimesPerOperator();
        $this->assertFalse($responseTimes->contains('operator_id', $systemUser->id));
        $this->assertTrue($responseTimes->contains('operator_id', $human->id));

        // Verify satisfaction per operator excludes system bot
        $satisfaction = $this->analytics->getSatisfactionRatings();
        $this->assertFalse($satisfaction['by_operator']->contains('operator_id', $systemUser->id));
        $this->assertTrue($satisfaction['by_operator']->contains('operator_id', $human->id));

        // Overall rating excludes system ticket
        $this->assertSame(1, $satisfaction['overall']['total']);
        $this->assertSame(5.0, $satisfaction['overall']['average']);

        // Verify top operators excludes system bot
        $topOps = $this->analytics->getTopOperators();
        $this->assertFalse($topOps->contains('operator_id', $systemUser->id));
        $this->assertTrue($topOps->contains('operator_id', $human->id));
    }

    public function test_top_operators_ranking(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $topOp = User::factory()->create(['role' => User::ROLE_OPERATOR, 'name' => 'Top Operator', 'is_system' => false]);
        $secondOp = User::factory()->create(['role' => User::ROLE_OPERATOR, 'name' => 'Second Operator', 'is_system' => false]);

        // TopOp closes 2 tickets
        HelpdeskTicket::factory()->closed()->create(['reporter_id' => $peserta->id, 'operator_id' => $topOp->id]);
        HelpdeskTicket::factory()->closed()->create(['reporter_id' => $peserta->id, 'operator_id' => $topOp->id]);

        // SecondOp closes 1 ticket
        HelpdeskTicket::factory()->closed()->create(['reporter_id' => $peserta->id, 'operator_id' => $secondOp->id]);

        $topOps = $this->analytics->getTopOperators();

        $this->assertSame($topOp->id, $topOps->first()->operator_id);
        $this->assertSame(2, $topOps->first()->closed_count);

        $this->assertSame($secondOp->id, $topOps->skip(1)->first()->operator_id);
        $this->assertSame(1, $topOps->skip(1)->first()->closed_count);
    }
}
