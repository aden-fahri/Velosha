<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExtraEventSeeder extends Seeder
{
    /**
     * 4 event tambahan agar kalender, katalog, dan grafik dashboard
     * lebih variabel (tipe & bulan berbeda-beda). Idempoten: dilewati
     * bila judulnya sudah ada, sehingga aman dijalankan berulang.
     */
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

        $this->ensureDemoPhotos();

        $events = [
            [
                'title' => 'Jakarta Cikini Night Criterium 2026',
                'location_name' => 'Jakarta Pusat, DKI Jakarta',
                'event_date' => '2026-10-25',
                'type' => Event::TYPE_FUN_RIDE,
                'user_id' => $owner->id,
                'description' => 'Kriterium kota berlampu neon di kawasan Cikini. Dua kategori jarak untuk pemula dan peloton cepat, start malam hari.',
                'categories' => [
                    ['name' => 'Fun Ride', 'code' => 'FR', 'distance_km' => 25.00, 'elevation_gain_m' => 120.00, 'price' => 175000.00, 'quota' => 400, 'registered_count' => 0, 'wave_start_time' => 'Wave 1 - 19:00 WIB', 'cut_off_hours' => 2.50],
                    ['name' => 'Medio Fondo', 'code' => 'MF', 'distance_km' => 55.00, 'elevation_gain_m' => 380.00, 'price' => 275000.00, 'quota' => 250, 'registered_count' => 0, 'wave_start_time' => 'Wave 2 - 19:30 WIB', 'cut_off_hours' => 3.50],
                ],
            ],
            [
                'title' => 'Merapi Sunset Fondo 2026',
                'location_name' => 'Sleman, D.I. Yogyakarta',
                'event_date' => '2026-12-06',
                'type' => Event::TYPE_MEDIO_FONDO,
                'user_id' => $owner->id,
                'description' => 'Pendakian lembut menuju lereng Merapi dengan pemandangan kabut pagi di Kaliurang dan turunan aspal mulus.',
                'categories' => [
                    ['name' => 'Medio Fondo', 'code' => 'MF', 'distance_km' => 80.00, 'elevation_gain_m' => 900.00, 'price' => 375000.00, 'quota' => 350, 'registered_count' => 0, 'wave_start_time' => 'Wave 1 - 06:00 WIB', 'cut_off_hours' => 5.00],
                    ['name' => 'Fun Ride', 'code' => 'FR', 'distance_km' => 35.00, 'elevation_gain_m' => 240.00, 'price' => 225000.00, 'quota' => 300, 'registered_count' => 0, 'wave_start_time' => 'Wave 2 - 06:30 WIB', 'cut_off_hours' => 3.00],
                    ['name' => 'Gran Fondo', 'code' => 'GF', 'distance_km' => 125.00, 'elevation_gain_m' => 1550.00, 'price' => 525000.00, 'quota' => 250, 'registered_count' => 0, 'wave_start_time' => 'Wave 3 - 05:30 WIB', 'cut_off_hours' => 7.00],
                ],
            ],
            [
                'title' => 'Makassar Losari Gran Fondo 2027',
                'location_name' => 'Makassar, Sulawesi Selatan',
                'event_date' => '2027-04-11',
                'type' => Event::TYPE_GRAN_FONDO,
                'user_id' => $owner->id,
                'description' => 'Grand depart dari Pantai Losari menuju pegunungan Bugis Raya. Angin laut pagi dan tanjakan panjang di kilometer 60.',
                'categories' => [
                    ['name' => 'Gran Fondo', 'code' => 'GF', 'distance_km' => 145.00, 'elevation_gain_m' => 1700.00, 'price' => 575000.00, 'quota' => 350, 'registered_count' => 0, 'wave_start_time' => 'Wave 1 - 05:30 WITA', 'cut_off_hours' => 7.50],
                    ['name' => 'Medio Fondo', 'code' => 'MF', 'distance_km' => 70.00, 'elevation_gain_m' => 620.00, 'price' => 350000.00, 'quota' => 300, 'registered_count' => 0, 'wave_start_time' => 'Wave 2 - 06:15 WITA', 'cut_off_hours' => 4.50],
                ],
            ],
            [
                'title' => 'Dieng Plateau KOM Challenge 2027',
                'location_name' => 'Wonosobo, Jawa Tengah',
                'event_date' => '2027-05-16',
                'type' => Event::TYPE_CLIMB,
                'user_id' => $owner->id,
                'description' => 'Tantangan murni tanjakan ke dataran tinggi Dieng. Udara tipis, jalan menanjak konsisten, dan KOM terverifikasi di empat pos.',
                'categories' => [
                    ['name' => 'KOM Climb', 'code' => 'KOM', 'distance_km' => 85.00, 'elevation_gain_m' => 2100.00, 'price' => 625000.00, 'quota' => 200, 'registered_count' => 0, 'wave_start_time' => 'Wave 1 - 05:45 WIB', 'cut_off_hours' => 6.00],
                    ['name' => 'Medio Fondo', 'code' => 'MF', 'distance_km' => 55.00, 'elevation_gain_m' => 980.00, 'price' => 340000.00, 'quota' => 250, 'registered_count' => 0, 'wave_start_time' => 'Wave 2 - 06:15 WIB', 'cut_off_hours' => 4.00],
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
