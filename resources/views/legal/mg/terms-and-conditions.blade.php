{{-- MODELY: tsy maintsy hamarinin'ny mpahay lalàna alohan'ny hamoahana azy. Mila hamarinin'ny mpiteny teratany koa. --}}
<h2>1. Tompon'ny tranonkala</h2>
<p>Ny tranonkala {!! $legal->brand() !!} ({!! $legal->url() !!}) dia tantanin'ny:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Anaran\'ny orinasa', 'tax' => 'NIF / STAT', 'register' => 'RCS', 'address' => 'Foibe', 'email' => 'Mailaka fifandraisana', 'credit' => 'Laharana fisoratana mpanelanelana amin\'ny trosa']])

<h2>2. Ny tolotra</h2>
<p>Mpanome fampindramam-bola manokana mivantana amin'ny aterineto ny {!! $legal->brand() !!}. Mandinika mivantana ny fangatahana findramam-bola izahay ary manome famatsiam-bola ho an'ny mpanjifa araka ny fifanarahana trosa voasonia.</p>

<h2>3. Maimaim-poana ny fandinihana</h2>
<p>Maimaim-poana tanteraka ny fanaovana fangatahana sy ny fandinihana ny fahafaha-mandoa. Tsy mangataka vola mialoha na saram-pikarakarana mialoha amin'ny mpampiasa mihitsy ny {!! $legal->brand() !!}.</p>

<h2>4. Fampindramam-bola mivantana</h2>
<p>Ny {!! $legal->brand() !!} no manombana sy manome mivantana ny famatsiam-bola aorian'ny fanamarinana ny antontan-taratasy sy ny fanaovana sonia ny fifanarahana.</p>

<h2>5. Fepetran'ny trosa</h2>
<p>Voafaritra amin'ny fomba mazava ny vola, ny faharetana, ny tahan'ny zana-bola ary ny fepetra rehetra. Alohan'ny hanaovana sonia dia omena anao ny fampahalalana rehetra sy ny fifanarahana trosa.</p>

<h2>6. Ohatra fanehoana</h2>
<x-legal-example />

<h2>7. Adidin'ny mpampiasa</h2>
<ul>
    <li>Efa ampy taona ary miasa amin'ny anarany manokana.</li>
    <li>Manome angona marina, feno ary vaovao.</li>
    <li>Tsy mampiasa angon'olon-kafa ary tsy mampiasa ny tolotra amin'ny hosoka.</li>
</ul>

<h2>8. Andraikitra</h2>
<p>Mandinika ny fangatahana tsirairay araka ny fitsipiky ny fampindramam-bola tompon'andraikitra ny {!! $legal->brand() !!}. Miankina amin'ny fahamarinan'ny angona nomena ny fankatoavana. Mety tsy ho azo idirana vetivety ny tranonkala noho ny fikojakojana.</p>

<h2>9. Zo hiala</h2>
<p>Afaka miala amin'ny fifanarahana trosa natao tamin'ny {!! $legal->brand() !!} ianao ao anatin'ny 14 andro, tsy mila manome antony, amin'ny alalan'ny famerenana ny renivola sy ny zana-bola nisy.</p>

<h2>10. Fananana ara-tsaina</h2>
<p>An'ny {!! $legal->company_name !!} na an'ireo nanome alalana azy ny lahatsoratra, ny endrika, ny logo ary ny kaody amin'ny tranonkala. Voarara ny fanaovana kopia azy raha tsy nahazo alalana.</p>

<h2>11. Fanovana</h2>
<p>Mety ovaina ireo fepetra ireo. Ny dikan-teny navoaka tamin'ny fotoana nandefasanao ny fangatahana no manan-kery.</p>

<h2>12. Lalàna mihatra</h2>
<p>Ny lalàna malagasy no mihatra. Ny fitsarana ao {!! $legal->jurisdiction !!} no manana fahefana, tsy manimba ny zon'ny mpanjifa.</p>
