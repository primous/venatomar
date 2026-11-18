<?php

namespace Tests\Feature;

use App\Livewire\AccountDashboard;
use App\Livewire\MagicLinkRequest;
use App\Mail\DeletionRequested;
use App\Mail\MagicLoginLink;
use App\Models\Applicant;
use App\Models\DeletionRequest;
use App\Models\Lead;
use App\Models\MagicLoginToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    private function applicant(): Applicant
    {
        $applicant = Applicant::create(['email' => 'ana@example.com', 'phone' => '+34612345678', 'locale' => 'es']);
        Lead::create([
            'applicant_id' => $applicant->id, 'locale' => 'es', 'country' => 'es', 'currency' => 'EUR',
            'amount' => 1500, 'term_months' => 12, 'monthly_income' => 1600, 'employment_status' => 'employee', 'defaulter_list' => false,
            'first_name' => 'Ana', 'last_name' => 'Ruiz', 'document_number' => '12345678Z', 'birthday' => '1991-01-01',
            'email' => 'ana@example.com', 'phone' => '+34612345678',
            'geo_zip' => '28001', 'geo_province' => '28', 'geo_city' => 'Madrid', 'home_street' => 'Gran Vía', 'home_number' => '1',
            'payout_method' => 'iban', 'bank_account' => 'ES9121000418450200051332',
            'gender' => 'female', 'loan_purpose' => 'travel', 'marital_status' => 'single', 'company_sector' => 'retail',
            'education' => 'secondary', 'home_status' => 'tenant', 'regular_expenses' => 500, 'has_debt' => false, 'has_car' => false,
            'consent_at' => now(), 'consent_version' => 'test',
        ]);

        return $applicant;
    }

    public function test_magic_link_is_sent_only_on_match_but_the_answer_is_identical(): void
    {
        Mail::fake();
        $this->applicant();

        Livewire::test(MagicLinkRequest::class, ['locale' => 'es'])
            ->set('email', 'nobody@example.com')->set('phone', '612345678')
            ->call('send')->assertSet('sent', true)
            ->assertSee('Si existe una solicitud con estos datos');
        Mail::assertNothingQueued();

        Livewire::test(MagicLinkRequest::class, ['locale' => 'es'])
            ->set('email', 'ana@example.com')->set('phone', '600000000')
            ->call('send')->assertSet('sent', true);
        Mail::assertNothingQueued();

        Livewire::test(MagicLinkRequest::class, ['locale' => 'es'])
            ->set('email', 'ANA@example.com')->set('phone', '612 34 56 78')
            ->call('send')->assertSet('sent', true)
            ->assertSee('Si existe una solicitud con estos datos');
        Mail::assertQueued(MagicLoginLink::class, fn ($m) => $m->hasTo('ana@example.com') && str_contains($m->url, '/es/my-account/login/'));
    }

    public function test_valid_link_logs_in_once_then_is_refused(): void
    {
        $applicant = $this->applicant();
        [, $token] = MagicLoginToken::issue($applicant);
        $url = URL::temporarySignedRoute('account.login', now()->addMinutes(15), ['locale' => 'es', 'token' => $token]);

        $this->get($url)->assertRedirect('/es/my-account/dashboard');
        $this->assertAuthenticatedAs($applicant, 'applicant');
        $this->get('/es/my-account/dashboard')->assertOk()->assertSee('VN-')->assertSee('•••• 1332')->assertDontSee('ES9121000418450200051332');

        // Reuse => refused.
        $this->post('/es/my-account/logout');
        $this->assertGuest('applicant');
        $this->get($url)->assertRedirect('/es/my-account');
        $this->assertGuest('applicant');
    }

    public function test_expired_link_is_refused(): void
    {
        $applicant = $this->applicant();
        [, $token] = MagicLoginToken::issue($applicant);
        $url = URL::temporarySignedRoute('account.login', now()->addMinutes(15), ['locale' => 'es', 'token' => $token]);

        $this->travel(16)->minutes();
        $this->get($url)->assertForbidden(); // signature expired
        $this->assertGuest('applicant');
    }

    public function test_token_expiry_is_enforced_even_with_a_valid_signature(): void
    {
        $applicant = $this->applicant();
        [$model, $token] = MagicLoginToken::issue($applicant);
        $model->update(['expires_at' => now()->subMinute()]);
        $url = URL::temporarySignedRoute('account.login', now()->addMinutes(15), ['locale' => 'es', 'token' => $token]);

        $this->get($url)->assertRedirect('/es/my-account')->assertSessionHas('account_error');
        $this->assertGuest('applicant');
    }

    public function test_tampered_link_is_refused(): void
    {
        $applicant = $this->applicant();
        [, $token] = MagicLoginToken::issue($applicant);
        $url = URL::temporarySignedRoute('account.login', now()->addMinutes(15), ['locale' => 'es', 'token' => $token]);

        $this->get(str_replace($token, str_repeat('a', 64), $url))->assertForbidden();
    }

    public function test_deletion_request_creates_record_and_notifies_internal_mailbox(): void
    {
        Mail::fake();
        $applicant = $this->applicant();
        $this->actingAs($applicant, 'applicant');

        Livewire::test(AccountDashboard::class, ['locale' => 'es'])
            ->call('requestDeletion')
            ->assertSee('Solicitud de eliminación registrada');

        $this->assertSame(1, DeletionRequest::where('applicant_id', $applicant->id)->where('status', 'pending')->count());
        Mail::assertQueued(DeletionRequested::class, fn ($m) => $m->hasTo('info@venatomar.com'));

        // A second click does not duplicate it.
        Livewire::test(AccountDashboard::class, ['locale' => 'es'])->call('requestDeletion');
        $this->assertSame(1, DeletionRequest::count());
    }
}
