<?php

namespace App\Filament\Resources\BankClientLoans\Pages;

use App\Filament\Resources\BankClientLoans\BankClientLoanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBankClientLoans extends ListRecords
{
    protected static string $resource = BankClientLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Octroyer un nouveau prêt client'),
        ];
    }
}
