<?php

namespace Tests\Feature;

use App\Livewire\ApplyLoan;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class PayoutMethodSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_payout_options_contain_all_required_methods(): void
    {
        $component = Livewire::test(ApplyLoan::class, ['locale' => 'es']);
        $options = $component->instance()->payoutOptions();

        $this->assertArrayHasKey('transferencia', $options);
        $this->assertArrayHasKey('western_union', $options);
        $this->assertArrayHasKey('redpagos', $options);
        $this->assertArrayHasKey('abitab', $options);
        $this->assertArrayHasKey('midinero', $options);
    }

    public function test_western_union_can_be_selected_without_bank_account(): void
    {
        $component = Livewire::test(ApplyLoan::class, ['locale' => 'es'])
            ->set('step', 4)
            ->set('payout_method', 'western_union')
            ->set('bank_account', '')
            ->call('next');

        $component->assertHasNoErrors();
        $this->assertSame(5, $component->get('step'));
    }

    public function test_redpagos_can_be_selected_without_bank_account(): void
    {
        $component = Livewire::test(ApplyLoan::class, ['locale' => 'es'])
            ->set('step', 4)
            ->set('payout_method', 'redpagos')
            ->set('bank_account', '')
            ->call('next');

        $component->assertHasNoErrors();
        $this->assertSame(5, $component->get('step'));
    }

    public function test_abitab_can_be_selected_without_bank_account(): void
    {
        $component = Livewire::test(ApplyLoan::class, ['locale' => 'es'])
            ->set('step', 4)
            ->set('payout_method', 'abitab')
            ->set('bank_account', '')
            ->call('next');

        $component->assertHasNoErrors();
        $this->assertSame(5, $component->get('step'));
    }

    public function test_midinero_requires_card_or_account_number(): void
    {
        $component = Livewire::test(ApplyLoan::class, ['locale' => 'es'])
            ->set('step', 4)
            ->set('payout_method', 'midinero')
            ->set('bank_account', '')
            ->call('next');

        $component->assertHasErrors(['bank_account']);

        $component->set('bank_account', '1234567890123456')
            ->call('next')
            ->assertHasNoErrors();

        $this->assertSame(5, $component->get('step'));
    }

    public function test_transferencia_accepts_latin_america_or_foreign_account_numbers(): void
    {
        // Testing with an El Salvador account number (like the one that failed in user screenshot)
        $component = Livewire::test(ApplyLoan::class, ['locale' => 'sv'])
            ->set('step', 4)
            ->set('payout_method', 'transferencia')
            ->set('bank_account', 'SV00 0000 0000 0031 1448 3108')
            ->call('next');

        $component->assertHasNoErrors();
        $this->assertSame(5, $component->get('step'));
    }
}
