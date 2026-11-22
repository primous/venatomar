<?php

namespace App\Filament\Resources\LoanApprovalDocuments;

use App\Filament\Resources\InsuranceDocuments\InsuranceDocumentResource;
use App\Filament\Resources\LoanApprovalDocuments\Pages\CreateLoanApprovalDocument;
use App\Filament\Resources\LoanApprovalDocuments\Pages\ListLoanApprovalDocuments;
use App\Models\Lead;
use App\Models\LoanApprovalDocument;
use App\Services\LeadDocumentService;
use App\Support\AdminText as A;
use App\Support\InsurancePdf;
use App\Support\LoanApprovalPdf;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
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

/** Back-office resource for generating and downloading official loan approval certificates. */
class LoanApprovalDocumentResource extends Resource
{
    protected static ?string $model = LoanApprovalDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function getModelLabel(): string
    {
        return A::t('loan_approval.model');
    }

    public static function getPluralModelLabel(): string
    {
        return A::t('loan_approval.models');
    }

    public static function getNavigationLabel(): string
    {
        return A::t('nav.loan_approvals');
    }

    public static function form(Schema $schema): Schema
    {
        $i = fn (string $key) => A::t('loan_approval.'.$key);

        return $schema->components([
            Section::make($i('section_doc'))->columns(2)->columnSpanFull()->schema([
                Hidden::make('admin_email')
                    ->default(fn () => auth()->user()?->email ?: 'info@venatomar.com'),
                Select::make('locale')->label($i('doc_language'))->required()->default('es')
                    ->options(InsurancePdf::LOCALES)->in(array_keys(InsurancePdf::LOCALES)),
                Select::make('lead_id')->label($i('lead'))->helperText($i('lead_help'))
                    ->searchable()->preload()->live()
                    ->options(function () {
                        $admin = auth()->user()?->email;

                        return Lead::query()
                            ->when(config('venatomar.features.admin_isolation', true), function ($q) use ($admin) {
                                if ($admin === 'info@venatomar.com') {
                                    $q->where(function ($sub) {
                                        $sub->where('admin_email', 'info@venatomar.com')->orWhereNull('admin_email');
                                    });
                                } elseif ($admin) {
                                    $q->where('admin_email', $admin);
                                }
                            })
                            ->latest()->limit(300)->get()
                            ->mapWithKeys(fn (Lead $l) => [$l->id => $l->reference.' · '.$l->first_name.' '.$l->last_name]);
                    })
                    ->afterStateUpdated(function ($state, Set $set) {
                        if (! $state || ! ($lead = Lead::find($state))) {
                            return;
                        }
                        foreach (LeadDocumentService::prefillDataForApproval($lead) as $key => $val) {
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
                TextInput::make('total_repayable')->label($i('total_repayable'))->integer(),
                DatePicker::make('approval_date')->label($i('approval_date'))->default(now()),
                DatePicker::make('expiry_date')->label($i('expiry_date'))->default(fn () => now()->addDays(30)),
                TextInput::make('lender')->label($i('lender'))->maxLength(120)->columnSpan(2),
                Textarea::make('notes')->label($i('notes'))->rows(2)->columnSpanFull(),
            ]),
            Section::make($i('section_issuer'))->columns(3)->columnSpanFull()->schema([
                TextInput::make('issuer_address')->label($i('issuer_address'))->maxLength(255)->default('Carrer d’Estruc, 9, 08002 Barcelone'),
                TextInput::make('issuer_phone')->label($i('issuer_phone'))->maxLength(60)->default('+34 910 000 000'),
                TextInput::make('issuer_city')->label($i('issuer_city'))->maxLength(80)->default('Barcelone'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $i = fn (string $key) => A::t('loan_approval.'.$key);

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label($i('reference'))->searchable()->copyable()->weight('bold'),
                TextColumn::make('last_name')->label($i('client'))->searchable(['first_name', 'last_name'])
                    ->formatStateUsing(fn (LoanApprovalDocument $record) => $record->first_name.' '.$record->last_name)
                    ->description(fn (LoanApprovalDocument $record) => $record->phone),
                TextColumn::make('country')->label($i('country'))->searchable(),
                TextColumn::make('locale')->label($i('doc_language'))->badge()->formatStateUsing(fn ($state) => strtoupper($state)),
                TextColumn::make('amount')->label($i('amount'))->sortable()
                    ->formatStateUsing(fn (LoanApprovalDocument $record) => $record->money())
                    ->description(fn (LoanApprovalDocument $record) => A::t('loan_approval.months', ['count' => $record->term_months])),
                TextColumn::make('monthly_payment')->label($i('monthly_payment'))
                    ->formatStateUsing(fn (LoanApprovalDocument $record) => $record->monthlyPaymentFormatted()),
                TextColumn::make('created_at')->label($i('date'))->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('user.name')->label($i('by'))->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('locale')->label($i('doc_language'))->options(InsurancePdf::LOCALES),
            ])
            ->recordActions([
                self::downloadAction(),
                DeleteAction::make(),
            ]);
    }

    public static function downloadAction(): Action
    {
        return Action::make('download')
            ->label(A::t('loan_approval.download'))
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('success')
            ->action(fn (LoanApprovalDocument $record) => response()->streamDownload(
                fn () => print (LoanApprovalPdf::render($record)),
                $record->filename(),
                ['Content-Type' => 'application/pdf'],
            ));
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

    public static function getPages(): array
    {
        return [
            'index' => ListLoanApprovalDocuments::route('/'),
            'create' => CreateLoanApprovalDocument::route('/create'),
        ];
    }
}
