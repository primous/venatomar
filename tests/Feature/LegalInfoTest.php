<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalInfoTest extends TestCase
{
    use RefreshDatabase;

    public function test_legal_terms_page_displays_official_company_information(): void
    {
        $response = $this->get('/es/legal/terms-and-conditions');

        $response->assertStatus(200);
        $response->assertSee('Leads for Finance S.L.U');
        $response->assertSee('B67406025');
        $response->assertSee('tome 46865, folio 53, feuille B533516');
        $response->assertSee('Carrer d’Estruc, 9, 08002 Barcelone');
        $response->assertSee('info@venatomar.com');
        $response->assertSee('B12345678');
    }

    public function test_spanish_slug_legal_terms_page_displays_official_company_information(): void
    {
        $response = $this->get('/es/legal/terminos-y-condiciones');

        $response->assertStatus(200);
        $response->assertSee('Leads for Finance S.L.U');
        $response->assertSee('B67406025');
        $response->assertSee('tome 46865, folio 53, feuille B533516');
        $response->assertSee('Carrer d’Estruc, 9, 08002 Barcelone');
        $response->assertSee('info@venatomar.com');
        $response->assertSee('B12345678');
    }

    public function test_footer_displays_official_company_details_and_tax_id(): void
    {
        $response = $this->get('/es');

        $response->assertStatus(200);
        $response->assertSee('Leads for Finance S.L.U');
        $response->assertSee('Carrer d’Estruc, 9, 08002 Barcelone');
        $response->assertSee('B67406025');
        $response->assertSee('B12345678');
    }

    public function test_privacy_policy_page_displays_official_company_information(): void
    {
        $response = $this->get('/es/legal/politica-de-privacidad');

        $response->assertStatus(200);
        $response->assertSee('Leads for Finance S.L.U');
        $response->assertSee('B67406025');
        $response->assertSee('Carrer d’Estruc, 9, 08002 Barcelone');
    }
}
