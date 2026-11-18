<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Support\AdminText as A;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LeadResource::generateAllDocumentsAction(),

            ActionGroup::make([
                LeadResource::generateContractAction(),
                LeadResource::generateInsuranceAction(),
                LeadResource::generateApprovalAction(),
                LeadResource::openPortalAccountAction(),
                LeadResource::changeStatusAction(),
                LeadResource::retryMailsAction(),
            ])
                ->label('Actions & Documents')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray'),

            EditAction::make()->label('Modifier le dossier'),
            DeleteAction::make()->modalDescription(A::t('lead.delete_desc')),
        ];
    }
}
