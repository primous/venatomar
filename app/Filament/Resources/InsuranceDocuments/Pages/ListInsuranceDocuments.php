<?php

namespace App\Filament\Resources\InsuranceDocuments\Pages;

use App\Filament\Resources\InsuranceDocuments\InsuranceDocumentResource;
use App\Support\AdminText as A;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInsuranceDocuments extends ListRecords
{
    protected static string $resource = InsuranceDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label(A::t('insurance.generate'))];
    }
}
