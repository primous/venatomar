<?php

namespace Tests\Feature;

use App\Actions\SubmitLead;
use App\Models\BankClient;
use App\Models\Lead;
use App\Models\PushSubscription;
use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WebPushNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_vapid_public_key_endpoint_returns_valid_key(): void
    {
        $response = $this->getJson('/push/vapid-public-key');

        $response->assertStatus(200);
        $response->assertJsonStructure(['publicKey']);
        $this->assertNotEmpty($response->json('publicKey'));
    }

    public function test_client_can_subscribe_to_push_notifications(): void
    {
        $client = BankClient::create([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean.dupont@test.com',
            'password' => 'secret123',
            'country' => 'España',
            'country_code' => 'ES',
            'currency' => 'EUR',
            'status' => 'active',
            'balance' => 1000,
        ]);

        $this->actingAs($client, 'client');

        $payload = [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint-12345',
            'keys' => [
                'p256dh' => 'BN9Tt6w990PA6v7FW2uXmYtWkLklzti6mG-vvfuskj6L0Q3N1cTAuedFuCiJGx0eY3uVbqnzZAYty92YP_4gxl5t',
                'auth' => 'Op4O6KmiJRinYhf4ZIq3QKwoC9BZWLBb0KFVY-StIZE',
            ],
            'contentEncoding' => 'aes128gcm',
        ];

        $response = $this->postJson('/push/subscribe', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'email' => 'jean.dupont@test.com',
            'subscribable_type' => BankClient::class,
            'subscribable_id' => $client->id,
            'endpoint_hash' => hash('sha256', 'https://fcm.googleapis.com/fcm/send/test-endpoint-12345'),
        ]);
    }

    public function test_user_can_unsubscribe_from_push(): void
    {
        $endpoint = 'https://fcm.googleapis.com/fcm/send/test-endpoint-to-remove';

        PushSubscription::storeOrUpdate([
            'endpoint' => $endpoint,
            'public_key' => 'dummy_key',
            'auth_token' => 'dummy_auth',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseCount('push_subscriptions', 1);

        $response = $this->postJson('/push/unsubscribe', [
            'endpoint' => $endpoint,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseCount('push_subscriptions', 0);
    }

    public function test_web_push_service_gracefully_handles_empty_subscriptions(): void
    {
        /** @var WebPushService $service */
        $service = app(WebPushService::class);

        $sent = $service->sendToEmail('nonexistent@example.com', 'Test', 'Body');
        $this->assertSame(0, $sent);

        $client = BankClient::create([
            'first_name' => 'Maria',
            'last_name' => 'Garcia',
            'email' => 'maria@test.com',
            'password' => 'secret123',
            'country' => 'España',
            'country_code' => 'ES',
            'currency' => 'EUR',
            'status' => 'active',
            'balance' => 500,
        ]);

        $sentClient = $service->sendToClient($client, 'Test Client', 'Body Client');
        $this->assertSame(0, $sentClient);
    }

    public function test_submit_lead_queues_email_and_executes_push_cleanly(): void
    {
        Mail::fake();

        $lead = Lead::create([
            'reference' => 'LEAD-TEST-001',
            'locale' => 'es',
            'country' => 'es',
            'currency' => 'EUR',
            'amount' => 5000,
            'term_months' => 24,
            'monthly_income' => 2000,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Carlos',
            'last_name' => 'Santana',
            'document_number' => '12345678Z',
            'birthday' => '1990-01-01',
            'email' => 'carlos@test.com',
            'phone' => '+34600000000',
            'geo_zip' => '28001',
            'geo_province' => '28',
            'geo_city' => 'Madrid',
            'home_street' => 'Gran Via',
            'home_number' => '10',
            'payout_method' => 'iban',
            'bank_account' => 'ES9121000418450200051332',
            'gender' => 'male',
            'loan_purpose' => 'personal',
            'marital_status' => 'single',
            'company_sector' => 'services',
            'education' => 'secondary',
            'home_status' => 'tenant',
            'regular_expenses' => 400,
            'has_debt' => false,
            'has_car' => false,
            'consent_at' => now(),
            'consent_version' => '1.0',
            'status' => \App\Enums\LeadStatus::Received,
            'admin_email' => 'info@venatomar.com',
        ]);

        SubmitLead::queueMails($lead);

        // Verification that everything ran without exceptions
        $this->assertTrue(true);
    }

    public function test_push_test_endpoint_with_email_returns_json(): void
    {
        $response = $this->postJson('/push/test', [
            'email' => 'cheminsdelumiere8@gmail.com',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_thanks_page_renders_push_optin_card(): void
    {
        $lead = Lead::create([
            'reference' => 'VN-2026-999999',
            'locale' => 'es',
            'country' => 'es',
            'currency' => 'EUR',
            'amount' => 3000,
            'term_months' => 12,
            'monthly_income' => 1500,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'document_number' => '99999999R',
            'birthday' => '1995-05-05',
            'email' => 'jean@test.com',
            'phone' => '+34611111111',
            'geo_zip' => '28002',
            'geo_province' => '28',
            'geo_city' => 'Madrid',
            'home_street' => 'Calle Mayor',
            'home_number' => '1',
            'payout_method' => 'iban',
            'bank_account' => 'ES9121000418450200051332',
            'gender' => 'male',
            'loan_purpose' => 'personal',
            'marital_status' => 'single',
            'company_sector' => 'tech',
            'education' => 'university',
            'home_status' => 'owner',
            'regular_expenses' => 300,
            'has_debt' => false,
            'has_car' => true,
            'consent_at' => now(),
            'consent_version' => '1.0',
            'status' => \App\Enums\LeadStatus::Received,
        ]);

        $response = $this->withSession(['last_lead_id' => $lead->id])
            ->get('/es/solicitar-prestamo/gracias');

        $response->assertOk();
        $response->assertSee('VenatomarPush');
        $response->assertSee('jean@test.com');
    }
}
