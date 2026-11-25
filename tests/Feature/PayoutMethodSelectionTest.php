<?php

namespace Tests\Feature;

use App\Livewire\ApplyLoan;
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

    public function test_all_payout_methods_are_selection_only_without_any_account_input(): void
    {
        foreach (['transferencia', 'western_union', 'redpagos', 'abitab', 'midinero'] as $method) {
            $component = Livewire::test(ApplyLoan::class, ['locale' => 'es'])
                ->set('step', 4)
                ->set('payout_method', $method)
                ->set('bank_account', '')
                ->assertDontSeeHtml('name="bank_account"')
                ->assertDontSeeHtml('wire:model.live.blur="bank_account"')
                ->assertDontSeeHtml('wire:model.live.blur="mm_number"')
                ->assertDontSeeHtml('wire:model.live.blur="sort_code"')
                ->assertDontSeeHtml('wire:model.live.blur="uk_account_number"')
                ->call('next');

            $component->assertHasNoErrors();
            $this->assertSame(5, $component->get('step'));
        }
    }

    public function test_transferencia_on_foreign_country_works_with_selection_only(): void
    {
        $component = Livewire::test(ApplyLoan::class, ['locale' => 'sv'])
            ->set('step', 4)
            ->set('payout_method', 'transferencia')
            ->assertDontSeeHtml('wire:model.live.blur="bank_account"')
            ->call('next');

        $component->assertHasNoErrors();
        $this->assertSame(5, $component->get('step'));
    }
}
