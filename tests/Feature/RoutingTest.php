<?php

namespace Tests\Feature;

use App\Support\Venatomar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_spanish(): void
    {
        $this->get('/')->assertRedirect('/es');
    }

    public function test_unknown_locale_is_404(): void
    {
        $this->get('/fr')->assertNotFound();
        $this->get('/fr/apply-loan')->assertNotFound();
        $this->get('/xx/legal/privacy-policy')->assertNotFound();
    }

    public static function locales(): array
    {
        $config = require __DIR__.'/../../config/venatomar.php';

        return array_map(fn ($l) => [$l], array_keys($config['locales']));
    }

    #[DataProvider('locales')]
    public function test_public_pages_render_in_every_locale(string $locale): void
    {
        foreach (['', '/apply-loan', '/my-account', '/legal/terms-and-conditions', '/legal/privacy-policy', '/legal/cookie-policy'] as $path) {
            $this->get("/$locale$path")
                ->assertOk()
                ->assertSee('<html lang="'.$locale.'">', false);
        }
    }

    public function test_home_has_seo_tags_and_a_single_h1(): void
    {
        $html = $this->get('/es')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertStringContainsString('rel="canonical"', $html);
        foreach (array_keys(Venatomar::locales()) as $locale) {
            $this->assertStringContainsString('hreflang="'.$locale.'-', $html);
        }
        $this->assertStringContainsString('"@type":"FAQPage"', $html);
        $this->assertStringContainsString('{"@context":"https://schema.org"', $html);
        $this->assertStringNotContainsString('noindex', $html);
        // Tracking scripts are never in the HTML: they are injected only after consent.
        $this->assertStringNotContainsString('googletagmanager.com/gtag', $html);
        $this->assertStringNotContainsString('connect.facebook.net', $html);
    }

    public function test_private_pages_are_noindex(): void
    {
        $this->get('/es/my-account')->assertSee('noindex', false);
    }

    public function test_dashboard_requires_login(): void
    {
        $this->get('/de/my-account/dashboard')->assertRedirect('/de/my-account');
    }

    public function test_thanks_page_needs_a_submitted_request(): void
    {
        $this->get('/es/apply-loan/gracias')->assertNotFound();
    }

    public function test_sitemap_and_robots(): void
    {
        $this->get('/sitemap.xml')->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('/sk/legal/cookie-policy', false)
            ->assertSee('hreflang="el-GR"', false)
            ->assertDontSee('my-account', false);

        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /es/my-account')->assertSee('Sitemap:');
    }

    public function test_no_invented_figures_when_config_is_placeholder(): void
    {
        $html = $this->get('/es')->getContent();
        // Placeholders hide the figures instead of printing them.
        $this->assertStringNotContainsString('[MONTANT_MAX', $html);
        $this->assertStringNotContainsString('TAE mínima', $html);
        $this->assertStringContainsString('préstamo personal directo', $html);
    }

    public function test_figures_are_shown_from_config_only(): void
    {
        config([
            'venatomar.loan.es.amount_min' => 100, 'venatomar.loan.es.amount_max' => 5000,
            'venatomar.loan.es.term_min' => 3, 'venatomar.loan.es.term_max' => 60,
            'venatomar.loan.es.apr_min' => 5.5, 'venatomar.loan.es.apr_max' => 24.9,
            'venatomar.loan.es.example' => ['amount' => 1000, 'term' => 12, 'net' => 1000, 'total' => 1120.5, 'apr' => 21.7],
        ]);

        $this->get('/es')
            ->assertSee('Préstamos personales online de hasta 5.000', false)
            ->assertSee('TAE mínima 5,5 %', false)
            ->assertSee('importe total a devolver 1.120,50', false);
    }

    public function test_cookie_policy_lists_exactly_the_cookies_set(): void
    {
        $this->get('/es/legal/cookie-policy')->assertOk()
            ->assertSee(config('session.cookie'))->assertSee('XSRF-TOKEN')->assertSee('pf_consent')->assertSee('pf_utm')
            ->assertSee(config('session.lifetime').' minutos')->assertSee('365 días')
            ->assertDontSee('_ga')->assertDontSee('_fbp');

        config(['venatomar.tracking.ga4_id' => 'G-TEST']);
        $this->get('/en/legal/cookie-policy')->assertSee('_ga, _ga_*', false)->assertDontSee('_fbp');
    }

    public function test_legal_pages_contain_the_representative_example_source(): void
    {
        $this->get('/it/legal/terms-and-conditions')->assertSee('prestiti personali online');
        $this->get('/nl/legal/privacy-policy')->assertSee('nooit je BSN', false);
    }

    public function test_rating_badge_hidden_unless_enabled_with_real_values(): void
    {
        config(['venatomar.reviews.enabled' => true]); // values are still placeholders
        $this->get('/es')->assertDontSee('/5</strong>', false);
    }
}
