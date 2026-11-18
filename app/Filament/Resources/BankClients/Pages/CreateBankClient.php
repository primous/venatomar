<?php

namespace App\Filament\Resources\BankClients\Pages;

use App\Filament\Resources\BankClients\BankClientResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBankClient extends CreateRecord
{
    protected static string $resource = BankClientResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Compte client créé avec succès. Les identifiants sont prêts.';
    }
}
