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

    public function __construct(
        public BankClient $client,
        string $status = 'active',
        public ?string $reason = null,
        ?string $newStatus = null
    ) {
        $this->status = $newStatus ?? $status;
        $this->locale($client->locale ?: 'es');
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
        $adminEmail = $this->client->admin_email ?: config('venatomar.legal.contact_email');
        $accountRoute = $adminEmail === 'contact@venatomar.com' ? 'solicitar.account' : 'account';

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
                'loginUrl' => route($accountRoute, ['locale' => $locale]),
                'contactEmail' => $adminEmail,
                'privacyUrl' => route('legal', ['locale' => $locale, 'page' => 'privacy-policy']),
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
