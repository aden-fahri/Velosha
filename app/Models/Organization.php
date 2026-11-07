<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo_path',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(OrganizationMember::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(QuotaPurchase::class);
    }

    public function quotaLedger(): HasMany
    {
        return $this->hasMany(QuotaLedger::class);
    }

    /**
     * Hitung saldo kuota aktif dari akumulasi delta di ledger.
     */
    public function quotaBalance(): int
    {
        return (int) $this->quotaLedger()->sum('delta');
    }

    /**
     * Apakah organisasi memiliki kuota event yang bisa dipakai?
     */
    public function hasQuota(): bool
    {
        return $this->quotaBalance() > 0;
    }

    /**
     * Potong 1 kuota saat event ditayangkan (published).
     * Mencegah pemotongan ganda bila event sudah pernah dipotong kuotanya.
     */
    public function deductQuotaForEvent(Event $event): bool
    {
        $alreadyDeducted = $this->quotaLedger()
            ->where('event_id', $event->id)
            ->where('reason', 'publish_event')
            ->exists();

        if ($alreadyDeducted) {
            return true;
        }

        if (! $this->hasQuota()) {
            return false;
        }

        QuotaLedger::create([
            'organization_id' => $this->id,
            'delta' => -1,
            'reason' => 'publish_event',
            'event_id' => $event->id,
            'created_at' => now(),
        ]);

        return true;
    }
}

