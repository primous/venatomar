<?php

namespace App\Filament\Resources\LoanApprovalDocuments\Pages;

use App\Filament\Resources\LoanApprovalDocuments\LoanApprovalDocumentResource;
use App\Support\AdminText as A;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLoanApprovalDocuments extends ListRecords
{
    protected static string $resource = LoanApprovalDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label(A::t('loan_approval.generate'))];
    }
}
