<?php

namespace App\Console\Commands;

use App\Mail\ReviewInvitation;
use App\Models\Lead;
use App\Models\Review;
use App\Support\AdminMailer;
use Illuminate\Console\Command;

/**
 * Queues ONE review invitation per applicant, `delay_days` after their request.
 * Skips applicants already invited, who already left a review, or whose confirmation never left.
 */
class InviteReviews extends Command
{
    protected $signature = 'venatomar:invite-reviews';

    protected $description = 'Invite applicants to leave a review a few days after their request';

    public function handle(): int
    {
        $cfg = config('venatomar.reviews.invitation');
        if (! ($cfg['enabled'] ?? false)) {
            $this->info('Review invitations are disabled.');

            return self::SUCCESS;
        }

        $until = now()->subDays((int) $cfg['delay_days']);
        $from = $until->copy()->subDays((int) $cfg['window_days']);
        $sent = 0;

        Lead::whereNull('review_invited_at')
            ->whereNotNull('confirmation_mail_sent_at')
            ->whereBetween('created_at', [$from, $until])
            ->orderBy('id')
            ->each(function (Lead $lead) use (&$sent) {
                $alreadyHandled = Lead::where('email', $lead->email)->whereNotNull('review_invited_at')->exists()
                    || ($lead->applicant_id && Review::where('applicant_id', $lead->applicant_id)->exists());

                // Mark every lead as handled so nobody is ever asked twice.
                $lead->forceFill(['review_invited_at' => now()])->saveQuietly();
                if ($alreadyHandled) {
                    return;
                }

                AdminMailer::to($lead->email, $lead->admin_email)->locale($lead->locale)->queue(new ReviewInvitation($lead));

                try {
                    app(\App\Services\WebPushService::class)->sendToEmail(
                        $lead->email,
                        '¿Cómo valora su experiencia con Venatomar?',
                        'Su opinión es muy valiosa para nosotros. Déjenos su valoración en unos segundos.',
                        ['url' => '/']
                    );
                } catch (\Throwable $e) {}
                $sent++;
            });

        $this->info("$sent review invitation(s) queued.");

        return self::SUCCESS;
    }
}
