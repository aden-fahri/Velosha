<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HelpdeskTicket>
 */
class HelpdeskTicketFactory extends Factory
{
    protected $model = HelpdeskTicket::class;

    public function definition(): array
    {
        return [
            'reporter_id' => User::factory(),
            'operator_id' => null,
            'subject' => fake()->sentence(4),
            'description' => fake()->paragraph(3),
            'priority' => TicketPriority::LOW,
            'status' => TicketStatus::OPEN,
            'duplicate_of_id' => null,
            'is_reference' => false,
            'first_response_at' => null,
            'closed_at' => null,
            'resolution_time' => null,
            'response_due_at' => now()->addMinutes(240),
            'resolution_due_at' => now()->addMinutes(4320),
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::IN_PROGRESS,
            'operator_id' => User::factory()->create(['role' => User::ROLE_OPERATOR]),
            'first_response_at' => now(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::CLOSED,
            'operator_id' => User::factory()->create(['role' => User::ROLE_OPERATOR]),
            'first_response_at' => now()->subMinutes(60),
            'closed_at' => now(),
            'resolution_time' => 60,
        ]);
    }

    public function reference(?string $solution = 'Silakan periksa halaman terkait pada portal'): static
    {
        return $this->state(fn () => [
            'is_reference' => true,
            'solution_summary' => $solution,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::OPEN,
            'response_due_at' => now()->subMinutes(30),
            'resolution_due_at' => now()->subMinutes(10),
        ]);
    }
}
