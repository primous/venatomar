<?php

namespace Tests\Feature;

use App\Database\Seeders\AdminUserSeeder;
use App\Livewire\ApplyLoan;
use App\Mail\BankClientCreditedMail;
use App\Mail\BankClientDebitedMail;
use App\Mail\BankClientStatusChangedMail;
use App\Mail\BankClientWelcomeMail;
use App\Mail\InsuranceCertificateMail;
use App\Mail\LeadConfirmation;
use App\Mail\LeadReceivedInternal;
use App\Mail\LoanAcceptedMail;
use App\Mail\LoanContractMail;
use App\Models\BankClient;
use App\Models\BankClientLoan;
use App\Models\BankTransaction;
use App\Models\InsuranceDocument;
use App\Models\Lead;
use App\Models\LoanApprovalDocument;
use App\Models\LoanContract;
use App\Models\User;
use App\Services\LeadDocumentService;
use App\Support\InsurancePdf;
use App\Support\LoanContractPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
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

        // Assert internal notification is queued specifically to ayuda@venatomar.com (without external freemail Reply-To)
        Mail::assertQueued(LeadReceivedInternal::class, function (LeadReceivedInternal $mail) {
            return $mail->hasTo('ayuda@venatomar.com')
                && ! $mail->hasReplyTo('cliente.lisboa@example.com');
        });

        // Assert confirmation mail was also sent to applicant with reply-to set to ayuda@venatomar.com
        Mail::assertQueued(LeadConfirmation::class, function (LeadConfirmation $mail) {
            return $mail->hasTo('cliente.lisboa@example.com')
                && $mail->hasReplyTo('ayuda@venatomar.com');
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

    public function test_header_cta_on_solicitar_emprestimo_keeps_ayuda_route(): void
    {
        $response = $this->get('/es/solicitar-emprestimo');
        $response->assertStatus(200);
        $response->assertSee(route('solicitar.emprestimo'), false);
        $response->assertDontSee('href="'.route('solicitar').'"', false);
    }

    public function test_submitting_sv_country_on_es_solicitar_emprestimo_preserves_es_locale_and_routes_to_ayuda(): void
    {
        $this->travel(10)->seconds();

        $steps = ApplyLoanTest::spanish([
            1 => ['selected_country' => 'sv', 'amount' => 1000, 'term_months' => 12],
            2 => ['email' => 'salvador.ayuda@example.com', 'phone' => '+50370001234', 'document_number' => '012345678'],
            3 => ['geo_zip' => '1101', 'geo_province' => 'SS', 'geo_city' => 'San Salvador'],
            4 => ['payout_method' => 'western_union'],
            5 => ['has_car' => '1', 'car_number' => 'P123-456'],
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

        $lead = Lead::where('email', 'salvador.ayuda@example.com')->first();
        $this->assertNotNull($lead);
        $this->assertSame('es', $lead->locale);
        $this->assertSame('sv', $lead->country);
        $this->assertSame('ayuda@venatomar.com', $lead->admin_email);

        Mail::assertQueued(LeadReceivedInternal::class, fn (LeadReceivedInternal $mail) => $mail->hasTo('ayuda@venatomar.com'));
        Mail::assertQueued(LeadConfirmation::class, function (LeadConfirmation $mail) {
            $html = $mail->render();

            return $mail->hasTo('salvador.ayuda@example.com')
                && str_contains($html, 'ayuda@venatomar.com')
                && str_contains($html, 'Hemos recibido tu solicitud')
                && ! str_contains($html, 'Vi har tagit emot');
        });
    }

    public function test_emails_and_pdfs_never_mix_languages_even_when_admin_app_locale_is_fr(): void
    {
        App::setLocale('fr');

        $svLead = $this->createLead('sv.client@example.com', 'Margarita', 'Ascencio', 'ayuda@venatomar.com', 'ayuda');
        $svLead->forceFill(['locale' => 'sv', 'country' => 'sv', 'currency' => 'USD'])->save();

        $insuranceDoc = LeadDocumentService::generateInsuranceDocument($svLead, [
            'locale' => 'es',
            'document_type' => 'information',
            'document_fee' => 150,
            'guarantees' => ['death', 'ptia'],
        ]);
        $contract = LeadDocumentService::generateLoanContract($svLead, ['locale' => 'es', 'document_fee' => 250]);

        $svConfHtml = (new LeadConfirmation($svLead))->render();
        $this->assertStringContainsString('Hemos recibido tu solicitud', $svConfHtml);
        $this->assertStringNotContainsString('Vi har tagit emot', $svConfHtml);

        $acceptedMail = new LoanAcceptedMail($svLead, $insuranceDoc);
        $acceptedHtml = $acceptedMail->render();
        $this->assertStringContainsString('Hola, Margarita', $acceptedHtml);
        $this->assertStringContainsString('36 meses', $acceptedHtml);
        $this->assertStringNotContainsString('Avgift för dokument', $acceptedHtml);
        $this->assertStringNotContainsString('Contacter', $acceptedHtml);
        $this->assertSame('Ficha_Informacion_Seguro_'.$insuranceDoc->reference.'.pdf', $acceptedMail->attachments()[0]->as);

        $contractMail = new LoanContractMail($contract, $svLead);
        $contractHtml = $contractMail->render();
        $this->assertStringContainsString('contrato oficial de préstamo', $contractHtml);
        $this->assertStringNotContainsString('Contrat_Pret_', $contractMail->attachments()[0]->as);
        $this->assertSame('Contrato_Prestamo_'.$contract->reference.'.pdf', $contractMail->attachments()[0]->as);

        // Ensure French Mailable does not break on legal route (no /fr/legal/...) and uses French text
        $frContract = LeadDocumentService::generateLoanContract($svLead, ['locale' => 'fr', 'document_fee' => 200]);
        $frContractMail = new LoanContractMail($frContract, $svLead);
        $frHtml = $frContractMail->render();
        $this->assertStringContainsString('Votre contrat de prêt officiel', $frHtml);
        $this->assertStringNotContainsString('Tu contrato oficial de préstamo', $frHtml);
        $this->assertStringNotContainsString('/fr/legal/', $frHtml);
        $this->assertSame('Contrat_Pret_'.$frContract->reference.'.pdf', $frContractMail->attachments()[0]->as);

        // Portal status mail in ES must not leak French 'RESTRICTION' / 'ACTIF'
        $client = BankClient::create([
            'first_name' => 'Margarita', 'last_name' => 'Ascencio',
            'email' => 'sv.portal@example.com', 'password' => Hash::make('secret'),
            'country' => 'El Salvador', 'currency' => 'USD', 'balance' => 1000,
            'locale' => 'sv', 'admin_email' => 'ayuda@venatomar.com',
        ]);
        $frozenHtml = (new BankClientStatusChangedMail($client, 'frozen'))->render();
        $this->assertStringContainsString('RESTRINGIDA', $frozenHtml);
        $this->assertStringNotContainsString('RESTRICTION', $frozenHtml);
        $this->assertStringNotContainsString('Kontostatus', $frozenHtml);

        $welcomeHtml = (new BankClientWelcomeMail($client, 'TmpPass123'))->render();
        $this->assertStringContainsString('Tu espacio de banca en línea', $welcomeHtml);
        $this->assertStringNotContainsString('Ditt nätbanksutrymme', $welcomeHtml);

        // Insurance PDF generation in ES works cleanly even with sv guarantees array
        $pdf = InsurancePdf::render($insuranceDoc);
        $this->assertStringStartsWith('%PDF', $pdf);
    }

    public function test_three_workflow_emails_include_new_copywriting_and_centered_design(): void
    {
        $lead = $this->createLead('flow.client@example.com', 'Carlos', 'Mendoza', 'ayuda@venatomar.com', 'ayuda');
        $lead->forceFill(['locale' => 'fr'])->save();

        // 1. Confirmation: bien reçue + mise en examen + résumé
        $confHtml = (new LeadConfirmation($lead))->render();
        $this->assertStringContainsString('mise en examen', $confHtml);
        $this->assertStringContainsString($lead->reference, $confHtml);
        $this->assertStringContainsString('Suivre ma demande', $confHtml);

        // 2. Info assurance crédit: félicitations + évaluée et validée + assurance crédit requise + lecture sérieuse du doc
        $infoDoc = LeadDocumentService::generateInsuranceDocument($lead, [
            'locale' => 'fr',
            'document_type' => 'information',
            'document_fee' => 150,
        ]);
        $acceptedHtml = (new LoanAcceptedMail($lead, $infoDoc))->render();
        $this->assertStringContainsString('évaluée et validée', $acceptedHtml);
        $this->assertStringContainsString('assurance crédit', $acceptedHtml);
        $this->assertStringContainsString('sérieusement connaissance', $acceptedHtml);

        // 3. Certificat d'assurance: félicitations assurance active + ouverture de compte & coordonnées à suivre
        $certDoc = LeadDocumentService::generateInsuranceDocument($lead, [
            'locale' => 'fr',
            'document_type' => 'certificate',
            'document_fee' => 150,
        ]);
        $certHtml = (new InsuranceCertificateMail($certDoc, $lead))->render();
        $this->assertStringContainsString('disposez de votre assurance crédit', $certHtml);
        $this->assertStringContainsString('ouverture de votre compte bancaire', $certHtml);
        $this->assertStringContainsString('coordonnées bancaires', $certHtml);
    }

    public function test_client_mailables_always_route_reply_to_assigned_admin(): void
    {
        foreach (['contact@venatomar.com' => 'contact', 'ayuda@venatomar.com' => 'ayuda'] as $adminEmail => $channel) {
            $lead = $this->createLead("client.{$channel}@example.com", 'Lucia', 'Diaz', $adminEmail, $channel);
            $infoDoc = LeadDocumentService::generateInsuranceDocument($lead, ['document_type' => 'information', 'locale' => 'es']);
            $certDoc = LeadDocumentService::generateInsuranceDocument($lead, ['document_type' => 'certificate', 'locale' => 'es']);
            $contract = LeadDocumentService::generateLoanContract($lead, ['locale' => 'es']);
            $client = BankClient::create([
                'first_name' => 'Lucia', 'last_name' => 'Diaz',
                'email' => "client.{$channel}@example.com", 'password' => Hash::make('secret'),
                'country' => 'España', 'currency' => 'EUR', 'balance' => 1000,
                'locale' => 'es', 'admin_email' => $adminEmail,
            ]);

            $this->assertTrue((new LeadConfirmation($lead))->hasReplyTo($adminEmail));
            $this->assertTrue((new LoanAcceptedMail($lead, $infoDoc))->hasReplyTo($adminEmail));
            $this->assertTrue((new InsuranceCertificateMail($certDoc, $lead))->hasReplyTo($adminEmail));
            $this->assertTrue((new LoanContractMail($contract, $lead))->hasReplyTo($adminEmail));
            $this->assertTrue((new BankClientWelcomeMail($client, 'Pass123!'))->hasReplyTo($adminEmail));
            $this->assertTrue((new BankClientCreditedMail($client, 500, 'Abono', 1500))->hasReplyTo($adminEmail));
            $this->assertTrue((new BankClientDebitedMail($client, 100, 'Cargo', 900))->hasReplyTo($adminEmail));
            $this->assertTrue((new BankClientStatusChangedMail($client, 'frozen'))->hasReplyTo($adminEmail));
        }

        // When per-admin SMTP passwords are configured, From and mailer also isolate per admin
        config([
            'mail.mailers.smtp_contact.password' => 'contact-secret',
            'mail.mailers.smtp_ayuda.password' => 'ayuda-secret',
        ]);

        $contactLead = $this->createLead('c.smtp@example.com', 'Ana', 'Perez', 'contact@venatomar.com', 'contact');
        $ayudaLead = $this->createLead('a.smtp@example.com', 'Luis', 'Gomez', 'ayuda@venatomar.com', 'ayuda');

        $contactMail = new LeadConfirmation($contactLead);
        $this->assertTrue($contactMail->usesMailer('smtp_contact'));
        $this->assertTrue($contactMail->hasFrom('contact@venatomar.com'));
        $this->assertTrue($contactMail->hasReplyTo('contact@venatomar.com'));

        $ayudaMail = new LeadConfirmation($ayudaLead);
        $this->assertTrue($ayudaMail->usesMailer('smtp_ayuda'));
        $this->assertTrue($ayudaMail->hasFrom('ayuda@venatomar.com'));
        $this->assertTrue($ayudaMail->hasReplyTo('ayuda@venatomar.com'));
    }
}
