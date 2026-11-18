<?php

namespace Database\Seeders;

use App\Enums\LeadStatus;
use App\Models\BankClient;
use App\Models\BankClientLoan;
use App\Models\BankTransaction;
use App\Models\Lead;
use App\Support\BankDirectory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestClientSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'yenickoprimous@gmail.com';
        $password = '12345678';

        // 1. Create or update Lead (loan request) validated
        $lead = Lead::updateOrCreate(
            ['email' => $email],
            [
                'first_name' => 'Yenicko',
                'last_name' => 'Primous',
                'phone' => '+34612345678',
                'document_number' => 'Y1234567P',
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
                'geo_city' => 'Barcelona',
                'geo_province' => 'Barcelona',
                'home_status' => 'owner_outright',
                'amount' => 15000,
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
                'admin_email' => 'info@venatomar.com',
                'consent_at' => now(),
                'consent_version' => '1.0',
            ]
        );

        // 2. Create or update BankClient account
        $iban = BankDirectory::generateIban('ES');
        $accountNumber = 'VTM-' . strtoupper(substr(md5($email), 0, 8));

        $client = BankClient::updateOrCreate(
            ['email' => $email],
            [
                'first_name' => 'Yenicko',
                'last_name' => 'Primous',
                'password' => Hash::make($password),
                'phone' => '+34612345678',
                'document_number' => 'Y1234567P',
                'birth_date' => '1990-05-20',
                'country' => 'España',
                'country_code' => 'ES',
                'locale' => 'es',
                'city' => 'Barcelona',
                'address' => 'Carrer d’Estruc, 9, 2ºA',
                'postal_code' => '08002',
                'currency' => 'EUR',
                'balance' => 15000.00,
                'savings_balance' => 2500.00,
                'authorized_overdraft' => 1000.00,
                'status' => 'active',
                'tier' => 'gold',
                'can_receive_otp_email' => true,
                'admin_email' => 'info@venatomar.com',
                'account_number' => $accountNumber,
                'iban' => $iban,
                'bic' => 'VTMARSM1XXX',
            ]
        );

        // 3. Clear old test loans and transactions for this client
        BankClientLoan::where('bank_client_id', $client->id)->delete();
        BankTransaction::where('bank_client_id', $client->id)->delete();

        // 4. Create active validated loan
        BankClientLoan::create([
            'bank_client_id' => $client->id,
            'reference' => 'LOAN-' . date('Ym') . '-PRIMOUS',
            'title' => 'Préstamo Personal Venatomar',
            'amount' => 15000.00,
            'remaining_amount' => 14375.00,
            'term_months' => 24,
            'interest_rate' => 3.50,
            'monthly_payment' => 648.12,
            'total_repayable' => 15554.88,
            'disbursement_date' => now()->subDays(15),
            'next_payment_date' => now()->addDays(15),
            'status' => 'active',
        ]);

        // 5. Initial transactions (loan payout + savings deposit)
        BankTransaction::create([
            'bank_client_id' => $client->id,
            'reference' => 'TRX-' . date('Ym') . '-CREDIT',
            'type' => 'loan_payout',
            'category' => 'crédito',
            'amount' => 15000.00,
            'currency' => 'EUR',
            'status' => 'completed',
            'description' => 'Desembolso Préstamo Personal Venatomar — Ref: LOAN-PRIMOUS',
            'completed_at' => now()->subDays(15),
            'approved_by_admin' => true,
        ]);

        BankTransaction::create([
            'bank_client_id' => $client->id,
            'reference' => 'TRX-' . date('Ym') . '-SAVING',
            'type' => 'savings_deposit',
            'category' => 'ahorro',
            'amount' => 2500.00,
            'currency' => 'EUR',
            'status' => 'completed',
            'description' => 'Aportación a la Reserva de Ahorro Remunerada (3,20% neto)',
            'completed_at' => now()->subDays(10),
            'approved_by_admin' => true,
        ]);
    }
}
