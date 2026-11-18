<?php

namespace App\Mail;

use App\Models\DeletionRequest;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Internal (Spanish): an applicant asked for the erasure of their data (GDPR art. 17). */
class DeletionRequested extends QueuedMail
{
    public function __construct(public DeletionRequest $deletion)
    {
        $this->locale('es');
    }

    public function envelope(): Envelope
    {
        return $this->baseEnvelope(
            sprintf('[%s] Solicitud de supresión de datos #%d', config('venatomar.brand'), $this->deletion->id),
            new Address($this->deletion->email),
        );
    }

    public function content(): Content
    {
        $references = $this->deletion->applicant?->leads()->pluck('reference')->all() ?? [];

        return new Content(
            view: 'mail.deletion-requested',
            text: 'mail.deletion-requested-text',
            with: [
                'rows' => [
                    'Solicitud' => '#'.$this->deletion->id,
                    'E-mail' => $this->deletion->email,
                    'Idioma' => $this->deletion->locale,
                    'Fecha' => $this->deletion->created_at->timezone('Europe/Madrid')->format('d/m/Y H:i').' (Madrid)',
                    'Solicitudes de préstamo' => $references ? implode(', ', $references) : '—',
                ],
            ],
        );
    }

    protected function failureContext(): array
    {
        return ['deletion_request' => $this->deletion->id];
    }
}
