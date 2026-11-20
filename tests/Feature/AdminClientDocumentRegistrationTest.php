<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Filament\Resources\InsuranceDocuments\Pages\CreateInsuranceDocument;
use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\LoanApprovalDocuments\Pages\CreateLoanApprovalDocument;
use App\Filament\Resources\LoanContracts\Pages\CreateLoanContract;
use App\Mail\InsuranceCertificateMail;
use App\Mail\LoanAcceptedMail;
use App\Mail\LoanContractMail;
use App\Models\BankClient;
use App\Models\Lead;
use App\Models\User;
use App\Services\LeadDocumentService;
use App\Support\InsurancePdf;
use App\Support\LoanApprovalPdf;
use App\Support\LoanContractPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class AdminClientDocumentRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@venatomar.test',
        ]);
    }

    public function test_admin_can_access_create_lead_page(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateLead::class)
            ->assertSuccessful();
    }

    public function test_admin_can_register_client_manually_without_public_form(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateLead::class)
            ->fillForm([
                'first_name' => 'Carlos',
                'last_name' => 'Santana',
                'email' => 'carlos.santana@example.com',
                'phone' => '+34612345678',
                'document_number' => '12345678Z',
                'birthday' => '1985-06-15',
                'gender' => 'male',
                'marital_status' => 'married',
                'education' => 'upper_secondary',
                'country' => 'es',
                'locale' => 'es',
                'currency' => 'EUR',
                'status' => LeadStatus::Accepted->value,
                'home_street' => 'Calle Mayor',
                'home_number' => '14',
                'apartment_floor' => '3ºB',
                'geo_zip' => '28013',
                'geo_city' => 'Madrid',
                'geo_province' => 'Madrid',
                'home_status' => 'owner_mortgage',
                'amount' => 25000,
                'term_months' => 48,
                'loan_purpose' => 'renovation',
                'monthly_income' => 3200,
                'regular_expenses' => 750,
                'employment_status' => 'employee',
                'company_sector' => 'technology',
                'payout_method' => 'iban',
                'bank_account' => 'ES9121000418450200051332',
                'create_bank_account' => true,
                'portal_password' => 'SecurePass2026!',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('leads', [
            'first_name' => 'Carlos',
            'last_name' => 'Santana',
            'email' => 'carlos.santana@example.com',
            'amount' => 25000,
            'term_months' => 48,
            'country' => 'es',
            'currency' => 'EUR',
            'status' => 'accepted',
        ]);

        $lead = Lead::where('email', 'carlos.santana@example.com')->first();
        $this->assertNotNull($lead);
        $this->assertNotNull($lead->reference);

        // Assert bank client portal account was also provisioned
        $this->assertDatabaseHas('bank_clients', [
            'email' => 'carlos.santana@example.com',
            'first_name' => 'Carlos',
            'last_name' => 'Santana',
        ]);
    }

    public function test_admin_can_generate_all_three_official_documents_with_identical_info(): void
    {
        $this->actingAs($this->admin);

        $lead = Lead::create([
            'status' => LeadStatus::Accepted,
            'locale' => 'es',
            'country' => 'es',
            'currency' => 'EUR',
            'amount' => 18000,
            'term_months' => 36,
            'monthly_income' => 3000,
            'regular_expenses' => 500,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Elena',
            'last_name' => 'Rodriguez',
            'document_number' => '87654321A',
            'birthday' => '1990-03-21',
            'email' => 'elena.rodriguez@example.com',
            'phone' => '+34699887766',
            'geo_zip' => '08001',
            'geo_province' => 'Barcelona',
            'geo_city' => 'Barcelona',
            'home_street' => 'Rambla Catalunya',
            'home_number' => '22',
            'apartment_floor' => 'Principal',
            'payout_method' => 'iban',
            'bank_account' => 'ES6000491500051234567892',
            'gender' => 'female',
            'loan_purpose' => 'help',
            'marital_status' => 'single',
            'company_sector' => 'finance',
            'education' => 'higher_long',
            'home_status' => 'tenant',
            'has_debt' => false,
            'has_car' => false,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        $documents = LeadDocumentService::generateAllThree($lead);

        $contract = $documents['contract'];
        $insurance = $documents['insurance'];
        $approval = $documents['approval'];

        $this->assertNotNull($contract->id);
        $this->assertNotNull($insurance->id);
        $this->assertNotNull($approval->id);

        // Assert all 3 documents share the exact same client information
        $this->assertSame('Elena', $contract->first_name);
        $this->assertSame('Elena', $insurance->first_name);
        $this->assertSame('Elena', $approval->first_name);

        $this->assertSame('Rodriguez', $contract->last_name);
        $this->assertSame('Rodriguez', $insurance->last_name);
        $this->assertSame('Rodriguez', $approval->last_name);

        $this->assertSame(18000, $contract->amount);
        $this->assertSame(18000, $insurance->amount);
        $this->assertSame(18000, $approval->amount);

        $this->assertSame(36, $contract->term_months);
        $this->assertSame(36, $insurance->term_months);
        $this->assertSame(36, $approval->term_months);

        $this->assertSame('EUR', $contract->currency);
        $this->assertSame('EUR', $insurance->currency);
        $this->assertSame('EUR', $approval->currency);

        $this->assertSame('es', $contract->locale);
        $this->assertSame('es', $insurance->locale);
        $this->assertSame('es', $approval->locale);

        // Assert PDFs render cleanly without errors
        $contractPdf = LoanContractPdf::render($contract);
        $this->assertNotEmpty($contractPdf);
        $this->assertStringStartsWith('%PDF', $contractPdf);

        $insurancePdf = InsurancePdf::render($insurance);
        $this->assertNotEmpty($insurancePdf);
        $this->assertStringStartsWith('%PDF', $insurancePdf);

        $approvalPdf = LoanApprovalPdf::render($approval);
        $this->assertNotEmpty($approvalPdf);
        $this->assertStringStartsWith('%PDF', $approvalPdf);
    }

    public function test_document_creation_pages_prefill_from_query_lead_id(): void
    {
        $this->actingAs($this->admin);

        $lead = Lead::create([
            'status' => LeadStatus::Accepted,
            'locale' => 'fr',
            'country' => 'fr',
            'currency' => 'EUR',
            'amount' => 12000,
            'term_months' => 24,
            'monthly_income' => 2500,
            'regular_expenses' => 600,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'document_number' => '123456789012',
            'birthday' => '1982-11-04',
            'email' => 'jean.dupont@example.com',
            'phone' => '+33612345678',
            'geo_zip' => '75008',
            'geo_province' => 'Île-de-France',
            'geo_city' => 'Paris',
            'home_street' => 'Boulevard Haussmann',
            'home_number' => '50',
            'payout_method' => 'iban',
            'bank_account' => 'FR7630006000011234567890189',
            'gender' => 'male',
            'loan_purpose' => 'renovation',
            'marital_status' => 'married',
            'company_sector' => 'public',
            'education' => 'higher_short',
            'home_status' => 'owner_mortgage',
            'has_debt' => false,
            'has_car' => false,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        Livewire::withQueryParams(['lead_id' => $lead->id])
            ->test(CreateLoanContract::class)
            ->assertFormSet([
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'amount' => 12000,
                'term_months' => 24,
                'locale' => 'fr',
            ]);

        Livewire::withQueryParams(['lead_id' => $lead->id])
            ->test(CreateInsuranceDocument::class)
            ->assertFormSet([
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'amount' => 12000,
                'term_months' => 24,
                'locale' => 'fr',
            ]);

        Livewire::withQueryParams(['lead_id' => $lead->id])
            ->test(CreateLoanApprovalDocument::class)
            ->assertFormSet([
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'amount' => 12000,
                'term_months' => 24,
                'locale' => 'fr',
            ]);
    }

    public function test_loan_contract_stores_manual_document_fee_and_renders_fee_notice_on_pdf(): void
    {
        $this->actingAs($this->admin);

        $lead = Lead::create([
            'status' => LeadStatus::Accepted,
            'locale' => 'fr',
            'country' => 'fr',
            'currency' => 'EUR',
            'amount' => 15000,
            'term_months' => 36,
            'monthly_income' => 2800,
            'regular_expenses' => 700,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Pierre',
            'last_name' => 'Dubois',
            'document_number' => '998877665544',
            'birthday' => '1984-04-12',
            'email' => 'pierre.dubois@example.com',
            'phone' => '+33611223344',
            'geo_zip' => '69001',
            'geo_province' => 'Rhône',
            'geo_city' => 'Lyon',
            'home_street' => 'Rue de la République',
            'home_number' => '10',
            'payout_method' => 'iban',
            'bank_account' => 'FR7611112222333344445555666',
            'gender' => 'male',
            'loan_purpose' => 'help',
            'marital_status' => 'married',
            'company_sector' => 'industry',
            'education' => 'upper_secondary',
            'home_status' => 'owner_free',
            'has_debt' => false,
            'has_car' => true,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        $contract = LeadDocumentService::generateLoanContract($lead, [
            'document_fee' => 280.50,
        ]);

        $this->assertEquals(280.50, (float) $contract->document_fee);
        $this->assertStringContainsString('280', $contract->formattedDocumentFee());

        // Verify PDF renders successfully with the fee notice
        $pdfContent = LoanContractPdf::render($contract);
        $this->assertNotEmpty($pdfContent);
        $this->assertStringStartsWith('%PDF', $pdfContent);

        // Also test view directly to assert exact fee banner text
        $t = fn (string $k, array $replace = []) => trans('loan_contract.'.$k, $replace + ['brand' => 'Venatomar'], 'fr');
        $html = view('pdf.loan-contract', [
            'contract' => $contract,
            't' => $t,
            'logo' => null,
            'countryName' => $contract->country,
            'amount' => $contract->money(),
            'monthlyPayment' => $contract->monthlyPaymentFormatted(),
            'totalInterest' => $contract->totalInterestFormatted(),
            'totalRepayable' => $contract->totalRepayableFormatted(),
            'date' => '12 mai 2026',
            'dateUpper' => '12 MAI 2026',
            'contractDate' => '12 mai 2026',
            'firstPaymentDate' => '12 juin 2026',
            'contactEmail' => 'contact@venatomar.test',
            'company' => 'Venatomar',
            'address' => 'Madrid',
            'issuerAddress' => 'Paseo de la Castellana 216, Madrid',
            'issuerPhone' => '+34 910 000 000',
            'issuerCity' => 'Madrid',
            'authCode' => 'AUTH123',
            'parapheSvg' => '',
            'stampAndSignatureSvg' => '',
            'rightSignatureSvg' => '',
            'sealSvg' => '',
            'lenderSignatureSvg' => '',
            'borrowerSignatureSvg' => '',
        ])->render();

        $this->assertStringContainsString('La réalisation de ce document coûte', $html);
        $this->assertStringContainsString('Article 7', $html);
        $this->assertStringContainsString('280', $html);
    }

    public function test_insurance_document_stores_manual_document_fee_and_renders_fee_notice_on_pdf(): void
    {
        $this->actingAs($this->admin);

        $lead = Lead::create([
            'status' => LeadStatus::Accepted,
            'locale' => 'es',
            'country' => 'es',
            'currency' => 'EUR',
            'amount' => 20000,
            'term_months' => 48,
            'monthly_income' => 3100,
            'regular_expenses' => 600,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Maria',
            'last_name' => 'Garcia',
            'document_number' => '44332211B',
            'birthday' => '1988-08-20',
            'email' => 'maria.garcia@example.com',
            'phone' => '+34655443322',
            'geo_zip' => '28001',
            'geo_province' => 'Madrid',
            'geo_city' => 'Madrid',
            'home_street' => 'Calle Serrano',
            'home_number' => '45',
            'payout_method' => 'iban',
            'bank_account' => 'ES9121000418450200051332',
            'gender' => 'female',
            'loan_purpose' => 'renovation',
            'marital_status' => 'married',
            'company_sector' => 'health',
            'education' => 'higher_long',
            'home_status' => 'owner_mortgage',
            'has_debt' => false,
            'has_car' => true,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        $insurance = LeadDocumentService::generateInsuranceDocument($lead, [
            'document_fee' => 175.00,
        ]);

        $this->assertEquals(175.00, (float) $insurance->document_fee);
        $this->assertStringContainsString('175', $insurance->formattedDocumentFee());

        // Verify PDF renders
        $pdfContent = InsurancePdf::render($insurance);
        $this->assertNotEmpty($pdfContent);
        $this->assertStringStartsWith('%PDF', $pdfContent);

        // Also test view directly in Spanish to assert fee banner and section 5
        $t = fn (string $k, array $replace = []) => trans('insurance.'.$k, $replace + ['brand' => 'Venatomar'], 'es');
        $html = view('pdf.certificate', [
            'doc' => $insurance,
            't' => $t,
            'logo' => null,
            'countryName' => $insurance->country,
            'amount' => $insurance->money(),
            'guaranteesList' => 'Décès, PTIA',
            'date' => '20 de agosto de 2026',
            'dateUpper' => '20 DE AGOSTO DE 2026',
            'effectiveDate' => '20 de agosto de 2026',
            'expiryDate' => '20 de agosto de 2030',
            'contactEmail' => 'contact@venatomar.test',
            'company' => 'Venatomar',
            'address' => 'Madrid',
            'issuerAddress' => 'Paseo de la Castellana 216, Madrid',
            'issuerPhone' => '+34 910 000 000',
            'issuerCity' => 'Madrid',
            'authCode' => 'AUTH456',
            'parapheSvg' => '',
            'stampAndSignatureSvg' => '',
            'rightSignatureSvg' => '',
            'sealSvg' => '',
        ])->render();

        $this->assertStringContainsString('La realización de este documento tiene un coste de', $html);
        $this->assertStringContainsString('175', $html);
    }

    public function test_loan_approval_document_remains_strictly_free(): void
    {
        $this->actingAs($this->admin);

        $lead = Lead::create([
            'status' => LeadStatus::Accepted,
            'locale' => 'es',
            'country' => 'es',
            'currency' => 'EUR',
            'amount' => 10000,
            'term_months' => 24,
            'monthly_income' => 2200,
            'regular_expenses' => 500,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Luis',
            'last_name' => 'Navarro',
            'document_number' => '77665544C',
            'birthday' => '1992-02-14',
            'email' => 'luis.navarro@example.com',
            'phone' => '+34611009988',
            'geo_zip' => '46001',
            'geo_province' => 'Valencia',
            'geo_city' => 'Valencia',
            'home_street' => 'Calle Colon',
            'home_number' => '12',
            'payout_method' => 'iban',
            'bank_account' => 'ES9121000418450200051332',
            'gender' => 'male',
            'loan_purpose' => 'car',
            'marital_status' => 'single',
            'company_sector' => 'commerce',
            'education' => 'upper_secondary',
            'home_status' => 'tenant',
            'has_debt' => false,
            'has_car' => false,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        $approval = LeadDocumentService::generateLoanApprovalDocument($lead);
        $this->assertNotNull($approval->id);

        // Assert LoanApprovalDocument does NOT have document_fee column or fee notice
        $this->assertFalse(array_key_exists('document_fee', $approval->getAttributes()));

        $pdfContent = LoanApprovalPdf::render($approval);
        $this->assertNotEmpty($pdfContent);
        $this->assertStringStartsWith('%PDF', $pdfContent);

        $t = fn (string $k) => trans('loan_approval.'.$k, ['brand' => 'Venatomar'], 'es');
        $html = view('pdf.loan-approval', [
            'doc' => $approval,
            't' => $t,
            'logo' => null,
            'countryName' => $approval->country,
            'amount' => $approval->money(),
            'monthlyPayment' => $approval->monthlyPaymentFormatted(),
            'totalRepayable' => $approval->totalRepayableFormatted(),
            'date' => '14 de febrero de 2026',
            'dateUpper' => '14 DE FEBRERO DE 2026',
            'approvalDate' => '14 de febrero de 2026',
            'expiryDate' => '16 de marzo de 2026',
            'contactEmail' => 'contact@venatomar.test',
            'company' => 'Venatomar',
            'address' => 'Madrid',
            'issuerAddress' => 'Paseo de la Castellana 216, Madrid',
            'issuerPhone' => '+34 910 000 000',
            'issuerCity' => 'Madrid',
            'authCode' => 'AUTH789',
            'parapheSvg' => '',
            'stampAndSignatureSvg' => '',
            'rightSignatureSvg' => '',
            'sealSvg' => '',
            'signatureSvg' => '',
        ])->render();

        $this->assertStringNotContainsString('La réalisation de ce document coûte', $html);
        $this->assertStringNotContainsString('La realización de este documento cuesta', $html);
    }

    public function test_admin_can_generate_all_three_documents_with_distinct_fees(): void
    {
        $this->actingAs($this->admin);

        $lead = Lead::create([
            'status' => LeadStatus::Accepted,
            'locale' => 'es',
            'country' => 'es',
            'currency' => 'EUR',
            'amount' => 30000,
            'term_months' => 60,
            'monthly_income' => 4500,
            'regular_expenses' => 1000,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Sofia',
            'last_name' => 'Morales',
            'document_number' => '55667788D',
            'birthday' => '1987-12-05',
            'email' => 'sofia.morales@example.com',
            'phone' => '+34677889900',
            'geo_zip' => '41001',
            'geo_province' => 'Sevilla',
            'geo_city' => 'Sevilla',
            'home_street' => 'Avenida de la Constitución',
            'home_number' => '5',
            'payout_method' => 'iban',
            'bank_account' => 'ES9121000418450200051332',
            'gender' => 'female',
            'loan_purpose' => 'help',
            'marital_status' => 'married',
            'company_sector' => 'services',
            'education' => 'higher_long',
            'home_status' => 'owner_free',
            'has_debt' => false,
            'has_car' => true,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        $docs = LeadDocumentService::generateAllThree($lead, [
            'contract' => ['document_fee' => 320.00],
            'insurance' => ['document_fee' => 190.00],
        ]);

        $this->assertEquals(320.00, (float) $docs['contract']->document_fee);
        $this->assertEquals(190.00, (float) $docs['insurance']->document_fee);
        $this->assertFalse(array_key_exists('document_fee', $docs['approval']->getAttributes()));
    }

    public function test_admin_accept_loan_action_updates_status_generates_insurance_and_queues_mail(): void
    {
        Mail::fake();

        $this->actingAs($this->admin);

        $lead = Lead::create([
            'status' => LeadStatus::Received,
            'locale' => 'fr',
            'country' => 'fr',
            'currency' => 'EUR',
            'amount' => 15000,
            'term_months' => 36,
            'monthly_income' => 3200,
            'regular_expenses' => 800,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'document_number' => '123456789012',
            'birthday' => '1982-04-12',
            'email' => 'jean.dupont@example.com',
            'phone' => '+33612345678',
            'geo_zip' => '75001',
            'geo_province' => 'Île-de-France',
            'geo_city' => 'Paris',
            'home_street' => 'Rue de Rivoli',
            'home_number' => '10',
            'payout_method' => 'iban',
            'bank_account' => 'FR7630001007941234567890185',
            'gender' => 'male',
            'loan_purpose' => 'auto',
            'marital_status' => 'single',
            'company_sector' => 'commerce',
            'education' => 'upper_secondary',
            'home_status' => 'tenant',
            'has_debt' => false,
            'has_car' => true,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        $res = LeadResource::acceptLead($lead, ['document_fee' => 175.00]);

        $this->assertSame(LeadStatus::Accepted, $lead->fresh()->status);
        $this->assertNotNull($res['insurance']);
        $this->assertSame('information', $res['insurance']->document_type);
        $this->assertSame('Jean', $res['insurance']->first_name);
        $this->assertSame('Dupont', $res['insurance']->last_name);
        $this->assertEquals(175.00, (float) $res['insurance']->document_fee);

        $pdf = InsurancePdf::render($res['insurance'], 'information');
        $this->assertStringContainsString('175', $pdf);

        Mail::assertQueued(LoanAcceptedMail::class, function (LoanAcceptedMail $mail) use ($lead) {
            $rendered = $mail->render();
            return $mail->lead->id === $lead->id
                && $mail->insuranceDocument->document_type === 'information'
                && $mail->hasTo($lead->email)
                && str_contains($rendered, '175');
        });
    }

    public function test_loan_accepted_mail_renders_view_with_pdf_attachment(): void
    {
        $lead = Lead::create([
            'status' => LeadStatus::Accepted,
            'locale' => 'fr',
            'country' => 'fr',
            'currency' => 'EUR',
            'amount' => 12000,
            'term_months' => 24,
            'monthly_income' => 2800,
            'regular_expenses' => 700,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Claire',
            'last_name' => 'Bernard',
            'document_number' => '987654321098',
            'birthday' => '1990-08-20',
            'email' => 'claire.bernard@example.com',
            'phone' => '+33698765432',
            'geo_zip' => '69002',
            'geo_province' => 'Rhône',
            'geo_city' => 'Lyon',
            'home_street' => 'Place Bellecour',
            'home_number' => '3',
            'payout_method' => 'iban',
            'bank_account' => 'FR7630001007941234567890185',
            'gender' => 'female',
            'loan_purpose' => 'renovation',
            'marital_status' => 'married',
            'company_sector' => 'services',
            'education' => 'higher_long',
            'home_status' => 'owner_mortgage',
            'has_debt' => false,
            'has_car' => true,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        $insuranceDoc = LeadDocumentService::generateInsuranceDocument($lead, [
            'document_type' => 'information',
        ]);

        $mail = new LoanAcceptedMail($lead, $insuranceDoc);
        $renderedHtml = $mail->render();

        $this->assertStringContainsString('acceptée et validée', $renderedHtml);
        $this->assertStringContainsString('24 heures', $renderedHtml);
        $this->assertStringContainsString('assurance crédit', $renderedHtml);
        $this->assertStringContainsString('Le service clientèle', $renderedHtml);

        $attachments = $mail->attachments();
        $this->assertCount(1, $attachments);
        $this->assertStringEndsWith('.pdf', $attachments[0]->as ?? $attachments[0]->name ?? 'file.pdf');
    }

    public function test_loan_contract_mail_renders_view_with_pdf_attachment(): void
    {
        $lead = Lead::create([
            'status' => LeadStatus::Accepted,
            'locale' => 'fr',
            'country' => 'fr',
            'currency' => 'EUR',
            'amount' => 15000,
            'term_months' => 36,
            'monthly_income' => 3000,
            'regular_expenses' => 700,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Paul',
            'last_name' => 'Martin',
            'document_number' => '112233445566',
            'birthday' => '1984-05-10',
            'email' => 'paul.martin@example.com',
            'phone' => '+33611223344',
            'geo_zip' => '13001',
            'geo_province' => 'Provence',
            'geo_city' => 'Marseille',
            'home_street' => 'La Canebière',
            'home_number' => '12',
            'payout_method' => 'iban',
            'bank_account' => 'FR7630001007941234567890185',
            'gender' => 'male',
            'loan_purpose' => 'personal',
            'marital_status' => 'single',
            'company_sector' => 'industry',
            'education' => 'upper_secondary',
            'home_status' => 'tenant',
            'has_debt' => false,
            'has_car' => false,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        $contract = LeadDocumentService::generateLoanContract($lead, [
            'document_fee' => 250.00,
        ]);

        $mail = new LoanContractMail($contract, $lead);
        $renderedHtml = $mail->render();

        $this->assertStringContainsString('CONTRAT DE PRÊT OFFICIEL', $renderedHtml);
        $this->assertStringContainsString('Paul', $renderedHtml);
        $this->assertStringContainsString('250', $renderedHtml);
        $this->assertStringContainsString('Contrat_Pret_', $renderedHtml);

        $attachments = $mail->attachments();
        $this->assertCount(1, $attachments);
        $this->assertStringEndsWith('.pdf', $attachments[0]->as ?? $attachments[0]->name ?? 'file.pdf');
    }

    public function test_insurance_certificate_mail_renders_view_with_pdf_attachment(): void
    {
        $lead = Lead::create([
            'status' => LeadStatus::Accepted,
            'locale' => 'fr',
            'country' => 'fr',
            'currency' => 'EUR',
            'amount' => 20000,
            'term_months' => 48,
            'monthly_income' => 3500,
            'regular_expenses' => 900,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Emma',
            'last_name' => 'Rousseau',
            'document_number' => '998877665544',
            'birthday' => '1988-11-25',
            'email' => 'emma.rousseau@example.com',
            'phone' => '+33699887766',
            'geo_zip' => '33000',
            'geo_province' => 'Gironde',
            'geo_city' => 'Bordeaux',
            'home_street' => 'Rue Sainte-Catherine',
            'home_number' => '45',
            'payout_method' => 'iban',
            'bank_account' => 'FR7630001007941234567890185',
            'gender' => 'female',
            'loan_purpose' => 'auto',
            'marital_status' => 'married',
            'company_sector' => 'health',
            'education' => 'higher_long',
            'home_status' => 'owner_free',
            'has_debt' => false,
            'has_car' => true,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        $insuranceDoc = LeadDocumentService::generateInsuranceDocument($lead, [
            'document_type' => 'certificate',
            'document_fee' => 150.00,
        ]);

        $mail = new InsuranceCertificateMail($insuranceDoc, $lead);
        $renderedHtml = $mail->render();

        $this->assertStringContainsString('CERTIFICAT D&#039;ASSURANCE CRÉDIT', $renderedHtml);
        $this->assertStringContainsString('Emma', $renderedHtml);
        $this->assertStringContainsString('150', $renderedHtml);
        $this->assertStringContainsString('Certificat_Assurance_', $renderedHtml);

        $attachments = $mail->attachments();
        $this->assertCount(1, $attachments);
        $this->assertStringEndsWith('.pdf', $attachments[0]->as ?? $attachments[0]->name ?? 'file.pdf');
    }
}
