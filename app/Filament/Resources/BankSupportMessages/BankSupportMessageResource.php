<?php

namespace App\Filament\Resources\BankSupportMessages;

use App\Filament\Resources\BankSupportMessages\Pages\ListBankSupportMessages;
use App\Models\BankClient;
use App\Models\BankSupportMessage;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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

class BankSupportMessageResource extends Resource
{
    protected static ?string $model = BankSupportMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Banque en Ligne';

    protected static ?int $navigationSort = 13;

    protected static ?string $recordTitleAttribute = 'subject';

    public static function getModelLabel(): string
    {
        return 'Message Support / Fraude';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Messages & Contestations Fraude';
    }

    public static function getNavigationLabel(): string
    {
        return 'Support & Litiges Fraude';
    }

    public static function getNavigationBadge(): ?string
    {
        $open = BankSupportMessage::whereIn('status', ['open', 'in_progress'])->count();

        return $open > 0 ? (string) $open : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Détails de la réclamation')->columns(2)->columnSpanFull()->schema([
                Select::make('bank_client_id')->label('Client')
                    ->relationship('client', 'account_number')
                    ->getOptionLabelFromRecordUsing(fn (BankClient $c) => "{$c->account_number} — {$c->fullName()}")
                    ->disabled()
                    ->required(),
                Select::make('category')->label('Catégorie')
                    ->options([
                        'general' => 'Question générale',
                        'fraud_alert' => 'Alerte Fraude / Sécurité',
                        'transaction_dispute' => 'Contestation d\'opération',
                        'loan_inquiry' => 'Demande de prêt / crédit',
                        'technical' => 'Support technique',
                    ])
                    ->required(),
                TextInput::make('subject')->label('Objet')->required()->columnSpanFull(),
                Textarea::make('message')->label('Message du client')->rows(5)->disabled()->columnSpanFull(),
            ]),

            Section::make('Réponse Administrateur & Résolution')->columns(2)->columnSpanFull()->schema([
                Select::make('status')->label('Statut du dossier')
                    ->options([
                        'open' => 'Ouvert (Nouveau)',
                        'in_progress' => 'En cours d\'analyse',
                        'resolved' => 'Résolu',
                        'closed' => 'Clôturé',
                    ])
                    ->required(),
                Textarea::make('admin_reply')->label('Réponse officielle envoyée au client')
                    ->rows(5)
                    ->placeholder('Rédigez la réponse qui apparaîtra dans l\'espace client...')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('client.full_name')->label('Client')
                    ->state(fn (BankSupportMessage $record) => $record->client?->fullName())
                    ->description(fn (BankSupportMessage $record) => $record->client?->email)
                    ->searchable(),
                TextColumn::make('category')->label('Catégorie')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'fraud_alert' => 'Alerte Fraude',
                        'transaction_dispute' => 'Contestation',
                        'loan_inquiry' => 'Prêt',
                        default => 'Support',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'fraud_alert', 'transaction_dispute' => 'danger',
                        'loan_inquiry' => 'info',
                        default => 'secondary',
                    }),
                TextColumn::make('subject')->label('Objet')->searchable()->limit(40),
                TextColumn::make('status')->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'open' => 'Nouveau',
                        'in_progress' => 'En cours',
                        'resolved' => 'Résolu',
                        'closed' => 'Fermé',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'open' => 'danger',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                SelectFilter::make('category')->label('Catégorie')->options([
                    'fraud_alert' => 'Alerte Fraude',
                    'transaction_dispute' => 'Contestation',
                    'general' => 'Général',
                ]),
                SelectFilter::make('status')->label('Statut')->options([
                    'open' => 'Nouveau',
                    'in_progress' => 'En cours',
                    'resolved' => 'Résolu',
                ]),
            ])
            ->recordActions([
                Action::make('reply')
                    ->label('Répondre')
                    ->icon(Heroicon::OutlinedChatBubbleLeft)
                    ->color('primary')
                    ->form([
                        Textarea::make('admin_reply')->label('Votre réponse au client')
                            ->default(fn (BankSupportMessage $record) => $record->admin_reply)
                            ->rows(5)
                            ->required(),
                        Select::make('status')->label('Nouveau statut')
                            ->options([
                                'in_progress' => 'En cours de traitement',
                                'resolved' => 'Résolu (Dossier clos)',
                            ])
                            ->default('resolved')
                            ->required(),
                    ])
                    ->action(function (BankSupportMessage $record, array $data) {
                        $record->update([
                            'admin_reply' => $data['admin_reply'],
                            'status' => $data['status'],
                            'replied_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Réponse enregistrée')
                            ->body('Le client peut consulter votre réponse dans son espace bancaire.')
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

        if ($admin === 'contact@venatomar.com') {
            return $query->whereHas('client', fn ($q) => $q->where('admin_email', 'contact@venatomar.com'));
        }

        if ($admin === 'info@venatomar.com') {
            return $query->whereHas('client', fn ($q) => $q->where('admin_email', 'info@venatomar.com')->orWhereNull('admin_email'));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankSupportMessages::route('/'),
        ];
    }
}
