{{-- VZOR: pred zverejnením musí skontrolovať právnik. --}}
<h2>1. Prevádzkovateľ osobných údajov</h2>
@include('legal.partials.identity', ['l' => ['company' => 'Prevádzkovateľ', 'tax' => 'IČO / DIČ', 'register' => 'Obchodný register', 'address' => 'Adresa', 'email' => 'Kontakt pre ochranu údajov', 'credit' => 'Č. registrácie finančného sprostredkovateľa']])
<p>Zodpovedná osoba (ak bola určená): {!! $legal->dpo_email !!}.</p>

<h2>2. Spracúvané údaje</h2>
<ul>
    <li><strong>Príjem:</strong> požadovaná suma a doba, čistý príjem, pracovné zaradenie, záznamy v registroch SRBI / NRKI.</li>
    <li><strong>Osobné údaje:</strong> meno, priezvisko, rodné číslo, dátum narodenia, e-mail a telefón.</li>
    <li><strong>Adresa:</strong> PSČ, kraj, obec, ulica, číslo domu a byt.</li>
    <li><strong>Bankový účet:</strong> IBAN.</li>
    <li><strong>Doplňujúce informácie:</strong> pohlavie, účel, rodinný stav, odvetvie, vzdelanie, bývanie, výdavky, dlhy a vozidlo.</li>
    <li><strong>Technické údaje:</strong> IP adresa, prehliadač, jazyk, kampaň pôvodu (UTM), dátum a verzia súhlasu.</li>
</ul>
<p>Rodné číslo, IBAN a dátum narodenia ukladáme šifrovane.</p>

<h2>3. Účely a právne základy</h2>
<ul>
    <li><strong>Vybavenie žiadosti</strong> a jej odovzdanie veriteľom: predzmluvné opatrenia na vašu žiadosť (čl. 6 ods. 1 písm. b GDPR).</li>
    <li><strong>Obchodná komunikácia</strong>: len s vaším súhlasom, ktorý môžete kedykoľvek odvolať (písm. a).</li>
    <li><strong>Predchádzanie podvodom a bezpečnosť</strong> (limity odoslaní, technické záznamy): oprávnený záujem (písm. f).</li>
    <li><strong>Zákonné povinnosti</strong>: písm. c.</li>
</ul>

<h2>4. Príjemcovia</h2>
<ul>
    <li>Partnerskí veritelia, ktorým sa žiadosť odovzdá: {!! $legal->partners !!}.</li>
    <li>Poskytovateľ hostingu a e-mailu: {!! $legal->hosting_provider !!} ako sprostredkovateľ.</li>
    <li>Orgány verejnej moci, ak to vyžaduje zákon.</li>
</ul>
<p>Vaše údaje nikdy nepredávame. Polia formulára sa nezdieľajú s analytickými nástrojmi.</p>

<h2>5. Prenos mimo EÚ</h2>
<p>Údaje sú uložené v Európskom hospodárskom priestore. Ak prijmete štatistické alebo marketingové cookies, Google alebo Meta môžu spracúvať údaje o prehliadaní mimo EHP so zárukami podľa GDPR (rozhodnutie o primeranosti alebo štandardné zmluvné doložky).</p>

<h2>6. Doba uchovávania</h2>
<p>Údaje zo žiadosti uchovávame {!! $legal->retention !!} a potom obmedzene počas zákonných premlčacích lehôt. Technické bezpečnostné údaje uchovávame len nevyhnutne dlho.</p>

<h2>7. Vaše práva</h2>
<p>Máte právo na prístup, opravu, vymazanie, námietku, obmedzenie a prenosnosť. Napíšte na <a href="mailto:{!! $legal->contact_email !!}">{!! $legal->contact_email !!}</a> alebo použite {!! $legal->accountLink('Môj účet') !!}, kde môžete požiadať o vymazanie údajov.</p>
<p>Sťažnosť môžete podať na {!! $legal->authority() !!}.</p>

<h2>8. Automatizované rozhodovanie</h2>
<p>{!! $legal->brand() !!} neprijíma rozhodnutia založené výlučne na automatizovanom spracúvaní s právnymi účinkami. O pôžičke rozhoduje každý veriteľ, ktorý vás informuje o vlastnom spracúvaní.</p>

<h2>9. Cookies</h2>
<p>Pozrite si {!! $legal->link('cookie-policy', 'zásady cookies') !!}.</p>
