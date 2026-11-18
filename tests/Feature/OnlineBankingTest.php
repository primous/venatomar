<?php

namespace Tests\Feature;

use App\Models\BankClient;
use App\Models\BankClientLoan;
use App\Models\BankTransaction;
use App\Models\BankSupportMessage;
use App\Support\BankDirectory;
use App\Support\BankPdfEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OnlineBankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_bank_directory_has_required_countries(): void
    {
        $countries = BankDirectory::countryNames();
        $this->assertArrayHasKey('Uruguay', $countries);
        $this->assertArrayHasKey('El Salvador', $countries);
        $this->assertArrayHasKey('Perú', $countries);
        $this->assertArrayHasKey('México', $countries);
        $this->assertArrayHasKey('España', $countries);
        $this->assertArrayHasKey('France', $countries);

        $uruguayBanks = BankDirectory::banksForCountry('Uruguay');
        $this->assertContains('Banco República (BROU)', $uruguayBanks);

        $mexicoBanks = BankDirectory::banksForCountry('México');
        $this->assertContains('BBVA México', $mexicoBanks);

        $peruBanks = BankDirectory::banksForCountry('Perú');
        $this->assertContains('Banco de Crédito del Perú (BCP)', $peruBanks);

        $salvadorBanks = BankDirectory::banksForCountry('El Salvador');
        $this->assertContains('Banco Agrícola', $salvadorBanks);
    }

    public function test_client_model_auto_generates_iban_and_account_number(): void
    {
        $client = BankClient::create([
            'first_name' => 'Carlos',
            'last_name' => 'Gomez',
            'email' => 'carlos.gomez@example.com',
            'password' => Hash::make('Secret123!'),
            'country' => 'México',
            'balance' => 15000.00,
            'savings_balance' => 5000.00,
        ]);

        $this->assertNotEmpty($client->account_number);
        $this->assertStringStartsWith('VTM-', $client->account_number);
        $this->assertNotEmpty($client->iban);
        $this->assertEquals('MXN', $client->currency);
        $this->assertEquals(15000.00, (float) $client->balance);
    }

    public function test_client_can_login_to_portal(): void
    {
        $client = BankClient::create([
            'first_name' => 'Elena',
            'last_name' => 'Vargas',
            'email' => 'elena.vargas@example.com',
            'password' => Hash::make('SecuredPass88!'),
            'country' => 'Perú',
            'balance' => 4500.00,
        ]);

        $response = $this->post(route('portal.login.submit'), [
            'email' => 'elena.vargas@example.com',
            'password' => 'SecuredPass88!',
        ]);

        $response->assertRedirect(route('portal.dashboard', ['locale' => $client->locale]));
        $this->assertAuthenticatedAs($client, 'client');
    }

    public function test_blocked_client_is_denied_access(): void
    {
        $client = BankClient::create([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean.blocked@example.com',
            'password' => Hash::make('SecuredPass88!'),
            'country' => 'France',
            'status' => 'blocked',
        ]);

        $response = $this->post(route('portal.login.submit'), [
            'email' => 'jean.blocked@example.com',
            'password' => 'SecuredPass88!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('client');
    }

    public function test_dashboard_renders_for_authenticated_client(): void
    {
        $client = BankClient::create([
            'first_name' => 'Mateo',
            'last_name' => 'Silva',
            'email' => 'mateo.silva@example.com',
            'password' => Hash::make('Password123!'),
            'country' => 'France',
            'locale' => 'fr',
            'balance' => 28000.00,
            'savings_balance' => 12000.00,
        ]);

        $loan = BankClientLoan::create([
            'bank_client_id' => $client->id,
            'title' => 'Prêt Personnel Facelia',
            'amount' => 10000.00,
            'remaining_amount' => 8500.00,
            'term_months' => 24,
            'interest_rate' => 3.50,
            'monthly_payment' => 432.00,
            'total_repayable' => 10368.00,
            'status' => 'active',
        ]);

        $response = $this->actingAs($client, 'client')->get(route('portal.dashboard', ['locale' => $client->locale]));

        $response->assertStatus(200);
        $response->assertSee('Tous les comptes');
        $response->assertSee('Compte individuel');
        $response->assertSee('Prêt Personnel Facelia');
    }

    public function test_transfer_creation_and_otp_verification(): void
    {
        $client = BankClient::create([
            'first_name' => 'Alejandro',
            'last_name' => 'Reyes',
            'email' => 'alejandro@example.com',
            'password' => Hash::make('Password123!'),
            'country' => 'El Salvador',
            'currency' => 'USD',
            'balance' => 2500.00,
            'can_receive_otp_email' => false,
        ]);

        // Initiate transfer
        $response = $this->actingAs($client, 'client')->post(route('portal.transfers.store', ['locale' => $client->locale]), [
            'target_bank_name' => 'Banco Agrícola',
            'target_account_holder' => 'Maria Santos',
            'target_account_number' => 'SV28AGRI0000123456789012',
            'amount' => 500.00,
            'description' => 'Acompte travaux',
        ]);

        $response->assertRedirect(route('portal.transfers', ['locale' => $client->locale]));

        // Check transaction was created in pending state
        $tx = BankTransaction::where('bank_client_id', $client->id)->first();
        $this->assertNotNull($tx);
        $this->assertEquals('pending', $tx->status);
        $this->assertEquals('Banco Agrícola', $tx->target_bank_name);
        $this->assertNotEmpty($tx->otp_code);

        // Verify valid OTP execution
        $otpResponse = $this->actingAs($client, 'client')
            ->post(route('portal.transfers.verify-otp', $tx), [
                'otp_code' => $tx->otp_code,
            ]);

        $otpResponse->assertRedirect(route('portal.transfers'));

        $tx->refresh();
        $client->refresh();

        $this->assertEquals('completed', $tx->status);
        $this->assertEquals(2000.00, (float) $client->balance);
    }

    public function test_savings_internal_transfer(): void
    {
        $client = BankClient::create([
            'first_name' => 'Lucas',
            'last_name' => 'Martin',
            'email' => 'lucas.martin@example.com',
            'password' => Hash::make('Password123!'),
            'country' => 'France',
            'currency' => 'EUR',
            'balance' => 1000.00,
            'savings_balance' => 500.00,
        ]);

        // Move 300 from checking to savings
        $response = $this->actingAs($client, 'client')
            ->post(route('portal.loans.savings-transfer', ['locale' => $client->locale]), [
                'direction' => 'to_savings',
                'amount' => 300.00,
            ]);

        $response->assertRedirect();
        $client->refresh();

        $this->assertEquals(700.00, (float) $client->balance);
        $this->assertEquals(800.00, (float) $client->savings_balance);
    }

    public function test_client_pdf_downloads(): void
    {
        $client = BankClient::create([
            'first_name' => 'Sophie',
            'last_name' => 'Bernard',
            'email' => 'sophie.bernard@example.com',
            'password' => Hash::make('Password123!'),
            'country' => 'France',
            'balance' => 5200.00,
            'savings_balance' => 2000.00,
        ]);

        $ribResponse = $this->actingAs($client, 'client')->get(route('portal.documents.rib', ['locale' => $client->locale]));
        $ribResponse->assertStatus(200);
        $ribResponse->assertHeader('Content-Type', 'application/pdf');

        $statementResponse = $this->actingAs($client, 'client')->get(route('portal.documents.statement', ['locale' => $client->locale]));
        $statementResponse->assertStatus(200);
        $statementResponse->assertHeader('Content-Type', 'application/pdf');

        $attestationResponse = $this->actingAs($client, 'client')->get(route('portal.documents.attestation', ['locale' => $client->locale]));
        $attestationResponse->assertStatus(200);
        $attestationResponse->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_support_and_fraud_alert_submission(): void
    {
        $client = BankClient::create([
            'first_name' => 'David',
            'last_name' => 'Roux',
            'email' => 'david.roux@example.com',
            'password' => Hash::make('Password123!'),
            'country' => 'France',
        ]);

        $response = $this->actingAs($client, 'client')->post(route('portal.support.store', ['locale' => $client->locale]), [
            'category' => 'fraud_alert',
            'subject' => 'Suspicion débit frauduleux',
            'message' => 'J\'ai constaté un retrait suspect ce matin.',
        ]);

        $response->assertRedirect(route('portal.support', ['locale' => $client->locale]));

        $this->assertDatabaseHas('bank_support_messages', [
            'bank_client_id' => $client->id,
            'category' => 'fraud_alert',
            'subject' => 'Suspicion débit frauduleux',
        ]);
    }

    public function test_client_with_assigned_french_locale_logs_in_at_es_my_account_and_redirects_to_french_portal(): void
    {
        $client = BankClient::create([
            'first_name' => 'Pierre',
            'last_name' => 'Dubois',
            'email' => 'pierre.dubois@example.fr',
            'password' => Hash::make('SecretFrance2026!'),
            'country' => 'France',
            'locale' => 'fr',
            'balance' => 8400.00,
        ]);

        // Client navigates to http://localhost:8000/es/my-account and posts credentials
        $response = $this->post('/es/my-account', [
            'email' => 'pierre.dubois@example.fr',
            'password' => 'SecretFrance2026!',
        ]);

        // Must automatically redirect to the French portal /fr/portal
        $response->assertRedirect('/fr/portal');
        $this->assertAuthenticatedAs($client, 'client');

        // Following redirect brings the French portal
        $portalResponse = $this->actingAs($client, 'client')->get('/fr/portal');
        $portalResponse->assertStatus(200);
        $portalResponse->assertSee('Tous les comptes');
        $portalResponse->assertSee('Compte individuel');
    }

    public function test_client_accessing_wrong_locale_portal_url_is_automatically_redirected_to_assigned_locale(): void
    {
        $client = BankClient::create([
            'first_name' => 'Mateo',
            'last_name' => 'Silva',
            'email' => 'mateo.silva@example.uy',
            'password' => Hash::make('SecretUruguay2026!'),
            'country' => 'Uruguay',
            'locale' => 'es',
            'balance' => 125000.00,
        ]);

        // If client inadvertently hits /fr/portal/transfers while assigned locale is 'es'
        $response = $this->actingAs($client, 'client')->get('/fr/portal/transfers');

        // Middleware EnsureClientPortalLocale immediately redirects to /es/portal/transfers
        $response->assertRedirect('/es/portal/transfers');

        // Following redirect to /es/portal/transfers displays Spanish translations
        $esResponse = $this->actingAs($client, 'client')->get('/es/portal/transfers');
        $esResponse->assertStatus(200);
        $esResponse->assertSee('Transferencias y Envíos');
        $esResponse->assertSee('Iniciar una nueva transferencia');
    }

    public function test_all_portal_views_render_in_client_locale_without_errors(): void
    {
        $client = BankClient::create([
            'first_name' => 'Lucia',
            'last_name' => 'Fernandez',
            'email' => 'lucia.f@example.com',
            'password' => Hash::make('SecretPass123!'),
            'country' => 'España',
            'locale' => 'es',
            'balance' => 10000.00,
            'savings_balance' => 2500.00,
        ]);

        $this->actingAs($client, 'client')->get('/es/portal')->assertStatus(200)->assertSee('Todas las cuentas');
        $this->actingAs($client, 'client')->get('/es/portal/transfers')->assertStatus(200)->assertSee('Transferencias y Envíos');
        $this->actingAs($client, 'client')->get('/es/portal/documents')->assertStatus(200)->assertSee('Tarjetas y Documentos Oficiales');
        $this->actingAs($client, 'client')->get('/es/portal/loans')->assertStatus(200)->assertSee('Ahorro y Financiación');
        $this->actingAs($client, 'client')->get('/es/portal/support')->assertStatus(200)->assertSee('Atención al Cliente y Seguridad');
        $this->actingAs($client, 'client')->get('/es/portal/profile')->assertStatus(200)->assertSee('Perfil y Parámetros de Seguridad');
    }
}
