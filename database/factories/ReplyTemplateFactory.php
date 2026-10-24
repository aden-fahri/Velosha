<?php

namespace Database\Factories;

use App\Models\ReplyTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReplyTemplate>
 */
class ReplyTemplateFactory extends Factory
{
    protected $model = ReplyTemplate::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'body' => fake()->paragraph(),
            'keywords' => 'tiket, race pack, bib',
            'is_active' => true,
            'use_count' => 0,
        ];
    }
}
