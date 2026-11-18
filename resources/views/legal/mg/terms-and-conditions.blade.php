{{-- MODELY: tsy maintsy hamarinin'ny mpahay lalàna alohan'ny hamoahana azy. Mila hamarinin'ny mpiteny teratany koa. --}}
<h2>1. Tompon'ny tranonkala</h2>
<p>Ny tranonkala {!! $legal->brand() !!} ({!! $legal->url() !!}) dia tantanin'ny:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Anaran\'ny orinasa', 'tax' => 'NIF / STAT', 'register' => 'RCS', 'address' => 'Foibe', 'email' => 'Mailaka fifandraisana', 'credit' => 'Laharana fisoratana mpanelanelana amin\'ny trosa']])

<h2>2. Ny tolotra</h2>
<p>Sampan-draharaha maimaim-poana hampitahana sy hanelanelanana trosa manokana ny {!! $legal->brand() !!}. Raisiny ny fangatahan'ny mpampiasa ary alefany any amin'ireo mpiara-miasa mpampindram-bola. <strong>Tsy manome trosa ny {!! $legal->brand() !!}</strong>, tsy manapaka ny fankatoavana ary tsy anisan'ny mpifanaraka amin'ny fifanarahana trosa.</p>

<h2>3. Maimaim-poana ho an'ny mpampiasa</h2>
<p>Maimaim-poana ny fampiasana ny fampitahana. Tsy mangataka vola, vola mialoha na saram-pandinihana amin'ny mpampiasa mihitsy ny {!! $legal->brand() !!}.</p>

<h2>4. Karama</h2>
<p>Mety handray saram-pikarakarana avy amin'ireo mpiara-miasa ny {!! $legal->brand() !!} rehefa alefa aminy ny fangatahana na rehefa misy fifanarahana vita. Tsy mampitombo ny saran'ny trosa ho an'ny mpampiasa izany. Mpiara-miasa: {!! $legal->partners !!}.</p>

<h2>5. Fepetran'ny trosa</h2>
<p>Ny mpampindram-bola tsirairay ihany no mametraka ny vola, ny faharetana, ny zana-bola, ny TEG ary ny fepetra hafa rehetra aorian'ny fandinihana ny fangatahana. Alohan'ny hanaovana sonia dia tsy maintsy omeny anao ny fampahalalana rehetra sy ny fifanarahana.</p>

<h2>6. Ohatra fanehoana</h2>
<x-legal-example />

<h2>7. Adidin'ny mpampiasa</h2>
<ul>
    <li>Efa ampy taona ary miasa amin'ny anarany manokana.</li>
    <li>Manome angona marina, feno ary vaovao.</li>
    <li>Tsy mampiasa angon'olon-kafa ary tsy mampiasa ny tolotra amin'ny hosoka.</li>
</ul>

<h2>8. Andraikitra</h2>
<p>Tsy miantoka ny {!! $legal->brand() !!} fa hekena ny fangatahana na ny fepetra atolotra. Tsy tompon'andraikitra amin'ny fanapahan-kevitra, tolotra na fifanarahan'ny mpampindram-bola izy, na amin'ny fahavoazana vokatry ny angona diso nomen'ny mpampiasa. Mety tsy ho azo idirana vetivety ny tranonkala noho ny fikojakojana.</p>

<h2>9. Zo hiala</h2>
<p>Afaka miala amin'ny fifanarahana trosa natao tamin'ny mpampindram-bola ianao ao anatin'ny 14 andro, tsy mila manome antony. Ampahafantaro ny mpampindram-bola ary avereno ny renivola sy ny zana-bola efa nisy.</p>

<h2>10. Fananana ara-tsaina</h2>
<p>An'ny {!! $legal->company_name !!} na an'ireo nanome alalana azy ny lahatsoratra, ny endrika, ny logo ary ny kaody amin'ny tranonkala. Voarara ny fanaovana kopia azy raha tsy nahazo alalana.</p>

<h2>11. Fanovana</h2>
<p>Mety ovaina ireo fepetra ireo. Ny dikan-teny navoaka tamin'ny fotoana nandefasanao ny fangatahana no manan-kery.</p>

<h2>12. Lalàna mihatra</h2>
<p>Ny lalàna malagasy no mihatra. Ny fitsarana ao {!! $legal->jurisdiction !!} no manana fahefana, tsy manimba ny zon'ny mpanjifa.</p>
