<?php

namespace App\Mail;

use App\Models\Applicant;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class MagicLoginLink extends QueuedMail
{
    public function __construct(public Applicant $applicant, public string $url, public string $userLocale)
    {
        $this->locale($userLocale);
        $this->useAdminSender($applicant->leads()->latest()->value('admin_email') ?: config('venatomar.legal.contact_email'));
    }

    public function envelope(): Envelope
    {
        $adminEmail = $this->applicant->leads()->latest()->value('admin_email') ?: config('venatomar.legal.contact_email');

        return $this->baseEnvelope(
            __('mail.magic.subject', ['brand' => config('venatomar.brand')]),
            $adminEmail ? new Address($adminEmail, config('mail.from.name')) : null
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.magic-link',
            text: 'mail.magic-link-text',
            with: [
                'url' => $this->url,
                'minutes' => (int) config('venatomar.magic_link_ttl_minutes'),
                'privacyUrl' => route('legal', ['locale' => $this->userLocale, 'page' => 'privacy-policy']),
            ],
        );
    }

    protected function failureContext(): array
    {
        return ['applicant_id' => $this->applicant->id];
    }
}
