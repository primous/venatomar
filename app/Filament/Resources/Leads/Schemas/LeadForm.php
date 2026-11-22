<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadStatus;
use App\Filament\Resources\Leads\LeadResource;
use App\Support\AdminText as A;
use App\Support\BankDirectory;
use App\Support\InsurancePdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('1. Dossier, Pays & Langue des Documents')
                ->description('Paramètres généraux du dossier client et choix de la langue pour les documents officiels.')
                ->columns(4)
                ->columnSpanFull()
                ->schema([
                    Hidden::make('admin_email')
                        ->default(fn () => auth()->user()?->email ?: 'info@venatomar.com'),
                    Select::make('country')
                        ->label('Pays de résidence')
                        ->options(BankDirectory::countryOptions())
                        ->default('es')
                        ->required()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if (! $state) {
                                return;
                            }
                            $countryName = BankDirectory::countryNameByCode($state);
                            $set('currency', BankDirectory::currencyForCountry($countryName));
                            $locale = BankDirectory::localeForCountry($countryName);
                            if (array_key_exists($locale, InsurancePdf::LOCALES)) {
                                $set('locale', $locale);
                            }
                        }),
                    Select::make('locale')
                        ->label('Langue du dossier / documents')
                        ->options(InsurancePdf::LOCALES)
                        ->default('es')
                        ->required()
                        ->searchable(),
                    Select::make('status')
                        ->label('Statut du dossier')
                        ->options(LeadResource::statusOptions())
                        ->default(LeadStatus::Accepted->value)
                        ->required(),
                    TextInput::make('currency')
                        ->label('Devise')
                        ->default('EUR')
                        ->required()
                        ->maxLength(3)
                        ->dehydrateStateUsing(fn ($state) => strtoupper((string) $state)),
                ]),

            Section::make('2. Identité du Client')
                ->description('Informations personnelles indispensables pour le Contrat, l\'Assurance et l\'Accord de Financement.')
                ->columns(3)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('first_name')
                        ->label('Prénom')
                        ->required()
                        ->maxLength(80),
                    TextInput::make('last_name')
                        ->label('Nom de famille')
                        ->required()
                        ->maxLength(120),
                    TextInput::make('document_number')
                        ->label('N° Pièce d\'identité (DNI / NIE / Passeport / CNI)')
                        ->maxLength(60),
                    TextInput::make('email')
                        ->label('Adresse E-mail')
                        ->email()
                        ->required()
                        ->maxLength(190),
                    TextInput::make('phone')
                        ->label('Téléphone')
                        ->tel()
                        ->required()
                        ->maxLength(32),
                    DatePicker::make('birthday')
                        ->label('Date de naissance')
                        ->displayFormat('d/m/Y')
                        ->native(false)
                        ->maxDate(now()->subYears(18)),
                    Select::make('gender')
                        ->label('Sexe / Genre')
                        ->options([
                            'male' => 'Homme',
                            'female' => 'Femme',
                            'other' => 'Non précisé',
                        ])
                        ->default('male')
                        ->required(),
                    Select::make('marital_status')
                        ->label('Situation familiale')
                        ->options([
                            'single' => 'Célibataire',
                            'married' => 'Marié(e)',
                            'divorced' => 'Divorcé(e)',
                            'widowed' => 'Veuf / Veuve',
                            'civil_partnership' => 'Union libre / PACS',
                        ])
                        ->default('single')
                        ->required(),
                    Select::make('education')
                        ->label('Niveau d\'études')
                        ->options([
                            'primary' => 'Primaire',
                            'secondary' => 'Secondaire / Collège',
                            'upper_secondary' => 'Baccalauréat / Lycée',
                            'higher_short' => 'Études supérieures courtes (Bac+2/3)',
                            'higher_long' => 'Études universitaires (Master / Doctorat)',
                        ])
                        ->default('upper_secondary')
                        ->required(),
                ]),

            Section::make('3. Adresse de Résidence')
                ->description('Adresse officielle du client figurant sur tous les contrats et certificats.')
                ->columns(3)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('home_street')
                        ->label('Rue / Voie / Adresse')
                        ->required()
                        ->maxLength(160)
                        ->columnSpan(2),
                    TextInput::make('home_number')
                        ->label('N° de voie / Bâtiment')
                        ->required()
                        ->default('1')
                        ->maxLength(20),
                    TextInput::make('apartment_floor')
                        ->label('Complément (Étage, Appartement)')
                        ->maxLength(40),
                    TextInput::make('geo_zip')
                        ->label('Code postal')
                        ->required()
                        ->maxLength(12),
                    TextInput::make('geo_city')
                        ->label('Ville')
                        ->required()
                        ->maxLength(120),
                    TextInput::make('geo_province')
                        ->label('Province / Département / Région')
                        ->maxLength(80),
                    Select::make('home_status')
                        ->label('Statut du logement')
                        ->options([
                            'tenant' => 'Locataire',
                            'owner_no_mortgage' => 'Propriétaire sans crédit en cours',
                            'owner_mortgage' => 'Propriétaire avec crédit en cours',
                            'family' => 'Hébergé(e) par la famille',
                            'other' => 'Autre',
                        ])
                        ->default('tenant')
                        ->required(),
                ]),

            Section::make('4. Prêt Demandé & Situation Financière')
                ->description('Conditions du prêt et indicateurs de solvabilité du client.')
                ->columns(3)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('amount')
                        ->label('Montant du prêt accordé / demandé')
                        ->required()
                        ->integer()
                        ->minValue(100)
                        ->maxValue(10000000)
                        ->default(15000),
                    TextInput::make('term_months')
                        ->label('Durée de remboursement (mois)')
                        ->required()
                        ->integer()
                        ->minValue(1)
                        ->maxValue(480)
                        ->default(48),
                    Select::make('loan_purpose')
                        ->label('Objet / Motif du prêt')
                        ->options([
                            'help' => 'Projet personnel / Trésorerie',
                            'renovation' => 'Travaux & Rénovation',
                            'car' => 'Achat d\'un véhicule',
                            'debts' => 'Rachat de crédits / Dettes',
                            'travel' => 'Voyage / Événement',
                            'medical' => 'Santé & Dépenses médicales',
                            'other' => 'Autre motif',
                        ])
                        ->default('help')
                        ->required(),
                    TextInput::make('monthly_income')
                        ->label('Revenus mensuels nets')
                        ->required()
                        ->integer()
                        ->minValue(0)
                        ->default(2800),
                    TextInput::make('regular_expenses')
                        ->label('Charges mensuelles régulières')
                        ->required()
                        ->integer()
                        ->minValue(0)
                        ->default(600),
                    Select::make('employment_status')
                        ->label('Situation professionnelle')
                        ->options([
                            'employee' => 'Salarié(e) (CDI / CDD)',
                            'civil_servant' => 'Fonctionnaire / Service public',
                            'self_employed' => 'Indépendant(e) / Profession libérale',
                            'retired' => 'Retraité(e)',
                            'informal' => 'Commerçant(e) / Secteur indépendant',
                            'unemployed' => 'Sans emploi',
                            'other' => 'Autre situation',
                        ])
                        ->default('employee')
                        ->required(),
                    Select::make('company_sector')
                        ->label('Secteur d\'activité')
                        ->options([
                            'services' => 'Services & Conseil',
                            'finance' => 'Banque & Assurance',
                            'technology' => 'Technologies & Télécoms',
                            'health' => 'Santé & Social',
                            'education' => 'Enseignement',
                            'retail' => 'Commerce & Distribution',
                            'industry' => 'Industrie',
                            'construction' => 'BTP & Construction',
                            'transport' => 'Transport & Logistique',
                            'hospitality' => 'Hôtellerie & Restauration',
                            'agriculture' => 'Agriculture',
                            'public' => 'Administration publique',
                            'other' => 'Autre',
                        ])
                        ->default('services')
                        ->required(),
                    Toggle::make('defaulter_list')
                        ->label('Fiché incident bancaire (ASNEF, FICP, etc.)')
                        ->default(false),
                    Toggle::make('has_debt')
                        ->label('Autres crédits en cours')
                        ->default(false),
                    Toggle::make('has_car')
                        ->label('Possède un véhicule')
                        ->default(false)
                        ->live(),
                    TextInput::make('car_number')
                        ->label('Numéro d\'immatriculation du véhicule')
                        ->maxLength(20)
                        ->visible(fn ($get) => (bool) $get('has_car')),
                ]),

            Section::make('5. Coordonnées Bancaires de Versement')
                ->description('Coordonnées du compte sur lequel les fonds du prêt seront transférés.')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    Select::make('payout_method')
                        ->label('Mode de versement des fonds')
                        ->options([
                            'iban' => 'Virement bancaire (IBAN / Compte bancaire)',
                            'mobile_money' => 'Mobile Money (Orange, MVola, Airtel...)',
                        ])
                        ->default('iban')
                        ->required()
                        ->live(),
                    TextInput::make('bank_account')
                        ->label('IBAN / Numéro de compte bancaire')
                        ->maxLength(80)
                        ->visible(fn ($get) => $get('payout_method') !== 'mobile_money')
                        ->required(fn ($get) => $get('payout_method') !== 'mobile_money'),
                    Select::make('mm_operator')
                        ->label('Opérateur Mobile Money')
                        ->options([
                            'mvola' => 'MVola',
                            'orange_money' => 'Orange Money',
                            'airtel_money' => 'Airtel Money',
                        ])
                        ->visible(fn ($get) => $get('payout_method') === 'mobile_money')
                        ->required(fn ($get) => $get('payout_method') === 'mobile_money'),
                    TextInput::make('mm_number')
                        ->label('Numéro Mobile Money')
                        ->maxLength(32)
                        ->visible(fn ($get) => $get('payout_method') === 'mobile_money')
                        ->required(fn ($get) => $get('payout_method') === 'mobile_money'),
                ]),

            Section::make('6. Accès Espace Client Banque en Ligne (Optionnel)')
                ->description('Création immédiate des identifiants pour permettre au client de se connecter à son espace bancaire.')
                ->columns(2)
                ->columnSpanFull()
                ->collapsed()
                ->schema([
                    Toggle::make('create_bank_account')
                        ->label('Ouvrir également un compte bancaire en ligne pour ce client')
                        ->helperText('Crée automatiquement son compte dans l\'Espace Client avec son IBAN et solde de prêt.')
                        ->default(false)
                        ->live(),
                    TextInput::make('portal_password')
                        ->label('Mot de passe de connexion client (/my-account)')
                        ->password()
                        ->revealable()
                        ->helperText('Laissez vide pour générer un mot de passe aléatoire sécurisé.')
                        ->visible(fn ($get) => (bool) $get('create_bank_account')),
                ]),
        ]);
    }
}
