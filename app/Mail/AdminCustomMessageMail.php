<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Direct reply/message sent by an assigned admin to an applicant from the back-office,
 * authenticated via the admin's dedicated SMTP account without webmail quote clutter.
 */
class AdminCustomMessageMail extends QueuedMail
{
    public function __construct(
        public Lead $lead,
        public string $emailSubject,
        public string $messageBody,
    ) {
        $this->locale($lead->locale ?: 'es');
        $this->useAdminSender($lead->admin_email ?: config('venatomar.legal.contact_email'));
    }

    public function envelope(): Envelope
    {
        $adminEmail = $this->lead->admin_email ?: config('venatomar.legal.contact_email');

        return $this->baseEnvelope(
            $this->emailSubject,
            $adminEmail ? new Address($adminEmail, config('mail.from.name')) : null
        );
    }

    public function content(): Content
    {
        $adminEmail = $this->lead->admin_email ?: config('venatomar.legal.contact_email');
        $accountRoute = in_array($adminEmail, ['contact@venatomar.com', 'ayuda@venatomar.com'], true) ? 'solicitar.account' : 'account';
        $routeLocale = \App\Support\Venatomar::isLocale($this->lead->locale) ? $this->lead->locale : config('venatomar.default_locale');

        return new Content(
            view: 'mail.admin-custom-message',
            text: 'mail.admin-custom-message-text',
            with: [
                'firstName' => $this->lead->first_name,
                'reference' => $this->lead->reference,
                'emailSubject' => $this->emailSubject,
                'messageBody' => $this->messageBody,
                'contactEmail' => $adminEmail,
                'accountUrl' => route($accountRoute, ['locale' => $routeLocale]),
                'privacyUrl' => route('legal', ['locale' => $routeLocale, 'page' => 'privacy-policy']),
            ],
        );
    }

    protected function failureContext(): array
    {
        return ['lead' => $this->lead->reference];
    }
}
