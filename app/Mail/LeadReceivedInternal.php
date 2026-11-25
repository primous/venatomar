<?php

namespace App\Mail;

use App\Models\Lead;
use App\Support\Country;
use App\Support\Venatomar;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Internal notification sent to the lead's assigned admin mailbox
 * (info@ / contact@ / ayuda@venatomar.com), formatted for reliable Hostinger INBOX delivery.
 */
class LeadReceivedInternal extends QueuedMail
{
    public function __construct(public Lead $lead)
    {
        $this->locale('es');
    }

    public function envelope(): Envelope
    {
        return $this->baseEnvelope(
            sprintf(
                'Ficha %s (%s - %s)',
                $this->lead->reference,
                strtoupper($this->lead->country),
                $this->lead->adminChannelLabel()
            )
        );
    }

    public function content(): Content
    {
        $l = $this->lead;
        $country = new Country($l->country);
        $amountDisplay = Venatomar::decimal($l->amount, new Country('es'), 0).' '.$country->currency;
        $payoutLabel = match ($l->payout_method) {
            'western_union' => 'Western Union',
            'redpagos' => 'REDPAGOS',
            'abitab' => 'ABITAB',
            'midinero' => 'MIDINERO',
            default => __('opt.payout_method.'.$l->payout_method, [], 'es'),
        };

        return new Content(
            text: 'mail.lead-internal-text',
            with: [
                'lead' => $l,
                'amountDisplay' => $amountDisplay,
                'payoutLabel' => $payoutLabel,
                'location' => trim($l->geo_city.' ('.strtoupper($l->country).')'),
                'submittedAt' => ($l->created_at ?? now())->timezone('Europe/Madrid')->format('d/m/Y H:i'),
                'adminEmail' => $l->admin_email ?: config('venatomar.lead_recipient'),
            ],
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
