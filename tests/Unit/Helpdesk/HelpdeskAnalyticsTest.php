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

    public function test_admin_handling_tickets_is_included_in_per_operator_analytics_with_exact_numbers(): void
    {
        $peserta = User::factory()->create(['role' => User::ROLE_PESERTA]);

        // Admin who handled tickets
        $adminHandler = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'name' => 'Admin Handler',
            'is_system' => false,
        ]);

        // Admin who never handled any ticket (must be excluded)
        $adminPure = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'name' => 'Admin Pure',
            'is_system' => false,
        ]);

        // Operator without tickets (always included)
        $operatorIdle = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'name' => 'Operator Idle',
            'is_system' => false,
        ]);

        // Operator with tickets (always included)
        $operatorActive = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'name' => 'Operator Active',
            'is_system' => false,
        ]);

        // Admin tickets: 30m and 50m -> avg 40m, both closed, ratings 5 and 3 -> avg 4.0
        $at1 = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $adminHandler->id,
            'created_at' => Carbon::parse('2026-10-25 10:00:00'),
            'first_response_at' => Carbon::parse('2026-10-25 10:30:00'),
        ]);
        SatisfactionRating::create(['ticket_id' => $at1->id, 'score' => 5, 'created_at' => now()]);

        $at2 = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $adminHandler->id,
            'created_at' => Carbon::parse('2026-10-25 10:00:00'),
            'first_response_at' => Carbon::parse('2026-10-25 10:50:00'),
        ]);
        SatisfactionRating::create(['ticket_id' => $at2->id, 'score' => 3, 'created_at' => now()]);

        // Operator ticket: 15m, closed, rating 4
        $ot1 = HelpdeskTicket::factory()->closed()->create([
            'reporter_id' => $peserta->id,
            'operator_id' => $operatorActive->id,
            'created_at' => Carbon::parse('2026-10-25 10:00:00'),
            'first_response_at' => Carbon::parse('2026-10-25 10:15:00'),
        ]);
        SatisfactionRating::create(['ticket_id' => $ot1->id, 'score' => 4, 'created_at' => now()]);

        // 1. Check average response times per operator
        $responseTimes = $this->analytics->getAverageResponseTimesPerOperator()->keyBy('operator_id');
        $this->assertTrue($responseTimes->has($adminHandler->id), 'Admin with handled tickets must be included in response times');
        $this->assertFalse($responseTimes->has($adminPure->id), 'Admin without handled tickets must NOT be included in response times');
        $this->assertTrue($responseTimes->has($operatorIdle->id), 'Operator without tickets must always be included');
        $this->assertTrue($responseTimes->has($operatorActive->id), 'Operator with tickets must always be included');

        $this->assertEquals(40.0, $responseTimes[$adminHandler->id]->avg_response_minutes);
        $this->assertSame('40m', $responseTimes[$adminHandler->id]->formatted_response_time);
        $this->assertSame(2, (int) $responseTimes[$adminHandler->id]->total_tickets);
        $this->assertSame(2, (int) $responseTimes[$adminHandler->id]->response_count);

        // 2. Check satisfaction ratings by operator
        $satisfaction = $this->analytics->getSatisfactionRatings();
        $byOperator = $satisfaction['by_operator']->keyBy('operator_id');
        $this->assertTrue($byOperator->has($adminHandler->id), 'Admin with handled tickets must be included in satisfaction');
        $this->assertFalse($byOperator->has($adminPure->id), 'Admin without handled tickets must NOT be included in satisfaction');
        $this->assertTrue($byOperator->has($operatorIdle->id), 'Operator without tickets must always be included');
        $this->assertTrue($byOperator->has($operatorActive->id), 'Operator with tickets must always be included');

        $adminSat = $byOperator[$adminHandler->id];
        $this->assertSame(2, (int) $adminSat->total_ratings);
        $this->assertSame(4.0, (float) $adminSat->avg_score);
        $this->assertSame('4.0', $adminSat->formatted_average);
        $this->assertSame([1 => 0, 2 => 0, 3 => 1, 4 => 0, 5 => 1], $adminSat->distribution);

        // 3. Check top operators ranking
        $topOps = $this->analytics->getTopOperators()->keyBy('operator_id');
        $this->assertTrue($topOps->has($adminHandler->id), 'Admin with handled tickets must be included in top operators');
        $this->assertFalse($topOps->has($adminPure->id), 'Admin without handled tickets must NOT be included in top operators');
        $this->assertTrue($topOps->has($operatorIdle->id), 'Operator without tickets must always be included');
        $this->assertTrue($topOps->has($operatorActive->id), 'Operator with tickets must always be included');

        $adminTop = $topOps[$adminHandler->id];
        $this->assertSame(2, (int) $adminTop->closed_count);
        $this->assertEquals(40.0, $adminTop->avg_response_minutes);
        $this->assertSame(4.0, (float) $adminTop->avg_rating);
        $this->assertSame('40m', $adminTop->formatted_response_time);
        $this->assertSame('4.0', $adminTop->formatted_rating);
    }
}
