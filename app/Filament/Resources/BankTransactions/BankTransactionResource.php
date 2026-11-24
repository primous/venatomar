<?php

namespace App\Filament\Resources\BankTransactions;

use App\Filament\Resources\BankTransactions\Pages\CreateBankTransaction;
use App\Filament\Resources\BankTransactions\Pages\EditBankTransaction;
use App\Filament\Resources\BankTransactions\Pages\ListBankTransactions;
use App\Mail\BankClientCreditedMail;
use App\Mail\BankClientDebitedMail;
use App\Models\BankClient;
use App\Models\BankTransaction;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class BankTransactionResource extends Resource
{
    protected static ?string $model = BankTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Banque en Ligne';

    protected static ?int $navigationSort = 11;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function getModelLabel(): string
    {
        return 'Virement / Transaction';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Virements & Opérations';
    }

    public static function getNavigationLabel(): string
    {
        return 'Virements & Opérations';
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = BankTransaction::where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Détails de l\'Opération')->columns(2)->columnSpanFull()->schema([
                Select::make('bank_client_id')->label('Compte Client')
                    ->relationship('client', 'account_number')
                    ->getOptionLabelFromRecordUsing(fn (BankClient $c) => "{$c->account_number} — {$c->fullName()} ({$c->country})")
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('reference')->label('Référence de transaction')
                    ->disabled()
                    ->placeholder('Généré automatiquement (ex: TRX-202611-AB1234)'),
                Select::make('type')->label('Type d\'opération')
                    ->options([
                        'transfer_out' => 'Virement émis (Débit)',
                        'transfer_in' => 'Virement reçu (Crédit)',
                        'savings_deposit' => 'Versement vers Épargne',
                        'savings_withdrawal' => 'Retrait depuis Épargne',
                        'fee' => 'Frais bancaires',
                    ])
                    ->default('transfer_out')
                    ->required(),
                TextInput::make('amount')->label('Montant')->numeric()->required()->minValue(0.01),
                TextInput::make('currency')->label('Devise')->default('EUR')->required(),
                Select::make('status')->label('Statut du transfert')
                    ->options([
                        'pending' => 'En attente de validation (OTP / Admin)',
                        'completed' => 'Validé & Exécuté',
                        'cancelled' => 'Annulé / Rejeté',
                    ])
                    ->default('pending')
                    ->required(),
            ]),

            Section::make('Coordonnées du Bénéficiaire & Sécurité')->columns(2)->columnSpanFull()->schema([
                TextInput::make('target_bank_name')->label('Banque Destinataire / Retrait'),
                TextInput::make('target_account_holder')->label('Titulaire du compte destinataire'),
                TextInput::make('target_account_number')->label('IBAN / N° de compte destinataire'),
                Textarea::make('description')->label('Motif du virement')->columnSpanFull(),
                TextInput::make('otp_code')->label('Code de Sécurité OTP (Généré pour ce virement)')
                    ->helperText('Code requis pour autoriser le virement. Lors de la 1ère tentative, l\'administrateur conserve ce code.'),
                TextInput::make('otp_expires_at')->label('Expiration du code')->disabled(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label('Réf. Virement')
                    ->searchable()->copyable()->weight('bold'),
                TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('client.full_name')->label('Client Émetteur')
                    ->state(fn (BankTransaction $record) => $record->client?->fullName())
                    ->description(fn (BankTransaction $record) => $record->client?->account_number)
                    ->searchable(),
                TextColumn::make('type')->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'transfer_out' => 'Virement émis',
                        'transfer_in' => 'Virement reçu',
                        'savings_deposit' => 'Vers épargne',
                        'savings_withdrawal' => 'Depuis épargne',
                        'fee' => 'Frais',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'transfer_out', 'fee' => 'danger',
                        'transfer_in' => 'success',
                        default => 'info',
                    }),
                TextColumn::make('amount')->label('Montant')
                    ->formatStateUsing(fn (BankTransaction $record) => $record->formattedAmount())
                    ->weight('bold')
                    ->color(fn (BankTransaction $record) => in_array($record->type, ['transfer_out', 'fee']) ? 'danger' : 'success'),
                TextColumn::make('target_bank_name')->label('Banque Destinataire')
                    ->description(fn (BankTransaction $record) => $record->target_account_holder ?: '—'),
                TextColumn::make('otp_code')->label('Code OTP (Admin)')
                    ->badge()
                    ->color('warning')
                    ->copyable()
                    ->tooltip('Code de sécurité à communiquer ou valider'),
                TextColumn::make('status')->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'En attente',
                        'completed' => 'Validé',
                        'cancelled' => 'Annulé',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options([
                    'pending' => 'En attente de validation',
                    'completed' => 'Validé / Exécuté',
                    'cancelled' => 'Annulé / Rejeté',
                ]),
                SelectFilter::make('type')->label('Type')->options([
                    'transfer_out' => 'Virement émis',
                    'transfer_in' => 'Virement reçu',
                    'savings_deposit' => 'Vers épargne',
                ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Valider le virement')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (BankTransaction $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->modalHeading('Confirmer et débloquer ce virement')
                    ->modalDescription(fn (BankTransaction $record) => "Valider le virement de {$record->amount} {$record->currency} vers la banque {$record->target_bank_name} ? Le montant sera débité du solde du client.")
                    ->action(function (BankTransaction $record) {
                        $client = $record->client;

                        // Check balance for debit
                        if (in_array($record->type, ['transfer_out', 'fee'], true)) {
                            if ($client->balance < $record->amount && ($client->balance + $client->authorized_overdraft) < $record->amount) {
                                Notification::make()
                                    ->title('Solde insuffisant')
                                    ->body("Le client ne dispose pas d'un solde suffisant pour exécuter ce virement.")
                                    ->danger()
                                    ->send();

                                return;
                            }
                            $client->decrement('balance', $record->amount);
                        }

                        $record->update([
                            'status' => 'completed',
                            'approved_by_admin' => true,
                            'completed_at' => now(),
                        ]);

                        if ($client && !empty($client->email)) {
                            try {
                                if (in_array($record->type, ['transfer_out', 'fee'], true)) {
                                    Mail::to($client->email)
                                        ->locale($client->locale ?: 'es')
                                        ->queue(new BankClientDebitedMail(
                                            client: $client->fresh(),
                                            amount: (float) $record->amount,
                                            description: (string) ($record->description ?: "Virement vers {$record->target_bank_name}"),
                                            newBalance: (float) $client->fresh()->balance,
                                            date: $record->completed_at ?: now()
                                        ));

                                    app(\App\Services\WebPushService::class)->sendToClient(
                                        $client,
                                        'Débito confirmado (-'.number_format($record->amount, 2, ',', ' ').' '.$record->currency.')',
                                        "Su transferencia de {$record->amount} {$record->currency} ha sido ejecutada.",
                                        ['url' => '/es/portal/transfers']
                                    );
                                } else {
                                    Mail::to($client->email)
                                        ->locale($client->locale ?: 'es')
                                        ->queue(new BankClientCreditedMail(
                                            client: $client->fresh(),
                                            amount: (float) $record->amount,
                                            description: (string) ($record->description ?: "Virement reçu"),
                                            newBalance: (float) $client->fresh()->balance,
                                            date: $record->completed_at ?: now()
                                        ));

                                    app(\App\Services\WebPushService::class)->sendToClient(
                                        $client,
                                        'Transferencia recibida (+'.number_format($record->amount, 2, ',', ' ').' '.$record->currency.')',
                                        "Su cuenta ha recibido {$record->amount} {$record->currency}.",
                                        ['url' => '/es/portal']
                                    );
                                }
                            } catch (\Throwable $e) {
                                report($e);
                            }
                        }

                        Notification::make()
                            ->title('Virement validé avec succès')
                            ->body("Le virement {$record->reference} a été validé et le client a été notifié par e-mail.")
                            ->success()
                            ->send();
                    }),

                Action::make('cancel')
                    ->label('Rejeter / Annuler')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (BankTransaction $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->action(function (BankTransaction $record) {
                        $record->update([
                            'status' => 'cancelled',
                        ]);

                        Notification::make()
                            ->title('Virement annulé')
                            ->body("Le virement {$record->reference} a été marqué comme annulé.")
                            ->warning()
                            ->send();
                    }),

                Action::make('authorize_client_otp')
                    ->label('Autoriser OTP Email Client')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->color('info')
                    ->visible(fn (BankTransaction $record) => ! ($record->client?->can_receive_otp_email ?? false))
                    ->action(function (BankTransaction $record) {
                        $record->client?->update(['can_receive_otp_email' => true]);

                        Notification::make()
                            ->title('Autorisation accordée')
                            ->body("Le client {$record->client?->fullName()} recevra désormais ses codes de confirmation directement par email.")
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
            'index' => ListBankTransactions::route('/'),
            'create' => CreateBankTransaction::route('/create'),
            'edit' => EditBankTransaction::route('/{record}/edit'),
        ];
    }
}
