<?php

namespace App\Http\Controllers;

use App\Models\BankClient;
use App\Models\PushSubscription;
use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebPushController extends Controller
{
    /**
     * Return the VAPID public key needed by the browser to subscribe.
     */
    public function vapidPublicKey(WebPushService $webPush): JsonResponse
    {
        return response()->json([
            'publicKey' => $webPush->getVapidPublicKey(),
        ]);
    }

    /**
     * Store or update a push subscription from the client browser.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
            'contentEncoding' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
        ]);

        $subscribableType = null;
        $subscribableId = null;
        $email = $validated['email'] ?? null;

        // Check if bank client is logged in
        if (Auth::guard('client')->check()) {
            /** @var BankClient $client */
            $client = Auth::guard('client')->user();
            $subscribableType = BankClient::class;
            $subscribableId = $client->id;
            $email = $client->email;
        } elseif (Auth::check()) { // Admin user
            /** @var User $user */
            $user = Auth::user();
            $subscribableType = User::class;
            $subscribableId = $user->id;
            $email = $user->email;
        } elseif (!empty($email)) {
            // Check if email belongs to BankClient
            $client = BankClient::where('email', strtolower(trim($email)))->first();
            if ($client) {
                $subscribableType = BankClient::class;
                $subscribableId = $client->id;
            } else {
                $user = User::where('email', strtolower(trim($email)))->first();
                if ($user) {
                    $subscribableType = User::class;
                    $subscribableId = $user->id;
                }
            }
        }

        $subscription = PushSubscription::storeOrUpdate([
            'endpoint' => $validated['endpoint'],
            'public_key' => $validated['keys']['p256dh'],
            'auth_token' => $validated['keys']['auth'],
            'content_encoding' => $validated['contentEncoding'] ?? 'aes128gcm',
            'email' => $email ? strtolower(trim($email)) : null,
            'subscribable_type' => $subscribableType,
            'subscribable_id' => $subscribableId,
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $subscription->id,
            'message' => 'Push subscription stored successfully.',
        ]);
    }

    /**
     * Remove a push subscription when the user turns it off in their browser.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
        ]);

        $hash = hash('sha256', $validated['endpoint']);
        PushSubscription::where('endpoint_hash', $hash)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Push subscription removed successfully.',
        ]);
    }

    /**
     * Send a quick test push notification to verify setup.
     */
    public function test(Request $request, WebPushService $webPush): JsonResponse
    {
        if (Auth::guard('client')->check()) {
            /** @var BankClient $client */
            $client = Auth::guard('client')->user();
            $count = $webPush->sendToClient(
                $client,
                'Notificación de prueba Venatomar',
                '¡Las notificaciones Push están activas y funcionan correctamente en su dispositivo!',
                ['url' => '/es/portal']
            );

            return response()->json([
                'success' => true,
                'sent' => $count,
                'message' => $count > 0
                    ? 'Notificación enviada correctamente.'
                    : 'Aún no se ha suscrito este navegador a las notificaciones.'
            ]);
        }

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $count = $webPush->sendToAdmin(
                $user->email,
                'Notification de test Admin Venatomar',
                'Les notifications Push sont bien actives sur votre compte administrateur.',
                ['url' => '/admin']
            );

            return response()->json([
                'success' => true,
                'sent' => $count,
                'message' => $count > 0
                    ? 'Notification envoyée avec succès.'
                    : 'Aucun abonnement push actif pour cet administrateur.'
            ]);
        }

        $email = $request->input('email');
        if (!empty($email) && is_string($email)) {
            $cleanEmail = strtolower(trim($email));
            $count = $webPush->sendToEmail(
                $cleanEmail,
                '🔔 Venatomar · Alertas activadas',
                '¡Las notificaciones están activas en este dispositivo! Le avisaremos en tiempo real de cualquier actualización.',
                ['url' => '/']
            );

            return response()->json([
                'success' => true,
                'sent' => $count,
                'message' => $count > 0
                    ? 'Notificación enviada correctamente a su pantalla.'
                    : 'Aún no se ha completado la suscripción de este navegador.'
            ]);
        }

        return response()->json(['error' => 'Non authentifié'], 401);
    }
}
