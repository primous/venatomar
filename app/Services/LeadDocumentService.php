<?php

namespace App\Services;

use App\Models\InsuranceDocument;
use App\Models\Lead;
use App\Models\LoanApprovalDocument;
use App\Models\LoanContract;
use App\Support\BankDirectory;
use App\Support\InsurancePdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class LeadDocumentService
{
    public static function cleanLocale(Lead $lead): string
    {
        if (! empty($lead->locale) && array_key_exists($lead->locale, InsurancePdf::LOCALES)) {
            return $lead->locale;
        }

        return 'es';
    }

    public static function cleanBirthDate(?string $birthday): ?string
    {
        if (empty($birthday)) {
            return null;
        }

        try {
            return Carbon::parse($birthday)->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }

    public static function cleanCountry(Lead $lead): string
    {
        return $lead->countryName();
    }

    public static function cleanAddress(Lead $lead): string
    {
        return $lead->fullAddress();
    }

    public static function calculateSchedule(int|float $amount, int $termMonths, float $rate = 3.50): array
    {
        if ($amount <= 0 || $termMonths <= 0) {
            return [
                'monthly_payment' => 0,
                'total_repayable' => 0,
                'total_interest' => 0,
            ];
        }

        if ($rate > 0) {
            $r = ($rate / 100) / 12;
            $monthly = round(($amount * $r) / (1 - pow(1 + $r, -$termMonths)));
        } else {
            $monthly = round($amount / $termMonths);
        }

        $totalRepayable = (int) ($monthly * $termMonths);
        $totalInterest = max(0, $totalRepayable - (int) $amount);

        return [
            'monthly_payment' => (int) $monthly,
            'total_repayable' => $totalRepayable,
            'total_interest' => $totalInterest,
        ];
    }

    public static function prefillDataForContract(Lead $lead): array
    {
        $sched = self::calculateSchedule($lead->amount, $lead->term_months, 3.50);

        return [
            'lead_id' => $lead->id,
            'locale' => self::cleanLocale($lead),
            'first_name' => $lead->first_name,
            'last_name' => $lead->last_name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'document_number' => $lead->document_number,
            'birth_date' => self::cleanBirthDate($lead->birthday),
            'country' => self::cleanCountry($lead),
            'address' => self::cleanAddress($lead),
            'amount' => (int) $lead->amount,
            'currency' => strtoupper($lead->currency ?: 'EUR'),
            'term_months' => (int) $lead->term_months,
            'interest_rate' => 3.50,
            'monthly_payment' => $sched['monthly_payment'],
            'total_interest' => $sched['total_interest'],
            'total_repayable' => $sched['total_repayable'],
            'contract_date' => now()->format('Y-m-d'),
            'first_payment_date' => now()->addMonth()->format('Y-m-d'),
            'bank_account' => $lead->bank_account,
            'payout_method' => $lead->payout_method ?: 'iban',
            'document_fee' => null,
            'admin_email' => $lead->admin_email ?: (auth()->user()?->email ?? 'info@venatomar.com'),
            'issuer_address' => 'Carrer d’Estruc, 9, 08002 Barcelone',
            'issuer_phone' => '+34 910 000 000',
            'issuer_city' => 'Barcelone',
        ];
    }

    public static function prefillDataForInsurance(Lead $lead): array
    {
        return [
            'lead_id' => $lead->id,
            'document_type' => 'certificate',
            'locale' => self::cleanLocale($lead),
            'first_name' => $lead->first_name,
            'last_name' => $lead->last_name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'document_number' => $lead->document_number,
            'birth_date' => self::cleanBirthDate($lead->birthday),
            'country' => self::cleanCountry($lead),
            'address' => self::cleanAddress($lead),
            'amount' => (int) $lead->amount,
            'currency' => strtoupper($lead->currency ?: 'EUR'),
            'term_months' => (int) $lead->term_months,
            'coverage_percent' => 100,
            'guarantees' => ['death', 'ptia', 'itt', 'ipt', 'job_loss'],
            'effective_date' => now()->format('Y-m-d'),
            'document_fee' => null,
            'admin_email' => $lead->admin_email ?: (auth()->user()?->email ?? 'info@venatomar.com'),
            'issuer_address' => 'Carrer d’Estruc, 9, 08002 Barcelone',
            'issuer_phone' => '+34 910 000 000',
            'issuer_city' => 'Barcelone',
        ];
    }

    public static function prefillDataForApproval(Lead $lead): array
    {
        $sched = self::calculateSchedule($lead->amount, $lead->term_months, 3.50);

        return [
            'lead_id' => $lead->id,
            'locale' => self::cleanLocale($lead),
            'first_name' => $lead->first_name,
            'last_name' => $lead->last_name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'document_number' => $lead->document_number,
            'birth_date' => self::cleanBirthDate($lead->birthday),
            'country' => self::cleanCountry($lead),
            'address' => self::cleanAddress($lead),
            'amount' => (int) $lead->amount,
            'currency' => strtoupper($lead->currency ?: 'EUR'),
            'term_months' => (int) $lead->term_months,
            'interest_rate' => 3.50,
            'monthly_payment' => $sched['monthly_payment'],
            'total_repayable' => $sched['total_repayable'],
            'approval_date' => now()->format('Y-m-d'),
            'expiry_date' => now()->addDays(30)->format('Y-m-d'),
            'payout_method' => $lead->payout_method ?: 'iban',
            'payout_delay' => '24_48h',
            'admin_email' => $lead->admin_email ?: (auth()->user()?->email ?? 'info@venatomar.com'),
            'issuer_address' => 'Carrer d’Estruc, 9, 08002 Barcelone',
            'issuer_phone' => '+34 910 000 000',
            'issuer_city' => 'Barcelone',
        ];
    }

    public static function generateLoanContract(Lead $lead, array $overrides = []): LoanContract
    {
        $data = array_merge(self::prefillDataForContract($lead), $overrides, [
            'user_id' => auth()->id(),
        ]);

        return LoanContract::create($data);
    }

    public static function generateInsuranceDocument(Lead $lead, array $overrides = []): InsuranceDocument
    {
        $data = array_merge(self::prefillDataForInsurance($lead), $overrides, [
            'user_id' => auth()->id(),
        ]);

        return InsuranceDocument::create($data);
    }

    public static function generateLoanApprovalDocument(Lead $lead, array $overrides = []): LoanApprovalDocument
    {
        $data = array_merge(self::prefillDataForApproval($lead), $overrides, [
            'user_id' => auth()->id(),
        ]);

        return LoanApprovalDocument::create($data);
    }

    public static function generateAllThree(Lead $lead, array $overrides = []): array
    {
        return DB::transaction(function () use ($lead, $overrides) {
            $contract = self::generateLoanContract($lead, $overrides['contract'] ?? []);
            $insurance = self::generateInsuranceDocument($lead, $overrides['insurance'] ?? []);
            $approval = self::generateLoanApprovalDocument($lead, $overrides['approval'] ?? []);

            return [
                'contract' => $contract,
                'insurance' => $insurance,
                'approval' => $approval,
            ];
        });
    }
}
