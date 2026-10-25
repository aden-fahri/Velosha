<?php

namespace Database\Factories;

use App\Models\HelpdeskTicket;
use App\Models\TicketResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketResponse>
 */
class TicketResponseFactory extends Factory
{
    protected $model = TicketResponse::class;

    public function definition(): array
    {
        return [
            'ticket_id' => HelpdeskTicket::factory(),
            'responder_id' => User::factory(),
            'message' => fake()->paragraph(),
            'is_auto_reply' => false,
            'created_at' => now(),
        ];
    }

    public function autoReply(): static
    {
        return $this->state(fn () => [
            'is_auto_reply' => true,
        ]);
    }
}
