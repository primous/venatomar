<?php

namespace Tests\Feature;

use App\Filament\Resources\LoanContracts\Pages\CreateLoanContract;
use App\Filament\Resources\LoanContracts\Pages\ListLoanContracts;
use App\Models\Lead;
use App\Models\LoanContract;
use App\Models\User;
use App\Support\InsurancePdf;
use App\Support\LoanContractPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoanContractTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'secret-password-123']);
    }

    public function test_every_loan_contract_language_has_the_same_keys(): void
    {
        $flatten = fn (array $a) => collect(\Illuminate\Support\Arr::dot($a))->keys()->sort()->values()->all();
        $reference = $flatten(require lang_path('fr/loan_contract.php'));

        foreach (array_keys(InsurancePdf::LOCALES) as $locale) {
            $this->assertSame($reference, $flatten(require lang_path("$locale/loan_contract.php")), "Keys differ in $locale");
        }
    }

    public function test_admin_page_renders_in_french(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin/loan-contracts')->assertOk()->assertSee('Contrat de prêt');
        $this->get('/admin/loan-contracts/create')->assertOk()->assertSee('Langue du contrat');
    }

    public function test_admin_generates_and_downloads_loan_contract_pdf(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateLoanContract::class)
            ->fillForm([
                'locale' => 'fr',
                'country' => 'France',
                'currency' => 'EUR',
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'phone' => '+33611223344',
                'email' => 'jean.dupont@example.test',
                'document_number' => '987654321',
                'birth_date' => '1985-05-15',
                'address' => '12 rue de la Paix, 75002 Paris',
                'amount' => 25000,
                'term_months' => 60,
                'interest_rate' => 3.50,
                'bank_account' => 'FR7630006000011234567890189',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $contract = LoanContract::sole();
        $this->assertSame('EUR', $contract->currency);
        $this->assertSame('France', $contract->country);
        $this->assertSame('Jean', $contract->first_name);
        $this->assertGreaterThan(0, $contract->monthly_payment);
        $this->assertGreaterThan(0, $contract->total_interest);
        $this->assertGreaterThan(0, $contract->total_repayable);
        $this->assertMatchesRegularExpression('/^VN-CTR-\d{4}-\d{6}$/', $contract->reference);

        $pdf = LoanContractPdf::render($contract);
        $this->assertStringStartsWith('%PDF', $pdf);
        $pageCount = preg_match_all('/\/Type\s*\/Page\b/', $pdf);
        $this->assertSame(1, $pageCount, 'Loan contract document should fit on a single page');

        foreach (array_keys(InsurancePdf::LOCALES) as $loc) {
            $contractTest = clone $contract;
            $contractTest->locale = $loc;
            $t = fn (string $key, array $replace = []) => trans('loan_contract.'.$key, $replace + ['brand' => 'Venatomar'], $loc);
            $html = view('pdf.loan-contract', [
                'contract' => $contractTest,
                't' => $t,
                'logo' => '',
                'countryName' => 'France',
                'amount' => '10.000 €',
                'monthlyPayment' => '220 €',
                'totalInterest' => '560 €',
                'totalRepayable' => '10.560 €',
                'date' => '16/11/2026',
                'dateUpper' => '16 NOVEMBRE 2026',
                'contractDate' => '16/11/2026',
                'firstPaymentDate' => '16/12/2026',
                'contactEmail' => 'info@venatomar.com',
                'issuerAddress' => 'Paseo de la Castellana 216, Madrid',
                'issuerPhone' => '+34 910 000 000',
                'issuerCity' => 'Madrid',
                'parapheSvg' => '',
                'stampAndSignatureSvg' => '',
                'rightSignatureSvg' => '',
            ])->render();

            $this->assertStringNotContainsString('loan_contract.', $html, "Raw translation key found in $loc");
        }

        Livewire::test(ListLoanContracts::class)
            ->assertCanSeeTableRecords([$contract])
            ->callTableAction('download', $contract)
            ->assertFileDownloaded($contract->filename());
    }

    public function test_prefilling_from_lead_populates_loan_contract_data(): void
    {
        $this->actingAs($this->admin());

        $lead = Lead::create([
            'locale' => 'fr',
            'country' => 'FR',
            'currency' => 'EUR',
            'amount' => 18000,
            'term_months' => 48,
            'monthly_income' => 3200,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Camille',
            'last_name' => 'Laurent',
            'document_number' => 'FR-123456',
            'birthday' => '1990-03-10',
            'email' => 'camille@example.test',
            'phone' => '+33699887766',
            'geo_zip' => '33000',
            'geo_province' => 'Gironde',
            'geo_city' => 'Bordeaux',
            'home_street' => 'Cours de l’Intendance',
            'home_number' => '42',
            'gender' => 'female',
            'loan_purpose' => 'personal',
            'marital_status' => 'single',
            'company_sector' => 'education',
            'education' => 'higher_long',
            'home_status' => 'tenant',
            'regular_expenses' => 850,
            'has_debt' => false,
            'has_car' => true,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        Livewire::test(CreateLoanContract::class)
            ->set('data.lead_id', $lead->id)
            ->assertSet('data.first_name', 'Camille')
            ->assertSet('data.last_name', 'Laurent')
            ->assertSet('data.phone', '+33699887766')
            ->assertSet('data.email', 'camille@example.test')
            ->assertSet('data.amount', 18000)
            ->assertSet('data.term_months', 48)
            ->assertSet('data.currency', 'EUR')
            ->assertSet('data.locale', 'fr')
            ->assertSet('data.monthly_payment', fn ($val) => $val > 0)
            ->assertSet('data.total_repayable', fn ($val) => $val > 18000)
            ->assertSet('data.total_interest', fn ($val) => $val > 0);
    }
}
