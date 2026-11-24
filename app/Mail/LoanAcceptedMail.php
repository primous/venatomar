<?php

namespace App\Mail;

use App\Models\InsuranceDocument;
use App\Models\Lead;
use App\Support\InsurancePdf;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Official loan acceptance notice sent to the borrower with the personalised
 * credit insurance information sheet (PDF) attached.
 */
class LoanAcceptedMail extends QueuedMail
{
    public function __construct(
        public Lead $lead,
        public InsuranceDocument $insuranceDocument
    ) {
        $this->locale($insuranceDocument->locale ?: ($lead->locale ?: 'es'));
    }

    public function envelope(): Envelope
    {
        return $this->baseEnvelope(__('mail.accepted.subject', ['reference' => $this->lead->reference]));
    }

    public function attachmentFilename(): string
    {
        $locale = $this->insuranceDocument->locale ?: ($this->lead->locale ?: 'es');
        $ref = $this->insuranceDocument->reference ?: $this->lead->reference;

        return trans('mail.accepted.attachment_filename', ['reference' => $ref], $locale);
    }

    public function content(): Content
    {
        $locale = $this->insuranceDocument->locale ?: ($this->lead->locale ?: 'es');
        $routeLocale = \App\Support\Venatomar::isLocale($locale) ? $locale : config('venatomar.default_locale');
        $accountRoute = in_array($this->lead->admin_email, ['contact@venatomar.com', 'ayuda@venatomar.com']) ? 'solicitar.account' : 'account';

        return new Content(
            view: 'mail.loan-accepted',
            text: 'mail.loan-accepted-text',
            with: [
                'firstName' => $this->lead->first_name,
                'reference' => $this->lead->reference,
                'docReference' => $this->insuranceDocument->reference,
                'attachmentName' => $this->attachmentFilename(),
                'documentFee' => ($this->insuranceDocument->document_fee && (float) $this->insuranceDocument->document_fee > 0)
                    ? $this->insuranceDocument->formattedDocumentFee()
                    : null,
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

    /** Attach the generated personalised credit insurance information sheet PDF. */
    public function attachments(): array
    {
        $pdf = InsurancePdf::render($this->insuranceDocument, 'information');

        return [
            Attachment::fromData(fn () => $pdf, $this->attachmentFilename())
                ->withMime('application/pdf'),
        ];
    }

    protected function failureContext(): array
    {
        return [
            'lead' => $this->lead->reference,
            'insurance_doc' => $this->insuranceDocument->reference,
        ];
    }
}
