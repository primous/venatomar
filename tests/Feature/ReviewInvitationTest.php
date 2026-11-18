<?php

namespace Tests\Feature;

use App\Mail\ReviewInvitation;
use App\Models\Applicant;
use App\Models\Lead;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReviewInvitationTest extends TestCase
{
    use RefreshDatabase;

    private function lead(string $email, int $daysAgo, bool $confirmed = true, ?Applicant $applicant = null): Lead
    {
        $lead = Lead::create([
            'applicant_id' => $applicant?->id, 'locale' => 'es', 'country' => 'es', 'currency' => 'EUR',
            'amount' => 1500, 'term_months' => 12, 'monthly_income' => 1600, 'employment_status' => 'employee', 'defaulter_list' => false,
            'first_name' => 'Ana', 'last_name' => 'Ruiz', 'document_number' => '12345678Z', 'birthday' => '1991-01-01',
            'email' => $email, 'phone' => '+34612345678',
            'geo_zip' => '28001', 'geo_province' => '28', 'geo_city' => 'Madrid', 'home_street' => 'Gran Vía', 'home_number' => '1',
            'payout_method' => 'iban', 'bank_account' => 'ES9121000418450200051332',
            'gender' => 'female', 'loan_purpose' => 'travel', 'marital_status' => 'single', 'company_sector' => 'retail',
            'education' => 'secondary', 'home_status' => 'tenant', 'regular_expenses' => 500, 'has_debt' => false, 'has_car' => false,
            'consent_at' => now(), 'consent_version' => 'test',
        ]);
        $lead->forceFill([
            'created_at' => now()->subDays($daysAgo),
            'confirmation_mail_sent_at' => $confirmed ? now()->subDays($daysAgo) : null,
        ])->saveQuietly();

        return $lead;
    }

    public function test_invites_once_after_the_delay(): void
    {
        Mail::fake();
        $due = $this->lead('due@example.com', 8);
        $this->lead('due@example.com', 9);            // same person, second request
        $this->lead('recent@example.com', 2);         // too early
        $this->lead('old@example.com', 60);           // too old
        $this->lead('unconfirmed@example.com', 8, confirmed: false);

        $this->artisan('venatomar:invite-reviews')->assertSuccessful();

        Mail::assertQueued(ReviewInvitation::class, 1);
        Mail::assertQueued(ReviewInvitation::class, fn ($m) => $m->hasTo('due@example.com'));
        $this->assertNotNull($due->fresh()->review_invited_at);

        $this->artisan('venatomar:invite-reviews')->assertSuccessful();
        Mail::assertQueued(ReviewInvitation::class, 1); // never twice
    }

    public function test_skips_applicants_who_already_reviewed(): void
    {
        Mail::fake();
        $applicant = Applicant::create(['email' => 'ana@example.com', 'phone' => '+34612345678', 'locale' => 'es']);
        $this->lead('ana@example.com', 8, applicant: $applicant);
        Review::create(['applicant_id' => $applicant->id, 'locale' => 'es', 'author_name' => 'Ana', 'rating' => 5, 'body' => 'Bien.', 'reviewed_at' => today()]);

        $this->artisan('venatomar:invite-reviews')->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_mail_is_in_the_request_language_and_links_to_the_form(): void
    {
        $lead = $this->lead('due@example.com', 8);
        $html = (new ReviewInvitation($lead))->render();

        $this->assertStringContainsString('Dejar mi opinión', $html);
        $this->assertStringContainsString('/es#leave-review', $html);
        $this->assertStringNotContainsString('ES9121000418450200051332', $html);
    }
}
