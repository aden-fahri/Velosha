<?php

namespace App\Support;

use App\Models\Event;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EventCardPresenter
{
    private const MONTHS = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    public static function card(Event $event): array
    {
        $categories = $event->categories;

        $maxElevation = $categories->max('elevation_gain_m');
        $maxCutOff = $categories->max('cut_off_hours');
        $maxDistance = $categories->max('distance_km');
        $lowestPrice = $categories->min('price');
        $totalQuota = (int) $categories->sum('quota');
        $remaining = (int) $categories->sum(fn (mixed $category): int => $category->remaining);

        return [
            'slug' => $event->slug,
            'type' => str_replace('_', '-', $event->type),
            'img' => static::image($event),
            'cat' => $event->type_label,
            'catClass' => static::typeClass($event->type),
            'dist' => static::distance($maxDistance),
            'date' => static::tanggal($event->event_date),
            'loc' => $event->location_name,
            'title' => $event->title,
            'excerpt' => Str::limit(strip_tags((string) $event->description), 150),
            'specs' => [
                'Elevasi' => $maxElevation === null ? '—' : '+'.static::angka((float) $maxElevation).' M',
                'Cut-Off' => $maxCutOff === null ? '—' : static::jam((float) $maxCutOff),
                'Kuota' => $totalQuota > 0 ? (string) $totalQuota : '—',
            ],
            'slots' => $categories->isEmpty() ? 'Buka' : ($remaining > 0 ? 'Slot tersisa '.$remaining : 'Penuh'),
            'slotsFull' => $categories->isNotEmpty() && $remaining === 0,
            'price' => $lowestPrice === null ? '—' : number_format((float) $lowestPrice, 0, ',', '.'),
            'url' => '/events/'.$event->slug,
        ];
    }

    public static function image(Event $event): string
    {
        $photo = $event->photos->first();

        if ($photo === null) {
            return '/images/hero-peloton.jpg';
        }

        return static::photoUrl($photo);
    }

    public static function photoUrl(?object $photo): string
    {
        if ($photo === null) {
            return '/images/hero-peloton.jpg';
        }

        if (Str::startsWith($photo->photo_path, ['http://', 'https://'])) {
            return $photo->photo_path;
        }

        return asset('storage/'.ltrim($photo->photo_path, '/'));
    }

    public static function typeClass(string $type): string
    {
        return match ($type) {
            Event::TYPE_CLIMB => 'bg-teal-800',
            Event::TYPE_FUN_RIDE => 'bg-teal-700',
            default => 'bg-race-600',
        };
    }

    public const DAYS = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu',
    ];

    public static function hari(mixed $date): string
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        return self::DAYS[$date->format('l')] ?? '';
    }

    public static function tanggalLengkap(mixed $date): string
    {
        return static::hari($date).', '.static::tanggal($date);
    }

    public static function tanggal(mixed $date): string
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        return ((int) $date->format('j')).' '.self::MONTHS[(int) $date->format('n')].' '.$date->format('Y');
    }

    public static function monthKey(mixed $date): string
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        return $date->format('Y-m');
    }

    public static function monthLabel(string $key): string
    {
        [$year, $month] = explode('-', $key);

        return self::MONTHS[(int) $month].' '.$year;
    }

    public static function distance(mixed $km): string
    {
        if ($km === null) {
            return '—';
        }

        $value = (float) $km;
        $formatted = $value == (int) $value
            ? (string) (int) $value
            : str_replace('.', ',', rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.'));

        return $formatted.' KM';
    }

    public static function jam(float $hours): string
    {
        $formatted = $hours == (int) $hours
            ? (string) (int) $hours
            : str_replace('.', ',', (string) $hours);

        return $formatted.' Jam';
    }

    public static function angka(float $value): string
    {
        return number_format($value, 0, ',', '.');
    }
}
