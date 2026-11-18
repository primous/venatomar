<?php

namespace App\Models;

use App\Support\InsurancePdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NumberFormatter;

/** Personalised loan approval / validation certificate issued by Venatomar. */
class LoanApprovalDocument extends Model
{
    public const PAYOUT_METHODS = ['iban', 'rib', 'mobile_money', 'express_transfer', 'cheque'];

    public const PAYOUT_DELAYS = ['immediate', '24_48h', '72h', 'standard'];

    protected $guarded = ['id'];

    protected $hidden = ['document_number'];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'term_months' => 'integer',
            'monthly_payment' => 'integer',
            'interest_rate' => 'decimal:2',
            'total_repayable' => 'integer',
            'document_number' => 'encrypted',
            'birth_date' => 'date',
            'approval_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (LoanApprovalDocument $doc) {
            $doc->reference = sprintf('VN-VAL-%s-%06d', $doc->created_at->format('Y'), $doc->id);
            if (! $doc->approval_date) {
                $doc->approval_date = $doc->created_at->toDateString();
            }
            if (! $doc->expiry_date) {
                $doc->expiry_date = $doc->created_at->copy()->addDays(30)->toDateString();
            }
            if (! $doc->monthly_payment && $doc->term_months > 0) {
                $rate = (float) ($doc->interest_rate ?? 3.50);
                if ($rate > 0) {
                    $r = ($rate / 100) / 12;
                    $n = $doc->term_months;
                    $monthly = ($doc->amount * $r) / (1 - pow(1 + $r, -$n));
                    $doc->monthly_payment = (int) round($monthly);
                } else {
                    $doc->monthly_payment = (int) round($doc->amount / $doc->term_months);
                }
            }
            if (! $doc->total_repayable && $doc->monthly_payment) {
                $doc->total_repayable = (int) ($doc->monthly_payment * $doc->term_months);
            }
            $doc->saveQuietly();
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
        $hash = strtoupper(hash('crc32b', ($this->reference ?: 'VN-VAL').$this->created_at.$this->amount));

        return sprintf('VN-VAL-%s-%04d', $hash, $this->id % 10000);
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

    public function totalRepayableFormatted(): string
    {
        return $this->formatCurrency($this->total_repayable);
    }

    public function filename(): string
    {
        return 'Venatomar-Accord-'.$this->reference.'-'.strtoupper($this->locale).'.pdf';
    }
}
