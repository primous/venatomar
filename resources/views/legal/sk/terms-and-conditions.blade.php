{{-- VZOR: pred zverejnením musí skontrolovať právnik. --}}
<h2>1. Prevádzkovateľ</h2>
<p>Webovú stránku {!! $legal->brand() !!} ({!! $legal->url() !!}) prevádzkuje:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Obchodné meno', 'tax' => 'IČO / DIČ', 'register' => 'Obchodný register', 'address' => 'Sídlo', 'email' => 'Kontaktný e-mail', 'credit' => 'Č. registrácie finančného sprostredkovateľa']])

<h2>2. Predmet služby</h2>
<p>{!! $legal->brand() !!} je priamym poskytovateľom online osobných úverov. Posudzuje žiadosti o pôžičku a priamo poskytuje financovanie spotrebiteľom v súlade so zmluvou o pôžičke.</p>

<h2>3. Bezplatné posúdenie žiadosti</h2>
<p>Podanie žiadosti a posúdenie úveruschopnosti sú úplne bezplatné. {!! $legal->brand() !!} nikdy nežiada od používateľa platbu vopred, poplatok za sprostredkovanie ani zálohu.</p>

<h2>4. Priame poskytnutie úveru</h2>
<p>{!! $legal->brand() !!} priamo poskytuje a vypláca financie po úspešnom posúdení žiadosti a podpísaní zmluvy.</p>

<h2>5. Podmienky pôžičiek</h2>
<p>Suma, doba splatnosti, úroková sadzba, RPMN a ostatné zmluvné podmienky sú transparentne dohodnuté. Pred podpisom obdržíte formulár so štandardnými európskymi informáciami a zmluvu o úvere.</p>

<h2>6. Reprezentatívny príklad</h2>
<x-legal-example />

<h2>7. Povinnosti používateľa</h2>
<ul>
    <li>Byť plnoletý a konať vo vlastnom mene.</li>
    <li>Uvádzať pravdivé, úplné a aktuálne údaje.</li>
    <li>Nepoužívať údaje tretích osôb a nezneužívať službu.</li>
</ul>

<h2>8. Zodpovednosť</h2>
<p>{!! $legal->brand() !!} posudzuje každú žiadosť podľa zásad zodpovedného úverovania. Poskytnutie pôžičky závisí od schválenia a pravdivosti uvedených údajov. Stránka môže byť dočasne nedostupná z dôvodu údržby.</p>

<h2>9. Právo na odstúpenie</h2>
<p>Od zmluvy o úvere uzavretej so spoločnosťou {!! $legal->brand() !!} môžete odstúpiť do 14 kalendárnych dní bez uvedenia dôvodu, vrátením poskytnutej istiny a vzniknutých úrokov.</p>

<h2>10. Duševné vlastníctvo</h2>
<p>Texty, dizajn, logá a kód stránky patria {!! $legal->company_name !!} alebo jej poskytovateľom licencií. Ich rozmnožovanie bez súhlasu je zakázané.</p>

<h2>11. Zmeny podmienok</h2>
<p>Tieto podmienky sa môžu zmeniť. Platí verzia zverejnená v čase odoslania žiadosti.</p>

<h2>12. Rozhodné právo</h2>
<p>Tieto podmienky sa riadia právom Slovenskej republiky. Príslušný je súd v {!! $legal->jurisdiction !!}, bez dotknutia práv spotrebiteľa.</p>
