{{-- PREDLOGA: pred objavo jo mora pregledati pravnik. --}}
<h2>1. Upravljavec spletne strani</h2>
<p>Spletno stran {!! $legal->brand() !!} ({!! $legal->url() !!}) upravlja:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Firma', 'tax' => 'Davčna številka', 'register' => 'Sodni register', 'address' => 'Sedež', 'email' => 'Kontaktni e-naslov', 'credit' => 'Št. vpisa kreditnega posrednika']])

<h2>2. Predmet storitve</h2>
<p>{!! $legal->brand() !!} je neposredni ponudnik spletnih osebnih posojil. Obravnava vloge za posojilo in neposredno odobrava financiranje potrošnikom v skladu s sklenjeno kreditno pogodbo.</p>

<h2>3. Brezplačna presoja vloge</h2>
<p>Oddaja vloge in kreditna ocena sta popolnoma brezplačni. {!! $legal->brand() !!} od uporabnika nikoli ne zahteva predplačil, vnaprejšnjih stroškov obdelave ali provizij.</p>

<h2>4. Neposredno odobravanje posojil</h2>
<p>{!! $legal->brand() !!} neposredno odobri in izplača sredstva po uspešni preveritvi podatkov ter podpisu pogodbe.</p>

<h2>5. Pogoji posojil</h2>
<p>Znesek, ročnost, obrestna mera, EOM in vsi pogodbeni pogoji so jasno opredeljeni. Pred podpisom prejmete standardne evropske informacije (SECCI) in kreditno pogodbo.</p>

<h2>6. Reprezentativni primer</h2>
<x-legal-example />

<h2>7. Obveznosti uporabnika</h2>
<ul>
    <li>Biti polnoleten in ravnati v svojem imenu.</li>
    <li>Navajati točne, popolne in posodobljene podatke.</li>
    <li>Ne uporabljati podatkov tretjih oseb in ne zlorabljati storitve.</li>
</ul>

<h2>8. Odgovornost</h2>
<p>{!! $legal->brand() !!} presoja vsako vlogo po načelih odgovornega kreditiranja. Odobritev posojila je odvisna od verodostojnosti podatkov. Stran je lahko začasno nedosegljiva zaradi vzdrževanja.</p>

<h2>9. Pravica do odstopa</h2>
<p>Od kreditne pogodbe, sklenjene z {!! $legal->brand() !!}, lahko odstopite v 14 koledarskih dneh brez navedbe razloga, z vračilom prejetega zneska in natečenih obresti.</p>

<h2>10. Intelektualna lastnina</h2>
<p>Besedila, oblikovanje, logotipi in koda strani so last {!! $legal->company_name !!} ali njenih dajalcev licenc. Razmnoževanje brez dovoljenja ni dovoljeno.</p>

<h2>11. Spremembe pogojev</h2>
<p>Ti pogoji se lahko spremenijo. Velja različica, objavljena ob oddaji vloge.</p>

<h2>12. Veljavno pravo</h2>
<p>Za te pogoje velja pravo Republike Slovenije. Pristojno je sodišče v {!! $legal->jurisdiction !!}, brez poseganja v pravice potrošnika.</p>
