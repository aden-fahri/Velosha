<?php

namespace Tests\Feature\Concurrency;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class BibConcurrencyTest extends TestCase
{
    use DatabaseMigrations;

    public function test_concurrent_payments(): void
    {
        $event = Event::factory()->create();
        $category = EventCategory::create([
            'event_id' => $event->id, 'name' => 'Gran Fondo', 'code' => 'GF',
            'distance_km' => 130, 'elevation_gain_m' => 1450, 'price' => 500000,
            'quota' => 5, 'registered_count' => 0, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 7,
        ]);

        $ids = [];
        for ($i = 0; $i < 20; $i++) {
            $user = User::factory()->create(['role' => 'peserta']);
            $reg = Registration::create([
                'user_id' => $user->id, 'event_category_id' => $category->id,
                'participant_name' => 'Rider '.$i, 'identity_number' => '123'.$i, 'birth_date' => '1998-08-24',
                'gender' => 'male', 'blood_type' => 'O', 'emergency_contact_name' => 'x',
                'emergency_contact_relation' => 'Istri / Suami', 'emergency_contact_phone' => '081',
                'medical_notes' => null, 'club' => null, 'status' => Registration::STATUS_PENDING,
            ]);
            $ids[] = $reg->id;
        }

        $processes = [];
        foreach ($ids as $id) {
            $processes[] = new Process(
                ['php', 'artisan', 'registration:complete', (string) $id],
                base_path(),
                [
                    'APP_ENV' => 'testing',
                    'APP_DEBUG' => 'false',
                    'DB_CONNECTION' => 'mysql',
                    'DB_DATABASE' => 'velosha_test',
                    'PAYMENT_SIMULATION' => 'true',
                ]
            );
        }

        foreach ($processes as $p) {
            $p->start();
        }
        foreach ($processes as $p) {
            $p->wait();
        }

        $paid = Registration::where('status', Registration::STATUS_PAID)->count();
        $this->assertSame(5, $paid);
        $this->assertSame(5, $category->fresh()->registered_count);

        $bibs = Registration::whereNotNull('bib_number')->pluck('bib_number');
        $this->assertCount(count($bibs->unique()), $bibs);

        // cleanup manual agar tidak mencemari tes lain
        \Illuminate\Support\Facades\DB::table('registrations')->delete();
        \Illuminate\Support\Facades\DB::table('event_categories')->delete();
        \Illuminate\Support\Facades\DB::table('events')->delete();
        \Illuminate\Support\Facades\DB::table('users')->delete();
        \Illuminate\Support\Facades\DB::table('bib_counters')->delete();
    }
}