<?php

namespace Database\Factories;

use App\Models\HelpdeskTicket;
use App\Models\SatisfactionRating;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SatisfactionRating>
 */
class SatisfactionRatingFactory extends Factory
{
    protected $model = SatisfactionRating::class;

    public function definition(): array
    {
        return [
            'ticket_id' => HelpdeskTicket::factory()->closed(),
            'score' => fake()->numberBetween(1, 5),
            'feedback' => fake()->sentence(),
            'created_at' => now(),
        ];
    }
}
