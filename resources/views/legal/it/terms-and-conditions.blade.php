{{-- MODELLO: da far validare a un legale prima della pubblicazione. --}}
<h2>1. Titolare del sito</h2>
<p>Il sito {!! $legal->brand() !!} ({!! $legal->url() !!}) è gestito da:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Ragione sociale', 'tax' => 'Partita IVA', 'register' => 'Registro delle imprese', 'address' => 'Sede', 'email' => 'E-mail di contatto', 'credit' => 'N. iscrizione mediatore creditizio']])

<h2>2. Oggetto del servizio</h2>
<p>{!! $legal->brand() !!} è un istituto di credito e prestiti personali online. Gestisce direttamente le richieste di prestito ed eroga finanziamenti a privati in conformità alle condizioni stabilite nel contratto stipulato con l'utente.</p>

<h2>3. Gratuità della valutazione</h2>
<p>La richiesta e la valutazione creditizia sono completamente gratuite. {!! $legal->brand() !!} non richiede mai all'utente spese anticipate di istruttoria né costi di gestione preliminari.</p>

<h2>4. Erogazione diretta</h2>
<p>{!! $legal->brand() !!} valuta e concede direttamente il prestito al richiedente a seguito della verifica creditizia positiva e della sottoscrizione del contratto.</p>

<h2>5. Condizioni dei prestiti</h2>
<p>L'importo, la durata, il tasso di interesse, il TAEG e tutte le condizioni contrattuali sono dettagliati nel modulo SECCI (Informazioni europee di base sul credito ai consumatori) e nel contratto di finanziamento consegnato all'utente prima della firma.</p>

<h2>6. Esempio rappresentativo</h2>
<x-legal-example />

<h2>7. Obblighi dell'utente</h2>
<ul>
    <li>Essere maggiorenne e agire in nome proprio.</li>
    <li>Fornire dati esatti, completi e aggiornati.</li>
    <li>Non usare dati di terzi né il servizio per scopi fraudolenti.</li>
</ul>

<h2>8. Responsabilità</h2>
<p>{!! $legal->brand() !!} analizza ogni richiesta secondo criteri di credito responsabile e solvibilità. La concessione del finanziamento è subordinata alla verifica dell'esattezza dei dati forniti. Il sito può essere temporaneamente non disponibile per manutenzione.</p>

<h2>9. Diritto di recesso</h2>
<p>L'utente ha diritto di recedere dal contratto di credito concluso con {!! $legal->brand() !!} entro 14 giorni di calendario, senza indicarne il motivo, restituendo il capitale erogato e gli interessi maturati.</p>

<h2>10. Proprietà intellettuale</h2>
<p>Testi, grafica, loghi e codice del sito appartengono a {!! $legal->company_name !!} o ai suoi licenzianti. Ne è vietata la riproduzione senza autorizzazione.</p>

<h2>11. Modifiche</h2>
<p>Queste condizioni possono essere modificate. Si applica la versione pubblicata al momento dell'invio della richiesta.</p>

<h2>12. Legge applicabile e foro</h2>
<p>Si applica la legge italiana. Foro competente: {!! $legal->jurisdiction !!}, fatto salvo il foro del consumatore previsto dalla legge.</p>
