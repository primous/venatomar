<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BankClient;
use App\Models\BankClientLoan;
use App\Models\BankTransaction;
use App\Models\InsuranceDocument;
use App\Models\Lead;
use App\Models\LoanApprovalDocument;
use App\Models\LoanContract;
use Database\Seeders\TestClientSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_and_info_admins_have_strict_data_isolation(): void
    {
        $contactAdmin = User::factory()->create([
            'email' => 'contact@venatomar.com',
            'name' => 'Admin Contact',
        ]);

        $infoAdmin = User::factory()->create([
            'email' => 'info@venatomar.com',
            'name' => 'Admin Info',
        ]);

        // Seed both test clients
        $this->seed(TestClientSeeder::class);

        // Verify Yenicko Primous belongs to contact@venatomar.com
        $yenickoLead = Lead::where('email', 'yenickoprimous@gmail.com')->first();
        $this->assertNotNull($yenickoLead);
        $this->assertSame('contact@venatomar.com', $yenickoLead->admin_email);

        $yenickoClient = BankClient::where('email', 'yenickoprimous@gmail.com')->first();
        $this->assertNotNull($yenickoClient);
        $this->assertSame('contact@venatomar.com', $yenickoClient->admin_email);

        // Verify Chemins Lumière belongs to info@venatomar.com
        $cheminsLead = Lead::where('email', 'cheminsdelumiere8@gmail.com')->first();
        $this->assertNotNull($cheminsLead);
        $this->assertSame('info@venatomar.com', $cheminsLead->admin_email);

        $cheminsClient = BankClient::where('email', 'cheminsdelumiere8@gmail.com')->first();
        $this->assertNotNull($cheminsClient);
        $this->assertSame('info@venatomar.com', $cheminsClient->admin_email);

        // When contact@venatomar.com accesses leads index: sees only Yenicko, NOT Chemins
        $this->actingAs($contactAdmin);
        $responseContactLeads = $this->get('/admin/leads');
        $responseContactLeads->assertOk();
        $responseContactLeads->assertSee('yenickoprimous@gmail.com');
        $responseContactLeads->assertDontSee('cheminsdelumiere8@gmail.com');

        // When info@venatomar.com accesses leads index: sees only Chemins, NOT Yenicko
        $this->actingAs($infoAdmin);
        $responseInfoLeads = $this->get('/admin/leads');
        $responseInfoLeads->assertOk();
        $responseInfoLeads->assertSee('cheminsdelumiere8@gmail.com');
        $responseInfoLeads->assertDontSee('yenickoprimous@gmail.com');

        // When contact@venatomar.com accesses bank clients index
        $this->actingAs($contactAdmin);
        $responseContactClients = $this->get('/admin/bank-clients');
        $responseContactClients->assertOk();
        $responseContactClients->assertSee('yenickoprimous@gmail.com');
        $responseContactClients->assertDontSee('cheminsdelumiere8@gmail.com');

        // When info@venatomar.com accesses bank clients index
        $this->actingAs($infoAdmin);
        $responseInfoClients = $this->get('/admin/bank-clients');
        $responseInfoClients->assertOk();
        $responseInfoClients->assertSee('cheminsdelumiere8@gmail.com');
        $responseInfoClients->assertDontSee('yenickoprimous@gmail.com');

        // When contact@venatomar.com accesses loan contracts
        $this->actingAs($contactAdmin);
        $responseContactContracts = $this->get('/admin/loan-contracts');
        $responseContactContracts->assertOk();
        $responseContactContracts->assertSee('Primous');
        $responseContactContracts->assertDontSee('Lumière');

        // When info@venatomar.com accesses loan contracts
        $this->actingAs($infoAdmin);
        $responseInfoContracts = $this->get('/admin/loan-contracts');
        $responseInfoContracts->assertOk();
        $responseInfoContracts->assertSee('Lumière');
        $responseInfoContracts->assertDontSee('Primous');

        // Test client login for Chemins Lumière on /es/my-account
        $clientResponse1 = $this->post('/es/my-account', [
            'email' => 'cheminsdelumiere8@gmail.com',
            'password' => '12345678',
        ]);
        $clientResponse1->assertRedirect('/es/portal');

        // Test client login for Yenicko Primous on /es/mi-cuenta
        $clientResponse2 = $this->post('/es/mi-cuenta', [
            'email' => 'yenickoprimous@gmail.com',
            'password' => '12345678',
        ]);
        $clientResponse2->assertRedirect('/es/portal');
    }
}
