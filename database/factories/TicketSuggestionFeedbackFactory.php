<?php

namespace Database\Factories;

use App\Models\HelpdeskTicket;
use App\Models\TicketSuggestionFeedback;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketSuggestionFeedback>
 */
class TicketSuggestionFeedbackFactory extends Factory
{
    protected $model = TicketSuggestionFeedback::class;

    public function definition(): array
    {
        return [
            'ticket_id' => HelpdeskTicket::factory(),
            'reporter_id' => User::factory(),
            'helpful' => true,
            'created_at' => now(),
        ];
    }
}
