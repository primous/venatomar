<?php

namespace App\Services;

use App\Models\BankClient;
use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected ?WebPush $webPush = null;

    /**
     * Get or initialize the WebPush client.
     */
    public function getWebPush(): ?WebPush
    {
        if ($this->webPush !== null) {
            return $this->webPush;
        }

        $subject = config('webpush.vapid.subject');
        $publicKey = config('webpush.vapid.public_key');
        $privateKey = config('webpush.vapid.private_key');

        if (empty($subject) || empty($publicKey) || empty($privateKey)) {
            Log::warning('[WebPush] VAPID configuration missing or incomplete.');
            return null;
        }

        try {
            $auth = [
                'VAPID' => [
                    'subject' => $subject,
                    'publicKey' => $publicKey,
                    'privateKey' => $privateKey,
                ],
            ];

            $defaultOptions = [
                'TTL' => (int) config('webpush.defaults.ttl', 2419200),
            ];

            $this->webPush = new WebPush($auth, $defaultOptions);
            return $this->webPush;
        } catch (\Throwable $e) {
            Log::error('[WebPush] Failed to initialize WebPush client: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the VAPID public key for frontend subscriptions.
     */
    public function getVapidPublicKey(): string
    {
        return (string) config('webpush.vapid.public_key', '');
    }

    /**
     * Build the standard push notification payload array.
     */
    public function buildPayload(string $title, string $body, array $options = []): array
    {
        return [
            'title' => $title,
            'body' => $body,
            'icon' => $options['icon'] ?? config('webpush.defaults.icon', '/images/icons/icon-192x192.png'),
            'badge' => $options['badge'] ?? config('webpush.defaults.badge', '/images/icons/badge-72x72.png'),
            'url' => $options['url'] ?? '/es/banca',
            'tag' => $options['tag'] ?? ('vtm-' . uniqid()),
            'data' => array_merge([
                'url' => $options['url'] ?? '/es/banca',
                'timestamp' => now()->toISOString(),
            ], $options['data'] ?? []),
        ];
    }

    /**
     * Send a notification to a specific push subscription.
     */
    public function sendToSubscription(PushSubscription $subscription, array $payload): bool
    {
        $webPush = $this->getWebPush();
        if (!$webPush) {
            return false;
        }

        try {
            $sub = $subscription->toMinishlinkSubscription();
            $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $report = $webPush->sendOneNotification($sub, $jsonPayload);

            if ($report->isSuccess()) {
                return true;
            }

            if ($report->isSubscriptionExpired()) {
                Log::info("[WebPush] Subscription expired ({$subscription->endpoint_hash}), deleting.");
                $subscription->delete();
                return false;
            }

            $response = $report->getResponse();
            $statusCode = $response ? $response->getStatusCode() : null;

            if (in_array($statusCode, [404, 410], true)) {
                Log::info("[WebPush] Subscription returned {$statusCode}, deleting.");
                $subscription->delete();
                return false;
            }

            Log::warning("[WebPush] Notification delivery failed: {$report->getReason()} (status: {$statusCode})");
            return false;
        } catch (\Throwable $e) {
            Log::error("[WebPush] Error sending push to subscription {$subscription->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a push notification to a collection of PushSubscription models.
     */
    public function sendToSubscriptions($subscriptions, string $title, string $body, array $options = []): int
    {
        if (empty($subscriptions) || (is_countable($subscriptions) && count($subscriptions) === 0)) {
            return 0;
        }

        $payload = $this->buildPayload($title, $body, $options);
        $successCount = 0;

        foreach ($subscriptions as $subscription) {
            if ($this->sendToSubscription($subscription, $payload)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Send push notification to an email address (looks up all associated subscriptions).
     */
    public function sendToEmail(string $email, string $title, string $body, array $options = []): int
    {
        if (empty($email)) {
            return 0;
        }

        $cleanEmail = strtolower(trim($email));

        $subscriptions = PushSubscription::where('email', $cleanEmail)->get();

        // Also check if any BankClient matches this email
        $client = BankClient::where('email', $cleanEmail)->first();
        if ($client) {
            $clientSubs = PushSubscription::where('subscribable_type', BankClient::class)
                ->where('subscribable_id', $client->id)
                ->get();
            $subscriptions = $subscriptions->merge($clientSubs)->unique('id');
        }

        // Also check if any User matches this email
        $user = User::where('email', $cleanEmail)->first();
        if ($user) {
            $userSubs = PushSubscription::where('subscribable_type', User::class)
                ->where('subscribable_id', $user->id)
                ->get();
            $subscriptions = $subscriptions->merge($userSubs)->unique('id');
        }

        return $this->sendToSubscriptions($subscriptions, $title, $body, $options);
    }

    /**
     * Send push notification directly to a BankClient.
     */
    public function sendToClient(BankClient $client, string $title, string $body, array $options = []): int
    {
        $options['url'] = $options['url'] ?? '/es/banca';

        $subscriptions = PushSubscription::where(function ($query) use ($client) {
            $query->where('subscribable_type', BankClient::class)
                  ->where('subscribable_id', $client->id);
            if (!empty($client->email)) {
                $query->orWhere('email', strtolower(trim($client->email)));
            }
        })->get();

        return $this->sendToSubscriptions($subscriptions, $title, $body, $options);
    }

    /**
     * Send push notification to an admin (by email, or all admins).
     */
    public function sendToAdmin(string $adminEmail, string $title, string $body, array $options = []): int
    {
        $options['url'] = $options['url'] ?? '/admin';

        $cleanEmail = strtolower(trim($adminEmail));

        $subscriptions = PushSubscription::where('email', $cleanEmail)->get();

        $user = User::where('email', $cleanEmail)->first();
        if ($user) {
            $userSubs = PushSubscription::where('subscribable_type', User::class)
                ->where('subscribable_id', $user->id)
                ->get();
            $subscriptions = $subscriptions->merge($userSubs)->unique('id');
        }

        return $this->sendToSubscriptions($subscriptions, $title, $body, $options);
    }

    /**
     * Send push notification to all admins with active subscriptions.
     */
    public function sendToAllAdmins(string $title, string $body, array $options = []): int
    {
        $options['url'] = $options['url'] ?? '/admin';

        $adminEmails = ['contact@venatomar.com', 'info@venatomar.com', 'ayuda@venatomar.com'];

        $subscriptions = PushSubscription::where('subscribable_type', User::class)
            ->orWhereIn('email', $adminEmails)
            ->get();

        return $this->sendToSubscriptions($subscriptions, $title, $body, $options);
    }
}
