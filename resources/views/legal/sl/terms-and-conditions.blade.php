{{-- PREDLOGA: pred objavo jo mora pregledati pravnik. --}}
<h2>1. Upravljavec spletne strani</h2>
<p>Spletno stran {!! $legal->brand() !!} ({!! $legal->url() !!}) upravlja:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Firma', 'tax' => 'Davčna številka', 'register' => 'Sodni register', 'address' => 'Sedež', 'email' => 'Kontaktni e-naslov', 'credit' => 'Št. vpisa kreditnega posrednika']])

<h2>2. Predmet storitve</h2>
<p>{!! $legal->brand() !!} je brezplačna storitev primerjave in posredovanja potrošniških posojil. Sprejme vlogo uporabnika in jo posreduje partnerskim kreditodajalcem. <strong>{!! $legal->brand() !!} ne odobrava posojil</strong>, ne odloča o odobritvi in ni stranka kreditne pogodbe.</p>

<h2>3. Brezplačnost za uporabnika</h2>
<p>Uporaba primerjalnika je brezplačna. {!! $legal->brand() !!} od uporabnika nikoli ne zahteva plačila, predplačila ali stroškov obdelave.</p>

<h2>4. Plačilo</h2>
<p>{!! $legal->brand() !!} lahko prejme provizijo od partnerskih kreditodajalcev, ko jim posreduje vlogo ali ko je sklenjena pogodba. Provizija ne poviša stroškov posojila za uporabnika. Partnerji: {!! $legal->partners !!}.</p>

<h2>5. Pogoji posojil</h2>
<p>Znesek, ročnost, obrestno mero, EOM in druge pogoje določi izključno vsak kreditodajalec po presoji vloge. Pred podpisom vam mora izročiti standardizirane evropske informacije o potrošniškem kreditu in pogodbo.</p>

<h2>6. Reprezentativni primer</h2>
<x-legal-example />

<h2>7. Obveznosti uporabnika</h2>
<ul>
    <li>Biti polnoleten in ravnati v svojem imenu.</li>
    <li>Navajati točne, popolne in posodobljene podatke.</li>
    <li>Ne uporabljati podatkov tretjih oseb in ne zlorabljati storitve.</li>
</ul>

<h2>8. Odgovornost</h2>
<p>{!! $legal->brand() !!} ne jamči odobritve vloge niti ponujenih pogojev. Ne odgovarja za odločitve, ponudbe ali pogodbe kreditodajalcev niti za škodo zaradi netočnih podatkov uporabnika. Stran je lahko začasno nedosegljiva zaradi vzdrževanja.</p>

<h2>9. Pravica do odstopa</h2>
<p>Od kreditne pogodbe, sklenjene s kreditodajalcem, lahko odstopite v 14 dneh brez navedbe razloga. O tem obvestite kreditodajalca in vrnete glavnico z obrestmi za čas črpanja.</p>

<h2>10. Intelektualna lastnina</h2>
<p>Besedila, oblikovanje, logotipi in koda strani so last {!! $legal->company_name !!} ali njenih dajalcev licenc. Razmnoževanje brez dovoljenja ni dovoljeno.</p>

<h2>11. Spremembe pogojev</h2>
<p>Ti pogoji se lahko spremenijo. Velja različica, objavljena ob oddaji vloge.</p>

<h2>12. Veljavno pravo</h2>
<p>Za te pogoje velja pravo Republike Slovenije. Pristojno je sodišče v {!! $legal->jurisdiction !!}, brez poseganja v pravice potrošnika.</p>
