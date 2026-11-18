<?php

namespace Tests\Feature;

use App\Filament\Resources\InsuranceDocuments\Pages\CreateInsuranceDocument;
use App\Filament\Resources\InsuranceDocuments\Pages\ListInsuranceDocuments;
use App\Models\InsuranceDocument;
use App\Models\User;
use App\Support\InsurancePdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InsuranceDocumentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'secret-password-123']);
    }

    public function test_every_document_language_has_the_same_keys(): void
    {
        $flatten = fn (array $a) => collect(\Illuminate\Support\Arr::dot($a))->keys()->sort()->values()->all();
        $reference = $flatten(require lang_path('fr/insurance.php'));

        foreach (array_keys(InsurancePdf::LOCALES) as $locale) {
            $this->assertSame($reference, $flatten(require lang_path("$locale/insurance.php")), "Keys differ in $locale");
        }
    }

    public function test_admin_page_renders_in_french(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin/insurance-documents')->assertOk()->assertSee('Assurance de prêt');
        $this->get('/admin/insurance-documents/create')->assertOk()->assertSee('Langue du document');
    }

    public function test_admin_generates_and_downloads_a_pdf(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateInsuranceDocument::class)
            ->fillForm([
                'locale' => 'el', 'country' => 'Κύπρος', 'currency' => 'eur', 'first_name' => 'Maria', 'last_name' => 'Papadopoulou',
                'phone' => '+306912345678', 'amount' => 5000, 'term_months' => 24, 'coverage_percent' => 100,
                'guarantees' => ['death', 'itt'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $doc = InsuranceDocument::sole();
        $this->assertSame('EUR', $doc->currency);
        $this->assertSame('Κύπρος', $doc->country);
        $this->assertStringContainsString('5.000', $doc->money());
        $this->assertMatchesRegularExpression('/^VN-AS-\d{4}-\d{6}$/', $doc->reference);

        $pdf = InsurancePdf::render($doc);
        $this->assertStringStartsWith('%PDF', $pdf);
        $pageCount = preg_match_all('/\/Type\s*\/Page\b/', $pdf);
        $this->assertSame(1, $pageCount, 'Certificate should fit on a single page');

        Livewire::test(ListInsuranceDocuments::class)
            ->assertCanSeeTableRecords([$doc])
            ->callTableAction('download', $doc)
            ->assertFileDownloaded($doc->filename());
    }

    public function test_form_rejects_unknown_language_and_guarantee(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateInsuranceDocument::class)
            ->fillForm(['locale' => 'xx', 'country' => 'France', 'currency' => 'EUR', 'first_name' => 'A', 'last_name' => 'B', 'amount' => 100, 'term_months' => 6, 'coverage_percent' => 100, 'guarantees' => []])
            ->call('create')
            ->assertHasFormErrors(['locale', 'guarantees']);

        $this->assertSame(0, InsuranceDocument::count());
    }

    public function test_generates_official_insurance_certificate_in_french_and_spanish(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateInsuranceDocument::class)
            ->fillForm([
                'document_type' => 'certificate',
                'locale' => 'fr',
                'country' => 'France',
                'currency' => 'EUR',
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'phone' => '+33612345678',
                'email' => 'jean.dupont@example.test',
                'document_number' => '123456789',
                'birth_date' => '1985-06-15',
                'address' => '12 rue de la Paix, 75001 Paris',
                'amount' => 15000,
                'term_months' => 48,
                'effective_date' => '2026-11-16',
                'coverage_percent' => 100,
                'lender' => 'Banque Populaire',
                'guarantees' => ['death', 'ptia', 'itt', 'job_loss'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $doc = InsuranceDocument::where('first_name', 'Jean')->sole();
        $this->assertSame('certificate', $doc->document_type);
        $this->assertSame('Venatomar-Certificat-'.$doc->reference.'-FR.pdf', $doc->filename());
        $this->assertNotEmpty($doc->authCode());
        $this->assertNotNull($doc->expiryDate());

        $pdf = InsurancePdf::render($doc);
        $this->assertStringStartsWith('%PDF', $pdf);
        $pageCount = preg_match_all('/\/Type\s*\/Page\b/', $pdf);
        $this->assertSame(1, $pageCount, 'Certificate should fit on a single page');

        // Also test Spanish certificate
        $docEs = InsuranceDocument::create([
            'document_type' => 'certificate',
            'locale' => 'es',
            'country' => 'España',
            'currency' => 'EUR',
            'first_name' => 'Carlos',
            'last_name' => 'García',
            'phone' => '+34612345678',
            'amount' => 8000,
            'term_months' => 24,
            'coverage_percent' => 100,
            'guarantees' => ['death', 'itt'],
        ]);

        $pdfEs = InsurancePdf::render($docEs);
        $this->assertStringStartsWith('%PDF', $pdfEs);
        $this->assertSame(1, preg_match_all('/\/Type\s*\/Page\b/', $pdfEs));

        foreach (array_keys(InsurancePdf::LOCALES) as $loc) {
            $docTest = clone $docEs;
            $docTest->locale = $loc;
            $t = fn (string $key, array $replace = []) => trans('insurance.'.$key, $replace + ['brand' => 'Venatomar'], $loc);
            $html = view('pdf.certificate', [
                'doc' => $docTest,
                't' => $t,
                'logo' => '',
                'countryName' => 'España',
                'amount' => '8.000 €',
                'dateUpper' => '16 NOVIEMBRE 2026',
                'effectiveDate' => '16/11/2026',
                'expiryDate' => '16/11/2028',
                'guaranteesList' => 'Décès, PTIA',
                'issuerAddress' => 'Paseo de la Castellana 216, Madrid',
                'issuerPhone' => '+34 910 000 000',
                'issuerCity' => 'Madrid',
                'parapheSvg' => '',
                'stampAndSignatureSvg' => '',
                'rightSignatureSvg' => '',
                'authCode' => $docTest->authCode(),
            ])->render();

            $this->assertStringNotContainsString('insurance.cert.', $html, "Raw translation key found in $loc");

            $htmlInfo = view('pdf.insurance', [
                'doc' => $docTest,
                't' => $t,
                'logo' => '',
                'countryName' => 'España',
                'amount' => '8.000 €',
                'dateUpper' => '16 NOVIEMBRE 2026',
                'effectiveDate' => '16/11/2026',
                'expiryDate' => '16/11/2028',
                'guaranteesList' => 'Décès, PTIA',
                'issuerAddress' => 'Paseo de la Castellana 216, Madrid',
                'issuerPhone' => '+34 910 000 000',
                'issuerCity' => 'Madrid',
                'parapheSvg' => '',
                'stampAndSignatureSvg' => '',
                'rightSignatureSvg' => '',
                'authCode' => $docTest->authCode(),
            ])->render();

            $this->assertStringNotContainsString('insurance.info.', $htmlInfo, "Raw translation key found in info for $loc");
        }

        // Test rendering information sheet PDF on 2 pages
        $docInfo = clone $docEs;
        $docInfo->document_type = 'information';
        $pdfInfo = InsurancePdf::render($docInfo, 'information');
        $this->assertStringStartsWith('%PDF', $pdfInfo);
        $this->assertSame(2, preg_match_all('/\/Type\s*\/Page\b/', $pdfInfo), 'Info sheet should fit on exactly 2 pages');
    }

    public function test_prefilling_from_lead_populates_client_and_loan_data(): void
    {
        $this->actingAs($this->admin());

        $lead = \App\Models\Lead::create([
            'locale' => 'es',
            'country' => 'ES',
            'currency' => 'EUR',
            'amount' => 12000,
            'term_months' => 36,
            'monthly_income' => 2500,
            'employment_status' => 'employee',
            'defaulter_list' => false,
            'first_name' => 'Alejandro',
            'last_name' => 'Morales',
            'document_number' => '12345678Z',
            'birthday' => '1990-03-21',
            'email' => 'alejandro@example.test',
            'phone' => '+34600112233',
            'geo_zip' => '28001',
            'geo_province' => 'Madrid',
            'geo_city' => 'Madrid',
            'home_street' => 'Calle Mayor',
            'home_number' => '10',
            'gender' => 'male',
            'loan_purpose' => 'car',
            'marital_status' => 'single',
            'company_sector' => 'technology',
            'education' => 'higher_long',
            'home_status' => 'tenant',
            'regular_expenses' => 800,
            'has_debt' => false,
            'has_car' => true,
            'consent_at' => now(),
            'consent_version' => '1.0',
        ]);

        Livewire::test(CreateInsuranceDocument::class)
            ->set('data.lead_id', $lead->id)
            ->assertSet('data.first_name', 'Alejandro')
            ->assertSet('data.last_name', 'Morales')
            ->assertSet('data.phone', '+34600112233')
            ->assertSet('data.email', 'alejandro@example.test')
            ->assertSet('data.amount', 12000)
            ->assertSet('data.term_months', 36)
            ->assertSet('data.currency', 'EUR')
            ->assertSet('data.locale', 'es');
    }
}
