{{-- VZOR: pred zverejnením musí skontrolovať právnik. --}}
<h2>1. Čo sú cookies?</h2>
<p>Sú to malé súbory, ktoré stránka ukladá vo vašom prehliadači. {!! $legal->brand() !!} používa iba cookies uvedené v tabuľke. Štatistické a marketingové cookies sa aktivujú len s vaším súhlasom.</p>

<h2>2. Používané cookies</h2>
@include('legal.partials.cookies-table', ['t' => [
    'h' => ['Cookie', 'Účel', 'Doba uloženia', 'Prevádzkovateľ', 'Kategória'],
    'own' => 'Vlastné',
    'session' => 'Udržiava vašu reláciu a rozpracovaný formulár.',
    'xsrf' => 'Chráni formuláre pred podvrhnutými požiadavkami (CSRF).',
    'consent' => 'Pamätá si vašu voľbu cookies a verziu týchto zásad.',
    'utm' => 'Pamätá si kampaň, z ktorej ste prišli (len so súhlasom).',
    'ga' => 'Google Analytics: súhrnné meranie návštevnosti.',
    'ads' => 'Google Ads: meranie konverzií.',
    'meta' => 'Meta Pixel: meranie kampaní.',
    'minutes' => ':n minút nečinnosti',
    'days' => ':n dní',
    'provider' => 'Podľa poskytovateľa',
    'necessary' => 'Nevyhnutné', 'statistics' => 'Štatistické', 'marketing' => 'Marketingové',
    'none' => 'Momentálne sa neukladajú žiadne cookies tretích strán.',
]])

<h2>3. Správa súhlasu</h2>
<p>Svoju voľbu môžete kedykoľvek zmeniť tlačidlom „Nastavenia cookies“ v pätičke. Cookies môžete vymazať aj v nastaveniach prehliadača.</p>
<p>Viac informácií v dokumente {!! $legal->link('privacy-policy', 'Ochrana osobných údajov') !!}.</p>
