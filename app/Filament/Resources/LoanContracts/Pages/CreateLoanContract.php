<?php

namespace App\Filament\Resources\LoanContracts\Pages;

use App\Filament\Resources\LoanContracts\LoanContractResource;
use App\Models\Lead;
use App\Services\LeadDocumentService;
use App\Support\AdminText as A;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanContract extends CreateRecord
{
    protected static string $resource = LoanContractResource::class;

    protected static bool $canCreateAnother = false;

    public function mount(): void
    {
        parent::mount();

        $leadId = request()->query('lead_id') ?? request()->input('lead_id');
        if ($leadId && ($lead = Lead::find($leadId))) {
            $this->form->fill(LeadDocumentService::prefillDataForContract($lead));
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return A::t('loan_contract.created');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
