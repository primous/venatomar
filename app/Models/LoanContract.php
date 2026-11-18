<?php

namespace App\Models;

use App\Support\InsurancePdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NumberFormatter;

/** Formal personal loan contract / credit agreement between Venatomar and the borrower. */
class LoanContract extends Model
{
    public const PAYOUT_METHODS = ['iban', 'rib', 'mobile_money', 'express_transfer', 'cheque'];

    protected $guarded = ['id'];

    protected $hidden = ['document_number', 'bank_account'];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'document_fee' => 'decimal:2',
            'term_months' => 'integer',
            'interest_rate' => 'decimal:2',
            'monthly_payment' => 'integer',
            'total_interest' => 'integer',
            'total_repayable' => 'integer',
            'document_number' => 'encrypted',
            'bank_account' => 'encrypted',
            'birth_date' => 'date',
            'contract_date' => 'date',
            'first_payment_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (LoanContract $contract) {
            $contract->reference = sprintf('VN-CTR-%s-%06d', $contract->created_at->format('Y'), $contract->id);
            if (! $contract->contract_date) {
                $contract->contract_date = $contract->created_at->toDateString();
            }
            if (! $contract->first_payment_date) {
                $contract->first_payment_date = Carbon::parse($contract->contract_date)->addMonth()->toDateString();
            }
            if (! $contract->monthly_payment && $contract->term_months > 0) {
                $rate = (float) ($contract->interest_rate ?? 3.50);
                if ($rate > 0) {
                    $r = ($rate / 100) / 12;
                    $n = $contract->term_months;
                    $monthly = ($contract->amount * $r) / (1 - pow(1 + $r, -$n));
                    $contract->monthly_payment = (int) round($monthly);
                } else {
                    $contract->monthly_payment = (int) round($contract->amount / $contract->term_months);
                }
            }
            if (! $contract->total_repayable && $contract->monthly_payment) {
                $contract->total_repayable = (int) ($contract->monthly_payment * $contract->term_months);
            }
            if (! $contract->total_interest && $contract->total_repayable && $contract->amount) {
                $contract->total_interest = max(0, $contract->total_repayable - $contract->amount);
            }
            $contract->saveQuietly();
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function authCode(): string
    {
        $hash = strtoupper(hash('crc32b', ($this->reference ?: 'VN-CTR').$this->created_at.$this->amount));

        return sprintf('VN-CTR-%s-%04d', $hash, $this->id % 10000);
    }

    public function formatCurrency(int|float|null $amount): string
    {
        if ($amount === null) {
            return '—';
        }

        $fmt = new NumberFormatter(InsurancePdf::NUMBER_LOCALES[$this->locale] ?? 'es_ES', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);

        return str_replace(["\u{202F}", "\u{00A0}"], ' ', $fmt->formatCurrency($amount, $this->currency));
    }

    public function money(): string
    {
        return $this->formatCurrency($this->amount);
    }

    public function monthlyPaymentFormatted(): string
    {
        return $this->formatCurrency($this->monthly_payment);
    }

    public function totalInterestFormatted(): string
    {
        return $this->formatCurrency($this->total_interest);
    }

    public function totalRepayableFormatted(): string
    {
        return $this->formatCurrency($this->total_repayable);
    }

    public function formattedDocumentFee(): string
    {
        if ($this->document_fee === null || (float) $this->document_fee <= 0) {
            return '—';
        }

        return $this->formatCurrency((float) $this->document_fee);
    }

    public function filename(): string
    {
        return 'Venatomar-Contrat-'.$this->reference.'-'.strtoupper($this->locale).'.pdf';
    }
}
