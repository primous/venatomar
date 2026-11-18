<?php

namespace App\Filament\Resources\InsuranceDocuments\Pages;

use App\Filament\Resources\InsuranceDocuments\InsuranceDocumentResource;
use App\Models\Lead;
use App\Services\LeadDocumentService;
use App\Support\AdminText as A;
use Filament\Resources\Pages\CreateRecord;

class CreateInsuranceDocument extends CreateRecord
{
    protected static string $resource = InsuranceDocumentResource::class;

    protected static bool $canCreateAnother = false;

    public function mount(): void
    {
        parent::mount();

        $leadId = request()->query('lead_id') ?? request()->input('lead_id');
        if ($leadId && ($lead = Lead::find($leadId))) {
            $this->form->fill(LeadDocumentService::prefillDataForInsurance($lead));
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return A::t('insurance.created');
    }

    /** Back to the list, where the new sheet is on top with its download button. */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
