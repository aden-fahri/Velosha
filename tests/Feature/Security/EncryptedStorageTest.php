<?php

namespace Tests\Feature\Security;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EncryptedStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_nik_stored_encrypted(): void
    {
        $user = User::factory()->create(['role' => 'peserta']);
        $event = \App\Models\Event::factory()->create();
        $category = \App\Models\EventCategory::create([
            'event_id' => $event->id, 'name' => 'Gran Fondo', 'code' => 'GF',
            'distance_km' => 130, 'elevation_gain_m' => 1450, 'price' => 500000,
            'quota' => 10, 'registered_count' => 0, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 7,
        ]);
        $plain = '3308012408980002';
        $reg = Registration::create([
            'user_id' => $user->id, 'event_category_id' => $category->id,
            'participant_name' => 'Arif', 'identity_number' => $plain, 'birth_date' => '1998-08-24',
            'gender' => 'male', 'blood_type' => 'O', 'emergency_contact_name' => 'x',
            'emergency_contact_relation' => 'Istri / Suami', 'emergency_contact_phone' => '081',
            'medical_notes' => null, 'club' => null, 'status' => Registration::STATUS_PENDING,
        ]);

        $raw = DB::table('registrations')->where('id', $reg->id)->value('identity_number');
        $this->assertNotSame($plain, $raw);
        $this->assertSame($plain, Crypt::decryptString($raw));
        $this->assertSame($plain, $reg->fresh()->identity_number);
    }
}