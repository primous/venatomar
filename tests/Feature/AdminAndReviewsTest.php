<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Filament\Resources\DeletionRequests\Pages\ListDeletionRequests;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Leads\Pages\ViewLead;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Livewire\LeaveReview;
use App\Models\Applicant;
use App\Models\DeletionRequest;
use App\Models\Lead;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAndReviewsTest extends TestCase
{
    use RefreshDatabase;

    private function lead(?Applicant $applicant = null): Lead
    {
        return Lead::create([
            'applicant_id' => $applicant?->id, 'locale' => 'es', 'country' => 'es', 'currency' => 'EUR',
            'amount' => 1500, 'term_months' => 12, 'monthly_income' => 1600, 'employment_status' => 'employee', 'defaulter_list' => false,
            'first_name' => 'Ana', 'last_name' => 'Ruiz', 'document_number' => '12345678Z', 'birthday' => '1991-01-01',
            'email' => 'ana@example.com', 'phone' => '+34612345678',
            'geo_zip' => '28001', 'geo_province' => '28', 'geo_city' => 'Madrid', 'home_street' => 'Gran Vía', 'home_number' => '1',
            'payout_method' => 'iban', 'bank_account' => 'ES9121000418450200051332',
            'gender' => 'female', 'loan_purpose' => 'travel', 'marital_status' => 'single', 'company_sector' => 'retail',
            'education' => 'secondary', 'home_status' => 'tenant', 'regular_expenses' => 500, 'has_debt' => false, 'has_car' => false,
            'consent_at' => now(), 'consent_version' => 'test',
        ]);
    }

    private function admin(): User
    {
        return User::create(['name' => 'Admin', 'email' => 'info@venatomar.com', 'password' => 'secret-password-123']);
    }

    private function fillReview($component)
    {
        return $component->call('setRating', 4)
            ->set('author_name', 'Lucía P.')
            ->set('title', 'Muy claro')
            ->set('body', 'El formulario es claro y rápido de rellenar.')
            ->set('consent', true);
    }

    // --- Back office -------------------------------------------------------

    public function test_admin_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_admin_pages_render(): void
    {
        $lead = $this->lead();
        $this->actingAs($this->admin());

        $this->get('/admin')->assertOk()->assertSee('Demandes aujourd’hui');
        $this->get('/admin/leads')->assertOk()->assertSee($lead->reference);
        $this->get('/admin/leads/'.$lead->id)->assertOk()
            ->assertSee('ES9121000418450200051332')->assertSee('Gran Vía');
        $this->get('/admin/reviews')->assertOk();
        $this->get('/admin/deletion-requests')->assertOk();
    }

    public function test_admin_is_french_by_default_and_switches_language(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin/leads')->assertOk()->assertSee('Demandes')->assertSee('Référence');
        $this->from('/admin/leads')->get('/admin/lang/es')->assertRedirect('/admin/leads');
        $this->get('/admin/leads')->assertOk()->assertSee('Solicitudes')->assertSee('Referencia');
        $this->get('/admin/lang/en');
        $this->get('/admin/leads')->assertOk()->assertSee('Requests')->assertSee('Reference');
        $this->get('/admin/lang/xx')->assertNotFound();
    }

    public function test_admin_option_labels_are_translated(): void
    {
        $lead = $this->lead();
        $this->actingAs($this->admin());

        $this->get('/admin/leads/'.$lead->id)->assertOk()->assertSee('Locataire')->assertSee('Voyage');
        $this->get('/admin/lang/es');
        $this->get('/admin/leads/'.$lead->id)->assertOk()->assertSee('Inquilino/a')->assertSee('Viajes');
    }

    public function test_admin_changes_lead_status(): void
    {
        $lead = $this->lead();
        $this->actingAs($this->admin());

        Livewire::test(ViewLead::class, ['record' => $lead->getRouteKey()])
            ->callAction('changeStatus', data: ['status' => LeadStatus::Accepted->value])
            ->assertHasNoActionErrors();

        $this->assertSame(LeadStatus::Accepted, $lead->fresh()->status);
        Livewire::test(ListLeads::class)->assertCanSeeTableRecords([$lead]);
    }

    public function test_admin_publishes_a_pending_review(): void
    {
        $review = Review::create(['locale' => 'es', 'author_name' => 'Jorge', 'rating' => 5, 'body' => 'Texto real de prueba.', 'reviewed_at' => today(), 'is_published' => false]);
        $this->actingAs($this->admin());

        Livewire::test(ListReviews::class)->assertCanSeeTableRecords([$review])
            ->callTableAction('publish', $review);

        $this->assertTrue($review->fresh()->is_published);
    }

    public function test_admin_erases_applicant_data(): void
    {
        $applicant = Applicant::create(['email' => 'ana@example.com', 'phone' => '+34612345678', 'locale' => 'es']);
        $this->lead($applicant);
        $deletion = DeletionRequest::create(['applicant_id' => $applicant->id, 'email' => $applicant->email, 'locale' => 'es', 'status' => 'pending']);
        $this->actingAs($this->admin());

        Livewire::test(ListDeletionRequests::class)->callTableAction('erase', $deletion);

        $this->assertSame(0, Lead::count());
        $this->assertNull(Applicant::find($applicant->id));
        $this->assertSame('done', $deletion->fresh()->status);
    }

    // --- Public review form ------------------------------------------------

    public function test_review_form_is_on_the_home_page(): void
    {
        $this->get('/es')->assertOk()->assertSee('Deja tu opinión')->assertSee('id="leave-review"', false);
    }

    public function test_submitted_review_waits_for_moderation(): void
    {
        $c = Livewire::test(LeaveReview::class, ['locale' => 'es']);
        $this->travel(10)->seconds();
        $this->fillReview($c)->call('submit')->assertHasNoErrors()->assertSet('sent', true);

        $review = Review::sole();
        $this->assertFalse($review->is_published);
        $this->assertFalse($review->is_verified);
        $this->assertSame('site', $review->source);
        $this->get('/es')->assertDontSee('El formulario es claro');
    }

    public function test_review_form_rejects_bots_links_and_missing_rating(): void
    {
        $c = Livewire::test(LeaveReview::class, ['locale' => 'es']);
        $this->fillReview($c)->call('submit')->assertHasErrors('form'); // too fast

        $this->travel(10)->seconds();
        $this->fillReview($c)->set('website', 'spam')->call('submit')->assertHasErrors('form');

        $this->fillReview($c)->set('website', '')->set('body', 'Mirad https://spam.example para ganar')->call('submit')->assertHasErrors('body');
        $this->fillReview($c)->set('rating', 0)->call('submit')->assertHasErrors('rating');

        $this->assertSame(0, Review::count());
    }

    public function test_logged_in_applicant_review_is_verified(): void
    {
        $applicant = Applicant::create(['email' => 'ana@example.com', 'phone' => '+34612345678', 'locale' => 'es']);
        $this->lead($applicant);
        $this->actingAs($applicant, 'applicant');

        $c = Livewire::test(LeaveReview::class, ['locale' => 'es'])->assertSee('Verificado');
        $this->travel(10)->seconds();
        $this->fillReview($c)->call('submit')->assertHasNoErrors();

        $review = Review::sole();
        $this->assertTrue($review->is_verified);
        $this->assertSame($applicant->id, $review->applicant_id);
        $this->assertSame('account', $review->source);
    }

    public function test_demo_seeder_creates_25_reviews_locally(): void
    {
        $this->app->detectEnvironment(fn () => 'local');
        $this->seed(\Database\Seeders\DemoReviewsSeeder::class);

        $this->assertSame(25, Review::published('es')->count());
    }
}
