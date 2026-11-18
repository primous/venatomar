{{-- VORLAGE: vor der Veröffentlichung von einer Rechtsanwältin / einem Rechtsanwalt prüfen lassen. --}}
<h2>1. Was sind Cookies?</h2>
<p>Cookies sind kleine Dateien, die die Website in Ihrem Browser speichert. {!! $legal->brand() !!} verwendet ausschließlich die unten aufgeführten Cookies. Statistik- und Marketing-Cookies werden nur mit Ihrer Einwilligung gesetzt (§ 25 TDDDG).</p>

<h2>2. Verwendete Cookies</h2>
@include('legal.partials.cookies-table', ['t' => [
    'h' => ['Cookie', 'Zweck', 'Speicherdauer', 'Anbieter', 'Kategorie'],
    'own' => 'Eigenes Cookie',
    'session' => 'Hält Ihre Sitzung und den Formularentwurf aufrecht.',
    'xsrf' => 'Schützt Formulare vor gefälschten Anfragen (CSRF).',
    'consent' => 'Speichert Ihre Cookie-Auswahl und die Version dieser Richtlinie.',
    'utm' => 'Speichert die Herkunftskampagne Ihres Besuchs (nur nach Einwilligung).',
    'ga' => 'Google Analytics: zusammengefasste Reichweitenmessung.',
    'ads' => 'Google Ads: Conversion-Messung.',
    'meta' => 'Meta-Pixel: Kampagnenmessung.',
    'minutes' => ':n Minuten Inaktivität',
    'days' => ':n Tage',
    'provider' => 'Laut Anbieter',
    'necessary' => 'Notwendig', 'statistics' => 'Statistik', 'marketing' => 'Marketing',
    'none' => 'Derzeit werden keine Cookies von Drittanbietern gesetzt.',
]])

<h2>3. Einwilligung verwalten</h2>
<p>Sie können Ihre Auswahl jederzeit über die Schaltfläche „Cookie-Einstellungen“ in der Fußzeile ändern. Cookies lassen sich auch in den Browsereinstellungen löschen.</p>
<p>Weitere Informationen in der {!! $legal->link('privacy-policy', 'Datenschutzerklärung') !!}.</p>
