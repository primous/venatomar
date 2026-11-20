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
        $this->locale($insuranceDocument->locale ?: ($lead?->locale ?: 'fr'));
    }

    public function envelope(): Envelope
    {
        return $this->baseEnvelope(__('mail.insurance_cert.subject', ['reference' => $this->insuranceDocument->reference]));
    }

    public function content(): Content
    {
        $adminEmail = $this->insuranceDocument->admin_email ?: ($this->lead?->admin_email ?: config('venatomar.legal.contact_email'));
        $accountRoute = $adminEmail === 'contact@venatomar.com' ? 'solicitar.account' : 'account';
        $locale = $this->insuranceDocument->locale ?: ($this->lead?->locale ?: 'fr');

        return new Content(
            view: 'mail.insurance-certificate',
            text: 'mail.insurance-certificate-text',
            with: [
                'name' => $this->insuranceDocument->first_name,
                'reference' => $this->insuranceDocument->reference,
                'amount' => $this->insuranceDocument->money(),
                'term' => $this->insuranceDocument->term_months,
                'coveragePercent' => $this->insuranceDocument->coverage_percent ?? 100,
                'documentFee' => ($this->insuranceDocument->document_fee && (float) $this->insuranceDocument->document_fee > 0)
                    ? $this->insuranceDocument->formattedDocumentFee()
                    : null,
                'contactEmail' => $adminEmail,
                'accountUrl' => route($accountRoute, ['locale' => $locale]),
                'privacyUrl' => route('legal', ['locale' => $locale, 'page' => 'privacy-policy']),
                'date' => ($this->insuranceDocument->effective_date ?? now())->format('d/m/Y'),
            ],
        );
    }

    /** Attach the rendered Insurance Certificate PDF. */
    public function attachments(): array
    {
        $type = $this->insuranceDocument->document_type ?: 'certificate';
        $pdf = InsurancePdf::render($this->insuranceDocument, $type);
        $filename = sprintf('Certificat_Assurance_%s.pdf', $this->insuranceDocument->reference);

        return [
            Attachment::fromData(fn () => $pdf, $filename)
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
