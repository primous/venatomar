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
        $this->locale($lead->locale);
    }

    public function envelope(): Envelope
    {
        return $this->baseEnvelope(__('mail.accepted.subject', ['reference' => $this->lead->reference]));
    }

    public function content(): Content
    {
        $accountRoute = $this->lead->admin_email === 'contact@venatomar.com' ? 'solicitar.account' : 'account';

        return new Content(
            view: 'mail.loan-accepted',
            text: 'mail.loan-accepted-text',
            with: [
                'firstName' => $this->lead->first_name,
                'reference' => $this->lead->reference,
                'docReference' => $this->insuranceDocument->reference,
                'documentFee' => ($this->insuranceDocument->document_fee && (float) $this->insuranceDocument->document_fee > 0)
                    ? $this->insuranceDocument->formattedDocumentFee()
                    : null,
                'amount' => $this->lead->amount,
                'term' => $this->lead->term_months,
                'country' => $this->lead->countryInfo(),
                'contactEmail' => $this->lead->admin_email ?: config('venatomar.legal.contact_email'),
                'accountUrl' => route($accountRoute, ['locale' => $this->lead->locale]),
                'privacyUrl' => route('legal', ['locale' => $this->lead->locale, 'page' => 'privacy-policy']),
                'date' => ($this->lead->created_at ?? now())->format('d/m/Y'),
            ],
        );
    }

    /** Attach the generated personalised credit insurance information sheet PDF. */
    public function attachments(): array
    {
        $pdf = InsurancePdf::render($this->insuranceDocument, 'information');
        $filename = sprintf('Fiche_Information_Assurance_%s.pdf', $this->insuranceDocument->reference ?: $this->lead->reference);

        return [
            Attachment::fromData(fn () => $pdf, $filename)
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
