<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Applicant;
use App\Models\BankClient;
use App\Support\AdminText as A;
use App\Support\BankDirectory;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;

    protected static bool $canCreateAnother = false;

    protected ?string $portalPasswordToCreate = null;

    protected bool $shouldCreateBankAccount = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->shouldCreateBankAccount = ! empty($data['create_bank_account']);
        $this->portalPasswordToCreate = ! empty($data['portal_password'])
            ? (string) $data['portal_password']
            : Str::random(10);

        unset($data['create_bank_account'], $data['portal_password']);

        // Default compliance metadata
        $data['consent_at'] = now();
        $data['consent_version'] = config('venatomar.consent_version', '1.0');
        $data['ip_address'] = request()->ip();
        $data['user_agent'] = request()->userAgent();

        // Ensure associated applicant profile exists
        $applicant = Applicant::firstOrNew(['email' => $data['email']]);
        $applicant->fill([
            'phone' => $data['phone'] ?? null,
            'locale' => $data['locale'] ?? 'es',
        ])->save();
        $data['applicant_id'] = $applicant->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->shouldCreateBankAccount) {
            $existing = BankClient::where('email', $this->record->email)->first();
            if (! $existing) {
                $countryName = $this->record->countryName();
                $banks = BankDirectory::banksForCountry($countryName);

                BankClient::create([
                    'first_name' => $this->record->first_name,
                    'last_name' => $this->record->last_name,
                    'email' => $this->record->email,
                    'admin_email' => $this->record->admin_email ?: 'info@venatomar.com',
                    'phone' => $this->record->phone,
                    'password' => $this->portalPasswordToCreate,
                    'country' => $countryName,
                    'country_code' => strtoupper($this->record->country),
                    'national_bank_name' => $banks[0] ?? 'Banque Nationale Partenaire',
                    'currency' => $this->record->currency ?: 'EUR',
                    'locale' => $this->record->locale ?: 'es',
                    'birth_date' => $this->record->birthday,
                    'document_number' => $this->record->document_number,
                    'status' => 'active',
                    'balance' => $this->record->amount,
                ]);

                Notification::make()
                    ->title('Compte bancaire en ligne créé')
                    ->body("L'accès client a été activé avec le mot de passe : {$this->portalPasswordToCreate}")
                    ->success()
                    ->send();
            }
        }
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Client enregistré avec succès. Tous les documents officiels sont prêts à être générés.';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
