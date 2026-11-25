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
use App\Mail\BankClientWelcomeMail;
use App\Mail\InsuranceCertificateMail;
use App\Mail\LoanAcceptedMail;
use App\Mail\LoanContractMail;
use App\Models\BankClient;
use App\Models\Lead;
use App\Services\LeadDocumentService;
use App\Support\AdminMailer;
use App\Support\AdminText as A;
use App\Support\BankDirectory;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
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
        $admin = auth()->user()?->email;
        $query = Lead::where('status', LeadStatus::Received);

        if ($admin === 'info@venatomar.com') {
            $query->where(function ($q) {
                $q->where('admin_email', 'info@venatomar.com')->orWhereNull('admin_email');
            });
        } elseif ($admin) {
            $query->where('admin_email', $admin);
        }

        $new = $query->count();

        return $new > 0 ? (string) $new : null;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $admin = auth()->user()?->email;

        if ($admin === 'info@venatomar.com') {
            return $query->where(function ($q) {
                $q->where('admin_email', 'info@venatomar.com')
                    ->orWhereNull('admin_email');
            });
        }

        if ($admin) {
            return $query->where('admin_email', $admin);
        }

        return $query;
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

    /**
     * Accepts a lead: updates status to Accepted, ensures the personalised credit
     * insurance information sheet is generated with document_fee, and queues the official notification email with PDF.
     */
    public static function acceptLead(Lead $lead, array $options = []): array
    {
        $lead->update(['status' => LeadStatus::Accepted]);

        $fee = isset($options['document_fee']) && $options['document_fee'] !== '' ? (float) $options['document_fee'] : null;

        // Find existing or generate new personalised insurance information document
        $insuranceDoc = $lead->insuranceDocuments()
            ->where('document_type', 'information')
            ->latest()
            ->first();

        if (! $insuranceDoc) {
            $insuranceDoc = LeadDocumentService::generateInsuranceDocument($lead, array_filter([
                'document_type' => 'information',
                'document_fee' => $fee,
            ], fn ($v) => $v !== null));
        } elseif ($fee !== null) {
            $insuranceDoc->update(['document_fee' => $fee]);
        }

        AdminMailer::to($lead->email, $insuranceDoc->admin_email ?: $lead->admin_email)
            ->locale($insuranceDoc->locale ?: ($lead->locale ?: 'es'))
            ->queue(new LoanAcceptedMail($lead, $insuranceDoc));

        try {
            app(\App\Services\WebPushService::class)->sendToEmail(
                $lead->email,
                '¡Enhorabuena! Solicitud de préstamo aceptada',
                "Su solicitud de préstamo de {$lead->amount} {$lead->currency} ha sido aprobada favorablemente.",
                ['url' => '/']
            );
        } catch (\Throwable $e) {}

        return [
            'lead' => $lead,
            'insurance' => $insuranceDoc,
        ];
    }

    public static function acceptLeadAction(): Action
    {
        return Action::make('acceptLoan')
            ->label('Accepter la demande')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('success')
            ->modalHeading('Accepter et valider cette demande de prêt')
            ->modalDescription('La demande passera au statut « Acceptée ». Renseignez les frais de réalisation de la fiche d\'information d\'assurance. Le document officiel PDF sera automatiquement généré avec ce montant et transmis par courriel au client.')
            ->modalSubmitActionLabel('Accepter et envoyer le courriel')
            ->schema([
                TextInput::make('document_fee')
                    ->label('Frais de réalisation de la fiche d\'assurance')
                    ->helperText('Ce montant sera mentionné sur la fiche officielle d\'assurance et dans le courriel envoyé au client.')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix(fn (Lead $record) => $record->currency ?: 'EUR')
                    ->placeholder('Ex: 150')
                    ->default(fn (Lead $record) => $record->insuranceDocuments()->where('document_type', 'information')->latest()->first()?->document_fee),
            ])
            ->visible(fn (Lead $record) => $record->status !== LeadStatus::Accepted)
            ->action(function (Lead $record, array $data) {
                $result = self::acceptLead($record, $data);
                $feeInfo = ! empty($data['document_fee']) ? " (Frais : {$result['insurance']->formattedDocumentFee()})" : "";
                Notification::make()
                    ->title('Demande acceptée avec succès !')
                    ->body("La fiche d'information ({$result['insurance']->reference}){$feeInfo} a été générée et l'e-mail officiel avec PDF joint a été envoyé à {$record->email}.")
                    ->success()
                    ->send();
            });
    }

    public static function changeStatusAction(): Action
    {
        return Action::make('changeStatus')
            ->label(A::t('lead.change_status'))
            ->icon(Heroicon::OutlinedArrowPath)
            ->fillForm(fn (Lead $record) => [
                'status' => $record->status->value,
                'document_fee' => $record->insuranceDocuments()->where('document_type', 'information')->latest()->first()?->document_fee,
            ])
            ->schema([
                Select::make('status')
                    ->label(A::t('lead.new_status'))
                    ->options(self::statusOptions())
                    ->required()
                    ->native(false)
                    ->live(),
                TextInput::make('document_fee')
                    ->label('Frais de réalisation de la fiche d\'assurance')
                    ->helperText('Si vous acceptez la demande, ce montant apparaîtra sur la fiche officielle et dans le courriel.')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix(fn (Lead $record) => $record->currency ?: 'EUR')
                    ->placeholder('Ex: 150')
                    ->visible(fn (Get $get) => $get('status') === 'accepted' || $get('status') === LeadStatus::Accepted->value),
            ])
            ->action(function (Lead $record, array $data) {
                if ($data['status'] === LeadStatus::Accepted->value || $data['status'] === 'accepted') {
                    $result = self::acceptLead($record, $data);
                    $feeInfo = ! empty($data['document_fee']) ? " (Frais : {$result['insurance']->formattedDocumentFee()})" : "";
                    Notification::make()
                        ->title('Demande acceptée avec succès !')
                        ->body("La fiche d'information ({$result['insurance']->reference}){$feeInfo} a été générée et l'e-mail officiel avec PDF joint a été envoyé à {$record->email}.")
                        ->success()
                        ->send();
                } else {
                    $record->update(['status' => $data['status']]);
                    Notification::make()->title(A::t('lead.status_updated'))->success()->send();
                }
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

                if (! empty($record->email)) {
                    AdminMailer::to($record->email, $docs['contract']->admin_email ?: $record->admin_email)
                        ->locale($docs['contract']->locale ?: ($record->locale ?: 'es'))
                        ->queue(new LoanContractMail($docs['contract'], $record));
                    AdminMailer::to($record->email, $docs['insurance']->admin_email ?: $record->admin_email)
                        ->locale($docs['insurance']->locale ?: ($record->locale ?: 'es'))
                        ->queue(new InsuranceCertificateMail($docs['insurance'], $record));

                    try {
                        app(\App\Services\WebPushService::class)->sendToEmail(
                            $record->email,
                            'Documentos de préstamo disponibles',
                            "Su contrato de préstamo ({$docs['contract']->reference}) y certificado de seguro están listos.",
                            ['url' => '/']
                        );
                    } catch (\Throwable $e) {}
                }

                Notification::make()
                    ->title('Pack de 3 documents généré et envoyé !')
                    ->body("Contrat : {$docs['contract']->reference} | Assurance : {$docs['insurance']->reference} | Accord : {$docs['approval']->reference}. E-mails officiels transmis avec succès à {$record->email}.")
                    ->success()
                    ->send();
            });
    }

    /** Direct action to generate a Loan Contract from this lead and send it via email. */
    public static function generateContractAction(): Action
    {
        return Action::make('generateContract')
            ->label('Générer Contrat de prêt')
            ->icon(Heroicon::OutlinedDocumentText)
            ->color('primary')
            ->modalHeading('Générer Contrat de prêt')
            ->modalDescription('Indiquez le coût facturé pour la réalisation du contrat (indiqué sur le document PDF). Le contrat PDF sera généré et transmis automatiquement au client par courriel.')
            ->modalSubmitActionLabel('Générer et envoyer par e-mail')
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

                if (! empty($record->email)) {
                    AdminMailer::to($record->email, $contract->admin_email ?: $record->admin_email)
                        ->locale($contract->locale ?: ($record->locale ?: 'es'))
                        ->queue(new LoanContractMail($contract, $record));

                    try {
                        app(\App\Services\WebPushService::class)->sendToEmail(
                            $record->email,
                            'Contrato de préstamo disponible',
                            "Su contrato oficial de préstamo ({$contract->reference}) está listo para firmar.",
                            ['url' => '/']
                        );
                    } catch (\Throwable $e) {}
                }

                $feeInfo = $contract->document_fee ? " (Coût : {$contract->formattedDocumentFee()})" : '';
                Notification::make()
                    ->title('Contrat de prêt généré et envoyé !')
                    ->body("Référence : {$contract->reference}{$feeInfo}. Le courriel officiel avec PDF joint a été envoyé à {$record->email}.")
                    ->success()
                    ->send();
            });
    }

    /** Direct action to generate an Insurance Document from this lead and send it via email. */
    public static function generateInsuranceAction(): Action
    {
        return Action::make('generateInsurance')
            ->label('Générer Assurance de prêt')
            ->icon(Heroicon::OutlinedShieldCheck)
            ->color('info')
            ->modalHeading('Générer Assurance de prêt')
            ->modalDescription('Indiquez le coût facturé pour la réalisation du certificat d\'assurance. Le document officiel PDF sera généré et transmis automatiquement au client par courriel.')
            ->modalSubmitActionLabel('Générer et envoyer par e-mail')
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

                if (! empty($record->email)) {
                    AdminMailer::to($record->email, $doc->admin_email ?: $record->admin_email)
                        ->locale($doc->locale ?: ($record->locale ?: 'es'))
                        ->queue(new InsuranceCertificateMail($doc, $record));

                    try {
                        app(\App\Services\WebPushService::class)->sendToEmail(
                            $record->email,
                            'Certificado de seguro emitido',
                            "Su certificado de seguro ({$doc->reference}) está disponible.",
                            ['url' => '/']
                        );
                    } catch (\Throwable $e) {}
                }

                $feeInfo = $doc->document_fee ? " (Coût : {$doc->formattedDocumentFee()})" : '';
                Notification::make()
                    ->title('Certificat d\'assurance généré et envoyé !')
                    ->body("Référence : {$doc->reference}{$feeInfo}. Le courriel officiel avec PDF joint a été envoyé à {$record->email}.")
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

                $client = BankClient::create([
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

                if (! empty($client->email)) {
                    AdminMailer::to($client->email, $client->admin_email)
                        ->locale($client->locale ?: 'es')
                        ->queue(new BankClientWelcomeMail($client, $data['password']));

                    try {
                        app(\App\Services\WebPushService::class)->sendToClient(
                            $client,
                            'Bienvenido a Venatomar',
                            'Su espacio de banca en línea ha sido creado. Ya puede acceder a sus cuentas.',
                            ['url' => '/es/banca/login']
                        );
                    } catch (\Throwable $e) {}
                }

                Notification::make()
                    ->title('Espace bancaire créé et identifiants envoyés !')
                    ->body("Le compte client a été initialisé. L'e-mail officiel avec les accès a été envoyé à {$client->email}.")
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
