<?php

namespace App\Mail;

use App\Models\InsuranceDocument;
use App\Models\Lead;
use App\Support\InsurancePdf;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Official credit insurance certificate sent to the client with the certificate PDF attached.
 */
class InsuranceCertificateMail extends QueuedMail
{
    public function __construct(
        public InsuranceDocument $insuranceDocument,
        public ?Lead $lead = null
    ) {
        $this->locale($insuranceDocument->locale ?: ($lead?->locale ?: 'es'));
    }

    public function envelope(): Envelope
    {
        return $this->baseEnvelope(__('mail.insurance_cert.subject', ['reference' => $this->insuranceDocument->reference]));
    }

    public function attachmentFilename(): string
    {
        $locale = $this->insuranceDocument->locale ?: ($this->lead?->locale ?: 'es');

        return trans('mail.insurance_cert.attachment_filename', ['reference' => $this->insuranceDocument->reference], $locale);
    }

    public function content(): Content
    {
        $adminEmail = $this->insuranceDocument->admin_email ?: ($this->lead?->admin_email ?: config('venatomar.legal.contact_email'));
        $accountRoute = in_array($adminEmail, ['contact@venatomar.com', 'ayuda@venatomar.com']) ? 'solicitar.account' : 'account';
        $locale = $this->insuranceDocument->locale ?: ($this->lead?->locale ?: 'es');
        $routeLocale = \App\Support\Venatomar::isLocale($locale) ? $locale : config('venatomar.default_locale');

        return new Content(
            view: 'mail.insurance-certificate',
            text: 'mail.insurance-certificate-text',
            with: [
                'name' => $this->insuranceDocument->first_name,
                'reference' => $this->insuranceDocument->reference,
                'attachmentName' => $this->attachmentFilename(),
                'amount' => $this->insuranceDocument->money(),
                'term' => $this->insuranceDocument->term_months,
                'coveragePercent' => $this->insuranceDocument->coverage_percent ?? 100,
                'documentFee' => ($this->insuranceDocument->document_fee && (float) $this->insuranceDocument->document_fee > 0)
                    ? $this->insuranceDocument->formattedDocumentFee()
                    : null,
                'contactEmail' => $adminEmail,
                'accountUrl' => route($accountRoute, ['locale' => $routeLocale]),
                'privacyUrl' => route('legal', ['locale' => $routeLocale, 'page' => 'privacy-policy']),
                'date' => ($this->insuranceDocument->effective_date ?? now())->format('d/m/Y'),
            ],
        );
    }

    /** Attach the rendered Insurance Certificate PDF. */
    public function attachments(): array
    {
        $type = $this->insuranceDocument->document_type ?: 'certificate';
        $pdf = InsurancePdf::render($this->insuranceDocument, $type);

        return [
            Attachment::fromData(fn () => $pdf, $this->attachmentFilename())
                ->withMime('application/pdf'),
        ];
    }

    protected function failureContext(): array
    {
        return [
            'insurance' => $this->insuranceDocument->reference,
            'email' => $this->insuranceDocument->email,
        ];
    }
}
