<?php

namespace App\Support;

use Illuminate\Mail\PendingMail;
use Illuminate\Support\Facades\Mail;

/**
 * Resolves the dedicated SMTP mailer and From address for each admin inbox
 * (info@, contact@, ayuda@), falling back safely to the default mailer + Reply-To
 * when per-admin SMTP credentials are not configured in .env.
 */
final class AdminMailer
{
    public const MAILERS = [
        'contact@venatomar.com' => 'smtp_contact',
        'ayuda@venatomar.com' => 'smtp_ayuda',
    ];

    public static function mailerName(?string $adminEmail): ?string
    {
        $email = strtolower(trim((string) $adminEmail));
        $mailer = self::MAILERS[$email] ?? null;

        if ($mailer && filled(config("mail.mailers.{$mailer}.password"))) {
            return $mailer;
        }

        return null;
    }

    public static function fromAddress(?string $adminEmail): string
    {
        $email = strtolower(trim((string) $adminEmail));
        if ($email !== '' && self::mailerName($email) !== null) {
            return $email;
        }

        return (string) config('mail.from.address');
    }

    public static function to(mixed $recipient, ?string $adminEmail = null): PendingMail
    {
        $mailer = self::mailerName($adminEmail);

        return $mailer ? Mail::mailer($mailer)->to($recipient) : Mail::to($recipient);
    }
}
