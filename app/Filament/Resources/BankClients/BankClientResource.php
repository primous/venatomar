<?php

namespace App\Filament\Resources\BankClients;

use App\Filament\Resources\BankClients\Pages\CreateBankClient;
use App\Filament\Resources\BankClients\Pages\EditBankClient;
use App\Filament\Resources\BankClients\Pages\ListBankClients;
use App\Models\BankClient;
use App\Models\BankTransaction;
use App\Support\BankDirectory;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BankClientResource extends Resource
{
    protected static ?string $model = BankClient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Banque en Ligne';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'account_number';

    public static function getModelLabel(): string
    {
        return 'Compte Client';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Comptes Clients';
    }

    public static function getNavigationLabel(): string
    {
        return 'Clients & Comptes';
    }

    public static function form(Schema $schema): Schema
    {
        $countries = BankDirectory::countryNames();

        return $schema->components([
            Section::make('Identité & Accès Espace Client')->columns(2)->columnSpanFull()->schema([
                Select::make('admin_email')->label('Canal / Admin responsable')
                    ->options([
                        'info@venatomar.com' => 'Principal (info@venatomar.com)',
                        'contact@venatomar.com' => 'Contact (contact@venatomar.com)',
                    ])
                    ->default(fn () => auth()->user()?->email === 'contact@venatomar.com' ? 'contact@venatomar.com' : 'info@venatomar.com')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('first_name')->label('Prénom')->required()->maxLength(80),
                TextInput::make('last_name')->label('Nom de famille')->required()->maxLength(120),
                TextInput::make('email')->label('Adresse Email (Identifiant de connexion)')
                    ->email()->required()->maxLength(190)
                    ->unique(BankClient::class, 'email', ignoreRecord: true),
                TextInput::make('password')->label('Mot de passe de connexion')
                    ->password()->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText('Défini par l\'administrateur pour permettre la connexion du client.'),
                TextInput::make('phone')->label('Numéro de téléphone')->tel()->maxLength(32),
                DatePicker::make('birth_date')->label('Date de naissance')->native(false)->displayFormat('d/m/Y'),
                TextInput::make('document_number')->label('N° Pièce d\'identité (DNI / Passeport / NIE)')->maxLength(60),
            ]),

            Section::make('Localisation & Banques Nationales')->columns(2)->columnSpanFull()->schema([
                Select::make('country')->label('Pays de résidence')
                    ->options($countries)
                    ->required()
                    ->default('France')
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if (! $state) {
                            return;
                        }
                        $code = BankDirectory::codeForCountry($state);
                        $set('country_code', $code);
                        $set('currency', BankDirectory::currencyForCountry($state));
                        $set('locale', BankDirectory::localeForCountry($state));
                        $banks = BankDirectory::banksForCountry($state);
                        $set('available_banks_note', implode(', ', array_slice($banks, 0, 5)) . (count($banks) > 5 ? '...' : ''));
                    }),
                Select::make('locale')->label('Langue de l\'espace client')
                    ->options([
                        'es' => 'Español (ES)',
                        'fr' => 'Français (FR)',
                        'en' => 'English (EN)',
                        'de' => 'Deutsch (DE)',
                        'it' => 'Italiano (IT)',
                        'pt' => 'Português (PT)',
                        'nl' => 'Nederlands (NL)',
                    ])
                    ->default('es')
                    ->required()
                    ->helperText('Langue dans laquelle l\'espace client et ses documents s\'afficheront automatiquement.'),
                TextInput::make('country_code')->label('Code Pays (ISO)')->maxLength(3)->default('FR'),
                TextInput::make('city')->label('Ville')->maxLength(100),
                TextInput::make('address')->label('Adresse postale')->maxLength(255),
                TextInput::make('postal_code')->label('Code Postal')->maxLength(20),
                Select::make('currency')->label('Devise du compte')
                    ->options([
                        'EUR' => 'EUR - Euro (€)',
                        'USD' => 'USD - Dollar américain ($)',
                        'UYU' => 'UYU - Peso uruguayen ($U)',
                        'PEN' => 'PEN - Sol péruvien (S/)',
                        'MXN' => 'MXN - Peso mexicain ($)',
                        'GBP' => 'GBP - Livre sterling (£)',
                        'CHF' => 'CHF - Franc suisse (CHF)',
                        'CAD' => 'CAD - Dollar canadien ($)',
                        'XOF' => 'XOF - Franc CFA (BCEAO)',
                        'XAF' => 'XAF - Franc CFA (BEAC)',
                        'MAD' => 'MAD - Dirham marocain',
                        'MGA' => 'MGA - Ariary malgache',
                    ])
                    ->default('EUR')
                    ->required(),
                TextInput::make('account_number')->label('Numéro de compte')
                    ->placeholder('Généré automatiquement (ex: VTM-98421098)')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('iban')->label('IBAN / Compte bancaire')
                    ->placeholder('Généré automatiquement lors de la création')
                    ->maxLength(34),
                TextInput::make('bic')->label('Code BIC / SWIFT')->default('VTMARSM1XXX')->maxLength(11),
            ]),

            Section::make('Soldes & Ligne de Crédit')->columns(3)->columnSpanFull()->schema([
                TextInput::make('balance')->label('Solde Compte Courant')
                    ->numeric()->default(0.00)->required()->prefix('€ / Dev.'),
                TextInput::make('savings_balance')->label('Solde Compte d\'Épargne')
                    ->numeric()->default(0.00)->prefix('€ / Dev.'),
                TextInput::make('authorized_overdraft')->label('Découvert autorisé')
                    ->numeric()->default(0.00)->prefix('€ / Dev.'),
            ]),

            Section::make('Sécurité & Validation des Transferts')->columns(2)->columnSpanFull()->schema([
                Select::make('status')->label('Statut du compte')
                    ->options([
                        'active' => 'Actif (Accès normal)',
                        'frozen' => 'Gelé (Consultation uniquement, opérations bloquées)',
                        'blocked' => 'Bloqué (Accès interdit)',
                    ])
                    ->default('active')
                    ->required(),
                Select::make('tier')->label('Niveau du compte')
                    ->options([
                        'standard' => 'Standard',
                        'silver' => 'Silver Privilège',
                        'gold' => 'Gold Premium',
                        'vip' => 'VIP Banque Privée',
                    ])
                    ->default('standard')
                    ->required(),
                Toggle::make('can_receive_otp_email')
                    ->label('Autoriser l\'envoi de code de vérification par email au client')
                    ->helperText('Par défaut désactivé pour des raisons de sécurité : l\'administrateur conserve la réception des codes pour la première tentative. Activez cette option pour que le client reçoive automatiquement ses codes par email après vérification.')
                    ->default(false)
                    ->columnSpanFull(),
                Textarea::make('notes')->label('Notes internes de conformité (Admin uniquement)')
                    ->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('account_number')->label('N° Compte')
                    ->searchable()->copyable()->weight('bold'),
                TextColumn::make('full_name')->label('Client')
                    ->state(fn (BankClient $record) => $record->fullName())
                    ->searchable(['first_name', 'last_name'])
                    ->description(fn (BankClient $record) => $record->email),
                TextColumn::make('country')->label('Pays')->badge()->searchable(),
                TextColumn::make('locale')->label('Langue')->badge()->formatStateUsing(fn ($state) => strtoupper($state)),
                TextColumn::make('balance')->label('Solde Courant')
                    ->formatStateUsing(fn ($record) => $record->formattedBalance())
                    ->weight('bold')
                    ->color(fn (BankClient $record) => $record->balance >= 0 ? 'success' : 'danger'),
                TextColumn::make('savings_balance')->label('Épargne')
                    ->formatStateUsing(fn ($record) => $record->formattedSavingsBalance()),
                TextColumn::make('status')->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active' => 'Actif',
                        'frozen' => 'Gelé',
                        'blocked' => 'Bloqué',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'active' => 'success',
                        'frozen' => 'warning',
                        'blocked' => 'danger',
                        default => 'secondary',
                    }),
                IconColumn::make('can_receive_otp_email')->label('Email OTP')
                    ->boolean()
                    ->tooltip(fn (BankClient $record) => $record->can_receive_otp_email ? 'Le client reçoit ses codes par email' : 'L\'admin retient le code (sécurité 1ère tentative)'),
                TextColumn::make('admin_email')->label('Canal Admin')->badge()
                    ->formatStateUsing(fn (?string $state) => $state === 'contact@venatomar.com' ? 'contact@venatomar.com' : 'info@venatomar.com')
                    ->color(fn (?string $state) => $state === 'contact@venatomar.com' ? 'info' : 'success'),
                TextColumn::make('created_at')->label('Créé le')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('admin_email')->label('Canal Admin')
                    ->options([
                        'contact@venatomar.com' => 'Contact (contact@venatomar.com)',
                        'info@venatomar.com' => 'Principal (info@venatomar.com)',
                    ])
                    ->default(fn () => auth()->user()?->email === 'contact@venatomar.com' ? 'contact@venatomar.com' : null),
                SelectFilter::make('status')->label('Statut')->options([
                    'active' => 'Actif',
                    'frozen' => 'Gelé',
                    'blocked' => 'Bloqué',
                ]),
                SelectFilter::make('country')->label('Pays')->options(BankDirectory::countryNames()),
            ])
            ->recordActions([
                Action::make('credit')
                    ->label('Créditer')
                    ->icon(Heroicon::OutlinedPlusCircle)
                    ->color('success')
                    ->form([
                        TextInput::make('amount')->label('Montant à créditer')->numeric()->required()->minValue(1),
                        TextInput::make('description')->label('Motif / Libellé')->default('Dépôt de fonds / Virement créditeur')->required(),
                    ])
                    ->action(function (BankClient $record, array $data) {
                        $amount = (float) $data['amount'];
                        $record->increment('balance', $amount);

                        BankTransaction::create([
                            'bank_client_id' => $record->id,
                            'type' => 'transfer_in',
                            'category' => 'credit_admin',
                            'amount' => $amount,
                            'currency' => $record->currency,
                            'status' => 'completed',
                            'target_bank_name' => 'Venatomar Bank',
                            'target_account_holder' => $record->fullName(),
                            'target_account_number' => $record->account_number,
                            'description' => $data['description'],
                            'approved_by_admin' => true,
                            'completed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Compte crédité avec succès')
                            ->body("Le compte a été crédité de {$data['amount']} {$record->currency}.")
                            ->success()
                            ->send();
                    }),

                Action::make('debit')
                    ->label('Débiter')
                    ->icon(Heroicon::OutlinedMinusCircle)
                    ->color('danger')
                    ->form([
                        TextInput::make('amount')->label('Montant à débiter')->numeric()->required()->minValue(1),
                        TextInput::make('description')->label('Motif / Justification')->default('Frais de gestion / Débit administratif')->required(),
                    ])
                    ->action(function (BankClient $record, array $data) {
                        $amount = (float) $data['amount'];
                        $record->decrement('balance', $amount);

                        BankTransaction::create([
                            'bank_client_id' => $record->id,
                            'type' => 'transfer_out',
                            'category' => 'debit_admin',
                            'amount' => $amount,
                            'currency' => $record->currency,
                            'status' => 'completed',
                            'target_bank_name' => 'Venatomar Bank',
                            'target_account_holder' => $record->fullName(),
                            'target_account_number' => $record->account_number,
                            'description' => $data['description'],
                            'approved_by_admin' => true,
                            'completed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Compte débité avec succès')
                            ->body("Le compte a été débité de {$data['amount']} {$record->currency}.")
                            ->warning()
                            ->send();
                    }),

                Action::make('toggle_freeze')
                    ->label(fn (BankClient $record) => $record->isFrozen() ? 'Dégeler le compte' : 'Geler le compte')
                    ->icon(Heroicon::OutlinedLockClosed)
                    ->color(fn (BankClient $record) => $record->isFrozen() ? 'success' : 'warning')
                    ->requiresConfirmation()
                    ->action(function (BankClient $record) {
                        $newStatus = $record->isFrozen() ? 'active' : 'frozen';
                        $record->update(['status' => $newStatus]);

                        Notification::make()
                            ->title($newStatus === 'frozen' ? 'Compte gelé' : 'Compte réactivé')
                            ->success()
                            ->send();
                    }),

                Action::make('toggle_otp_email')
                    ->label(fn (BankClient $record) => $record->can_receive_otp_email ? 'Désactiver OTP Email' : 'Autoriser OTP Email')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->color(fn (BankClient $record) => $record->can_receive_otp_email ? 'secondary' : 'info')
                    ->action(function (BankClient $record) {
                        $record->update(['can_receive_otp_email' => ! $record->can_receive_otp_email]);
                        Notification::make()
                            ->title($record->can_receive_otp_email ? 'Autorisation OTP Email accordée' : 'Autorisation OTP Email révoquée')
                            ->body($record->can_receive_otp_email ? 'Le client recevra désormais ses codes par email.' : 'L\'administrateur recevra les codes de sécurité.')
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankClients::route('/'),
            'create' => CreateBankClient::route('/create'),
            'edit' => EditBankClient::route('/{record}/edit'),
        ];
    }
}
