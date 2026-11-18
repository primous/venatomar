<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Enums\LeadStatus;
use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use App\Support\AdminText as A;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Enregistrer un Client / Nouveau Dossier')
                ->icon(Heroicon::OutlinedUserPlus),
        ];
    }

    public function getTabs(): array
    {
        $tabs = ['all' => Tab::make(A::t('all'))];

        foreach (LeadStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make(LeadResource::statusLabel($status))
                ->badge(Lead::where('status', $status)->count() ?: null)
                ->badgeColor(LeadResource::statusColor($status))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status));
        }

        return $tabs;
    }
}
