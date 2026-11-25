<?php

namespace App\Mail;

use App\Support\AdminMailer;
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
 * Uses the assigned admin's dedicated SMTP account when configured in .env,
 * otherwise uses the default authenticated mailbox with Reply-To set to the admin.
 */
abstract class QueuedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    /** @var array<int,int> seconds */
    public array $backoff = [60, 300, 900];

    public ?string $adminSenderEmail = null;

    protected function useAdminSender(?string $adminEmail): void
    {
        $this->adminSenderEmail = $adminEmail ? strtolower(trim($adminEmail)) : null;

        if ($mailer = AdminMailer::mailerName($this->adminSenderEmail)) {
            $this->mailer($mailer);
        }
    }

    protected function baseEnvelope(string $subject, Address|string|null $replyTo = null): Envelope
    {
        $replyToArray = [];
        if ($replyTo instanceof Address) {
            $replyToArray = [$replyTo];
        } elseif (is_string($replyTo) && ! empty($replyTo)) {
            $replyToArray = [new Address($replyTo, config('mail.from.name'))];
        }

        $fromAddress = AdminMailer::fromAddress($this->adminSenderEmail);

        return new Envelope(
            from: new Address($fromAddress, config('mail.from.name')),
            replyTo: $replyToArray,
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
