{{-- MODELLO: da far validare a un legale prima della pubblicazione. --}}
<h2>1. Titolare del trattamento</h2>
@include('legal.partials.identity', ['l' => ['company' => 'Titolare', 'tax' => 'Partita IVA', 'register' => 'Registro delle imprese', 'address' => 'Sede', 'email' => 'Contatto privacy', 'credit' => 'N. iscrizione mediatore creditizio']])
<p>Responsabile della protezione dei dati (se nominato): {!! $legal->dpo_email !!}.</p>

<h2>2. Dati trattati</h2>
<ul>
    <li><strong>Reddito:</strong> importo e durata richiesti, reddito netto, situazione lavorativa, segnalazioni in CRIF.</li>
    <li><strong>Dati personali:</strong> nome, cognome, codice fiscale, data di nascita, e-mail e telefono.</li>
    <li><strong>Indirizzo:</strong> CAP, provincia, comune, via, numero civico e interno.</li>
    <li><strong>Conto bancario:</strong> IBAN.</li>
    <li><strong>Informazioni aggiuntive:</strong> sesso, finalità, stato civile, settore, studi, abitazione, spese, debiti e veicolo.</li>
    <li><strong>Dati tecnici:</strong> indirizzo IP, browser, lingua, campagna di provenienza (UTM), data e versione del consenso.</li>
</ul>
<p>Codice fiscale, IBAN e data di nascita sono conservati cifrati.</p>

<h2>3. Finalità e basi giuridiche</h2>
<ul>
    <li><strong>Gestione della richiesta</strong> e trasmissione agli enti: misure precontrattuali su tua richiesta (art. 6.1.b GDPR).</li>
    <li><strong>Comunicazioni commerciali</strong>: solo con il tuo consenso, revocabile in qualsiasi momento (art. 6.1.a GDPR).</li>
    <li><strong>Prevenzione delle frodi e sicurezza</strong> (limiti di invio, log tecnici): legittimo interesse (art. 6.1.f GDPR).</li>
    <li><strong>Obblighi di legge</strong>: art. 6.1.c GDPR.</li>
</ul>

<h2>4. Destinatari</h2>
<ul>
    <li>Enti finanziatori partner a cui viene trasmessa la richiesta: {!! $legal->partners !!}.</li>
    <li>Fornitore di hosting ed e-mail: {!! $legal->hosting_provider !!}, come responsabile del trattamento.</li>
    <li>Autorità pubbliche, quando richiesto dalla legge.</li>
</ul>
<p>Non vendiamo mai i tuoi dati. I campi del modulo non vengono condivisi con strumenti di analisi.</p>

<h2>5. Trasferimenti extra UE</h2>
<p>I dati sono ospitati nello Spazio economico europeo. Se accetti i cookie statistici o di marketing, Google o Meta possono trattare dati di navigazione fuori dallo SEE con le garanzie previste dal GDPR (decisione di adeguatezza o clausole contrattuali tipo).</p>

<h2>6. Conservazione</h2>
<p>Conserviamo i dati della richiesta per {!! $legal->retention !!} e successivamente, in forma limitata, per i termini di prescrizione previsti dalla legge. I dati tecnici di sicurezza sono conservati per il tempo minimo necessario.</p>

<h2>7. I tuoi diritti</h2>
<p>Puoi esercitare i diritti di accesso, rettifica, cancellazione, opposizione, limitazione e portabilità scrivendo a <a href="mailto:{!! $legal->contact_email !!}">{!! $legal->contact_email !!}</a> o da {!! $legal->accountLink('Il mio account') !!}, dove puoi chiedere la cancellazione dei tuoi dati.</p>
<p>Puoi proporre reclamo al {!! $legal->authority() !!}.</p>

<h2>8. Decisioni automatizzate</h2>
<p>{!! $legal->brand() !!} non adotta decisioni basate unicamente su trattamenti automatizzati che producano effetti giuridici nei tuoi confronti. La decisione sul prestito spetta a ciascun ente, che ti informerà sui propri trattamenti.</p>

<h2>9. Cookie</h2>
<p>Consulta la {!! $legal->link('cookie-policy', 'cookie policy') !!}.</p>
