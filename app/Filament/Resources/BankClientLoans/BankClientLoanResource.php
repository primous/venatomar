<?php

namespace App\Filament\Resources\BankClientLoans;

use App\Filament\Resources\BankClientLoans\Pages\ListBankClientLoans;
use App\Models\BankClient;
use App\Models\BankClientLoan;
use App\Models\BankTransaction;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BankClientLoanResource extends Resource
{
    protected static ?string $model = BankClientLoan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Banque en Ligne';

    protected static ?int $navigationSort = 12;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function getModelLabel(): string
    {
        return 'Prêt / Crédit Client';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Prêts & Crédits Clients';
    }

    public static function getNavigationLabel(): string
    {
        return 'Prêts & Échéanciers';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations du Prêt')->columns(2)->columnSpanFull()->schema([
                Select::make('bank_client_id')->label('Client Emprunteur')
                    ->relationship('client', 'account_number')
                    ->getOptionLabelFromRecordUsing(fn (BankClient $c) => "{$c->account_number} — {$c->fullName()}")
                    ->searchable()
                    ->required(),
                TextInput::make('title')->label('Intitulé du prêt')->default('Prêt Personnel Venatomar')->required(),
                TextInput::make('amount')->label('Montant accordé')->numeric()->required()->minValue(100),
                TextInput::make('remaining_amount')->label('Capital restant dû')->numeric()->required(),
                TextInput::make('term_months')->label('Durée (mois)')->numeric()->required()->default(24),
                TextInput::make('interest_rate')->label('Taux d\'intérêt annuel (%)')->numeric()->default(3.50)->required(),
                TextInput::make('monthly_payment')->label('Mensualité')->numeric()->required(),
                TextInput::make('total_repayable')->label('Montant total à rembourser')->numeric()->required(),
                Select::make('status')->label('Statut du prêt')
                    ->options([
                        'active' => 'En cours d\'amortissement',
                        'pending_approval' => 'En attente d\'approbation',
                        'completed' => 'Intégralement remboursé',
                        'rejected' => 'Refusé',
                    ])
                    ->default('active')
                    ->required(),
                DatePicker::make('disbursement_date')->label('Date de déblocage des fonds')->default(now())->native(false),
                DatePicker::make('next_payment_date')->label('Date de la prochaine échéance')->default(now()->addMonth())->native(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label('Réf. Prêt')->searchable()->copyable()->weight('bold'),
                TextColumn::make('client.full_name')->label('Emprunteur')
                    ->state(fn (BankClientLoan $record) => $record->client?->fullName())
                    ->description(fn (BankClientLoan $record) => $record->client?->account_number),
                TextColumn::make('title')->label('Intitulé'),
                TextColumn::make('amount')->label('Montant Initial')
                    ->formatStateUsing(fn (BankClientLoan $record) => $record->client ? $record->client->formatMoney($record->amount) : number_format($record->amount, 2) . ' €')
                    ->weight('bold'),
                TextColumn::make('remaining_amount')->label('Restant Dû')
                    ->formatStateUsing(fn (BankClientLoan $record) => $record->formattedRemaining())
                    ->color('danger')
                    ->weight('bold'),
                TextColumn::make('monthly_payment')->label('Mensualité')
                    ->formatStateUsing(fn (BankClientLoan $record) => $record->formattedMonthly() . ' / mois'),
                TextColumn::make('term_months')->label('Durée')
                    ->formatStateUsing(fn ($state) => "{$state} mois"),
                TextColumn::make('status')->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active' => 'Actif',
                        'pending_approval' => 'En attente',
                        'completed' => 'Soldé',
                        'rejected' => 'Refusé',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'active' => 'success',
                        'pending_approval' => 'warning',
                        'completed' => 'info',
                        'rejected' => 'danger',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options([
                    'active' => 'Actif',
                    'pending_approval' => 'En attente',
                    'completed' => 'Soldé',
                ]),
            ])
            ->recordActions([
                Action::make('record_repayment')
                    ->label('Encaisser mensualité')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->color('success')
                    ->visible(fn (BankClientLoan $record) => $record->status === 'active' && $record->remaining_amount > 0)
                    ->form([
                        TextInput::make('amount')->label('Montant de la mensualité')
                            ->numeric()
                            ->default(fn (BankClientLoan $record) => (float) $record->monthly_payment)
                            ->required(),
                    ])
                    ->action(function (BankClientLoan $record, array $data) {
                        $payment = (float) $data['amount'];
                        $newRemaining = max(0, (float) $record->remaining_amount - $payment);
                        $isCompleted = $newRemaining <= 0;

                        $record->update([
                            'remaining_amount' => $newRemaining,
                            'status' => $isCompleted ? 'completed' : 'active',
                            'next_payment_date' => now()->addMonth(),
                        ]);

                        if ($record->client) {
                            BankTransaction::create([
                                'bank_client_id' => $record->client->id,
                                'type' => 'loan_repayment',
                                'category' => 'credit_repayment',
                                'amount' => $payment,
                                'currency' => $record->client->currency,
                                'status' => 'completed',
                                'target_bank_name' => 'Venatomar Credit Services',
                                'target_account_holder' => 'Service des Prêts',
                                'description' => "Remboursement échéance prêt ref. {$record->reference}",
                                'approved_by_admin' => true,
                                'completed_at' => now(),
                            ]);
                        }

                        Notification::make()
                            ->title('Mensualité enregistrée')
                            ->body("Reste dû : " . number_format($newRemaining, 2) . " {$record->client->currency}")
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $admin = auth()->user()?->email;

        if ($admin === 'info@venatomar.com') {
            return $query->whereHas('client', fn ($q) => $q->where('admin_email', 'info@venatomar.com')->orWhereNull('admin_email'));
        }

        if ($admin) {
            return $query->whereHas('client', fn ($q) => $q->where('admin_email', $admin));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankClientLoans::route('/'),
        ];
    }
}
