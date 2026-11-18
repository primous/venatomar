{{-- VORLAGE: vor der Veröffentlichung von einer Rechtsanwältin / einem Rechtsanwalt prüfen lassen. --}}
<h2>1. Verantwortlicher</h2>
@include('legal.partials.identity', ['l' => ['company' => 'Verantwortlicher', 'tax' => 'USt-IdNr.', 'register' => 'Handelsregister', 'address' => 'Anschrift', 'email' => 'Datenschutzkontakt', 'credit' => 'Registernummer Kreditvermittler']])
<p>Datenschutzbeauftragte/r (falls benannt): {!! $legal->dpo_email !!}.</p>

<h2>2. Verarbeitete Daten</h2>
<ul>
    <li><strong>Einkommen:</strong> gewünschter Betrag und Laufzeit, Nettoeinkommen, Beschäftigungsverhältnis, negativer SCHUFA-Eintrag.</li>
    <li><strong>Persönliche Daten:</strong> Vor- und Nachname, Geburtsdatum, E-Mail und Telefonnummer. Eine Ausweisnummer wird in Deutschland nicht erhoben.</li>
    <li><strong>Adresse:</strong> PLZ, Bundesland, Ort, Straße, Hausnummer und Wohnung.</li>
    <li><strong>Bankverbindung:</strong> IBAN.</li>
    <li><strong>Weitere Angaben:</strong> Geschlecht, Verwendungszweck, Familienstand, Branche, Bildung, Wohnsituation, Ausgaben, Schulden und Fahrzeug.</li>
    <li><strong>Technische Daten:</strong> IP-Adresse, Browser, Sprache, Herkunftskampagne (UTM), Datum und Version der Einwilligung.</li>
</ul>
<p>IBAN und Geburtsdatum werden verschlüsselt gespeichert.</p>

<h2>3. Zwecke und Rechtsgrundlagen</h2>
<ul>
    <li><strong>Bearbeitung Ihrer Anfrage</strong> und Weiterleitung an Banken: vorvertragliche Maßnahmen auf Ihre Anfrage (Art. 6 Abs. 1 lit. b DSGVO).</li>
    <li><strong>Werbliche Kommunikation</strong>: nur mit Ihrer jederzeit widerruflichen Einwilligung (Art. 6 Abs. 1 lit. a DSGVO).</li>
    <li><strong>Betrugsprävention und Sicherheit</strong> (Sendebegrenzung, technische Protokolle): berechtigtes Interesse (Art. 6 Abs. 1 lit. f DSGVO).</li>
    <li><strong>Erfüllung rechtlicher Pflichten</strong>: Art. 6 Abs. 1 lit. c DSGVO.</li>
</ul>

<h2>4. Empfänger</h2>
<ul>
    <li>Partnerbanken, an die Ihre Anfrage übermittelt wird: {!! $legal->partners !!}.</li>
    <li>Hosting- und E-Mail-Anbieter: {!! $legal->hosting_provider !!} als Auftragsverarbeiter.</li>
    <li>Behörden, soweit gesetzlich vorgeschrieben.</li>
</ul>
<p>Wir verkaufen Ihre Daten nicht. Formularfelder werden niemals an Analysewerkzeuge übermittelt.</p>

<h2>5. Übermittlung in Drittländer</h2>
<p>Die Daten werden im Europäischen Wirtschaftsraum gehostet. Wenn Sie Statistik- oder Marketing-Cookies zulassen, können Google oder Meta Nutzungsdaten außerhalb des EWR verarbeiten, auf Grundlage eines Angemessenheitsbeschlusses oder von Standardvertragsklauseln.</p>

<h2>6. Speicherdauer</h2>
<p>Anfragedaten speichern wir {!! $legal->retention !!} und danach gesperrt für die Dauer der gesetzlichen Aufbewahrungs- und Verjährungsfristen. Technische Sicherheitsdaten werden nur so lange wie nötig gespeichert.</p>

<h2>7. Ihre Rechte</h2>
<p>Sie haben das Recht auf Auskunft, Berichtigung, Löschung, Widerspruch, Einschränkung und Datenübertragbarkeit. Schreiben Sie an <a href="mailto:{!! $legal->contact_email !!}">{!! $legal->contact_email !!}</a> oder nutzen Sie {!! $legal->accountLink('Mein Konto') !!}, wo Sie die Löschung Ihrer Daten beantragen können.</p>
<p>Sie können sich bei der zuständigen Aufsichtsbehörde beschweren: {!! $legal->authority() !!}.</p>

<h2>8. Automatisierte Entscheidungen</h2>
<p>{!! $legal->brand() !!} trifft keine ausschließlich automatisierten Entscheidungen mit rechtlicher Wirkung für Sie. Über den Kredit entscheidet jede Bank selbst und informiert Sie über ihre eigene Datenverarbeitung.</p>

<h2>9. Cookies</h2>
<p>Siehe unsere {!! $legal->link('cookie-policy', 'Cookie-Richtlinie') !!}.</p>
