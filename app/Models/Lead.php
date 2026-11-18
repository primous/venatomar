<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Support\BankDirectory;
use App\Support\Country;
use App\Support\Venatomar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    protected $guarded = ['id'];

    /** Never serialise sensitive values by accident. */
    protected $hidden = ['document_number', 'bank_account', 'mm_number', 'birthday', 'monthly_income', 'ip_address', 'user_agent'];

    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'defaulter_list' => 'boolean',
            'has_debt' => 'boolean',
            'has_car' => 'boolean',
            'amount' => 'integer',
            'term_months' => 'integer',
            'monthly_income' => 'integer',
            'regular_expenses' => 'integer',
            'utm' => 'array',
            'consent_at' => 'datetime',
            'internal_mail_sent_at' => 'datetime',
            'confirmation_mail_sent_at' => 'datetime',
            'mail_failed_at' => 'datetime',
            // Encrypted at rest (APP_KEY).
            'document_number' => 'encrypted',
            'bank_account' => 'encrypted',
            'mm_number' => 'encrypted',
            'birthday' => 'encrypted',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Lead $lead) {
            $lead->reference = Venatomar::reference($lead->id, $lead->created_at);
            $lead->saveQuietly();
        });
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function loanContracts(): HasMany
    {
        return $this->hasMany(LoanContract::class)->latest();
    }

    public function insuranceDocuments(): HasMany
    {
        return $this->hasMany(InsuranceDocument::class)->latest();
    }

    public function loanApprovalDocuments(): HasMany
    {
        return $this->hasMany(LoanApprovalDocument::class)->latest();
    }

    public function bankClient(): HasOne
    {
        return $this->hasOne(BankClient::class, 'email', 'email');
    }

    public function countryName(): string
    {
        return BankDirectory::countryNameByCode($this->country) ?? strtoupper($this->country);
    }

    public function fullAddress(): string
    {
        $parts = [
            trim($this->home_street.' '.$this->home_number),
            $this->apartment_floor,
            trim($this->geo_zip.' '.$this->geo_city),
            $this->geo_province,
            $this->countryName(),
        ];

        return implode(', ', array_filter(array_map('trim', $parts)));
    }

    public function countryInfo(): Country
    {
        return new Country($this->country);
    }

    public function maskedPayout(): string
    {
        return $this->payout_method === 'mobile_money'
            ? Venatomar::maskTail($this->mm_number, 3)
            : Venatomar::maskTail($this->bank_account);
    }

    public function isAdminContact(): bool
    {
        return $this->admin_email === 'contact@venatomar.com';
    }

    public function adminChannelLabel(): string
    {
        return $this->admin_email === 'contact@venatomar.com' ? 'Contact (contact@venatomar.com)' : 'Principal (info@venatomar.com)';
    }
}
