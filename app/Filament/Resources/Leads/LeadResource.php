<?php

namespace App\Filament\Resources\Leads;

use App\Actions\SubmitLead;
use App\Enums\LeadStatus;
use App\Filament\Resources\InsuranceDocuments\InsuranceDocumentResource;
use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Filament\Resources\Leads\Pages\EditLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Leads\Pages\ViewLead;
use App\Filament\Resources\Leads\Schemas\LeadForm;
use App\Filament\Resources\Leads\Schemas\LeadInfolist;
use App\Filament\Resources\Leads\Tables\LeadsTable;
use App\Filament\Resources\LoanApprovalDocuments\LoanApprovalDocumentResource;
use App\Filament\Resources\LoanContracts\LoanContractResource;
use App\Models\BankClient;
use App\Models\Lead;
use App\Services\LeadDocumentService;
use App\Support\AdminText as A;
use App\Support\BankDirectory;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Str;

/** Client loan requests & registered borrower dossiers. */
class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function getModelLabel(): string
    {
        return A::t('model.lead');
    }

    public static function getPluralModelLabel(): string
    {
        return A::t('model.leads');
    }

    public static function getNavigationLabel(): string
    {
        return A::t('nav.leads');
    }

    public static function getNavigationBadge(): ?string
    {
        $new = Lead::where('status', LeadStatus::Received)->count();

        return $new > 0 ? (string) $new : null;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return LeadForm::configure($schema);
    }

    public static function statusOptions(): array
    {
        return collect(LeadStatus::cases())->mapWithKeys(fn (LeadStatus $s) => [$s->value => self::statusLabel($s)])->all();
    }

    public static function statusLabel(LeadStatus $status): string
    {
        return A::t('status.'.$status->value);
    }

    public static function statusColor(LeadStatus $status): string
    {
        return match ($status) {
            LeadStatus::Received => 'warning',
            LeadStatus::Sent => 'info',
            LeadStatus::Accepted, LeadStatus::Funded => 'success',
            LeadStatus::Rejected => 'danger',
        };
    }

    public static function changeStatusAction(): Action
    {
        return Action::make('changeStatus')
            ->label(A::t('lead.change_status'))
            ->icon(Heroicon::OutlinedArrowPath)
            ->fillForm(fn (Lead $record) => ['status' => $record->status->value])
            ->schema([
                Select::make('status')->label(A::t('lead.new_status'))->options(self::statusOptions())->required()->native(false),
            ])
            ->action(function (Lead $record, array $data) {
                $record->update(['status' => $data['status']]);
                Notification::make()->title(A::t('lead.status_updated'))->success()->send();
            });
    }

    public static function retryMailsAction(): Action
    {
        return Action::make('retryMails')
            ->label(A::t('lead.retry_mails'))
            ->icon(Heroicon::OutlinedEnvelope)
            ->color('gray')
            ->requiresConfirmation()
            ->modalDescription(A::t('lead.retry_desc'))
            ->visible(fn (Lead $record) => $record->mail_failed_at !== null
                || $record->internal_mail_sent_at === null || $record->confirmation_mail_sent_at === null)
            ->action(function (Lead $record) {
                $record->forceFill(['mail_failed_at' => null])->saveQuietly();
                SubmitLead::queueMails($record);
                Notification::make()->title(A::t('lead.mails_queued'))->success()->send();
            });
    }

    /** Generates all 3 official documents (Contract, Insurance, Approval) in one click with identical client & loan data. */
    public static function generateAllDocumentsAction(): Action
    {
        return Action::make('generateAllDocuments')
            ->label('Générer le Pack Complet (3 Documents)')
            ->icon(Heroicon::OutlinedSparkles)
            ->color('success')
            ->modalHeading('Générer le Pack de 3 documents officiels')
            ->modalDescription('Définissez les coûts des documents payants. L\'attestation de validation de prêt reste 100% GRATUITE.')
            ->schema([
                TextInput::make('contract_fee')
                    ->label('Coût de réalisation du Contrat de prêt')
                    ->helperText('Ce montant apparaîtra sur le contrat : "La réalisation de ce document coûte : [MONTANT]"')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix(fn (Lead $record) => $record->currency ?: 'EUR')
                    ->placeholder('Ex: 250'),
                TextInput::make('insurance_fee')
                    ->label('Coût de réalisation de l\'Assurance crédit')
                    ->helperText('Ce montant apparaîtra sur le certificat d\'assurance : "La réalisation de ce document coûte : [MONTANT]"')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix(fn (Lead $record) => $record->currency ?: 'EUR')
                    ->placeholder('Ex: 150'),
            ])
            ->action(function (Lead $record, array $data) {
                $overrides = [
                    'contract' => ! empty($data['contract_fee']) ? ['document_fee' => (float) $data['contract_fee']] : [],
                    'insurance' => ! empty($data['insurance_fee']) ? ['document_fee' => (float) $data['insurance_fee']] : [],
                ];

                $docs = LeadDocumentService::generateAllThree($record, $overrides);

                Notification::make()
                    ->title('Pack de 3 documents généré avec succès !')
                    ->body("Contrat : {$docs['contract']->reference} | Assurance : {$docs['insurance']->reference} | Accord (Gratuit) : {$docs['approval']->reference}")
                    ->success()
                    ->send();
            });
    }

    /** Direct action to generate a Loan Contract from this lead. */
    public static function generateContractAction(): Action
    {
        return Action::make('generateContract')
            ->label('Générer Contrat de prêt')
            ->icon(Heroicon::OutlinedDocumentText)
            ->color('primary')
            ->modalHeading('Générer Contrat de prêt')
            ->modalDescription('Indiquez le coût facturé pour la réalisation du contrat (indiqué sur le document PDF).')
            ->schema([
                TextInput::make('document_fee')
                    ->label('Coût de réalisation du document')
                    ->helperText('Ce montant apparaîtra sur le contrat : "La réalisation de ce document coûte : [MONTANT]"')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix(fn (Lead $record) => $record->currency ?: 'EUR')
                    ->placeholder('Ex: 250'),
            ])
            ->action(function (Lead $record, array $data) {
                $overrides = [];
                if (! empty($data['document_fee'])) {
                    $overrides['document_fee'] = (float) $data['document_fee'];
                }
                $contract = LeadDocumentService::generateLoanContract($record, $overrides);
                Notification::make()
                    ->title('Contrat de prêt généré avec succès')
                    ->body("Référence : {$contract->reference}".($contract->document_fee ? " (Coût : {$contract->formattedDocumentFee()})" : ''))
                    ->success()
                    ->send();
            });
    }

    /** Direct action to generate an Insurance Document from this lead. */
    public static function generateInsuranceAction(): Action
    {
        return Action::make('generateInsurance')
            ->label('Générer Assurance de prêt')
            ->icon(Heroicon::OutlinedShieldCheck)
            ->color('info')
            ->modalHeading('Générer Assurance de prêt')
            ->modalDescription('Indiquez le coût facturé pour la réalisation du certificat d\'assurance.')
            ->schema([
                TextInput::make('document_fee')
                    ->label('Coût de réalisation de l\'assurance')
                    ->helperText('Ce montant apparaîtra sur le certificat : "La réalisation de ce document coûte : [MONTANT]"')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix(fn (Lead $record) => $record->currency ?: 'EUR')
                    ->placeholder('Ex: 150'),
            ])
            ->action(function (Lead $record, array $data) {
                $overrides = [];
                if (! empty($data['document_fee'])) {
                    $overrides['document_fee'] = (float) $data['document_fee'];
                }
                $doc = LeadDocumentService::generateInsuranceDocument($record, $overrides);
                Notification::make()
                    ->title('Certificat d\'assurance généré avec succès')
                    ->body("Référence : {$doc->reference}".($doc->document_fee ? " (Coût : {$doc->formattedDocumentFee()})" : ''))
                    ->success()
                    ->send();
            });
    }

    /** Direct action to generate a Loan Approval Document from this lead. */
    public static function generateApprovalAction(): Action
    {
        return Action::make('generateApproval')
            ->label('Générer Accord de financement (Gratuit)')
            ->icon(Heroicon::OutlinedCheckBadge)
            ->color('warning')
            ->modalHeading('Générer Accord de financement')
            ->modalDescription('L\'accord et validation de financement est une attestation officielle délivrée à titre 100% GRATUIT.')
            ->action(function (Lead $record) {
                $doc = LeadDocumentService::generateLoanApprovalDocument($record);
                Notification::make()
                    ->title('Attestation d\'accord de financement générée (Gratuite)')
                    ->body("Référence : {$doc->reference}")
                    ->success()
                    ->send();
            });
    }

    /** Quick action to open online banking portal access for this lead. */
    public static function openPortalAccountAction(): Action
    {
        return Action::make('openPortalAccount')
            ->label('Créer Espace Banque en Ligne')
            ->icon(Heroicon::OutlinedUserPlus)
            ->color('gray')
            ->visible(fn (Lead $record) => ! BankClient::where('email', $record->email)->exists())
            ->schema([
                TextInput::make('password')
                    ->label('Mot de passe d\'accès espace client')
                    ->default(fn () => Str::random(10))
                    ->required(),
            ])
            ->action(function (Lead $record, array $data) {
                $countryName = $record->countryName();
                $banks = BankDirectory::banksForCountry($countryName);

                BankClient::create([
                    'first_name' => $record->first_name,
                    'last_name' => $record->last_name,
                    'email' => $record->email,
                    'admin_email' => $record->admin_email ?: 'info@venatomar.com',
                    'phone' => $record->phone,
                    'password' => $data['password'],
                    'country' => $countryName,
                    'country_code' => strtoupper($record->country),
                    'national_bank_name' => $banks[0] ?? 'Banque Nationale Partenaire',
                    'currency' => $record->currency ?: 'EUR',
                    'locale' => $record->locale ?: 'es',
                    'birth_date' => $record->birthday,
                    'document_number' => $record->document_number,
                    'status' => 'active',
                    'balance' => $record->amount,
                ]);

                Notification::make()
                    ->title('Espace bancaire créé avec succès')
                    ->body("Le client peut se connecter sur /my-account avec l'email {$record->email} et le mot de passe défini.")
                    ->success()
                    ->send();
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeadInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeads::route('/'),
            'create' => CreateLead::route('/create'),
            'view' => ViewLead::route('/{record}'),
            'edit' => EditLead::route('/{record}/edit'),
        ];
    }
}
