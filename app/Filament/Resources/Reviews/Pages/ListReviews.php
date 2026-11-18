<?php

namespace App\Filament\Resources\Reviews\Pages;

use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\Review;
use App\Support\AdminText as A;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make(A::t('review.pending'))
                ->badge(Review::where('is_published', false)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', false)),
            'published' => Tab::make(A::t('review.published_tab'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', true)),
            'all' => Tab::make(A::t('all')),
        ];
    }
}
