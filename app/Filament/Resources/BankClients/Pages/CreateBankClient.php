<?php

namespace App\Filament\Resources\BankClients\Pages;

use App\Filament\Resources\BankClients\BankClientResource;
use App\Mail\BankClientWelcomeMail;
use App\Support\AdminMailer;
use Filament\Resources\Pages\CreateRecord;

class CreateBankClient extends CreateRecord
{
    protected static string $resource = BankClientResource::class;

    protected static bool $canCreateAnother = false;

    public ?string $plainPassword = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->plainPassword = $data['password'] ?? null;

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->plainPassword && ! empty($this->record->email)) {
            AdminMailer::to($this->record->email, $this->record->admin_email)
                ->locale($this->record->locale ?: 'es')
                ->queue(new BankClientWelcomeMail($this->record, $this->plainPassword));

            try {
                app(\App\Services\WebPushService::class)->sendToClient(
                    $this->record,
                    'Bienvenido a Venatomar',
                    'Su cuenta bancaria ha sido creada. Sus credenciales de acceso seguro han sido emitidas.',
                    ['url' => '/es/banca/login']
                );
            } catch (\Throwable $e) {}
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Compte client créé avec succès. L\'e-mail avec les identifiants a été envoyé au client.';
    }
}

