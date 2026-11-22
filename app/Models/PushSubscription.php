<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Minishlink\WebPush\Subscription as MinishlinkSubscription;

class PushSubscription extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the owning subscribable model (BankClient, User, Lead, etc.).
     */
    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Convert to Minishlink WebPush Subscription instance.
     */
    public function toMinishlinkSubscription(): MinishlinkSubscription
    {
        return MinishlinkSubscription::create([
            'endpoint' => $this->endpoint,
            'publicKey' => $this->public_key,
            'authToken' => $this->auth_token,
            'contentEncoding' => $this->content_encoding ?: 'aes128gcm',
        ]);
    }

    /**
     * Create or update a push subscription by endpoint hash.
     */
    public static function storeOrUpdate(array $attributes): self
    {
        $endpoint = $attributes['endpoint'];
        $hash = hash('sha256', $endpoint);

        return static::updateOrCreate(
            ['endpoint_hash' => $hash],
            [
                'endpoint' => $endpoint,
                'public_key' => $attributes['public_key'],
                'auth_token' => $attributes['auth_token'],
                'content_encoding' => $attributes['content_encoding'] ?? 'aes128gcm',
                'email' => $attributes['email'] ?? null,
                'subscribable_type' => $attributes['subscribable_type'] ?? null,
                'subscribable_id' => $attributes['subscribable_id'] ?? null,
                'user_agent' => $attributes['user_agent'] ?? null,
            ]
        );
    }
}
