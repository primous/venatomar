{{-- SJABLOON: vóór publicatie laten controleren door een jurist. --}}
<h2>1. Wat zijn cookies?</h2>
<p>Cookies zijn kleine bestanden die de website in je browser opslaat. {!! $legal->brand() !!} gebruikt alleen de cookies hieronder. Statistische en marketingcookies worden alleen geplaatst als je ze accepteert.</p>

<h2>2. Gebruikte cookies</h2>
@include('legal.partials.cookies-table', ['t' => [
    'h' => ['Cookie', 'Doel', 'Bewaartermijn', 'Eigenaar', 'Categorie'],
    'own' => 'Eigen cookie',
    'session' => 'Houdt je sessie en het concept van het formulier bij.',
    'xsrf' => 'Beschermt formulieren tegen vervalste verzoeken (CSRF).',
    'consent' => 'Onthoudt je cookiekeuze en de versie van dit beleid.',
    'utm' => 'Onthoudt de campagne waarmee je bezoek begon (alleen na toestemming).',
    'ga' => 'Google Analytics: geaggregeerde bezoekersmeting.',
    'ads' => 'Google Ads: conversiemeting.',
    'meta' => 'Meta Pixel: campagnemeting.',
    'minutes' => ':n minuten inactiviteit',
    'days' => ':n dagen',
    'provider' => 'Volgens de aanbieder',
    'necessary' => 'Noodzakelijk', 'statistics' => 'Statistiek', 'marketing' => 'Marketing',
    'none' => 'Er worden momenteel geen cookies van derden geplaatst.',
]])

<h2>3. Toestemming beheren</h2>
<p>Je kunt je keuze altijd wijzigen met de knop 'Cookie-instellingen' onderaan de pagina. Je kunt cookies ook verwijderen via je browserinstellingen.</p>
<p>Meer informatie in ons {!! $legal->link('privacy-policy', 'privacybeleid') !!}.</p>
