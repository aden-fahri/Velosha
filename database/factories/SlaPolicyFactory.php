<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Models\SlaPolicy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SlaPolicy>
 */
class SlaPolicyFactory extends Factory
{
    protected $model = SlaPolicy::class;

    public function definition(): array
    {
        return [
            'priority' => TicketPriority::LOW,
            'response_minutes' => 240,
            'resolution_minutes' => 4320,
        ];
    }
}
