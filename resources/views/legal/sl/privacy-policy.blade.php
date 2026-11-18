{{-- PREDLOGA: pred objavo jo mora pregledati pravnik. --}}
<h2>1. Upravljavec osebnih podatkov</h2>
@include('legal.partials.identity', ['l' => ['company' => 'Upravljavec', 'tax' => 'Davčna številka', 'register' => 'Sodni register', 'address' => 'Naslov', 'email' => 'Kontakt za zasebnost', 'credit' => 'Št. vpisa kreditnega posrednika']])
<p>Pooblaščena oseba za varstvo podatkov (če je imenovana): {!! $legal->dpo_email !!}.</p>

<h2>2. Podatki, ki jih obdelujemo</h2>
<ul>
    <li><strong>Prihodki:</strong> želeni znesek in ročnost, neto prihodek, zaposlitveni status, negativni zapisi v SISBON.</li>
    <li><strong>Osebni podatki:</strong> ime, priimek, EMŠO, datum rojstva, e-pošta in telefon.</li>
    <li><strong>Naslov:</strong> poštna številka, regija, kraj, ulica, hišna številka in stanovanje.</li>
    <li><strong>Bančni račun:</strong> IBAN.</li>
    <li><strong>Dodatne informacije:</strong> spol, namen, zakonski stan, dejavnost, izobrazba, bivanje, stroški, dolgovi in vozilo.</li>
    <li><strong>Tehnični podatki:</strong> IP-naslov, brskalnik, jezik, izvorna kampanja (UTM), datum in različica soglasja.</li>
</ul>
<p>EMŠO, IBAN in datum rojstva hranimo šifrirano.</p>

<h2>3. Nameni in pravne podlage</h2>
<ul>
    <li><strong>Obravnava vloge</strong> in posredovanje kreditodajalcem: predpogodbeni ukrepi na vašo zahtevo (člen 6(1)(b) GDPR).</li>
    <li><strong>Trženjska sporočila</strong>: samo z vašim soglasjem, ki ga lahko kadar koli prekličete (člen 6(1)(a)).</li>
    <li><strong>Preprečevanje goljufij in varnost</strong> (omejitve pošiljanja, tehnični dnevniki): zakoniti interes (člen 6(1)(f)).</li>
    <li><strong>Zakonske obveznosti</strong>: člen 6(1)(c).</li>
</ul>

<h2>4. Uporabniki podatkov</h2>
<ul>
    <li>Partnerski kreditodajalci, ki jim posredujemo vlogo: {!! $legal->partners !!}.</li>
    <li>Ponudnik gostovanja in e-pošte: {!! $legal->hosting_provider !!} kot obdelovalec.</li>
    <li>Državni organi, kadar to zahteva zakon.</li>
</ul>
<p>Vaših podatkov nikoli ne prodajamo. Polja obrazca se ne delijo z analitičnimi orodji.</p>

<h2>5. Prenos zunaj EU</h2>
<p>Podatki gostujejo v Evropskem gospodarskem prostoru. Če sprejmete statistične ali trženjske piškotke, lahko Google ali Meta obdelujeta podatke o brskanju zunaj EGP z zaščitnimi ukrepi po GDPR (sklep o ustreznosti ali standardne pogodbene klavzule).</p>

<h2>6. Rok hrambe</h2>
<p>Podatke iz vloge hranimo {!! $legal->retention !!}, nato omejeno v zakonskih zastaralnih rokih. Tehnične varnostne podatke hranimo le toliko, kolikor je nujno.</p>

<h2>7. Vaše pravice</h2>
<p>Imate pravico do dostopa, popravka, izbrisa, ugovora, omejitve in prenosljivosti. Pišite na <a href="mailto:{!! $legal->contact_email !!}">{!! $legal->contact_email !!}</a> ali uporabite {!! $legal->accountLink('Moj račun') !!}, kjer lahko zahtevate izbris podatkov.</p>
<p>Pritožbo lahko vložite pri {!! $legal->authority() !!}.</p>

<h2>8. Avtomatizirano odločanje</h2>
<p>{!! $legal->brand() !!} ne sprejema odločitev, ki bi temeljile izključno na avtomatizirani obdelavi s pravnimi učinki. O posojilu odloča vsak kreditodajalec, ki vas obvesti o svoji obdelavi.</p>

<h2>9. Piškotki</h2>
<p>Glejte {!! $legal->link('cookie-policy', 'politiko piškotkov') !!}.</p>
