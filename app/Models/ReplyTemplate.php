<?php

namespace App\Models;

use App\Enums\Helpdesk\TicketCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model template balasan cepat helpdesk.
 *
 * @property int $id
 * @property TicketCategory $category
 * @property string $title
 * @property string $body
 * @property string|null $keywords
 * @property array<string>|null $keyword_triggers
 * @property bool $is_active
 * @property int $use_count
 */
class ReplyTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'title',
        'body',
        'keywords',
        'keyword_triggers',
        'is_active',
        'use_count',
    ];

    protected $casts = [
        'category' => TicketCategory::class,
        'keyword_triggers' => 'array',
        'is_active' => 'boolean',
        'use_count' => 'integer',
    ];

    /**
     * Mengambil daftar kata kunci pemicu pencocokan template.
     *
     * @return array<int, string>
     */
    public function getKeywordList(): array
    {
        if (is_array($this->keyword_triggers) && count($this->keyword_triggers) > 0) {
            return array_map('trim', $this->keyword_triggers);
        }

        if (! empty($this->keywords)) {
            return array_filter(array_map('trim', explode(',', $this->keywords)));
        }

        return [];
    }
}
