<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Confirmation to the applicant, in the language of the request.
 * NEVER contains IBAN, identity number or income.
 */
class LeadConfirmation extends QueuedMail
{
    public function __construct(public Lead $lead)
    {
        $this->locale($lead->locale);
        $this->useAdminSender($lead->admin_email ?: config('venatomar.legal.contact_email'));
    }

    public function envelope(): Envelope
    {
        $adminEmail = $this->lead->admin_email ?: config('venatomar.legal.contact_email');

        return $this->baseEnvelope(
            __('mail.confirmation.subject', ['reference' => $this->lead->reference]),
            $adminEmail ? new Address($adminEmail, config('mail.from.name')) : null
        );
    }

    public function content(): Content
    {
        $accountRoute = in_array($this->lead->admin_email, ['contact@venatomar.com', 'ayuda@venatomar.com']) ? 'solicitar.account' : 'account';
        $routeLocale = \App\Support\Venatomar::isLocale($this->lead->locale) ? $this->lead->locale : config('venatomar.default_locale');

        return new Content(
            view: 'mail.lead-confirmation',
            text: 'mail.lead-confirmation-text',
            with: [
                'reference' => $this->lead->reference,
                'firstName' => $this->lead->first_name,
                'amount' => $this->lead->amount,
                'term' => $this->lead->term_months,
                'country' => $this->lead->countryInfo(),
                'contactEmail' => $this->lead->admin_email ?: config('venatomar.legal.contact_email'),
                'accountUrl' => route($accountRoute, ['locale' => $routeLocale]),
                'privacyUrl' => route('legal', ['locale' => $routeLocale, 'page' => 'privacy-policy']),
                'date' => ($this->lead->created_at ?? now())->format('d/m/Y'),
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
