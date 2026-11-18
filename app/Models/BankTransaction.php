<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NumberFormatter;

class BankTransaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'otp_expires_at' => 'datetime',
            'completed_at' => 'datetime',
            'is_otp_authorized' => 'boolean',
            'approved_by_admin' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BankTransaction $tx) {
            if (empty($tx->reference)) {
                $tx->reference = 'TRX-' . date('Ym') . '-' . strtoupper(substr(uniqid(), -6));
            }
            if (empty($tx->otp_code)) {
                $tx->otp_code = sprintf('%06d', mt_rand(100000, 999999));
                $tx->otp_expires_at = now()->addMinutes(30);
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(BankClient::class, 'bank_client_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function formattedAmount(): string
    {
        $val = (float) $this->amount;
        $sign = in_array($this->type, ['transfer_out', 'savings_deposit', 'loan_repayment', 'fee'], true) ? '-' : '+';
        $locale = app()->getLocale() === 'fr' ? 'fr_FR' : 'es_ES';
        $fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

        $money = str_replace(["\u{202F}", "\u{00A0}"], ' ', $fmt->formatCurrency(abs($val), $this->currency ?: 'EUR'));

        return $sign . ' ' . $money;
    }
}
