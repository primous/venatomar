<?php

namespace App\Filament\Resources\InsuranceDocuments;

use App\Filament\Resources\InsuranceDocuments\Pages\CreateInsuranceDocument;
use App\Filament\Resources\InsuranceDocuments\Pages\ListInsuranceDocuments;
use App\Mail\InsuranceCertificateMail;
use App\Mail\LoanAcceptedMail;
use App\Models\InsuranceDocument;
use App\Models\Lead;
use App\Services\LeadDocumentService;
use App\Support\AdminText as A;
use App\Support\InsurancePdf;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

/** Personalised credit-insurance certificate and information sheets (PDF), in the selected language. */
class InsuranceDocumentResource extends Resource
{
    protected static ?string $model = InsuranceDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function getModelLabel(): string
    {
        return A::t('insurance.model');
    }

    public static function getPluralModelLabel(): string
    {
        return A::t('insurance.models');
    }

    public static function getNavigationLabel(): string
    {
        return A::t('nav.insurance');
    }

    /** Suggestions only: the country is free text, any country can be typed. */
    public static function countrySuggestions(): array
    {
        return collect(config('countries'))->pluck('name')
            ->merge(['France', 'Belgique', 'Suisse', 'Luxembourg', 'Canada', 'Maroc', 'Sénégal', 'Côte d’Ivoire', 'Cameroun', 'Congo', 'Bénin', 'Togo', 'Maurice', 'La Réunion', 'Mayotte', 'Comores', 'Tunisie', 'Algérie', 'Österreich', 'Polska', 'România', 'Česko', 'Hrvatska', 'United States'])
            ->unique()->sort()->values()->all();
    }

    public static function guaranteeOptions(): array
    {
        return trans('admin.insurance.guarantee_names');
    }

