<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EncryptCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_encrypt_command_idempotent(): void
    {
        DB::table('registrations')->insert([
            'user_id' => \App\Models\User::factory()->create()->id,
            'event_category_id' => \App\Models\EventCategory::create([
                'event_id' => \App\Models\Event::factory()->create()->id,
                'name' => 'Gran Fondo', 'code' => 'GF', 'distance_km' => 130,
                'elevation_gain_m' => 1450, 'price' => 500000, 'quota' => 10,
                'registered_count' => 0, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 7,
            ])->id,
            'participant_name' => 'Arif',
            'identity_number' => '3308012408980002',
            'birth_date' => '1998-08-24',
            'gender' => 'male',
            'blood_type' => 'O',
            'emergency_contact_name' => 'x',
            'emergency_contact_relation' => 'Istri / Suami',
            'emergency_contact_phone' => '081',
            'medical_notes' => null,
            'club' => null,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('registrations:encrypt-sensitive')->assertExitCode(0);
        $first = DB::table('registrations')->value('identity_number');

        $this->artisan('registrations:encrypt-sensitive')->assertExitCode(0);
        $second = DB::table('registrations')->value('identity_number');

        $this->assertSame($first, $second);
        $this->assertSame('3308012408980002', Crypt::decryptString($second));
    }
}