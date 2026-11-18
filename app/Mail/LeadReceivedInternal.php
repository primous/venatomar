<?php

namespace App\Mail;

use App\Models\Lead;
use App\Support\Country;
use App\Support\Venatomar;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Internal notification to info@venatomar.com — always in Spanish. */
class LeadReceivedInternal extends QueuedMail
{
    public function __construct(public Lead $lead)
    {
        $this->locale('es');
    }

    public function envelope(): Envelope
    {
        $country = new Country($this->lead->country);

        return $this->baseEnvelope(
            sprintf('[%s] Nueva solicitud %s · %s · %s %s / %d meses',
                config('venatomar.brand'),
                $this->lead->reference,
                strtoupper($this->lead->country),
                Venatomar::decimal($this->lead->amount, new Country('es'), 0),
                $country->currency,
                $this->lead->term_months,
            ),
            // The applicant goes in Reply-To, NEVER in From.
            new Address($this->lead->email, $this->lead->first_name.' '.$this->lead->last_name),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.lead-internal',
            text: 'mail.lead-internal-text',
            with: ['rows' => (new LeadSummary($this->lead))->sections()],
        );
    }

    protected function failureContext(): array
    {
        return ['lead' => $this->lead->reference];
    }

    protected function onFailed(): void
    {
        $this->lead->forceFill(['mail_failed_at' => now()])->saveQuietly();
    }
}
