<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * One-time login token. Only the SHA-256 hash is stored; the clear token only
 * exists in the e-mailed (signed) URL.
 */
class MagicLoginToken extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'used_at' => 'datetime'];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /** @return array{0: self, 1: string} model + clear token */
    public static function issue(Applicant $applicant): array
    {
        $token = Str::random(64);
        $model = self::create([
            'applicant_id' => $applicant->id,
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addMinutes((int) config('venatomar.magic_link_ttl_minutes')),
        ]);

        return [$model, $token];
    }

    public static function findValid(string $token): ?self
    {
        $model = self::where('token_hash', hash('sha256', $token))->first();

        if ($model === null || $model->used_at !== null || $model->expires_at->isPast()) {
            return null;
        }

        return $model;
    }

    /** Atomically consume the token; false if it was already used concurrently. */
    public function consume(): bool
    {
        return self::whereKey($this->id)->whereNull('used_at')->update(['used_at' => now()]) === 1;
    }
}
