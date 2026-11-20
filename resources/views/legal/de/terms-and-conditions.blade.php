{{-- VORLAGE: vor der Veröffentlichung von einer Rechtsanwältin / einem Rechtsanwalt prüfen lassen. --}}
<h2>1. Anbieter</h2>
<p>Die Website {!! $legal->brand() !!} ({!! $legal->url() !!}) wird betrieben von:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Firma', 'tax' => 'USt-IdNr.', 'register' => 'Handelsregister', 'address' => 'Anschrift', 'email' => 'Kontakt-E-Mail', 'credit' => 'Registernummer Kreditvermittler']])

<h2>2. Gegenstand des Dienstes</h2>
<p>{!! $legal->brand() !!} ist ein direkter Online-Kreditgeber für Privatdarlehen. Der Dienst prüft Kreditanfragen und vergibt Darlehen an Verbraucher gemäß den im Kreditvertrag vereinbarten Bedingungen.</p>

<h2>3. Kostenlose Antragsprüfung</h2>
<p>Die Kreditanfrage und die Bonitätsprüfung sind vollkommen kostenlos. {!! $legal->brand() !!} verlangt von Ihnen niemals Bearbeitungsgebühren, Vorauszahlungen oder Vorabkosten.</p>

<h2>4. Direkte Kreditvergabe</h2>
<p>{!! $legal->brand() !!} prüft und bewilligt die Finanzierung direkt nach erfolgreicher Bonitätsprüfung und Abschluss des Darlehensvertrags.</p>

<h2>5. Kreditkonditionen</h2>
<p>Betrag, Laufzeit, Sollzinssatz, effektiver Jahreszins und alle weiteren Bedingungen werden transparent festgelegt. Vor Vertragsschluss erhalten Sie die Europäischen Standardinformationen für Verbraucherkredite sowie den vollständigen Vertrag.</p>

<h2>6. Repräsentatives Beispiel</h2>
<x-legal-example />

<h2>7. Pflichten der Nutzer</h2>
<ul>
    <li>Volljährig sein und im eigenen Namen handeln.</li>
    <li>Richtige, vollständige und aktuelle Angaben machen.</li>
    <li>Keine Daten Dritter verwenden und den Dienst nicht missbräuchlich nutzen.</li>
</ul>

<h2>8. Haftung</h2>
<p>{!! $legal->brand() !!} prüft jeden Antrag nach den Maßstäben einer verantwortungsvollen Kreditvergabe. Die Bewilligung erfolgt vorbehaltlich der Richtigkeit der Angaben. Die Website kann wartungsbedingt vorübergehend nicht erreichbar sein.</p>

<h2>9. Widerrufsrecht</h2>
<p>Sie haben das gesetzliche Recht, den mit {!! $legal->brand() !!} geschlossenen Darlehensvertrag innerhalb von 14 Kalendertagen ohne Angabe von Gründen zu widerrufen, indem Sie den erhaltenen Betrag nebst Zinsen zurückzahlen.</p>

<h2>10. Urheberrecht</h2>
<p>Texte, Gestaltung, Logos und Code der Website gehören {!! $legal->company_name !!} oder deren Lizenzgebern. Eine Vervielfältigung ohne Zustimmung ist untersagt.</p>

<h2>11. Änderungen</h2>
<p>Diese Bedingungen können geändert werden. Maßgeblich ist die Fassung zum Zeitpunkt Ihrer Anfrage.</p>

<h2>12. Anwendbares Recht und Gerichtsstand</h2>
<p>Es gilt deutsches Recht. Gerichtsstand ist {!! $legal->jurisdiction !!}, soweit gesetzlich zulässig; zwingende Verbraucherschutzvorschriften bleiben unberührt.</p>
