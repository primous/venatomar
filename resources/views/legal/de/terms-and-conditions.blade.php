{{-- VORLAGE: vor der Veröffentlichung von einer Rechtsanwältin / einem Rechtsanwalt prüfen lassen. --}}
<h2>1. Anbieter</h2>
<p>Die Website {!! $legal->brand() !!} ({!! $legal->url() !!}) wird betrieben von:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Firma', 'tax' => 'USt-IdNr.', 'register' => 'Handelsregister', 'address' => 'Anschrift', 'email' => 'Kontakt-E-Mail', 'credit' => 'Registernummer Kreditvermittler']])

<h2>2. Gegenstand des Dienstes</h2>
<p>{!! $legal->brand() !!} ist ein kostenloser Vergleichs- und Vermittlungsdienst für Privatkredite. Er nimmt Ihre Anfrage entgegen und leitet sie an Partnerbanken weiter. <strong>{!! $legal->brand() !!} vergibt keine Kredite</strong>, entscheidet nicht über die Bewilligung und ist nicht Partei des Kreditvertrags.</p>

<h2>3. Kostenlos für Sie</h2>
<p>Die Nutzung des Vergleichs ist kostenlos. {!! $legal->brand() !!} verlangt von Ihnen niemals Gebühren, Vorauszahlungen oder Bearbeitungsentgelte.</p>

<h2>4. Vergütung</h2>
<p>{!! $legal->brand() !!} kann von den Partnerbanken eine Provision erhalten, wenn eine Anfrage übermittelt oder ein Vertrag geschlossen wird. Diese Provision erhöht die Kreditkosten für Sie nicht. Partnerbanken: {!! $legal->partners !!}.</p>

<h2>5. Kreditkonditionen</h2>
<p>Betrag, Laufzeit, Sollzins, effektiver Jahreszins und alle übrigen Konditionen legt ausschließlich die jeweilige Bank nach Prüfung Ihrer Anfrage fest. Vor der Unterschrift muss Ihnen die Bank die Europäischen Standardinformationen für Verbraucherkredite und den Vertrag übergeben.</p>

<h2>6. Repräsentatives Beispiel</h2>
<x-legal-example />

<h2>7. Pflichten der Nutzer</h2>
<ul>
    <li>Volljährig sein und im eigenen Namen handeln.</li>
    <li>Richtige, vollständige und aktuelle Angaben machen.</li>
    <li>Keine Daten Dritter verwenden und den Dienst nicht missbräuchlich nutzen.</li>
</ul>

<h2>8. Haftung</h2>
<p>{!! $legal->brand() !!} garantiert weder die Annahme Ihrer Anfrage noch bestimmte Konditionen. Für Entscheidungen, Angebote oder Verträge der Banken sowie für Schäden aus unrichtigen Angaben der Nutzer wird nicht gehaftet, soweit gesetzlich zulässig. Die Website kann wartungsbedingt vorübergehend nicht erreichbar sein.</p>

<h2>9. Widerrufsrecht</h2>
<p>Sie können einen mit der Bank geschlossenen Verbraucherkreditvertrag innerhalb von 14 Tagen ohne Angabe von Gründen widerrufen. Der Widerruf ist gegenüber der Bank zu erklären; das Darlehen ist samt angefallener Zinsen zurückzuzahlen.</p>

<h2>10. Urheberrecht</h2>
<p>Texte, Gestaltung, Logos und Code der Website gehören {!! $legal->company_name !!} oder deren Lizenzgebern. Eine Vervielfältigung ohne Zustimmung ist untersagt.</p>

<h2>11. Änderungen</h2>
<p>Diese Bedingungen können geändert werden. Maßgeblich ist die Fassung zum Zeitpunkt Ihrer Anfrage.</p>

<h2>12. Anwendbares Recht und Gerichtsstand</h2>
<p>Es gilt deutsches Recht. Gerichtsstand ist {!! $legal->jurisdiction !!}, soweit gesetzlich zulässig; zwingende Verbraucherschutzvorschriften bleiben unberührt.</p>
