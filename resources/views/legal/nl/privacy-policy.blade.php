{{-- SJABLOON: vóór publicatie laten controleren door een jurist. --}}
<h2>1. Verwerkingsverantwoordelijke</h2>
@include('legal.partials.identity', ['l' => ['company' => 'Verantwoordelijke', 'tax' => 'Btw-nummer', 'register' => 'Handelsregister (KvK)', 'address' => 'Adres', 'email' => 'Privacycontact', 'credit' => 'Registratienummer kredietbemiddelaar']])
<p>Functionaris gegevensbescherming (indien aangesteld): {!! $legal->dpo_email !!}.</p>

<h2>2. Welke gegevens wij verwerken</h2>
<ul>
    <li><strong>Inkomen:</strong> gewenst bedrag en looptijd, netto-inkomen, arbeidssituatie, negatieve BKR-registratie.</li>
    <li><strong>Persoonsgegevens:</strong> voor- en achternaam, geboortedatum, e-mailadres en telefoonnummer. <strong>Wij vragen en verwerken nooit je BSN.</strong></li>
    <li><strong>Adres:</strong> postcode, provincie, woonplaats, straat, huisnummer en toevoeging.</li>
    <li><strong>Bankrekening:</strong> IBAN.</li>
    <li><strong>Aanvullende informatie:</strong> geslacht, doel, burgerlijke staat, sector, opleiding, woonsituatie, maandlasten, schulden en voertuig.</li>
    <li><strong>Technische gegevens:</strong> IP-adres, browser, taal, herkomstcampagne (UTM), datum en versie van de toestemming.</li>
</ul>
<p>IBAN en geboortedatum worden versleuteld opgeslagen.</p>

<h2>3. Doelen en grondslagen</h2>
<ul>
    <li><strong>Behandeling van je aanvraag</strong> en doorsturen naar kredietverstrekkers: precontractuele maatregelen op jouw verzoek (art. 6 lid 1 sub b AVG).</li>
    <li><strong>Commerciële berichten</strong>: alleen met je toestemming, die je altijd kunt intrekken (art. 6 lid 1 sub a AVG).</li>
    <li><strong>Fraudepreventie en beveiliging</strong> (verzendlimieten, technische logs): gerechtvaardigd belang (art. 6 lid 1 sub f AVG).</li>
    <li><strong>Wettelijke verplichtingen</strong>: art. 6 lid 1 sub c AVG.</li>
</ul>

<h2>4. Ontvangers</h2>
<ul>
    <li>Partnerkredietverstrekkers aan wie je aanvraag wordt doorgestuurd: {!! $legal->partners !!}.</li>
    <li>Hosting- en e-mailprovider: {!! $legal->hosting_provider !!}, als verwerker.</li>
    <li>Overheidsinstanties, als de wet dat vereist.</li>
</ul>
<p>Wij verkopen je gegevens nooit. Formuliervelden worden nooit gedeeld met analysetools.</p>

<h2>5. Doorgifte buiten de EER</h2>
<p>De gegevens worden gehost in de Europese Economische Ruimte. Als je statistische of marketingcookies accepteert, kunnen Google of Meta surfgegevens buiten de EER verwerken met passende waarborgen (adequaatheidsbesluit of standaardcontractbepalingen).</p>

<h2>6. Bewaartermijn</h2>
<p>Wij bewaren aanvraaggegevens {!! $legal->retention !!} en daarna afgeschermd gedurende de wettelijke verjaringstermijnen. Technische beveiligingsgegevens bewaren we zo kort mogelijk.</p>

<h2>7. Jouw rechten</h2>
<p>Je hebt recht op inzage, rectificatie, verwijdering, bezwaar, beperking en overdraagbaarheid. Mail naar <a href="mailto:{!! $legal->contact_email !!}">{!! $legal->contact_email !!}</a> of gebruik {!! $legal->accountLink('Mijn account') !!}, waar je verwijdering van je gegevens kunt aanvragen.</p>
<p>Je kunt een klacht indienen bij de {!! $legal->authority() !!}.</p>

<h2>8. Geautomatiseerde besluiten</h2>
<p>{!! $legal->brand() !!} neemt geen uitsluitend geautomatiseerde besluiten met rechtsgevolgen voor jou. Het kredietbesluit ligt bij elke kredietverstrekker, die je over de eigen verwerking informeert.</p>

<h2>9. Cookies</h2>
<p>Zie ons {!! $legal->link('cookie-policy', 'cookiebeleid') !!}.</p>
