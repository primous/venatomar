<?php

namespace App\Filament\Resources\LoanContracts;

use App\Filament\Resources\InsuranceDocuments\InsuranceDocumentResource;
use App\Filament\Resources\LoanContracts\Pages\CreateLoanContract;
use App\Filament\Resources\LoanContracts\Pages\ListLoanContracts;
use App\Models\Lead;
use App\Models\LoanContract;
use App\Services\LeadDocumentService;
use App\Support\AdminText as A;
use App\Support\InsurancePdf;
use App\Support\LoanContractPdf;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/** Back-office resource for generating and downloading official personal loan agreements. */
class LoanContractResource extends Resource
{
    protected static ?string $model = LoanContract::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function getModelLabel(): string
    {
        return A::t('loan_contract.model');
    }

    public static function getPluralModelLabel(): string
    {
        return A::t('loan_contract.models');
    }

    public static function getNavigationLabel(): string
    {
        return A::t('nav.loan_contracts');
    }

    public static function form(Schema $schema): Schema
    {
        $i = fn (string $key) => A::t('loan_contract.'.$key);

        return $schema->components([
            Section::make($i('section_contract'))->columns(3)->columnSpanFull()->schema([
                Select::make('admin_email')->label('Canal / Admin')
                    ->options([
                        'info@venatomar.com' => 'Principal (info@venatomar.com)',
                        'contact@venatomar.com' => 'Contact (contact@venatomar.com)',
                    ])
                    ->default(fn () => auth()->user()?->email === 'contact@venatomar.com' ? 'contact@venatomar.com' : 'info@venatomar.com')
                    ->required(),
                Select::make('locale')->label($i('contract_language'))->required()->default('es')
                    ->options(InsurancePdf::LOCALES)->in(array_keys(InsurancePdf::LOCALES)),
                Select::make('lead_id')->label($i('lead'))->helperText($i('lead_help'))
                    ->searchable()->preload()->live()
                    ->options(fn () => Lead::latest()->limit(300)->get()
                        ->mapWithKeys(fn (Lead $l) => [$l->id => $l->reference.' · '.$l->first_name.' '.$l->last_name]))
                    ->afterStateUpdated(function ($state, Set $set) {
                        if (! $state || ! ($lead = Lead::find($state))) {
                            return;
                        }
                        foreach (LeadDocumentService::prefillDataForContract($lead) as $key => $val) {
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
                    ->datalist(InsuranceDocumentResource::countrySuggestions())->helperText($i('country_help')),
                TextInput::make('address')->label($i('address'))->maxLength(255),
            ]),
            Section::make($i('section_terms'))->columns(4)->columnSpanFull()->schema([
                TextInput::make('amount')->label($i('amount'))->required()->integer()->minValue(1)->maxValue(100000000),
                TextInput::make('currency')->label($i('currency'))->required()->default('EUR')->length(3)
                    ->regex('/^[A-Za-z]{3}$/')->datalist(InsurancePdf::CURRENCIES)
                    ->dehydrateStateUsing(fn ($state) => strtoupper((string) $state)),
                TextInput::make('term_months')->label($i('term'))->required()->integer()->minValue(1)->maxValue(480),
                TextInput::make('interest_rate')->label($i('interest_rate'))->numeric()->default(3.50)->step(0.01),
                TextInput::make('monthly_payment')->label($i('monthly_payment'))->helperText($i('monthly_payment_help'))->integer(),
                TextInput::make('total_interest')->label($i('total_interest'))->integer(),
                TextInput::make('total_repayable')->label($i('total_repayable'))->integer(),
                DatePicker::make('contract_date')->label($i('contract_date'))->default(now()),
                DatePicker::make('first_payment_date')->label($i('first_payment_date'))->default(fn () => now()->addMonth()),
                TextInput::make('bank_account')->label($i('bank_account'))->maxLength(80)->columnSpan(2),
                TextInput::make('document_fee')
                    ->label('Coût de réalisation du contrat')
                    ->helperText('Montant des frais de réalisation facturés et affichés sur le contrat (ex: 250).')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix(fn ($get) => $get('currency') ?: 'EUR')
                    ->columnSpan(2),
                Textarea::make('notes')->label($i('notes'))->rows(2)->columnSpanFull(),
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
        $i = fn (string $key) => A::t('loan_contract.'.$key);

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label($i('reference'))->searchable()->copyable()->weight('bold'),
                TextColumn::make('last_name')->label($i('client'))->searchable(['first_name', 'last_name'])
                    ->formatStateUsing(fn (LoanContract $record) => $record->first_name.' '.$record->last_name)
                    ->description(fn (LoanContract $record) => $record->phone),
                TextColumn::make('country')->label($i('country'))->searchable(),
                TextColumn::make('locale')->label($i('contract_language'))->badge()->formatStateUsing(fn ($state) => strtoupper($state)),
                TextColumn::make('amount')->label($i('amount'))->sortable()
                    ->formatStateUsing(fn (LoanContract $record) => $record->money())
                    ->description(fn (LoanContract $record) => A::t('loan_contract.months', ['count' => $record->term_months])),
                TextColumn::make('monthly_payment')->label($i('monthly_payment'))
                    ->formatStateUsing(fn (LoanContract $record) => $record->monthlyPaymentFormatted()),
                TextColumn::make('document_fee')->label('Coût document')
                    ->formatStateUsing(fn (LoanContract $record) => $record->formattedDocumentFee())
                    ->placeholder('—'),
                TextColumn::make('admin_email')->label('Canal Admin')->badge()
                    ->formatStateUsing(fn (?string $state) => $state === 'contact@venatomar.com' ? 'contact@venatomar.com' : 'info@venatomar.com')
                    ->color(fn (?string $state) => $state === 'contact@venatomar.com' ? 'info' : 'success'),
                TextColumn::make('contract_date')->label($i('date'))->date('d/m/Y')->sortable(),
                TextColumn::make('user.name')->label($i('by'))->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('admin_email')->label('Canal Admin')
                    ->options([
                        'contact@venatomar.com' => 'Contact (contact@venatomar.com)',
                        'info@venatomar.com' => 'Principal (info@venatomar.com)',
                    ])
                    ->default(fn () => auth()->user()?->email === 'contact@venatomar.com' ? 'contact@venatomar.com' : null),
                SelectFilter::make('locale')->label($i('contract_language'))->options(InsurancePdf::LOCALES),
            ])
            ->recordActions([
                self::downloadAction(),
                DeleteAction::make(),
            ]);
    }

    public static function downloadAction(): Action
    {
        return Action::make('download')
            ->label(A::t('loan_contract.download'))
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('success')
            ->action(fn (LoanContract $record) => response()->streamDownload(
                fn () => print (LoanContractPdf::render($record)),
                $record->filename(),
                ['Content-Type' => 'application/pdf'],
            ));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoanContracts::route('/'),
            'create' => CreateLoanContract::route('/create'),
        ];
    }
}
