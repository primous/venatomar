<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankSupportMessage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'replied_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(BankClient::class, 'bank_client_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(BankTransaction::class, 'bank_transaction_id');
    }

    public function isFraudAlert(): bool
    {
        return $this->category === 'fraud_alert' || $this->category === 'transaction_dispute';
    }
}
