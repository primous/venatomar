<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Sends one plain e-mail RIGHT NOW (no queue) to check the SMTP settings.
 *
 *   php artisan venatomar:test-mail            -> to the internal recipient
 *   php artisan venatomar:test-mail you@x.com
 */
class TestMail extends Command
{
    protected $signature = 'venatomar:test-mail {to? : Recipient (default: VENATOMAR_LEAD_RECIPIENT)}';

    protected $description = 'Send a test e-mail immediately to check the SMTP configuration';

    public function handle(): int
    {
        $to = $this->argument('to') ?: config('venatomar.lead_recipient');
        $from = config('mail.from.address');

        $this->line(sprintf('SMTP %s:%s (%s) · user %s · from %s · to %s',
            config('mail.mailers.smtp.host'), config('mail.mailers.smtp.port'),
            config('mail.mailers.smtp.scheme') ?: 'auto', config('mail.mailers.smtp.username'), $from, $to));

        if (blank(config('mail.mailers.smtp.password'))) {
            $this->error('MAIL_PASSWORD is empty in .env.');

            return self::FAILURE;
        }

        try {
            Mail::raw(
                'Test e-mail from '.config('app.name').' sent at '.now()->format('d/m/Y H:i:s').'. SMTP works.',
                fn ($m) => $m->to($to)->subject(config('app.name').' · SMTP test')
            );
        } catch (Throwable $e) {
            $this->error('Sending failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Sent. Check the inbox (and the spam folder) of '.$to.'.');

        return self::SUCCESS;
    }
}
