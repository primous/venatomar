<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * A loan requester. Authenticates on the "applicant" guard with a one-time
 * magic link only: there is no password column.
 */
class Applicant extends Authenticatable
{
    protected $guarded = ['id'];

    protected $rememberTokenName = '';

    protected function casts(): array
    {
        return ['last_login_at' => 'datetime'];
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class)->latest('id');
    }

    public function deletionRequests(): HasMany
    {
        return $this->hasMany(DeletionRequest::class);
    }

    public function getAuthPassword(): string
    {
        return '';
    }
}
