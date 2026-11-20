<?php

namespace App\Mail;

use App\Models\Lead;
use App\Models\LoanContract;
use App\Support\LoanContractPdf;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Official loan agreement sent to the client with the contract PDF attached.
 */
class LoanContractMail extends QueuedMail
{
    public function __construct(
        public LoanContract $contract,
        public ?Lead $lead = null
    ) {
        $this->locale($contract->locale ?: ($lead?->locale ?: 'fr'));
    }

    public function envelope(): Envelope
    {
        return $this->baseEnvelope(__('mail.contract.subject', ['reference' => $this->contract->reference]));
    }

    public function content(): Content
    {
        $adminEmail = $this->contract->admin_email ?: ($this->lead?->admin_email ?: config('venatomar.legal.contact_email'));
        $accountRoute = $adminEmail === 'contact@venatomar.com' ? 'solicitar.account' : 'account';
        $locale = $this->contract->locale ?: ($this->lead?->locale ?: 'fr');

        return new Content(
            view: 'mail.loan-contract',
            text: 'mail.loan-contract-text',
            with: [
                'name' => $this->contract->first_name,
                'reference' => $this->contract->reference,
                'amount' => $this->contract->money(),
                'term' => $this->contract->term_months,
                'monthlyPayment' => $this->contract->monthlyPaymentFormatted(),
                'interestRate' => $this->contract->interest_rate,
                'documentFee' => ($this->contract->document_fee && (float) $this->contract->document_fee > 0)
                    ? $this->contract->formattedDocumentFee()
                    : null,
                'contactEmail' => $adminEmail,
                'accountUrl' => route($accountRoute, ['locale' => $locale]),
                'privacyUrl' => route('legal', ['locale' => $locale, 'page' => 'privacy-policy']),
                'date' => ($this->contract->contract_date ?? now())->format('d/m/Y'),
            ],
        );
    }

    /** Attach the rendered Loan Contract PDF. */
    public function attachments(): array
    {
        $pdf = LoanContractPdf::render($this->contract);
        $filename = sprintf('Contrat_Pret_%s.pdf', $this->contract->reference);

        return [
            Attachment::fromData(fn () => $pdf, $filename)
                ->withMime('application/pdf'),
        ];
    }

    protected function failureContext(): array
    {
        return [
            'contract' => $this->contract->reference,
            'email' => $this->contract->email,
        ];
    }
}
