<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'qr_code_data',
        'pdf_path',
        'is_checked_in',
        'checked_in_at',
        'checked_in_by_admin',
    ];

    protected $casts = [
        'is_checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * Terbitkan tiket QR untuk pendaftaran lunas.
     * Payload: VELOSHA-VERIFY-[HASH_SHA256]-[BIB] (tanpa data pribadi).
     */
    public static function issueFor(Registration $registration): self
    {
        $existing = static::where('registration_id', $registration->id)->first();
        if ($existing) {
            return $existing;
        }

        $hash = strtoupper(hash('sha256', uniqid('velosha-', true)));

        return static::create([
            'registration_id' => $registration->id,
            'qr_code_data' => sprintf('VELOSHA-VERIFY-%s-%s', $hash, $registration->bib_number),
        ]);
    }
}
