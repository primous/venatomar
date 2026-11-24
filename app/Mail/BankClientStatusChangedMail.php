<?php

namespace App\Mail;

use App\Models\BankClient;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Notice sent to the client when their account status is changed (e.g. frozen or reactivated).
 */
class BankClientStatusChangedMail extends QueuedMail
{
    public string $status;
    public string $loginUrl;

    public function __construct(
        public BankClient $client,
        string $status = 'active',
        public ?string $reason = null,
        ?string $newStatus = null,
        ?string $loginUrl = null
    ) {
        $this->status = $newStatus ?? $status;
        $locale = $client->locale ?: 'es';
        $this->locale($locale);

        $adminEmail = $client->admin_email ?: config('venatomar.legal.contact_email');
        $accountRoute = ($locale === 'es' || in_array($adminEmail, ['contact@venatomar.com', 'ayuda@venatomar.com'])) ? 'solicitar.account' : 'account';

        $this->loginUrl = $loginUrl ?: route($accountRoute, ['locale' => $locale]);
    }

    public function envelope(): Envelope
    {
        return $this->baseEnvelope(__('mail.portal.status_subject', [
            'account' => $this->client->account_number,
        ]));
    }

    public function content(): Content
    {
        $locale = $this->client->locale ?: 'es';
        $routeLocale = \App\Support\Venatomar::isLocale($locale) ? $locale : config('venatomar.default_locale');
        $adminEmail = $this->client->admin_email ?: config('venatomar.legal.contact_email');

        return new Content(
            view: 'mail.bank-client-status-changed',
            text: 'mail.bank-client-status-changed-text',
            with: [
                'name' => $this->client->first_name,
                'accountNumber' => $this->client->account_number,
                'status' => $this->status,
                'isFrozen' => $this->status === 'frozen',
                'isActive' => $this->status === 'active',
                'reason' => $this->reason,
                'loginUrl' => $this->loginUrl,
                'contactEmail' => $adminEmail,
                'privacyUrl' => route('legal', ['locale' => $routeLocale, 'page' => 'privacy-policy']),
            ],
        );
    }

    protected function failureContext(): array
    {
        return [
            'client_id' => $this->client->id,
            'status' => $this->status,
        ];
    }
}

