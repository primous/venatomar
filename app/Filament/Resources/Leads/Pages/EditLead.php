<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['create_bank_account'], $data['portal_password']);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Informations du dossier client mises à jour avec succès.';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
