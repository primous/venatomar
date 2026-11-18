{{-- VZOR: pred zverejnením musí skontrolovať právnik. --}}
<h2>1. Prevádzkovateľ</h2>
<p>Webovú stránku {!! $legal->brand() !!} ({!! $legal->url() !!}) prevádzkuje:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Obchodné meno', 'tax' => 'IČO / DIČ', 'register' => 'Obchodný register', 'address' => 'Sídlo', 'email' => 'Kontaktný e-mail', 'credit' => 'Č. registrácie finančného sprostredkovateľa']])

<h2>2. Predmet služby</h2>
<p>{!! $legal->brand() !!} je bezplatná služba porovnania a sprostredkovania spotrebiteľských pôžičiek. Prijíma žiadosť používateľa a odovzdáva ju partnerským veriteľom. <strong>{!! $legal->brand() !!} neposkytuje pôžičky</strong>, nerozhoduje o ich schválení a nie je zmluvnou stranou úverovej zmluvy.</p>

<h2>3. Bezplatnosť pre používateľa</h2>
<p>Používanie porovnávača je bezplatné. {!! $legal->brand() !!} nikdy nežiada od používateľa platbu, zálohu ani poplatok za spracovanie.</p>

<h2>4. Odmena</h2>
<p>{!! $legal->brand() !!} môže dostať odmenu od partnerských veriteľov, keď im odovzdá žiadosť alebo keď sa uzavrie zmluva. Táto odmena nezvyšuje náklady na pôžičku pre používateľa. Partneri: {!! $legal->partners !!}.</p>

<h2>5. Podmienky pôžičiek</h2>
<p>Sumu, dobu splácania, úrokovú sadzbu, RPMN a ostatné podmienky určuje výlučne každý veriteľ po posúdení žiadosti. Pred podpisom vám veriteľ musí odovzdať formulár so štandardnými európskymi informáciami o spotrebiteľskom úvere a zmluvu.</p>

<h2>6. Reprezentatívny príklad</h2>
<x-legal-example />

<h2>7. Povinnosti používateľa</h2>
<ul>
    <li>Byť plnoletý a konať vo vlastnom mene.</li>
    <li>Uvádzať pravdivé, úplné a aktuálne údaje.</li>
    <li>Nepoužívať údaje tretích osôb a nezneužívať službu.</li>
</ul>

<h2>8. Zodpovednosť</h2>
<p>{!! $legal->brand() !!} nezaručuje schválenie žiadosti ani ponúkané podmienky. Nezodpovedá za rozhodnutia, ponuky ani zmluvy veriteľov, ani za škody spôsobené nepravdivými údajmi používateľa. Stránka môže byť dočasne nedostupná z dôvodu údržby.</p>

<h2>9. Právo na odstúpenie</h2>
<p>Od zmluvy o spotrebiteľskom úvere uzavretej s veriteľom môžete odstúpiť do 14 dní bez uvedenia dôvodu. Oznámte to veriteľovi a vráťte istinu spolu s úrokmi za dobu čerpania.</p>

<h2>10. Duševné vlastníctvo</h2>
<p>Texty, dizajn, logá a kód stránky patria {!! $legal->company_name !!} alebo jej poskytovateľom licencií. Ich rozmnožovanie bez súhlasu je zakázané.</p>

<h2>11. Zmeny podmienok</h2>
<p>Tieto podmienky sa môžu zmeniť. Platí verzia zverejnená v čase odoslania žiadosti.</p>

<h2>12. Rozhodné právo</h2>
<p>Tieto podmienky sa riadia právom Slovenskej republiky. Príslušný je súd v {!! $legal->jurisdiction !!}, bez dotknutia práv spotrebiteľa.</p>
