<?php

namespace Tests\Feature;

use App\Filament\Resources\LoanApprovalDocuments\Pages\CreateLoanApprovalDocument;
use App\Filament\Resources\LoanApprovalDocuments\Pages\ListLoanApprovalDocuments;
use App\Models\Lead;
use App\Models\LoanApprovalDocument;
use App\Models\User;
use App\Support\InsurancePdf;
use App\Support\LoanApprovalPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoanApprovalDocumentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'secret-password-123']);
    }

    public function test_every_loan_approval_language_has_the_same_keys(): void
    {
        $flatten = fn (array $a) => collect(\Illuminate\Support\Arr::dot($a))->keys()->sort()->values()->all();
        $reference = $flatten(require lang_path('fr/loan_approval.php'));

        foreach (array_keys(InsurancePdf::LOCALES) as $locale) {
            $this->assertSame($reference, $flatten(require lang_path("$locale/loan_approval.php")), "Keys differ in $locale");
        }
    }

    public function test_admin_page_renders_in_french(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin/loan-approval-documents')->assertOk()->assertSee('Validation de prêt');
        $this->get('/admin/loan-approval-documents/create')->assertOk()->assertSee('Langue du document');
    }

    public function test_admin_generates_and_downloads_loan_approval_pdf(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateLoanApprovalDocument::class)
            ->fillForm([
                'locale' => 'fr',
                'country' => 'France',
                'currency' => 'EUR',
                'first_name' => 'Pierre',
                'last_name' => 'Martin',
                'phone' => '+33611223344',
                'email' => 'pierre.martin@example.test',
                'document_number' => '123456789',
                'birth_date' => '1984-09-12',
                'address' => '8 boulevard Haussmann, 75009 Paris',
                'amount' => 20000,
                'term_months' => 48,
                'interest_rate' => 3.50,
                'lender' => 'BNP Paribas',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $doc = LoanApprovalDocument::sole();
        $this->assertSame('EUR', $doc->currency);
        $this->assertSame('France', $doc->country);
        $this->assertSame('Pierre', $doc->first_name);
        $this->assertGreaterThan(0, $doc->monthly_payment);
        $this->assertGreaterThan(0, $doc->total_repayable);
        $this->assertMatchesRegularExpression('/^VN-VAL-\d{4}-\d{6}$/', $doc->reference);

        $pdf = LoanApprovalPdf::render($doc);
        $this->assertStringStartsWith('%PDF', $pdf);
        $pageCount = preg_match_all('/\/Type\s*\/Page\b/', $pdf);
        $this->assertSame(1, $pageCount, 'Loan approval document should fit on a single page');

        foreach (array_keys(InsurancePdf::LOCALES) as $loc) {
            $docTest = clone $doc;
            $docTest->locale = $loc;
            $t = fn (string $key, array $replace = []) => trans('loan_approval.'.$key, $replace + ['brand' => 'Venatomar'], $loc);
            $html = view('pdf.loan-approval', [
                'doc' => $docTest,
                't' => $t,
                'logo' => '',
                'countryName' => 'France',
                'amount' => '10.000 €',
                'monthlyPayment' => '220 €',
                'totalRepayable' => '10.560 €',
                'date' => '16/11/2026',
                'dateUpper' => '16 NOVEMBRE 2026',
                'approvalDate' => '16/11/2026',
                'expiryDate' => '16/12/2026',
                'contactEmail' => 'info@venatomar.com',
                'issuerAddress' => 'Paseo de la Castellana 216, Madrid',
                'issuerPhone' => '+34 910 000 000',
                'issuerCity' => 'Madrid',
                'parapheSvg' => '',
                'stampAndSignatureSvg' => '',
                'rightSignatureSvg' => '',
            ])->render();

            $this->assertStringNotContainsString('loan_approval.', $html, "Raw translation key found in $loc");
        }

        Livewire::test(ListLoanApprovalDocuments::class)
            ->assertCanSeeTableRecords([$doc])
            ->callTableAction('download', $doc)
            ->assertFileDownloaded($doc->filename());
    }

    public function test_prefilling_from_lead_populates_loan_approval_data(): void
    {
        $this->actingAs($this->admin());

        $lead = Lead::create([
            'locale' => 'fr',
            'country' => 'FR',
            'currency' => 'EUR',
            'amount' => 15000,
            'term_months' => 36,
            'monthly_income' => 3000,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Sophie',
            'last_name' => 'Bernard',
            'document_number' => 'FR-998877',
            'birthday' => '1988-11-20',
            'email' => 'sophie@example.test',
            'phone' => '+33655443322',
            'geo_zip' => '69001',
            'geo_province' => 'Rhône',
            'geo_city' => 'Lyon',
            'home_street' => 'Rue de la République',
            'home_number' => '15',
            'gender' => 'female',
            'loan_purpose' => 'renovation',
            'marital_status' => 'married',
            'company_sector' => 'services',
            'education' => 'higher_long',
            'home_status' => 'owner_mortgage',
            'regular_expenses' => 1000,
            'has_debt' => false,
            'has_car' => true,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        Livewire::test(CreateLoanApprovalDocument::class)
            ->set('data.lead_id', $lead->id)
            ->assertSet('data.first_name', 'Sophie')
            ->assertSet('data.last_name', 'Bernard')
            ->assertSet('data.phone', '+33655443322')
            ->assertSet('data.email', 'sophie@example.test')
            ->assertSet('data.amount', 15000)
            ->assertSet('data.term_months', 36)
            ->assertSet('data.currency', 'EUR')
            ->assertSet('data.locale', 'fr')
            ->assertSet('data.monthly_payment', fn ($val) => $val > 0);
    }
}
