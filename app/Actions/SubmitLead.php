<?php

namespace App\Actions;

use App\Enums\LeadStatus;
use App\Mail\LeadConfirmation;
use App\Mail\LeadReceivedInternal;
use App\Models\Applicant;
use App\Models\Lead;
use App\Support\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Stores the request in the database FIRST, then queues the two e-mails.
 * A queue/SMTP problem can never lose a request: failures are logged and
 * flagged with mail_failed_at (see venatomar:retry-mails).
 */
class SubmitLead
{
    public function handle(Country $country, array $data, array $meta): Lead
    {
        $lead = DB::transaction(function () use ($country, $data, $meta) {
            $applicant = Applicant::firstOrNew(['email' => $data['email']]);
            $applicant->fill(['phone' => $data['phone'], 'locale' => $country->locale])->save();

            return Lead::create($data + [
                'applicant_id' => $applicant->id,
                'status' => LeadStatus::Received,
                'locale' => $country->locale,
                'country' => $country->code,
                'currency' => $country->currency,
                'admin_email' => $meta['admin_email'] ?? ($data['admin_email'] ?? 'info@venatomar.com'),
                'channel' => $meta['channel'] ?? ($data['channel'] ?? 'info'),
                'ip_address' => $meta['ip_address'] ?? null,
                'user_agent' => $meta['user_agent'] ?? null,
                'utm' => $meta['utm'] ?? null,
                'consent_at' => now(),
                'consent_version' => config('venatomar.consent_version'),
            ]);
        });

        self::queueMails($lead);

        return $lead->refresh();
    }

    /** Queue the missing e-mails of a lead. Safe to call again (retry command). */
    public static function queueMails(Lead $lead): void
    {
        try {
            $internalRecipient = $lead->admin_email ?: config('venatomar.lead_recipient');
            if ($lead->internal_mail_sent_at === null) {
                Mail::to($internalRecipient)->queue(new LeadReceivedInternal($lead));
            }
            if ($lead->confirmation_mail_sent_at === null) {
                Mail::to($lead->email)->locale($lead->locale)->queue(new LeadConfirmation($lead));
            }
        } catch (Throwable $e) {
            // The lead is already stored: flag it, never lose it.
            $lead->forceFill(['mail_failed_at' => now()])->saveQuietly();
            Log::channel('mail')->error('Could not queue lead e-mails', ['lead' => $lead->reference, 'error' => $e->getMessage()]);
        }
    }
}
