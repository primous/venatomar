<?php

namespace App\Filament\Resources\BankClients\Pages;

use App\Filament\Resources\BankClients\BankClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBankClients extends ListRecords
{
    protected static string $resource = BankClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Ouvrir un nouveau compte client'),
        ];
    }
}
