{{-- MODELLO: da far validare a un legale prima della pubblicazione. --}}
<h2>1. Titolare del sito</h2>
<p>Il sito {!! $legal->brand() !!} ({!! $legal->url() !!}) è gestito da:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Ragione sociale', 'tax' => 'Partita IVA', 'register' => 'Registro delle imprese', 'address' => 'Sede', 'email' => 'E-mail di contatto', 'credit' => 'N. iscrizione mediatore creditizio']])

<h2>2. Oggetto del servizio</h2>
<p>{!! $legal->brand() !!} è un servizio gratuito di confronto e intermediazione di prestiti personali. Raccoglie la richiesta dell'utente e la trasmette a enti finanziatori partner. <strong>{!! $legal->brand() !!} non concede prestiti</strong>, non decide sull'approvazione e non è parte del contratto di finanziamento.</p>

<h2>3. Gratuità per l'utente</h2>
<p>L'uso del comparatore è gratuito. {!! $legal->brand() !!} non chiede mai all'utente pagamenti, anticipi o spese di istruttoria.</p>

<h2>4. Remunerazione</h2>
<p>{!! $legal->brand() !!} può ricevere un compenso dagli enti partner quando trasmette una richiesta o quando viene stipulato un contratto. Tale compenso non aumenta il costo del prestito per l'utente. Enti partner: {!! $legal->partners !!}.</p>

<h2>5. Condizioni dei prestiti</h2>
<p>Importo, durata, tasso, TAEG e ogni altra condizione sono fissati esclusivamente da ciascun ente dopo la valutazione della richiesta. Prima della firma l'ente deve consegnare il modulo SECCI (Informazioni europee di base sul credito ai consumatori) e il contratto.</p>

<h2>6. Esempio rappresentativo</h2>
<x-legal-example />

<h2>7. Obblighi dell'utente</h2>
<ul>
    <li>Essere maggiorenne e agire in nome proprio.</li>
    <li>Fornire dati esatti, completi e aggiornati.</li>
    <li>Non usare dati di terzi né il servizio per scopi fraudolenti.</li>
</ul>

<h2>8. Responsabilità</h2>
<p>{!! $legal->brand() !!} non garantisce l'accettazione della richiesta né le condizioni offerte. Non risponde delle decisioni, offerte o contratti degli enti, né dei danni derivanti da dati inesatti forniti dall'utente. Il sito può essere temporaneamente non disponibile per manutenzione.</p>

<h2>9. Diritto di recesso</h2>
<p>L'utente può recedere dal contratto di credito stipulato con l'ente entro 14 giorni, senza indicarne il motivo, ai sensi del Codice del consumo e del TUB. Il recesso va comunicato all'ente, restituendo il capitale e gli interessi maturati.</p>

<h2>10. Proprietà intellettuale</h2>
<p>Testi, grafica, loghi e codice del sito appartengono a {!! $legal->company_name !!} o ai suoi licenzianti. Ne è vietata la riproduzione senza autorizzazione.</p>

<h2>11. Modifiche</h2>
<p>Queste condizioni possono essere modificate. Si applica la versione pubblicata al momento dell'invio della richiesta.</p>

<h2>12. Legge applicabile e foro</h2>
<p>Si applica la legge italiana. Foro competente: {!! $legal->jurisdiction !!}, fatto salvo il foro del consumatore previsto dalla legge.</p>
