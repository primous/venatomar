<?php

namespace App\Mail;

use App\Models\BankClient;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Notice sent to the client when their account is credited by admin or via transaction.
 */
class BankClientCreditedMail extends QueuedMail
{
    public string $loginUrl;

    public function __construct(
        public BankClient $client,
        public float|int $amount,
        public string $description,
        public float|int|string|null $newBalance = null,
        public mixed $date = null,
        ?string $loginUrl = null
    ) {
        $locale = $client->locale ?: 'es';
        $this->locale($locale);

        $adminEmail = $client->admin_email ?: config('venatomar.legal.contact_email');
        $accountRoute = ($locale === 'es' || in_array($adminEmail, ['contact@venatomar.com', 'ayuda@venatomar.com'])) ? 'solicitar.account' : 'account';

        $this->loginUrl = $loginUrl ?: route($accountRoute, ['locale' => $locale]);
    }

    public function envelope(): Envelope
    {
        return $this->baseEnvelope(__('mail.portal.credited_subject', [
            'account' => $this->client->account_number,
            'amount' => $this->client->formatMoney($this->amount),
        ]));
    }

    public function content(): Content
    {
        $locale = $this->client->locale ?: 'es';
        $routeLocale = \App\Support\Venatomar::isLocale($locale) ? $locale : config('venatomar.default_locale');
        $adminEmail = $this->client->admin_email ?: config('venatomar.legal.contact_email');

        return new Content(
            view: 'mail.bank-client-credited',
            text: 'mail.bank-client-credited-text',
            with: [
                'name' => $this->client->first_name,
                'accountNumber' => $this->client->account_number,
                'iban' => $this->client->iban,
                'amount' => $this->client->formatMoney($this->amount),
                'description' => $this->description,
                'newBalance' => $this->newBalance ?: $this->client->formattedBalance(),
                'date' => now()->format('d/m/Y H:i'),
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
            'amount' => $this->amount,
        ];
    }
}

