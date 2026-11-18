<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeletionRequest extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['processed_at' => 'datetime'];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
