<?php

namespace App\Mail;

use App\Models\BankClient;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Notice sent to the client when their account is debited by admin or via transfer approval.
 */
class BankClientDebitedMail extends QueuedMail
{
    public function __construct(
        public BankClient $client,
        public float|int $amount,
        public string $description,
        public float|int|string|null $newBalance = null,
        public mixed $date = null
    ) {
        $this->locale($client->locale ?: 'es');
    }

    public function envelope(): Envelope
    {
        return $this->baseEnvelope(__('mail.portal.debited_subject', [
            'account' => $this->client->account_number,
            'amount' => $this->client->formatMoney($this->amount),
        ]));
    }

    public function content(): Content
    {
        $locale = $this->client->locale ?: 'es';
        $adminEmail = $this->client->admin_email ?: config('venatomar.legal.contact_email');
        $accountRoute = $adminEmail === 'contact@venatomar.com' ? 'solicitar.account' : 'account';

        return new Content(
            view: 'mail.bank-client-debited',
            text: 'mail.bank-client-debited-text',
            with: [
                'name' => $this->client->first_name,
                'accountNumber' => $this->client->account_number,
                'iban' => $this->client->iban,
                'amount' => $this->client->formatMoney($this->amount),
                'description' => $this->description,
                'newBalance' => is_numeric($this->newBalance) ? $this->client->formatMoney((float) $this->newBalance) : ($this->newBalance ?: $this->client->formattedBalance()),
                'date' => $this->date ? (is_string($this->date) ? $this->date : $this->date->format('d/m/Y H:i')) : now()->format('d/m/Y H:i'),
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
            'amount' => $this->amount,
        ];
    }
}
