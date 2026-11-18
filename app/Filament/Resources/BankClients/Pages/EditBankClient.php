<?php

namespace App\Filament\Resources\BankClients\Pages;

use App\Filament\Resources\BankClients\BankClientResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBankClient extends EditRecord
{
    protected static string $resource = BankClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
