<?php

namespace Tests\Feature;

use App\Livewire\ApplyLoan;
use App\Models\Lead;
use App\Rules\Checksums;
use App\Support\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SalvadorUruguayCountryTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_salvador_and_uruguay_countries_are_configured_properly(): void
    {
        $sv = new Country('sv');
        $this->assertSame('El Salvador', $sv->name);
        $this->assertSame('USD', $sv->currency);
        $this->assertSame('+503', $sv->phonePrefix);
        $this->assertSame('sv_dui', $sv->document['type']);
        $this->assertCount(14, $sv->regions());
        $this->assertArrayHasKey('SS', $sv->regions());
        $this->assertSame('San Salvador', $sv->regions()['SS']);

        $uy = new Country('uy');
        $this->assertSame('Uruguay', $uy->name);
        $this->assertSame('UYU', $uy->currency);
        $this->assertSame('+598', $uy->phonePrefix);
        $this->assertSame('uy_ci', $uy->document['type']);
        $this->assertCount(19, $uy->regions());
        $this->assertArrayHasKey('MO', $uy->regions());
        $this->assertSame('Montevideo', $uy->regions()['MO']);
    }

    public function test_dui_and_ci_checksum_algorithms(): void
    {
        // El Salvador DUI: 01234567 -> sum: 0*9 + 1*8 + 2*7 + 3*6 + 4*5 + 5*4 + 6*3 + 7*2 = 8+14+18+20+20+18+14 = 112 -> 112 % 10 = 2 -> 10-2 = 8
        $this->assertTrue(Checksums::svDui('012345678'));
        $this->assertTrue(Checksums::svDui('01234567-8'));
        $this->assertFalse(Checksums::svDui('012345679'));

        // Uruguay CI: 1234567 -> 7 digits (1234567) -> weights: 1*2 + 2*9 + 3*8 + 4*7 + 5*6 + 6*3 + 7*4 = 2+18+24+28+30+18+28 = 148 -> 148 % 10 = 8 -> 10-8 = 2
        $this->assertTrue(Checksums::uyCi('12345672'));
        $this->assertTrue(Checksums::uyCi('1.234.567-2'));
        $this->assertFalse(Checksums::uyCi('12345673'));
    }

    public function test_el_salvador_and_uruguay_public_routes_render(): void
    {
        $this->get('/sv')->assertOk()->assertSee('El Salvador');
        $this->get('/sv/apply-loan')->assertOk()->assertSee('USD');
        $this->get('/sv/solicitar-prestamo')->assertOk()->assertSee('USD');

        $this->get('/uy')->assertOk()->assertSee('Uruguay');
        $this->get('/uy/apply-loan')->assertOk()->assertSee('UYU');
        $this->get('/uy/solicitar-prestamo')->assertOk()->assertSee('UYU');
    }

    public function test_country_selector_updates_loan_parameters_dynamically(): void
    {
        $component = Livewire::test(ApplyLoan::class, ['locale' => 'es']);
        $this->assertSame('es', $component->get('selected_country'));

        // Switch to El Salvador
        $component->set('selected_country', 'sv');
        $this->assertSame('sv', $component->get('selected_country'));
        $this->assertSame('USD', $component->instance()->country()->currency);
        $this->assertSame('+503', $component->instance()->country()->phonePrefix);

        // Switch to Uruguay
        $component->set('selected_country', 'uy');
        $this->assertSame('uy', $component->get('selected_country'));
        $this->assertSame('UYU', $component->instance()->country()->currency);
        $this->assertSame('+598', $component->instance()->country()->phonePrefix);
    }
}
