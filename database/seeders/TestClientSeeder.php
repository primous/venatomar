<?php

namespace Database\Seeders;

use App\Enums\LeadStatus;
use App\Models\BankClient;
use App\Models\BankClientLoan;
use App\Models\BankTransaction;
use App\Models\InsuranceDocument;
use App\Models\Lead;
use App\Models\LoanApprovalDocument;
use App\Models\LoanContract;
use App\Services\LeadDocumentService;
use App\Support\BankDirectory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestClientSeeder extends Seeder
{
    public function run(): void
    {
        // Comptes de test : jamais en production.
        if (app()->isProduction()) {
            $this->command?->error('TestClientSeeder refusé en production.');

            return;
        }

        // Aucun mot de passe en clair dans le dépôt : TEST_CLIENT_PASSWORD (.env) ou valeur aléatoire.
        $password = (string) (env('TEST_CLIENT_PASSWORD') ?: Str::password(12));
        $this->command?->warn('Clients de test / mot de passe : '.$password);

        // 1. Client for contact@venatomar.com (Channel /es/mi-cuenta)
        $this->seedClient(
            email: 'yenickoprimous@gmail.com',
            firstName: 'Yenicko',
            lastName: 'Primous',
            adminEmail: 'contact@venatomar.com',
            password: $password,
            amount: 15000.00,
            city: 'Barcelona',
            phone: '+34612345678',
        );

        // 2. Client for info@venatomar.com (Channel /es/my-account)
        $this->seedClient(
            email: 'cheminsdelumiere8@gmail.com',
            firstName: 'Chemins',
            lastName: 'Lumière',
            adminEmail: 'info@venatomar.com',
            password: $password,
            amount: 15000.00,
            city: 'Madrid',
            phone: '+34698765432',
        );
    }

    private function seedClient(
        string $email,
        string $firstName,
        string $lastName,
        string $adminEmail,
        string $password,
        float $amount,
        string $city,
        string $phone,
    ): void {
        // 1. Lead
        $lead = Lead::updateOrCreate(
            ['email' => $email],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'document_number' => 'Y' . rand(1000000, 9999999) . 'P',
                'birthday' => '1990-05-20',
                'gender' => 'male',
                'marital_status' => 'single',
                'education' => 'university',
                'country' => 'es',
                'locale' => 'es',
                'currency' => 'EUR',
                'status' => LeadStatus::Accepted,
                'home_street' => 'Carrer d’Estruc',
                'home_number' => '9',
                'apartment_floor' => '2ºA',
                'geo_zip' => '08002',
                'geo_city' => $city,
                'geo_province' => $city,
                'home_status' => 'owner_outright',
                'amount' => (int) $amount,
                'term_months' => 24,
                'loan_purpose' => 'personal',
                'monthly_income' => 3500,
                'regular_expenses' => 600,
                'employment_status' => 'employee',
                'company_sector' => 'technology',
                'defaulter_list' => false,
                'has_debt' => false,
                'has_car' => false,
                'payout_method' => 'iban',
                'bank_account' => 'ES9121000418450200051332',
                'admin_email' => $adminEmail,
                'consent_at' => now(),
                'consent_version' => '1.0',
            ]
        );

        // 2. BankClient
        $iban = BankDirectory::generateIban('ES');
        $accountNumber = 'VTM-' . strtoupper(substr(md5($email), 0, 8));

        $client = BankClient::updateOrCreate(
            ['email' => $email],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'password' => Hash::make($password),
                'phone' => $phone,
                'document_number' => 'Y' . rand(1000000, 9999999) . 'P',
                'birth_date' => '1990-05-20',
                'country' => 'España',
                'country_code' => 'ES',
                'locale' => 'es',
                'city' => $city,
                'address' => 'Carrer d’Estruc, 9, 2ºA',
                'postal_code' => '08002',
                'currency' => 'EUR',
                'balance' => $amount,
                'savings_balance' => 2500.00,
                'authorized_overdraft' => 1000.00,
                'status' => 'active',
                'tier' => 'gold',
                'can_receive_otp_email' => true,
                'admin_email' => $adminEmail,
                'account_number' => $accountNumber,
                'iban' => $iban,
                'bic' => 'VTMARSM1XXX',
            ]
        );

        // 3. Clear old loans/transactions
        BankClientLoan::where('bank_client_id', $client->id)->delete();
        BankTransaction::where('bank_client_id', $client->id)->delete();

        // 4. Create active loan
        $slug = strtoupper(substr(md5($email), 0, 6));
        BankClientLoan::create([
            'bank_client_id' => $client->id,
            'reference' => 'LOAN-' . date('Ym') . '-' . $slug,
            'title' => 'Préstamo Personal Venatomar',
            'amount' => $amount,
            'remaining_amount' => $amount - 625.00,
            'term_months' => 24,
            'interest_rate' => 3.50,
            'monthly_payment' => 648.12,
            'total_repayable' => 15554.88,
            'disbursement_date' => now()->subDays(15),
            'next_payment_date' => now()->addDays(15),
            'status' => 'active',
        ]);

        // 5. Initial transactions
        BankTransaction::create([
            'bank_client_id' => $client->id,
            'reference' => 'TRX-' . date('Ym') . '-CR-' . $slug,
            'type' => 'loan_payout',
            'category' => 'crédito',
            'amount' => $amount,
            'currency' => 'EUR',
            'status' => 'completed',
            'description' => "Desembolso Préstamo Personal Venatomar — Ref: LOAN-{$slug}",
            'completed_at' => now()->subDays(15),
            'approved_by_admin' => true,
        ]);

        BankTransaction::create([
            'bank_client_id' => $client->id,
            'reference' => 'TRX-' . date('Ym') . '-SAV-' . $slug,
            'type' => 'savings_deposit',
            'category' => 'ahorro',
            'amount' => 2500.00,
            'currency' => 'EUR',
            'status' => 'completed',
            'description' => 'Aportación a la Reserva de Ahorro Remunerada (3,20% neto)',
            'completed_at' => now()->subDays(10),
            'approved_by_admin' => true,
        ]);

        // 6. Generate official documents for this client under their admin
        LoanContract::where('email', $email)->delete();
        InsuranceDocument::where('email', $email)->delete();
        LoanApprovalDocument::where('email', $email)->delete();

        LeadDocumentService::generateAllThree($lead, [
            'contract' => ['admin_email' => $adminEmail],
            'insurance' => ['admin_email' => $adminEmail],
            'approval' => ['admin_email' => $adminEmail],
        ]);
    }
}
