<?php

namespace Tests\Feature;

use App\Livewire\ApplyLoan;
use App\Mail\LeadConfirmation;
use App\Mail\LeadReceivedInternal;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ApplyLoanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        config([
            'venatomar.loan.es.amount_min' => 100, 'venatomar.loan.es.amount_max' => 5000,
            'venatomar.loan.es.term_min' => 3, 'venatomar.loan.es.term_max' => 60,
        ]);
    }

    /** Valid Spanish request, step by step. */
    public static function spanish(array $override = []): array
    {
        return array_replace_recursive([
            1 => ['amount' => '3000', 'term_months' => '24', 'monthly_income' => '1800', 'employment_status' => 'employee', 'defaulter_list' => '0'],
            2 => ['first_name' => 'María', 'last_name' => 'García López', 'document_number' => '12345678z', 'birthday' => ['d' => '05', 'm' => '03', 'y' => '1990'], 'email' => 'Maria@Example.com', 'phone' => '612 345 678', 'accept_privacy' => true],
            3 => ['geo_zip' => '28001', 'geo_province' => '28', 'geo_city' => 'Madrid', 'home_street' => 'Calle de Alcalá', 'home_number' => '12', 'apartment_floor' => '3º B'],
            4 => ['payout_method' => 'transferencia'],
            5 => ['gender' => 'female', 'loan_purpose' => 'car', 'marital_status' => 'single', 'company_sector' => 'health', 'education' => 'higher_long', 'home_status' => 'tenant', 'regular_expenses' => '700', 'has_debt' => '0', 'has_car' => '1', 'car_number' => '1234 bcd'],
        ], $override);
    }

    private function fillAll(string $locale, array $steps)
    {
        $component = Livewire::test(ApplyLoan::class, ['locale' => $locale]);
        foreach ($steps as $step => $fields) {
            foreach ($fields as $field => $value) {
                $component->set($field, $value);
            }
            if ($step < 5) {
                $component->call('next')->assertHasNoErrors()->assertSet('step', $step + 1);
            }
        }

        return $component;
    }

    public function test_full_request_is_stored_encrypted_and_two_mails_are_queued(): void
    {
        $this->travel(10)->seconds();
        $component = $this->fillAll('es', self::spanish());
        $this->travel(10)->seconds();
        $component->call('next')->assertHasNoErrors()->assertRedirect('/es/apply-loan/gracias');

        $lead = Lead::sole();
        $this->assertSame('VN-'.now()->format('Y').'-000001', $lead->reference);
        $this->assertSame('maria@example.com', $lead->email);
        $this->assertSame('+34612345678', $lead->phone);
        $this->assertSame('12345678Z', $lead->document_number);
        $this->assertSame('transferencia', $lead->payout_method);
        $this->assertNull($lead->bank_account);
        $this->assertSame('1990-03-05', $lead->birthday);
        $this->assertSame('1234 BCD', $lead->car_number);
        $this->assertSame('received', $lead->status->value);
        $this->assertSame(config('venatomar.consent_version'), $lead->consent_version);
        $this->assertNotNull($lead->consent_at);

        // Encrypted at rest.
        $raw = DB::table('leads')->first();
        foreach (['document_number', 'birthday'] as $col) {
            $this->assertStringNotContainsString($lead->{$col}, $raw->{$col});
        }

        Mail::assertQueued(LeadReceivedInternal::class, function ($mail) use ($lead) {
            return $mail->hasTo('info@venatomar.com')
                && ! $mail->hasReplyTo('maria@example.com')
                && ! $mail->hasFrom('maria@example.com')
                && str_contains($mail->envelope()->subject, 'Ficha '.$lead->reference.' (ES)');
        });
        Mail::assertQueued(LeadConfirmation::class, fn ($mail) => $mail->hasTo('maria@example.com') && $mail->locale === 'es');
        Mail::assertQueuedCount(2);

        // Thanks page shows the reference and a masked e-mail.
        $this->withSession(['last_lead_id' => $lead->id])->get('/es/apply-loan/gracias')
            ->assertOk()->assertSee($lead->reference)->assertSee('ma•••@example.com')->assertSee('noindex', false);
    }

    public function test_internal_mail_contains_everything_and_confirmation_contains_no_sensitive_data(): void
    {
        $this->travel(10)->seconds();
        $component = $this->fillAll('es', self::spanish());
        $this->travel(10)->seconds();
        $component->call('next');
        $lead = Lead::sole();

        $internal = (new LeadReceivedInternal($lead))->render();
        $this->assertStringContainsString('Transferencia bancaria', $internal);
        $this->assertStringContainsString('maria@example.com', $internal);
        $this->assertStringContainsString('+34612345678', $internal);
        $this->assertStringContainsString('3.000 EUR', $internal);
        $this->assertStringContainsString('24 meses', $internal);

        $confirmation = (new LeadConfirmation($lead))->render();
        foreach (['ES91', '0005 1332', '12345678Z', '1.800', '1800'] as $secret) {
            $this->assertStringNotContainsString($secret, $confirmation);
        }
        $this->assertStringContainsString($lead->reference, $confirmation);
    }

    public function test_cannot_skip_a_step_without_valid_data(): void
    {
        Livewire::test(ApplyLoan::class, ['locale' => 'es'])
            ->call('next')
            ->assertHasErrors(['amount', 'term_months', 'monthly_income', 'employment_status', 'defaulter_list'])
            ->assertSet('step', 1)
            ->set('amount', '99999')
            ->call('next')
            ->assertHasErrors(['amount' => 'max'])
            ->assertSet('step', 1);
    }

    public function test_country_validators_are_applied(): void
    {
        $component = $this->fillAll('es', [1 => self::spanish()[1]]);
        $component->set('document_number', '12345678A')
            ->set('birthday', ['d' => '1', 'm' => '1', 'y' => (string) (now()->year - 17)])
            ->set('phone', '912345678')
            ->call('next')
            ->assertHasErrors(['document_number', 'birthday', 'phone', 'first_name', 'accept_privacy'])
            ->assertSet('step', 2);
    }

    public function test_progress_starts_at_zero_and_reaches_100(): void
    {
        $this->assertSame(0, Livewire::test(ApplyLoan::class, ['locale' => 'es'])->instance()->progress());

        $component = $this->fillAll('es', self::spanish());
        $this->assertSame(100, $component->instance()->progress());
    }

    public function test_netherlands_never_collects_a_bsn(): void
    {
        $component = Livewire::test(ApplyLoan::class, ['locale' => 'nl'])->set('step', 2);
        $this->assertArrayNotHasKey('document_number', $component->instance()->rulesForStep(2));
        $component->assertDontSeeHtml('wire:model.live.blur="document_number"');
    }

    public function test_madagascar_mobile_money_payout(): void
    {
        $c = Livewire::test(ApplyLoan::class, ['locale' => 'mg'])->assertSet('payout_method', 'transferencia');
        $rules = $c->set('payout_method', 'mobile_money')->instance()->rulesForStep(4);
        $this->assertArrayHasKey('payout_method', $rules);
        $this->assertArrayNotHasKey('mm_operator', $rules);
        $this->assertArrayNotHasKey('mm_number', $rules);
        $this->assertArrayNotHasKey('bank_account', $rules);
    }

    public function test_honeypot_blocks_the_request(): void
    {
        $this->travel(10)->seconds();
        $component = $this->fillAll('es', self::spanish());
        $this->travel(10)->seconds();
        $component->set('website', 'http://spam.example')->call('next')->assertHasErrors('form');
        $this->assertSame(0, Lead::count());
        Mail::assertNothingQueued();
    }

    public function test_submitting_faster_than_four_seconds_is_refused(): void
    {
        $this->freezeTime();
        $component = $this->fillAll('es', self::spanish());
        $component->call('next')->assertHasErrors('form');
        $this->assertSame(0, Lead::count());
    }

    public function test_rate_limit_three_requests_per_email_per_day(): void
    {
        for ($i = 1; $i <= 4; $i++) {
            $component = $this->fillAll('es', self::spanish());
            $this->travel(10)->seconds();
            $component->call('next');
            if ($i <= 3) {
                $component->assertHasNoErrors();
            } else {
                $component->assertHasErrors('form');
            }
        }
        $this->assertSame(3, Lead::count());
    }

    public function test_rate_limit_five_requests_per_ip_per_hour(): void
    {
        for ($i = 1; $i <= 6; $i++) {
            $component = $this->fillAll('es', self::spanish([2 => ['email' => "user$i@example.com"]]));
            $this->travel(10)->seconds();
            $component->call('next');
            if ($i <= 5) {
                $component->assertHasNoErrors();
            } else {
                $component->assertHasErrors('form');
            }
        }
        $this->assertSame(5, Lead::count());
    }

    public function test_definitive_mail_failure_flags_the_lead_and_retry_command_requeues(): void
    {
        $this->travel(10)->seconds();
        $component = $this->fillAll('es', self::spanish());
        $this->travel(10)->seconds();
        $component->call('next');
        $lead = Lead::sole();

        (new LeadReceivedInternal($lead))->failed(new \RuntimeException('SMTP down'));
        $this->assertNotNull($lead->fresh()->mail_failed_at);

        Mail::fake(); // reset the queue spy
        $this->artisan('venatomar:retry-mails')->assertSuccessful();
        Mail::assertQueued(LeadReceivedInternal::class);
        $this->assertNull($lead->fresh()->mail_failed_at);
    }

    public function test_mailables_are_queued_with_three_tries_and_backoff(): void
    {
        $mail = new LeadReceivedInternal(new Lead);
        $this->assertSame(3, $mail->tries);
        $this->assertSame([60, 300, 900], $mail->backoff);
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $mail);
    }
}
