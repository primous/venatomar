<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Enums\LeadStatus;
use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use App\Support\AdminText as A;
use App\Support\Venatomar;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        $countries = collect(config('venatomar.locales'))->values()->mapWithKeys(fn ($cc) => [$cc => strtoupper($cc)])->all();

        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Lead $record) => LeadResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('reference')->label(A::t('lead.reference'))->searchable()->copyable()->weight('bold'),
                TextColumn::make('created_at')->label(A::t('lead.date'))->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('last_name')->label(A::t('lead.applicant'))
                    ->formatStateUsing(fn (Lead $record) => $record->first_name.' '.$record->last_name)
                    ->description(fn (Lead $record) => $record->email)
                    ->searchable(['first_name', 'last_name', 'email', 'phone']),
                TextColumn::make('country')->label(A::t('lead.country'))->badge()->formatStateUsing(fn ($state) => strtoupper($state)),
                TextColumn::make('amount')->label(A::t('lead.amount'))->sortable()->alignEnd()
                    ->formatStateUsing(fn (Lead $record) => Venatomar::money($record->amount, $record->countryInfo()))
                    ->description(fn (Lead $record) => $record->term_months.' '.A::t('months')),
                TextColumn::make('status')->label(A::t('lead.status'))->badge()
                    ->formatStateUsing(fn (LeadStatus $state) => LeadResource::statusLabel($state))
                    ->color(fn (LeadStatus $state) => LeadResource::statusColor($state)),
                IconColumn::make('mail_failed_at')->label(A::t('lead.mail'))
                    ->state(fn (Lead $record) => $record->mail_failed_at === null && $record->internal_mail_sent_at !== null)
                    ->boolean()
                    ->tooltip(fn (Lead $record) => A::t($record->mail_failed_at ? 'lead.mail_failed' : ($record->internal_mail_sent_at ? 'lead.mail_sent' : 'lead.mail_queued'))),
            ])
            ->filters([
                SelectFilter::make('status')->label(A::t('lead.status'))->options(LeadResource::statusOptions())->multiple(),
                SelectFilter::make('country')->label(A::t('lead.country'))->options($countries),
                Filter::make('mail_failed')->label(A::t('lead.filter_mail_failed'))->toggle()
                    ->query(fn (Builder $query) => $query->whereNotNull('mail_failed_at')),
            ])
            ->recordActions([
                LeadResource::acceptLeadAction(),
                ViewAction::make(),
                ActionGroup::make([
                    LeadResource::acceptLeadAction(),
                    LeadResource::generateAllDocumentsAction(),
                    LeadResource::generateContractAction(),
                    LeadResource::generateInsuranceAction(),
                    LeadResource::generateApprovalAction(),
                    LeadResource::openPortalAccountAction(),
                    LeadResource::changeStatusAction(),
                    LeadResource::retryMailsAction(),
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulkStatus')->label(A::t('lead.change_status'))->icon(Heroicon::OutlinedArrowPath)
                        ->schema([
                            Select::make('status')->label(A::t('lead.new_status'))->options(LeadResource::statusOptions())->required(),
                            TextInput::make('document_fee')
                                ->label('Frais de réalisation de la fiche d\'assurance')
                                ->helperText('Appliqué aux dossiers acceptés pour la génération de la fiche d\'assurance.')
                                ->numeric()
                                ->minValue(0)
                                ->step(0.01)
                                ->placeholder('Optionnel (Ex: 150)'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            if ($data['status'] === LeadStatus::Accepted->value || $data['status'] === 'accepted') {
                                $records->each(fn (Lead $lead) => LeadResource::acceptLead($lead, $data));
                            } else {
                                $records->each->update(['status' => $data['status']]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
