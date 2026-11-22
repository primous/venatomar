<?php

namespace Tests\Feature;

use App\Database\Seeders\AdminUserSeeder;
use App\Livewire\ApplyLoan;
use App\Mail\LeadConfirmation;
use App\Mail\LeadReceivedInternal;
use App\Models\BankClient;
use App\Models\BankClientLoan;
use App\Models\BankTransaction;
use App\Models\InsuranceDocument;
use App\Models\Lead;
use App\Models\LoanApprovalDocument;
use App\Models\LoanContract;
use App\Models\User;
use App\Services\LeadDocumentService;
use App\Support\LoanContractPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class AyudaEmprestimoAdminTest extends TestCase
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

    public function test_solicitar_emprestimo_routes_are_accessible(): void
    {
        $response = $this->get('/es/solicitar-emprestimo');
        $response->assertStatus(200);
        $response->assertSee('solicitar.emprestimo.thanks');

        $shortResponse = $this->get('/es/emprestimo');
        $shortResponse->assertStatus(200);
    }

    public function test_submitting_on_solicitar_emprestimo_attributes_lead_to_ayuda_admin_and_sends_mails_to_ayuda(): void
    {
        $this->travel(10)->seconds();

        $steps = ApplyLoanTest::spanish([
            2 => ['email' => 'cliente.lisboa@example.com'],
        ]);

        $component = Livewire::test(ApplyLoan::class, [
            'locale' => 'es',
            'channel' => 'ayuda',
            'adminEmail' => 'ayuda@venatomar.com',
            'thanksRoute' => 'solicitar.emprestimo.thanks',
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
        $component->call('next')->assertHasNoErrors()->assertRedirect('/es/solicitar-emprestimo/gracias');

        $lead = Lead::where('email', 'cliente.lisboa@example.com')->first();
        $this->assertNotNull($lead);
        $this->assertSame('ayuda@venatomar.com', $lead->admin_email);
        $this->assertSame('ayuda', $lead->channel);
        $this->assertTrue($lead->isAdminAyuda());
        $this->assertSame('Ayuda', $lead->adminChannelLabel());

        // Assert internal notification is queued specifically to ayuda@venatomar.com
        Mail::assertQueued(LeadReceivedInternal::class, function (LeadReceivedInternal $mail) {
            return $mail->hasTo('ayuda@venatomar.com');
        });

        // Assert confirmation mail was also sent to applicant with correct contact email
        Mail::assertQueued(LeadConfirmation::class, function (LeadConfirmation $mail) {
            return $mail->hasTo('cliente.lisboa@example.com');
        });
    }

    public function test_admin_seeder_creates_ayuda_admin(): void
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

        $this->assertDatabaseHas('users', [
            'email' => 'ayuda@venatomar.com',
            'name' => 'Admin Empréstimo (Ayuda)',
        ]);

        $ayudaAdmin = User::where('email', 'ayuda@venatomar.com')->first();
        $this->assertNotNull($ayudaAdmin);

        // Ayuda admin can access /admin panel
        $this->actingAs($ayudaAdmin);
        $response = $this->get('/admin');
        $response->assertStatus(200);
    }

    private function createLead(string $email, string $firstName, string $lastName, string $adminEmail, string $channel): Lead
    {
        return Lead::create([
            'status' => \App\Enums\LeadStatus::Received,
            'locale' => 'es',
            'country' => 'es',
            'currency' => 'EUR',
            'amount' => 15000,
            'term_months' => 36,
            'monthly_income' => 2800,
            'regular_expenses' => 500,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => '+34600112233',
            'document_number' => 'Y1234567P',
            'birthday' => '1990-01-01',
            'gender' => 'male',
            'marital_status' => 'single',
            'education' => 'university',
            'home_street' => 'Calle Mayor',
            'home_number' => '1',
            'geo_zip' => '28001',
            'geo_city' => 'Madrid',
            'geo_province' => 'Madrid',
            'home_status' => 'tenant',
            'employment_status' => 'employee',
            'company_sector' => 'services',
            'loan_purpose' => 'personal',
            'payout_method' => 'iban',
            'bank_account' => 'ES9121000418450200051332',
            'admin_email' => $adminEmail,
            'channel' => $channel,
            'has_debt' => false,
            'has_car' => false,
            'defaulter_list' => false,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);
    }

    public function test_ayuda_admin_has_strict_data_isolation(): void
    {
        $infoAdmin = User::factory()->create(['email' => 'info@venatomar.com', 'name' => 'Admin Info']);
        $contactAdmin = User::factory()->create(['email' => 'contact@venatomar.com', 'name' => 'Admin Contact']);
        $ayudaAdmin = User::factory()->create(['email' => 'ayuda@venatomar.com', 'name' => 'Admin Ayuda']);

        // Create a lead for each admin
        $infoLead = $this->createLead('jean.info@example.com', 'Jean', 'Info', 'info@venatomar.com', 'web');
        $contactLead = $this->createLead('carlos.contact@example.com', 'Carlos', 'Contact', 'contact@venatomar.com', 'contact');
        $ayudaLead = $this->createLead('manuel.ayuda@example.com', 'Manuel', 'Ayuda', 'ayuda@venatomar.com', 'ayuda');

        // Create bank clients for contact and ayuda
        $contactClient = BankClient::create([
            'first_name' => 'Carlos', 'last_name' => 'Contact',
            'email' => 'carlos.contact@example.com', 'password' => Hash::make('secret'),
            'country' => 'Espagne', 'currency' => 'EUR', 'balance' => 1000,
            'admin_email' => 'contact@venatomar.com',
        ]);

        $ayudaClient = BankClient::create([
            'first_name' => 'Manuel', 'last_name' => 'Ayuda',
            'email' => 'manuel.ayuda@example.com', 'password' => Hash::make('secret'),
            'country' => 'Espagne', 'currency' => 'EUR', 'balance' => 2000,
            'admin_email' => 'ayuda@venatomar.com',
        ]);

        // 1. Leads Isolation
        $this->actingAs($ayudaAdmin);
        $resp = $this->get('/admin/leads');
        $resp->assertOk();
        $resp->assertSee('manuel.ayuda@example.com');
        $resp->assertDontSee('carlos.contact@example.com');
        $resp->assertDontSee('jean.info@example.com');

        // 2. Bank Clients Isolation
        $respClients = $this->get('/admin/bank-clients');
        $respClients->assertOk();
        $respClients->assertSee('manuel.ayuda@example.com');
        $respClients->assertDontSee('carlos.contact@example.com');

        // Contact Admin cannot see Ayuda
        $this->actingAs($contactAdmin);
        $respContact = $this->get('/admin/leads');
        $respContact->assertOk();
        $respContact->assertSee('carlos.contact@example.com');
        $respContact->assertDontSee('manuel.ayuda@example.com');
    }

    public function test_document_generated_for_ayuda_lead_displays_ayuda_email(): void
    {
        $lead = Lead::create([
            'status' => \App\Enums\LeadStatus::Accepted,
            'locale' => 'es',
            'country' => 'es',
            'currency' => 'EUR',
            'amount' => 18000,
            'term_months' => 48,
            'monthly_income' => 3000,
            'regular_expenses' => 700,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Fernanda',
            'last_name' => 'Rocha',
            'document_number' => '88776655P',
            'birthday' => '1992-05-15',
            'email' => 'fernanda.rocha@example.com',
            'phone' => '+34611554433',
            'geo_zip' => '08001',
            'geo_province' => 'Barcelona',
            'geo_city' => 'Barcelona',
            'home_street' => 'Rambla',
            'home_number' => '12',
            'payout_method' => 'iban',
            'bank_account' => 'ES9121000418450200051332',
            'admin_email' => 'ayuda@venatomar.com',
            'channel' => 'ayuda',
            'gender' => 'female',
            'loan_purpose' => 'help',
            'marital_status' => 'single',
            'company_sector' => 'retail',
            'education' => 'higher_long',
            'home_status' => 'tenant',
            'has_debt' => false,
            'has_car' => false,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        $contract = LeadDocumentService::generateLoanContract($lead, [
            'document_fee' => 250.00,
        ]);

        $this->assertSame('ayuda@venatomar.com', $contract->admin_email);

        $pdfContent = LoanContractPdf::render($contract);
        $this->assertNotEmpty($pdfContent);
        $this->assertStringStartsWith('%PDF', $pdfContent);
    }
}
