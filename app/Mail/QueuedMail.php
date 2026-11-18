<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Base for every Venatomar e-mail: queued, 3 attempts with growing backoff,
 * definitive failures logged in storage/logs/mail.log.
 * "From" is ALWAYS the authenticated SMTP mailbox (MAIL_FROM_ADDRESS).
 */
abstract class QueuedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    /** @var array<int,int> seconds */
    public array $backoff = [60, 300, 900];

    protected function baseEnvelope(string $subject, ?Address $replyTo = null): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: $replyTo ? [$replyTo] : [],
            subject: $subject,
        );
    }

    public function failed(Throwable $e): void
    {
        Log::channel('mail')->error('Mail delivery failed definitively', [
            'mail' => static::class,
            'context' => $this->failureContext(),
            'error' => $e->getMessage(),
        ]);
        $this->onFailed();
    }

    /** Non-personal identifiers only. */
    protected function failureContext(): array
    {
        return [];
    }

    protected function onFailed(): void {}
}
