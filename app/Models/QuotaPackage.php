<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuotaPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'events_count',
        'price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'events_count' => 'integer',
        'price' => 'integer',
        'is_active' => 'boolean',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(QuotaPurchase::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

