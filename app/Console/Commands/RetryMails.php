<?php

namespace App\Console\Commands;

use App\Actions\SubmitLead;
use App\Models\Lead;
use Illuminate\Console\Command;

/**
 * Re-queues the e-mails of leads whose delivery failed definitively
 * (mail_failed_at set) or that were never sent.
 */
class RetryMails extends Command
{
    protected $signature = 'venatomar:retry-mails
                            {--all : Also include leads older than 7 days that were never sent}
                            {--dry-run : Only list the leads}';

    protected $description = 'Re-queue failed Venatomar lead confirmation e-mails';

    public function handle(): int
    {
        $leads = Lead::query()
            ->where(function ($q) {
                $q->whereNotNull('mail_failed_at')
                    ->orWhere(fn ($q) => $q->whereNull('confirmation_mail_sent_at')->where('created_at', '<', now()->subMinutes(30)));
            })
            ->when(! $this->option('all'), fn ($q) => $q->where('created_at', '>=', now()->subDays(7)))
            ->orderBy('id')
            ->get();

        if ($leads->isEmpty()) {
            $this->info('Nothing to retry.');

            return self::SUCCESS;
        }

        foreach ($leads as $lead) {
            $this->line(sprintf('%s  internal:%s  confirmation:%s', $lead->reference,
                $lead->internal_mail_sent_at ? 'sent' : 'pending', $lead->confirmation_mail_sent_at ? 'sent' : 'pending'));

            if (! $this->option('dry-run')) {
                $lead->forceFill(['mail_failed_at' => null])->saveQuietly();
                SubmitLead::queueMails($lead);
            }
        }

        $this->info($this->option('dry-run') ? 'Dry run: nothing queued.' : $leads->count().' lead(s) re-queued.');

        return self::SUCCESS;
    }
}
