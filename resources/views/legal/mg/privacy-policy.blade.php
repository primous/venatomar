{{-- MODELY: tsy maintsy hamarinin'ny mpahay lalàna alohan'ny hamoahana azy. Mila hamarinin'ny mpiteny teratany koa. --}}
<h2>1. Tompon'andraikitra amin'ny fikarakarana</h2>
@include('legal.partials.identity', ['l' => ['company' => 'Tompon\'andraikitra', 'tax' => 'NIF / STAT', 'register' => 'RCS', 'address' => 'Adiresy', 'email' => 'Fifandraisana momba ny angona', 'credit' => 'Laharana fisoratana mpanelanelana amin\'ny trosa']])
<p>Mpiandraikitra ny fiarovana ny angona (raha voatendry): {!! $legal->dpo_email !!}.</p>

<h2>2. Angona karakarainay</h2>
<ul>
    <li><strong>Fidiram-bola:</strong> vola sy faharetana angatahina, fidiram-bola madio, toe-draharaha, fisoratana ao amin'ny Centrale des risques (BFM).</li>
    <li><strong>Mombamomba anao:</strong> anarana, fanampin'anarana, laharana CIN, daty nahaterahana, mailaka ary finday.</li>
    <li><strong>Adiresy:</strong> kaody paositra, faritra, tanàna, lot / fokontany, laharana ary rihana.</li>
    <li><strong>Fandraisana ny vola:</strong> RIB na laharana Mobile Money sy ny mpandraharaha.</li>
    <li><strong>Fampahalalana fanampiny:</strong> lahy sa vavy, antony, toe-panambadiana, sehatra, fianarana, fonenana, fandaniana, trosa ary fiara na moto.</li>
    <li><strong>Angona ara-teknika:</strong> adiresy IP, mpitety tranonkala, fiteny, dokam-barotra niaviana (UTM), daty sy dikan-teny nanekena.</li>
</ul>
<p>Voatahiry voaaro (voafehy) ny laharana CIN, ny RIB na ny laharana Mobile Money ary ny daty nahaterahana.</p>

<h2>3. Tanjona sy fototra ara-dalàna</h2>
<ul>
    <li><strong>Fikarakarana ny fangatahanao</strong> sy fandefasana azy any amin'ny mpampindram-bola: dingana mialoha ny fifanarahana nangatahinao.</li>
    <li><strong>Hafatra ara-barotra</strong>: raha nanaiky ianao ihany, ary azonao foanana na oviana na oviana.</li>
    <li><strong>Fisorohana ny hosoka sy fiarovana</strong> (fetran'ny fandefasana, rakitra ara-teknika): tombontsoa ara-dalàna.</li>
    <li><strong>Adidy ara-dalàna</strong>.</li>
</ul>
<p>Mifanaraka amin'ny lalàna laharana faha-2014-038 momba ny fiarovana ny angona manokana ny fikarakarana.</p>

<h2>4. Mpandray ny angona</h2>
<ul>
    <li>Mpiara-miasa mpampindram-bola andefasana ny fangatahana: {!! $legal->partners !!}.</li>
    <li>Mpamatsy fampiantranoana sy mailaka: {!! $legal->hosting_provider !!}.</li>
    <li>Manampahefana, raha takian'ny lalàna.</li>
</ul>
<p>Tsy amidinay mihitsy ny angonao. Tsy zaraina amin'ny fitaovana fanadihadiana ny saha amin'ny taratasy fangatahana.</p>

<h2>5. Famindrana any ivelany</h2>
<p>Any amin'ny Faritra Ekonomika Eoropeana no itehirizana ny angona. Raha manaiky cookies antontan'isa na varotra ianao, mety hikarakara angona momba ny fitetezana any ivelany ny Google na ny Meta, miaraka amin'ny fiarovana mifanaraka amin'ny lalàna.</p>

<h2>6. Faharetan'ny fitehirizana</h2>
<p>Tehirizinay mandritra ny {!! $legal->retention !!} ny angon'ny fangatahana, avy eo voafetra mandritra ny fe-potoana ara-dalàna. Ny angona ara-teknika momba ny fiarovana dia tehirizina mandritra ny fotoana ilaina indrindra ihany.</p>

<h2>7. Ny zonao</h2>
<p>Manan-jo hijery, hanitsy, hamafa, handà, hametra ary hamindra ny angonao ianao. Soraty amin'ny <a href="mailto:{!! $legal->contact_email !!}">{!! $legal->contact_email !!}</a> na ampiasao ny {!! $legal->accountLink('Ny kaontiko') !!}, izay ahafahanao mangataka ny famafana ny angonao.</p>
<p>Afaka mitaraina any amin'ny {!! $legal->authority() !!} ianao.</p>

<h2>8. Fanapahan-kevitra mandeha ho azy</h2>
<p>Tsy mandray fanapahan-kevitra miorina tanteraka amin'ny fikarakarana mandeha ho azy ka misy vokany ara-dalàna aminao ny {!! $legal->brand() !!}. Ny mpampindram-bola tsirairay no manapaka momba ny trosa ary hampahafantatra anao ny fikarakarany manokana.</p>

<h2>9. Cookies</h2>
<p>Jereo ny {!! $legal->link('cookie-policy', 'politikan\'ny cookies') !!}.</p>
