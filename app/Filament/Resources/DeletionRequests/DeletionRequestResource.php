<?php

namespace App\Filament\Resources\DeletionRequests;

use App\Filament\Resources\DeletionRequests\Pages\ListDeletionRequests;
use App\Models\DeletionRequest;
use App\Models\Lead;
use App\Models\Review;
use App\Support\AdminText as A;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

/** GDPR erasure requests sent from "Mi cuenta". */
class DeletionRequestResource extends Resource
{
    protected static ?string $model = DeletionRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrash;

    protected static ?int $navigationSort = 3;

    public static function statuses(): array
    {
        return trans('admin.deletion.statuses');
    }

    public static function getModelLabel(): string
    {
        return A::t('model.deletion');
    }

    public static function getPluralModelLabel(): string
    {
        return A::t('model.deletions');
    }

    public static function getNavigationLabel(): string
    {
        return A::t('nav.deletions');
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = DeletionRequest::where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        $d = fn (string $key) => A::t('deletion.'.$key);

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label($d('date'))->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('email')->label($d('email'))->searchable()->copyable(),
                TextColumn::make('locale')->label($d('language'))->badge()->formatStateUsing(fn ($state) => strtoupper($state)),
                TextColumn::make('leads_count')->label($d('leads'))
                    ->state(fn (DeletionRequest $record) => $record->applicant_id ? Lead::where('applicant_id', $record->applicant_id)->count() : 0),
                TextColumn::make('status')->label($d('status'))->badge()
                    ->formatStateUsing(fn ($state) => self::statuses()[$state] ?? $state)
                    ->color(fn ($state) => ['pending' => 'warning', 'done' => 'success', 'rejected' => 'gray'][$state] ?? 'gray'),
                TextColumn::make('processed_at')->label($d('processed'))->dateTime('d/m/Y H:i')->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')->label($d('status'))->options(self::statuses())->default('pending'),
            ])
            ->recordActions([
                Action::make('erase')
                    ->label($d('erase'))
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('danger')
                    ->visible(fn (DeletionRequest $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading($d('erase_heading'))
                    ->modalDescription($d('erase_desc'))
                    ->action(function (DeletionRequest $record) use ($d) {
                        DB::transaction(function () use ($record) {
                            if ($applicant = $record->applicant) {
                                $applicant->leads()->delete();
                                Review::where('applicant_id', $applicant->id)->delete();
                                $applicant->delete(); // magic tokens cascade; this request keeps its e-mail as proof
                            }
                            $record->update(['status' => 'done', 'processed_at' => now()]);
                        });
                        Notification::make()->title($d('erased'))->success()->send();
                    }),
                Action::make('reject')
                    ->label($d('reject'))
                    ->color('gray')
                    ->visible(fn (DeletionRequest $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn (DeletionRequest $record) => $record->update(['status' => 'rejected', 'processed_at' => now()])),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListDeletionRequests::route('/')];
    }
}
