<?php

namespace Tests\Feature;

use App\Livewire\ApplyLoan;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HomeDesignTest extends TestCase
{
    use RefreshDatabase;

    private function review(array $attrs = []): Review
    {
        return Review::create($attrs + [
            'locale' => 'es', 'author_name' => 'Lucía P.', 'rating' => 5, 'title' => 'Muy claro',
            'body' => 'Texto real de prueba.', 'reviewed_at' => '2026-11-10', 'is_published' => true, 'is_verified' => true,
        ]);
    }

    public function test_calculator_uses_currency_defaults_while_bounds_are_placeholders(): void
    {
        $this->get('/es')->assertOk()
            ->assertSee('id="calc-amount"', false)->assertSee('min="1000" max="70000"', false)
            ->assertSee('id="calc-term"', false)->assertSee('min="3" max="120"', false)
            ->assertSee("Solicitar 10.000\u{a0}€", false);
        $this->get('/mg')->assertOk()->assertSee('min="100000" max="5000000"', false)->assertSee('min="3" max="120"', false);
    }

    public function test_calculator_uses_configured_bounds_only(): void
    {
        config(['venatomar.loan.es.amount_min' => 100, 'venatomar.loan.es.amount_max' => 5000,
            'venatomar.loan.es.term_min' => 3, 'venatomar.loan.es.term_max' => 12]);

        $this->get('/es')->assertOk()
            ->assertSee('id="calc-amount"', false)
            ->assertSee('min="100" max="5000"', false)
            ->assertSee('min="3" max="12"', false);
    }

    public function test_calculator_prefill_is_bounded(): void
    {
        config(['venatomar.loan.es.amount_min' => 100, 'venatomar.loan.es.amount_max' => 5000,
            'venatomar.loan.es.term_min' => 3, 'venatomar.loan.es.term_max' => 12]);

        Livewire::withQueryParams(['amount' => '2400', 'term' => '9'])
            ->test(ApplyLoan::class, ['locale' => 'es'])
            ->assertSet('amount', 2400)->assertSet('term_months', 9);

        Livewire::withQueryParams(['amount' => '999999', 'term' => 'abc'])
            ->test(ApplyLoan::class, ['locale' => 'es'])
            ->assertSet('amount', null)->assertSet('term_months', null);
    }

    public function test_reviews_grid_shows_title_verified_badge_and_real_average(): void
    {
        $this->review();
        $this->review(['author_name' => 'Jorge M.', 'rating' => 4, 'is_verified' => false]);

        $this->get('/es')->assertOk()
            ->assertSee('Muy claro')->assertSee('Verificado')
            ->assertSee('Fecha de la reseña:')->assertSee('10/11/26')
            ->assertSee('4,5/5', false)->assertSee('2 opiniones');
    }

    public function test_demo_reviews_never_show_outside_local(): void
    {
        $this->review(['source' => Review::DEMO_SOURCE, 'title' => 'Demo title']);

        $this->assertFalse(app()->environment('local'));
        $this->get('/es')->assertOk()->assertDontSee('Demo title')->assertDontSee('opiniones</span>', false);
    }

    public function test_demo_seeder_refuses_to_run_outside_local(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->seed(\Database\Seeders\DemoReviewsSeeder::class);
    }
}
