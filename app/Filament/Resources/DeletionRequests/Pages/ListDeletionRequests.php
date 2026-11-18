<?php

namespace App\Filament\Resources\DeletionRequests\Pages;

use App\Filament\Resources\DeletionRequests\DeletionRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListDeletionRequests extends ListRecords
{
    protected static string $resource = DeletionRequestResource::class;
}
