<?php

namespace App\Filament\Resources\BankTransactions\Pages;

use App\Filament\Resources\BankTransactions\BankTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBankTransaction extends CreateRecord
{
    protected static string $resource = BankTransactionResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
