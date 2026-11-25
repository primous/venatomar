<?php

namespace App\Mail;

use App\Models\Lead;
use App\Models\LoanContract;
use App\Support\LoanContractPdf;
use Illuminate\Mail\Mailables\Address;
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
        $this->locale($contract->locale ?: ($lead?->locale ?: 'es'));
        $this->useAdminSender($contract->admin_email ?: ($lead?->admin_email ?: config('venatomar.legal.contact_email')));
    }

    public function envelope(): Envelope
    {
        $adminEmail = $this->contract->admin_email ?: ($this->lead?->admin_email ?: config('venatomar.legal.contact_email'));

        return $this->baseEnvelope(
            __('mail.contract.subject', ['reference' => $this->contract->reference]),
            $adminEmail ? new Address($adminEmail, config('mail.from.name')) : null
        );
    }

    public function attachmentFilename(): string
    {
        $locale = $this->contract->locale ?: ($this->lead?->locale ?: 'es');

        return trans('mail.contract.attachment_filename', ['reference' => $this->contract->reference], $locale);
    }

    public function content(): Content
    {
        $adminEmail = $this->contract->admin_email ?: ($this->lead?->admin_email ?: config('venatomar.legal.contact_email'));
        $accountRoute = in_array($adminEmail, ['contact@venatomar.com', 'ayuda@venatomar.com']) ? 'solicitar.account' : 'account';
        $locale = $this->contract->locale ?: ($this->lead?->locale ?: 'es');
        $routeLocale = \App\Support\Venatomar::isLocale($locale) ? $locale : config('venatomar.default_locale');

        return new Content(
            view: 'mail.loan-contract',
            text: 'mail.loan-contract-text',
            with: [
                'name' => $this->contract->first_name,
                'reference' => $this->contract->reference,
                'attachmentName' => $this->attachmentFilename(),
                'amount' => $this->contract->money(),
                'term' => $this->contract->term_months,
                'monthlyPayment' => $this->contract->monthlyPaymentFormatted(),
                'interestRate' => $this->contract->interest_rate,
                'documentFee' => ($this->contract->document_fee && (float) $this->contract->document_fee > 0)
                    ? $this->contract->formattedDocumentFee()
                    : null,
                'contactEmail' => $adminEmail,
                'accountUrl' => route($accountRoute, ['locale' => $routeLocale]),
                'privacyUrl' => route('legal', ['locale' => $routeLocale, 'page' => 'privacy-policy']),
                'date' => ($this->contract->contract_date ?? now())->format('d/m/Y'),
            ],
        );
    }

    /** Attach the rendered Loan Contract PDF. */
    public function attachments(): array
    {
        $pdf = LoanContractPdf::render($this->contract);

        return [
            Attachment::fromData(fn () => $pdf, $this->attachmentFilename())
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
