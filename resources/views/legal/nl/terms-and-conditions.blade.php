{{-- SJABLOON: vóór publicatie laten controleren door een jurist. --}}
<h2>1. Wie wij zijn</h2>
<p>De website {!! $legal->brand() !!} ({!! $legal->url() !!}) wordt beheerd door:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Bedrijfsnaam', 'tax' => 'Btw-nummer', 'register' => 'Handelsregister (KvK)', 'address' => 'Adres', 'email' => 'Contact-e-mail', 'credit' => 'Registratienummer kredietbemiddelaar']])

<h2>2. Doel van de dienst</h2>
<p>{!! $legal->brand() !!} is een directe online kredietverstrekker voor persoonlijke leningen. Wij beoordelen leningaanvragen en verstrekken rechtstreeks financiering aan consumenten volgens de voorwaarden van de kredietovereenkomst.</p>

<h2>3. Kosteloze aanvraag en beoordeling</h2>
<p>Het aanvragen van een lening en de kredietbeoordeling zijn volledig gratis. {!! $legal->brand() !!} brengt nooit afsluitkosten, administratiekosten vooraf of voorschotten in rekening.</p>

<h2>4. Directe leningverstrekking</h2>
<p>{!! $legal->brand() !!} verstrekt de financiering rechtstreeks na een positieve krediettoetsing en ondertekening van het contract.</p>

<h2>5. Voorwaarden van de leningen</h2>
<p>Bedrag, looptijd, rente, JKP en alle overige contractvoorwaarden worden duidelijk gespecificeerd. Vóór ondertekening ontvangt u de Europese standaardinformatie (SECCI) en de kredietovereenkomst.</p>

<h2>6. Representatief voorbeeld</h2>
<x-legal-example />

<h2>7. Jouw verplichtingen</h2>
<ul>
    <li>Meerderjarig zijn en op eigen naam aanvragen.</li>
    <li>Juiste, volledige en actuele gegevens verstrekken.</li>
    <li>Geen gegevens van anderen gebruiken en de dienst niet voor fraude gebruiken.</li>
</ul>

<h2>8. Aansprakelijkheid</h2>
<p>{!! $legal->brand() !!} beoordeelt elke aanvraag volgens normen van verantwoord lenen. Toekenning geschiedt onder voorbehoud van acceptatie en juistheid van de gegevens. De website kan tijdelijk onbereikbaar zijn voor onderhoud.</p>

<h2>9. Herroepingsrecht</h2>
<p>U heeft het wettelijke recht om de met {!! $legal->brand() !!} gesloten kredietovereenkomst binnen 14 kalenderdagen zonder opgave van redenen te herroepen, onder terugbetaling van het geleende bedrag en de opgebouwde rente.</p>

<h2>10. Intellectueel eigendom</h2>
<p>Teksten, ontwerp, logo's en code van de website zijn eigendom van {!! $legal->company_name !!} of haar licentiegevers. Verveelvoudiging zonder toestemming is niet toegestaan.</p>

<h2>11. Wijzigingen</h2>
<p>Deze voorwaarden kunnen worden gewijzigd. Van toepassing is de versie die gepubliceerd is op het moment van je aanvraag.</p>

<h2>12. Toepasselijk recht</h2>
<p>Op deze voorwaarden is Nederlands recht van toepassing. Bevoegd is de rechter te {!! $legal->jurisdiction !!}, onverminderd de dwingende rechten van consumenten.</p>
