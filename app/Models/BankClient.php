<?php

namespace App\Models;

use App\Support\BankDirectory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NumberFormatter;

class BankClient extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'birth_date' => 'date',
            'last_login_at' => 'datetime',
            'balance' => 'decimal:2',
            'savings_balance' => 'decimal:2',
            'authorized_overdraft' => 'decimal:2',
            'can_receive_otp_email' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BankClient $client) {
            if (empty($client->account_number)) {
                $client->account_number = 'VTM-' . strtoupper(substr(uniqid(), -8));
            }
            if (empty($client->iban)) {
                $code = BankDirectory::codeForCountry($client->country);
                $client->iban = BankDirectory::generateIban($code);
            }
            if (empty($client->currency)) {
                $client->currency = BankDirectory::currencyForCountry($client->country);
            }
            if (empty($client->locale)) {
                $client->locale = BankDirectory::localeForCountry($client->country);
            }
        });
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(BankTransaction::class)->latest('id');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(BankClientLoan::class)->latest('id');
    }

    public function supportMessages(): HasMany
    {
        return $this->hasMany(BankSupportMessage::class)->latest('id');
    }

    public function activeLoan(): ?BankClientLoan
    {
        return $this->loans()->where('status', 'active')->first();
    }

    public function fullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function formattedBalance(): string
    {
        return $this->formatMoney($this->balance);
    }

    public function formattedSavingsBalance(): string
    {
        return $this->formatMoney($this->savings_balance);
    }

    public function formatMoney(float|int|string|null $amount): string
    {
        $val = (float) ($amount ?? 0);
        $locale = app()->getLocale() === 'fr' ? 'fr_FR' : 'es_ES';
        $fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

        return str_replace(["\u{202F}", "\u{00A0}"], ' ', $fmt->formatCurrency($val, $this->currency ?: 'EUR'));
    }

    public function isFrozen(): bool
    {
        return $this->status === 'frozen';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
