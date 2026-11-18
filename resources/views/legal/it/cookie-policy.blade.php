{{-- MODELLO: da far validare a un legale prima della pubblicazione. --}}
<h2>1. Cosa sono i cookie?</h2>
<p>Sono piccoli file che il sito salva nel tuo browser. {!! $legal->brand() !!} usa solo i cookie elencati di seguito. I cookie statistici e di marketing vengono installati solo se li accetti.</p>

<h2>2. Cookie utilizzati</h2>
@include('legal.partials.cookies-table', ['t' => [
    'h' => ['Cookie', 'Finalità', 'Durata', 'Titolare', 'Categoria'],
    'own' => 'Prima parte',
    'session' => 'Mantiene la sessione e la bozza del modulo.',
    'xsrf' => 'Protegge i moduli da richieste contraffatte (CSRF).',
    'consent' => 'Ricorda la tua scelta sui cookie e la versione di questa policy.',
    'utm' => 'Ricorda la campagna di provenienza della visita (solo dopo il consenso).',
    'ga' => 'Google Analytics: misurazione aggregata del pubblico.',
    'ads' => 'Google Ads: misurazione delle conversioni.',
    'meta' => 'Meta Pixel: misurazione delle campagne.',
    'minutes' => ':n minuti di inattività',
    'days' => ':n giorni',
    'provider' => 'Secondo il fornitore',
    'necessary' => 'Necessario', 'statistics' => 'Statistico', 'marketing' => 'Marketing',
    'none' => 'Al momento non viene installato alcun cookie di terze parti.',
]])

<h2>3. Gestire il consenso</h2>
<p>Puoi modificare la tua scelta in qualsiasi momento con il pulsante «Impostazioni cookie» in fondo alla pagina. Puoi anche cancellare i cookie dalle impostazioni del browser.</p>
<p>Maggiori informazioni nell'{!! $legal->link('privacy-policy', 'informativa sulla privacy') !!}.</p>
