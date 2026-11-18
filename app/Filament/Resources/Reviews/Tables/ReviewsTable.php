<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Models\Review;
use App\Support\AdminText as A;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ReviewsTable
{
    public static function sources(): array
    {
        return trans('admin.review.sources');
    }

    public static function configure(Table $table): Table
    {
        $locales = array_keys(config('venatomar.locales'));
        $r = fn (string $key) => A::t('review.'.$key);

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('author_name')->label($r('name'))->searchable()
                    ->description(fn (Review $record) => $record->title),
                TextColumn::make('rating')->label($r('rating'))->sortable()
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state))->color('warning'),
                TextColumn::make('body')->label($r('text'))->limit(70)->wrap()->searchable(),
                TextColumn::make('locale')->label($r('language'))->badge()->formatStateUsing(fn ($state) => strtoupper($state)),
                TextColumn::make('source')->label($r('source'))->badge()->placeholder('—')
                    ->formatStateUsing(fn ($state) => self::sources()[$state] ?? $state),
                IconColumn::make('is_verified')->label($r('verified_short'))->boolean(),
                ToggleColumn::make('is_published')->label($r('published')),
                TextColumn::make('reviewed_at')->label($r('date'))->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('locale')->label($r('language'))->options(array_combine($locales, array_map('strtoupper', $locales))),
                SelectFilter::make('rating')->label($r('rating'))->options([5 => '5', 4 => '4', 3 => '3', 2 => '2', 1 => '1']),
                SelectFilter::make('source')->label($r('source'))->options(self::sources()),
                TernaryFilter::make('is_verified')->label($r('verified_filter')),
            ])
            ->recordActions([
                Action::make('publish')->label($r('publish'))->icon(Heroicon::OutlinedCheck)->color('success')
                    ->visible(fn (Review $record) => ! $record->is_published)
                    ->action(fn (Review $record) => $record->update(['is_published' => true])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publish')->label($r('publish'))->icon(Heroicon::OutlinedCheck)
                        ->action(fn (Collection $records) => $records->each->update(['is_published' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('unpublish')->label($r('unpublish'))->icon(Heroicon::OutlinedEyeSlash)
                        ->action(fn (Collection $records) => $records->each->update(['is_published' => false]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
