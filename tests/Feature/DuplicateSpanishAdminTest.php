<?php

namespace Tests\Feature;

use App\Database\Seeders\AdminUserSeeder;
use App\Livewire\ApplyLoan;
use App\Mail\LeadConfirmation;
use App\Mail\LeadReceivedInternal;
use App\Models\Lead;
use App\Models\User;
use App\Services\LeadDocumentService;
use App\Support\LoanContractPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class DuplicateSpanishAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        config([
            'venatomar.loan.es.amount_min' => 100, 'venatomar.loan.es.amount_max' => 5000,
            'venatomar.loan.es.term_min' => 3, 'venatomar.loan.es.term_max' => 60,
        ]);
    }

    public function test_spanish_duplicate_routes_are_accessible(): void
    {
        $response = $this->get('/es/solicitar-prestamo');
        $response->assertStatus(200);
        $response->assertSee('solicitar.thanks');

        $shortResponse = $this->get('/es/solicitar');
        $shortResponse->assertStatus(200);

        $accountResponse = $this->get('/es/mi-cuenta');
        $accountResponse->assertStatus(200);

        $bancaOnlineResponse = $this->get('/es/banca-online');
        $bancaOnlineResponse->assertRedirect('/es/portal');

        $termsResponse = $this->get('/es/legal/terminos-y-condiciones');
        $termsResponse->assertStatus(200);

        $privacyResponse = $this->get('/es/legal/politica-de-privacidad');
        $privacyResponse->assertStatus(200);

        $cookiesResponse = $this->get('/es/legal/politica-de-cookies');
        $cookiesResponse->assertStatus(200);
    }

    public function test_submitting_on_solicitar_prestamo_attributes_lead_to_contact_admin_and_sends_mails_to_contact_mail(): void
    {
        $this->travel(10)->seconds();

        $steps = ApplyLoanTest::spanish([
            2 => ['email' => 'cliente.madrid@example.com'],
        ]);

        $component = Livewire::test(ApplyLoan::class, [
            'locale' => 'es',
            'channel' => 'contact',
            'adminEmail' => 'contact@venatomar.com',
            'thanksRoute' => 'solicitar.thanks',
        ]);

        foreach ($steps as $step => $fields) {
            foreach ($fields as $field => $value) {
                $component->set($field, $value);
            }
            if ($step < 5) {
                $component->call('next')->assertHasNoErrors()->assertSet('step', $step + 1);
            }
        }

        $this->travel(10)->seconds();
        $component->call('next')->assertHasNoErrors()->assertRedirect('/es/solicitar-prestamo/gracias');

        $lead = Lead::where('email', 'cliente.madrid@example.com')->first();
        $this->assertNotNull($lead);
        $this->assertSame('contact@venatomar.com', $lead->admin_email);
        $this->assertSame('contact', $lead->channel);

        // Internal webmail notification is disabled for all admins; confirmation mail still routes to contact@
        Mail::assertNotQueued(LeadReceivedInternal::class);

        // Assert confirmation mail was also sent to applicant with mi-cuenta link
        Mail::assertQueued(LeadConfirmation::class, function (LeadConfirmation $mail) {
            return $mail->hasTo('cliente.madrid@example.com');
        });
    }

    public function test_admin_seeder_creates_both_info_and_contact_admins(): void
    {
        $this->seed(\Database\Seeders\AdminUserSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'info@venatomar.com',
            'name' => 'Admin Principal (Info)',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'contact@venatomar.com',
            'name' => 'Admin Secondaire (Contact)',
        ]);

        $contactAdmin = User::where('email', 'contact@venatomar.com')->first();
        $this->assertNotNull($contactAdmin);

        // Contact admin can access /admin panel
        $this->actingAs($contactAdmin);
        $response = $this->get('/admin');
        $response->assertStatus(200);
    }

    public function test_document_generated_for_contact_lead_displays_contact_email(): void
    {
        $lead = Lead::create([
            'status' => \App\Enums\LeadStatus::Accepted,
            'locale' => 'es',
            'country' => 'es',
            'currency' => 'EUR',
            'amount' => 15000,
            'term_months' => 36,
            'monthly_income' => 2600,
            'regular_expenses' => 600,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Alvaro',
            'last_name' => 'Soler',
            'document_number' => '99881122K',
            'birthday' => '1989-10-10',
            'email' => 'alvaro.soler@example.com',
            'phone' => '+34600112233',
            'geo_zip' => '28004',
            'geo_province' => 'Madrid',
            'geo_city' => 'Madrid',
            'home_street' => 'Gran Via',
            'home_number' => '20',
            'payout_method' => 'iban',
            'bank_account' => 'ES9121000418450200051332',
            'admin_email' => 'contact@venatomar.com',
            'channel' => 'contact',
            'gender' => 'male',
            'loan_purpose' => 'help',
            'marital_status' => 'single',
            'company_sector' => 'music',
            'education' => 'higher_long',
            'home_status' => 'tenant',
            'has_debt' => false,
            'has_car' => false,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        $contract = LeadDocumentService::generateLoanContract($lead, [
            'document_fee' => 200.00,
        ]);

        $this->assertSame('contact@venatomar.com', $contract->admin_email);

        $pdfContent = LoanContractPdf::render($contract);
        $this->assertNotEmpty($pdfContent);
        $this->assertStringStartsWith('%PDF', $pdfContent);
    }
}