    public static function form(Schema $schema): Schema
    {
        $i = fn (string $key) => A::t('insurance.'.$key);

        return $schema->components([
            Section::make($i('section_doc'))->columns(3)->columnSpanFull()->schema([
                Hidden::make('admin_email')
                    ->default(fn () => auth()->user()?->email === 'contact@venatomar.com' ? 'contact@venatomar.com' : 'info@venatomar.com'),
                Select::make('document_type')->label($i('doc_type'))->required()->default('certificate')
                    ->options([
                        'certificate' => $i('doc_types.certificate'),
                        'information' => $i('doc_types.information'),
                    ]),
                Select::make('locale')->label($i('doc_language'))->required()->default('es')
                    ->options(InsurancePdf::LOCALES)->in(array_keys(InsurancePdf::LOCALES)),
                Select::make('lead_id')->label($i('lead'))->helperText($i('lead_help'))
                    ->searchable()->preload()->live()
                    ->options(fn () => Lead::latest()->limit(300)->get()
                        ->mapWithKeys(fn (Lead $l) => [$l->id => $l->reference.' · '.$l->first_name.' '.$l->last_name]))
                    ->afterStateUpdated(function ($state, Set $set) {
                        if (! $state || ! ($lead = Lead::find($state))) {
                            return;
                        }
                        foreach (LeadDocumentService::prefillDataForInsurance($lead) as $key => $val) {
                            $set($key, $val);
                        }
                    }),
            ]),
            Section::make($i('section_client'))->columns(2)->columnSpanFull()->schema([
                TextInput::make('first_name')->label($i('first_name'))->required()->maxLength(80),
                TextInput::make('last_name')->label($i('last_name'))->required()->maxLength(120),
                TextInput::make('phone')->label($i('phone'))->tel()->maxLength(40),
                TextInput::make('email')->label($i('email'))->email()->maxLength(120),
                TextInput::make('document_number')->label($i('document_number'))->maxLength(40),
                DatePicker::make('birth_date')->label($i('birth_date')),
                TextInput::make('country')->label($i('country'))->required()->maxLength(80)
                    ->datalist(self::countrySuggestions())->helperText($i('country_help')),
                TextInput::make('address')->label($i('address'))->maxLength(255),
            ]),
            Section::make($i('section_loan'))->columns(4)->columnSpanFull()->schema([
                TextInput::make('amount')->label($i('amount'))->required()->integer()->minValue(1)->maxValue(100000000),
                TextInput::make('currency')->label($i('currency'))->required()->default('EUR')->length(3)
                    ->regex('/^[A-Za-z]{3}$/')->datalist(InsurancePdf::CURRENCIES)
                    ->dehydrateStateUsing(fn ($state) => strtoupper((string) $state)),
                TextInput::make('term_months')->label($i('term'))->required()->integer()->minValue(1)->maxValue(480),
                DatePicker::make('effective_date')->label($i('effective_date'))->default(now()),
                TextInput::make('document_fee')
                    ->label('Coût de réalisation de l\'assurance')
                    ->helperText('Montant des frais facturés et affichés sur le certificat (ex: 150).')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix(fn ($get) => $get('currency') ?: 'EUR'),
                TextInput::make('coverage_percent')->label($i('coverage'))->required()->integer()->minValue(1)->maxValue(100)->default(100),
                TextInput::make('lender')->label($i('lender'))->maxLength(120)->columnSpan(2),
                CheckboxList::make('guarantees')->label($i('guarantees'))->required()->columns(3)->columnSpanFull()
                    ->options(self::guaranteeOptions())->default(['death', 'ptia'])
                    ->in(InsuranceDocument::GUARANTEES),
            ]),
            Section::make($i('section_issuer'))->columns(3)->columnSpanFull()->schema([
                TextInput::make('issuer_address')->label($i('issuer_address'))->maxLength(255)->default('Paseo de la Castellana 216, Madrid'),
                TextInput::make('issuer_phone')->label($i('issuer_phone'))->maxLength(60)->default('+34 910 000 000'),
                TextInput::make('issuer_city')->label($i('issuer_city'))->maxLength(80)->default('Madrid'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $i = fn (string $key) => A::t('insurance.'.$key);

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label($i('reference'))->searchable()->copyable()->weight('bold'),
                TextColumn::make('document_type')->label($i('doc_type'))
                    ->badge()
                    ->color(fn (string $state) => $state === 'certificate' ? 'success' : 'info')
                    ->formatStateUsing(fn (string $state) => $state === 'certificate' ? 'Certificat' : 'Fiche info'),
                TextColumn::make('last_name')->label($i('client'))->searchable(['first_name', 'last_name'])
                    ->formatStateUsing(fn (InsuranceDocument $record) => $record->first_name.' '.$record->last_name)
                    ->description(fn (InsuranceDocument $record) => $record->phone),
                TextColumn::make('country')->label($i('country'))->searchable(),
                TextColumn::make('locale')->label($i('doc_language'))->badge()->formatStateUsing(fn ($state) => strtoupper($state)),
                TextColumn::make('amount')->label($i('amount'))->sortable()
                    ->formatStateUsing(fn (InsuranceDocument $record) => $record->money())
                    ->description(fn (InsuranceDocument $record) => A::t('insurance.months', ['count' => $record->term_months])),
                TextColumn::make('document_fee')->label('Coût document')
                    ->formatStateUsing(fn (InsuranceDocument $record) => $record->formattedDocumentFee())
                    ->placeholder('—'),
                TextColumn::make('created_at')->label($i('date'))->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('user.name')->label($i('by'))->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('locale')->label($i('doc_language'))->options(InsurancePdf::LOCALES),
                SelectFilter::make('document_type')->label($i('doc_type'))->options([
                    'certificate' => 'Certificat',
                    'information' => 'Fiche info',
                ]),
            ])
            ->recordActions([
                self::sendEmailAction(),
                self::downloadAction(),
                DeleteAction::make(),
            ]);
    }

    public static function sendEmailAction(): Action
    {
        return Action::make('sendEmail')
            ->label('Envoyer par courriel')
            ->icon(Heroicon::OutlinedEnvelope)
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Envoyer le document d\'assurance par courriel')
            ->modalDescription(fn (InsuranceDocument $record) => "Envoyer l'e-mail officiel avec le document PDF joint à {$record->email} ?")
            ->action(function (InsuranceDocument $record) {
                if (! empty($record->email)) {
                    if ($record->document_type === 'information' && $record->lead) {
                        Mail::to($record->email)->queue(new LoanAcceptedMail($record->lead, $record));
                    } else {
                        Mail::to($record->email)->queue(new InsuranceCertificateMail($record));
                    }
                    Notification::make()
                        ->title('Document d\'assurance envoyé par courriel !')
                        ->body("L'e-mail officiel avec PDF joint a été envoyé à {$record->email}.")
                        ->success()
                        ->send();
                }
            });
    }

    public static function downloadAction(): Action
    {
        return Action::make('download')
            ->label(A::t('insurance.download'))
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('success')
            ->action(fn (InsuranceDocument $record) => response()->streamDownload(
                fn () => print (InsurancePdf::render($record)),
                $record->filename(),
                ['Content-Type' => 'application/pdf'],
            ));
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $admin = auth()->user()?->email;

        if ($admin === 'contact@venatomar.com') {
            return $query->where('admin_email', 'contact@venatomar.com');
        }

        if ($admin === 'info@venatomar.com') {
            return $query->where(function ($q) {
                $q->where('admin_email', 'info@venatomar.com')
                    ->orWhereNull('admin_email');
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInsuranceDocuments::route('/'),
            'create' => CreateInsuranceDocument::route('/create'),
        ];
    }
}
