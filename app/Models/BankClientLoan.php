<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use NumberFormatter;

class BankClientLoan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'monthly_payment' => 'decimal:2',
            'total_repayable' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'disbursement_date' => 'date',
            'next_payment_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BankClientLoan $loan) {
            if (empty($loan->reference)) {
                $loan->reference = 'LOAN-' . date('Ym') . '-' . strtoupper(substr(uniqid(), -5));
            }
            if ($loan->remaining_amount === null) {
                $loan->remaining_amount = $loan->amount;
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(BankClient::class, 'bank_client_id');
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(BankLoanRepayment::class, 'bank_client_loan_id')->orderBy('installment_number');
    }

    public function schedules(): HasMany
    {
        return $this->repayments();
    }

    public function formattedRemaining(): string
    {
        $val = (float) ($this->remaining_amount ?? 0);
        return $this->client ? $this->client->formatMoney($val) : number_format($val, 2) . ' €';
    }

    public function formattedMonthly(): string
    {
        $val = (float) ($this->monthly_payment ?? 0);
        return $this->client ? $this->client->formatMoney($val) : number_format($val, 2) . ' €';
    }
}
