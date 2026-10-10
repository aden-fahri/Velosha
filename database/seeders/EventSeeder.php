<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    private const DEMO_PHOTOS = [
        'images/hero-peloton.jpg',
        'images/auth-cyclist.jpg',
        'images/events/kartu-1.jpg',
        'images/events/kartu-2.jpg',
        'images/events/kartu-3.jpg',
        'images/events/kartu-4.jpg',
        'images/events/kartu-5.jpg',
        'images/events/kartu-6.jpg',
        'images/events/kartu-7.jpg',
        'images/events/kartu-8.jpg',
    ];

    public function run(): void
    {
        $owner = User::where('email', 'penyelenggara@velosha.id')->first()
            ?? User::where('role', User::ROLE_PENYELENGGARA)->first()
            ?? User::firstOrFail();

        $admin = User::where('email', 'admin@velosha.id')->first() ?? $owner;

        $this->ensureDemoPhotos();

        $events = [
            [
                'title' => 'Velosha Gran Fondo Borobudur 2026',
                'location_name' => 'Magelang, Jawa Tengah',
                'event_date' => '2026-11-15',
                'type' => Event::TYPE_GRAN_FONDO,
                'user_id' => $owner->id,
                'description' => 'Grand depart dari pelataran Candi Borobudur saat fajar. Rute perbukitan Menoreh, jembatan gantung Kali Progo, dan KOM Summit di KM 98.',
                'categories' => [
                    ['name' => 'Gran Fondo', 'code' => 'GF', 'distance_km' => 130.00, 'elevation_gain_m' => 1450.00, 'price' => 500000.00, 'quota' => 500, 'registered_count' => 452, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 7.00],
                    ['name' => 'Medio Fondo', 'code' => 'MF', 'distance_km' => 75.00, 'elevation_gain_m' => 680.00, 'price' => 350000.00, 'quota' => 400, 'registered_count' => 210, 'wave_start_time' => 'Wave 2 - 06:15 WIB', 'cut_off_hours' => 4.50],
                    ['name' => 'Fun Ride', 'code' => 'FR', 'distance_km' => 30.00, 'elevation_gain_m' => 250.00, 'price' => 200000.00, 'quota' => 300, 'registered_count' => 88, 'wave_start_time' => 'Wave 3 - 06:45 WIB', 'cut_off_hours' => 3.00],
                ],
            ],
            [
                'title' => 'Bromo Summit Challenge KOM 2026',
                'location_name' => 'Probolinggo, Jawa Timur',
                'event_date' => '2026-11-29',
                'type' => Event::TYPE_CLIMB,
                'user_id' => $owner->id,
                'description' => 'Tantangan murni tanjakan menuju kawasan Tengger. Total elevasi +2.480m dengan KOM terverifikasi di tiga pos.',
                'categories' => [
                    ['name' => 'KOM Climb', 'code' => 'KOM', 'distance_km' => 95.00, 'elevation_gain_m' => 2480.00, 'price' => 650000.00, 'quota' => 250, 'registered_count' => 187, 'wave_start_time' => 'Wave 1 - 05:30 WIB', 'cut_off_hours' => 6.00],
                    ['name' => 'Medio Fondo', 'code' => 'MF', 'distance_km' => 60.00, 'elevation_gain_m' => 1100.00, 'price' => 400000.00, 'quota' => 300, 'registered_count' => 96, 'wave_start_time' => 'Wave 2 - 06:15 WIB', 'cut_off_hours' => 4.50],
                ],
            ],
            [
                'title' => 'Bali Sunrise Coastal Fondo 2026',
                'location_name' => 'Badung, Bali',
                'event_date' => '2026-12-13',
                'type' => Event::TYPE_MEDIO_FONDO,
                'user_id' => $owner->id,
                'description' => 'Rute tepi pantai dari Kuta menuju Bukit Peninsula. Angin sepoi dan pemandangan sunrise Uluwatu di kilometer akhir.',
                'categories' => [
                    ['name' => 'Medio Fondo', 'code' => 'MF', 'distance_km' => 75.00, 'elevation_gain_m' => 720.00, 'price' => 400000.00, 'quota' => 350, 'registered_count' => 143, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 4.50],
                    ['name' => 'Fun Ride', 'code' => 'FR', 'distance_km' => 35.00, 'elevation_gain_m' => 280.00, 'price' => 250000.00, 'quota' => 250, 'registered_count' => 61, 'wave_start_time' => 'Wave 2 - 06:30 WIB', 'cut_off_hours' => 3.00],
                ],
            ],
            [
                'title' => 'Lombok Mandalika Gran Fondo 2026',
                'location_name' => 'Lombok Tengah, NTB',
                'event_date' => '2027-01-17',
                'type' => Event::TYPE_GRAN_FONDO,
                'user_id' => $owner->id,
                'description' => 'Loop penuh mengelilingi kawasan Mandalika International Street Circuit dengan kombinasi tanjakan pegunungan dan jalan lurus pantai.',
                'categories' => [
                    ['name' => 'Gran Fondo', 'code' => 'GF', 'distance_km' => 140.00, 'elevation_gain_m' => 1600.00, 'price' => 550000.00, 'quota' => 400, 'registered_count' => 176, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 7.00],
                    ['name' => 'Medio Fondo', 'code' => 'MF', 'distance_km' => 80.00, 'elevation_gain_m' => 750.00, 'price' => 380000.00, 'quota' => 350, 'registered_count' => 102, 'wave_start_time' => 'Wave 2 - 06:15 WIB', 'cut_off_hours' => 4.50],
                ],
            ],
            [
                'title' => 'Bandung Highland Classic 2026',
                'location_name' => 'Bandung, Jawa Barat',
                'event_date' => '2027-02-14',
                'type' => Event::TYPE_GRAN_FONDO,
                'user_id' => $admin->id,
                'description' => 'Rute dataran tinggi Lembang dengan tanjakan ikonik Dago Gunung dan turunan cepat menuju Padalarang.',
                'categories' => [
                    ['name' => 'Gran Fondo', 'code' => 'GF', 'distance_km' => 120.00, 'elevation_gain_m' => 1350.00, 'price' => 480000.00, 'quota' => 450, 'registered_count' => 301, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 7.00],
                    ['name' => 'Medio Fondo', 'code' => 'MF', 'distance_km' => 70.00, 'elevation_gain_m' => 650.00, 'price' => 350000.00, 'quota' => 350, 'registered_count' => 154, 'wave_start_time' => 'Wave 2 - 06:15 WIB', 'cut_off_hours' => 4.50],
                    ['name' => 'Fun Ride', 'code' => 'FR', 'distance_km' => 25.00, 'elevation_gain_m' => 180.00, 'price' => 200000.00, 'quota' => 300, 'registered_count' => 47, 'wave_start_time' => 'Wave 3 - 06:45 WIB', 'cut_off_hours' => 3.00],
                ],
            ],
            [
                'title' => 'Jogja Royal Heritage Fondo 2026',
                'location_name' => 'Sleman, D.I. Yogyakarta',
                'event_date' => '2027-03-21',
                'type' => Event::TYPE_FUN_RIDE,
                'user_id' => $owner->id,
                'description' => 'Rute keluarga santai melintasi Candi Prambanan, Keraton, dan alun-alun. Cocok untuk ride pertama dan peserta usia 15+.',
                'categories' => [
                    ['name' => 'Fun Ride', 'code' => 'FR', 'distance_km' => 40.00, 'elevation_gain_m' => 300.00, 'price' => 250000.00, 'quota' => 600, 'registered_count' => 233, 'wave_start_time' => 'Wave 1 - 06:00 WIB', 'cut_off_hours' => 3.00],
                    ['name' => 'Medio Fondo', 'code' => 'MF', 'distance_km' => 65.00, 'elevation_gain_m' => 520.00, 'price' => 320000.00, 'quota' => 300, 'registered_count' => 74, 'wave_start_time' => 'Wave 2 - 06:30 WIB', 'cut_off_hours' => 4.50],
                ],
            ],
        ];

        foreach ($events as $index => $data) {
            $slug = Str::slug($data['title']);

            if (Event::where('slug', $slug)->exists()) {
                continue;
            }

            $categories = $data['categories'];
            unset($data['categories']);

            $event = Event::create($data + [
                'slug' => $slug,
                'status' => Event::STATUS_PUBLISHED,
                'start_time' => '05:45:00',
                'youtube_video_id' => null,
                'gpx_file_path' => null,
            ]);

            foreach ($categories as $category) {
                $event->categories()->create($category);
            }

            $offset = $index % count(self::DEMO_PHOTOS);
            $rotated = array_merge(
                array_slice(self::DEMO_PHOTOS, $offset),
                array_slice(self::DEMO_PHOTOS, 0, $offset),
            );

            foreach (array_slice($rotated, 0, 5) as $order => $photo) {
                $event->photos()->create([
                    'photo_path' => $photo,
                    'sort_order' => $order,
                ]);
            }
        }
    }

    private function ensureDemoPhotos(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $disk = Storage::disk('public');

        foreach (self::DEMO_PHOTOS as $photo) {
            if ($disk->exists($photo)) {
                continue;
            }

            $source = base_path('public/'.$photo);

            if (is_file($source)) {
                $disk->put($photo, file_get_contents($source));
            }
        }
    }
}
