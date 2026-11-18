{{-- SJABLOON: vóór publicatie laten controleren door een jurist. --}}
<h2>1. Wie wij zijn</h2>
<p>De website {!! $legal->brand() !!} ({!! $legal->url() !!}) wordt beheerd door:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Bedrijfsnaam', 'tax' => 'Btw-nummer', 'register' => 'Handelsregister (KvK)', 'address' => 'Adres', 'email' => 'Contact-e-mail', 'credit' => 'Registratienummer kredietbemiddelaar']])

<h2>2. Doel van de dienst</h2>
<p>{!! $legal->brand() !!} is een gratis vergelijkings- en bemiddelingsdienst voor persoonlijke leningen. Wij nemen je aanvraag op en sturen die door naar partnerkredietverstrekkers. <strong>{!! $legal->brand() !!} verstrekt geen leningen</strong>, beslist niet over goedkeuring en is geen partij bij de kredietovereenkomst.</p>

<h2>3. Gratis voor jou</h2>
<p>Het gebruik van de vergelijker is gratis. {!! $legal->brand() !!} vraagt nooit om een vergoeding, voorschot of afsluitkosten.</p>

<h2>4. Vergoeding</h2>
<p>{!! $legal->brand() !!} kan een vergoeding ontvangen van partnerkredietverstrekkers wanneer een aanvraag wordt doorgestuurd of een overeenkomst wordt gesloten. Deze vergoeding verhoogt de kosten van de lening voor jou niet. Partners: {!! $legal->partners !!}.</p>

<h2>5. Voorwaarden van de leningen</h2>
<p>Bedrag, looptijd, rente, JKP en alle andere voorwaarden worden uitsluitend door de kredietverstrekker bepaald na beoordeling van je aanvraag, inclusief toetsing bij het BKR. Vóór ondertekening ontvang je de Europese standaardinformatie voor consumentenkrediet en de overeenkomst.</p>

<h2>6. Representatief voorbeeld</h2>
<x-legal-example />

<h2>7. Jouw verplichtingen</h2>
<ul>
    <li>Meerderjarig zijn en op eigen naam aanvragen.</li>
    <li>Juiste, volledige en actuele gegevens verstrekken.</li>
    <li>Geen gegevens van anderen gebruiken en de dienst niet voor fraude gebruiken.</li>
</ul>

<h2>8. Aansprakelijkheid</h2>
<p>{!! $legal->brand() !!} garandeert niet dat een kredietverstrekker je aanvraag accepteert of welke voorwaarden worden geboden. Wij zijn niet aansprakelijk voor beslissingen, aanbiedingen of overeenkomsten van kredietverstrekkers, noch voor schade door onjuiste gegevens van de gebruiker. De website kan tijdelijk onbereikbaar zijn voor onderhoud.</p>

<h2>9. Herroepingsrecht</h2>
<p>Je kunt een met de kredietverstrekker gesloten kredietovereenkomst binnen 14 dagen zonder opgave van redenen ontbinden. Meld dit aan de kredietverstrekker en betaal het geleende bedrag met de opgebouwde rente terug.</p>

<h2>10. Intellectueel eigendom</h2>
<p>Teksten, ontwerp, logo's en code van de website zijn eigendom van {!! $legal->company_name !!} of haar licentiegevers. Verveelvoudiging zonder toestemming is niet toegestaan.</p>

<h2>11. Wijzigingen</h2>
<p>Deze voorwaarden kunnen worden gewijzigd. Van toepassing is de versie die gepubliceerd is op het moment van je aanvraag.</p>

<h2>12. Toepasselijk recht</h2>
<p>Op deze voorwaarden is Nederlands recht van toepassing. Bevoegd is de rechter te {!! $legal->jurisdiction !!}, onverminderd de dwingende rechten van consumenten.</p>
