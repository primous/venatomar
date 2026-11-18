<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Sent once per applicant, a few days after the request: invites them to leave a review. */
class ReviewInvitation extends QueuedMail
{
    public function __construct(public Lead $lead)
    {
        $this->locale($lead->locale);
    }

    public function envelope(): Envelope
    {
        return $this->baseEnvelope(__('mail.review.subject', ['brand' => config('venatomar.brand')]));
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.review-invitation',
            text: 'mail.review-invitation-text',
            with: [
                'firstName' => $this->lead->first_name,
                'reference' => $this->lead->reference,
                'reviewUrl' => route('home', ['locale' => $this->lead->locale]).'#leave-review',
                'privacyUrl' => route('legal', ['locale' => $this->lead->locale, 'page' => 'privacy-policy']),
            ],
        );
    }

    protected function failureContext(): array
    {
        return ['lead' => $this->lead->reference];
    }
}
