<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplyTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'keywords',
        'is_active',
        'use_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'use_count' => 'integer',
    ];
}
