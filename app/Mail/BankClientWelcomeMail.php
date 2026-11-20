<?php

namespace App\Mail;

use App\Models\BankClient;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Welcome email sent to a new bank client with their account credentials and portal link.
 */
class BankClientWelcomeMail extends QueuedMail
{
    public function __construct(
        public BankClient $client,
        public string $plainPassword,
        public ?string $loginUrl = null
    ) {
        $this->locale($client->locale ?: 'es');
    }

    public function envelope(): Envelope
    {
        return $this->baseEnvelope(__('mail.portal.welcome_subject'));
    }

    public function content(): Content
    {
        $locale = $this->client->locale ?: 'es';
        $adminEmail = $this->client->admin_email ?: config('venatomar.legal.contact_email');
        $accountRoute = $adminEmail === 'contact@venatomar.com' ? 'solicitar.account' : 'account';

        return new Content(
            view: 'mail.bank-client-welcome',
            text: 'mail.bank-client-welcome-text',
            with: [
                'name' => $this->client->first_name,
                'email' => $this->client->email,
                'password' => $this->plainPassword,
                'accountNumber' => $this->client->account_number,
                'iban' => $this->client->iban,
                'balance' => $this->client->formattedBalance(),
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
            'email' => $this->client->email,
        ];
    }
}

