<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** Source used by the local-only demo seeder. Never shown outside APP_ENV=local. */
    public const DEMO_SOURCE = 'demo';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'date',
            'is_published' => 'boolean',
            'is_verified' => 'boolean',
            'rating' => 'integer',
        ];
    }

    public function scopePublished(Builder $query, ?string $locale = null): Builder
    {
        return $query->where('is_published', true)
            ->when($locale, fn ($q) => $q->where('locale', $locale))
            ->when(! app()->environment('local'), fn ($q) => $q->where(
                fn ($q) => $q->whereNull('source')->orWhere('source', '!=', self::DEMO_SOURCE)
            ))
            ->orderByDesc('reviewed_at')->orderByDesc('id');
    }

    /** Real average and count, computed from the published rows (never typed by hand). */
    public static function summary(string $locale): ?array
    {
        $row = static::published($locale)->reorder()
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')->first();

        return $row && (int) $row->total > 0
            ? ['rating' => round((float) $row->avg_rating, 1), 'count' => (int) $row->total]
            : null;
    }

    public function initial(): string
    {
        return mb_strtoupper(mb_substr(trim($this->author_name), 0, 1));
    }
}
