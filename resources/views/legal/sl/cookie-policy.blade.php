{{-- PREDLOGA: pred objavo jo mora pregledati pravnik. --}}
<h2>1. Kaj so piškotki?</h2>
<p>Piškotki so majhne datoteke, ki jih stran shrani v vaš brskalnik. {!! $legal->brand() !!} uporablja samo piškotke iz spodnje tabele. Statistični in trženjski piškotki se vklopijo le, če jih sprejmete.</p>

<h2>2. Uporabljeni piškotki</h2>
@include('legal.partials.cookies-table', ['t' => [
    'h' => ['Piškotek', 'Namen', 'Trajanje', 'Upravljavec', 'Kategorija'],
    'own' => 'Lastni',
    'session' => 'Ohranja vašo sejo in osnutek obrazca.',
    'xsrf' => 'Ščiti obrazce pred ponarejenimi zahtevami (CSRF).',
    'consent' => 'Zapomni si vašo izbiro piškotkov in različico te politike.',
    'utm' => 'Zapomni si kampanjo, iz katere ste prišli (samo po soglasju).',
    'ga' => 'Google Analytics: zbirno merjenje obiska.',
    'ads' => 'Google Ads: merjenje konverzij.',
    'meta' => 'Meta Pixel: merjenje kampanj.',
    'minutes' => ':n minut nedejavnosti',
    'days' => ':n dni',
    'provider' => 'Po navedbah ponudnika',
    'necessary' => 'Nujni', 'statistics' => 'Statistični', 'marketing' => 'Trženjski',
    'none' => 'Trenutno se ne namešča noben piškotek tretjih oseb.',
]])

<h2>3. Upravljanje soglasja</h2>
<p>Izbiro lahko kadar koli spremenite z gumbom »Nastavitve piškotkov« v nogi strani. Piškotke lahko izbrišete tudi v nastavitvah brskalnika.</p>
<p>Več informacij v {!! $legal->link('privacy-policy', 'politiki zasebnosti') !!}.</p>
