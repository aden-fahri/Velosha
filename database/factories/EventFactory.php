<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title = ucwords($this->faker->unique()->words(3, true)).' Fondo';

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'description' => $this->faker->paragraphs(3, true),
            'location_name' => $this->faker->randomElement([
                'Bandung, Jawa Barat',
                'Magelang, Jawa Tengah',
                'Probolinggo, Jawa Timur',
                'Badung, Bali',
                'Lombok Tengah, NTB',
                'Sleman, D.I. Yogyakarta',
            ]),
            'event_date' => $this->faker->dateTimeBetween('+2 weeks', '+8 months')->format('Y-m-d'),
            'start_time' => '05:45:00',
            'type' => $this->faker->randomElement([
                Event::TYPE_GRAN_FONDO,
                Event::TYPE_MEDIO_FONDO,
                Event::TYPE_FUN_RIDE,
                Event::TYPE_CLIMB,
            ]),
            'status' => Event::STATUS_PUBLISHED,
            'youtube_video_id' => null,
            'gpx_file_path' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['status' => Event::STATUS_DRAFT]);
    }
}
