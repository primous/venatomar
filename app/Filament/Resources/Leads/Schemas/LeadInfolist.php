<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadStatus;
use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use App\Support\AdminText as A;
use App\Support\Venatomar;
use Carbon\Carbon;
use Closure;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadInfolist
{
    private static function opt(string $group): Closure
    {
        return fn (?string $state) => A::opt($group, $state);
    }

    private static function money(): Closure
    {
        return fn ($state, Lead $record) => $state === null ? null : Venatomar::money($state, $record->countryInfo());
    }

    private static function l(string $key): string
    {
        return A::t('lead.'.$key);
    }

    public static function configure(Schema $schema): Schema
    {
        $s = fn (string $key) => A::t('lead.sections.'.$key);

        return $schema->columns(3)->components([
            Section::make($s('request'))->columns(2)->columnSpan(2)->schema([
                TextEntry::make('reference')->label(self::l('reference'))->copyable()->weight('bold'),
                TextEntry::make('status')->label(self::l('status'))->badge()
                    ->formatStateUsing(fn (LeadStatus $state) => LeadResource::statusLabel($state))
                    ->color(fn (LeadStatus $state) => LeadResource::statusColor($state)),
                TextEntry::make('amount')->label(self::l('amount'))->formatStateUsing(self::money()),
                TextEntry::make('term_months')->label(self::l('term'))->suffix(' '.A::t('months')),
                TextEntry::make('loan_purpose')->label(self::l('purpose'))->formatStateUsing(self::opt('loan_purpose')),
                TextEntry::make('country')->label(self::l('country_locale'))
                    ->formatStateUsing(fn (Lead $record) => strtoupper($record->country).' · '.strtoupper($record->locale)),
            ]),

            Section::make($s('tracking'))->columnSpan(1)->schema([
                TextEntry::make('created_at')->label(self::l('received'))->dateTime('d/m/Y H:i'),
                TextEntry::make('internal_mail_sent_at')->label(self::l('internal_mail'))->dateTime('d/m/Y H:i')->placeholder(self::l('mail_queued')),
                TextEntry::make('confirmation_mail_sent_at')->label(self::l('confirmation_mail'))->dateTime('d/m/Y H:i')->placeholder(self::l('mail_queued')),
                TextEntry::make('mail_failed_at')->label(self::l('mail_failed_at'))->dateTime('d/m/Y H:i')->color('danger')->placeholder('—'),
            ]),

            Section::make($s('applicant'))->columns(2)->columnSpan(2)->schema([
                TextEntry::make('first_name')->label(self::l('name'))
                    ->formatStateUsing(fn (Lead $record) => $record->first_name.' '.$record->last_name),
                TextEntry::make('gender')->label(self::l('gender'))->formatStateUsing(self::opt('gender')),
                TextEntry::make('email')->label(self::l('email'))->copyable(),
                TextEntry::make('phone')->label(self::l('phone'))->copyable(),
                TextEntry::make('birthday')->label(self::l('birthday'))
                    ->formatStateUsing(fn (?string $state) => $state ? Carbon::parse($state)->format('d/m/Y') : null),
                TextEntry::make('document_number')->label(self::l('document'))->copyable()->placeholder('—'),
                TextEntry::make('marital_status')->label(self::l('marital'))->formatStateUsing(self::opt('marital')),
                TextEntry::make('education')->label(self::l('education'))->formatStateUsing(self::opt('education')),
            ]),

            Section::make($s('address'))->columnSpan(1)->schema([
                TextEntry::make('home_street')->label(self::l('street'))
                    ->formatStateUsing(fn (Lead $record) => trim($record->home_street.' '.$record->home_number.' '.($record->apartment_floor ?? ''))),
                TextEntry::make('geo_city')->label(self::l('city'))
                    ->formatStateUsing(fn (Lead $record) => $record->geo_zip.' '.$record->geo_city),
                TextEntry::make('geo_province')->label(self::l('province')),
                TextEntry::make('home_status')->label(self::l('home'))->formatStateUsing(self::opt('home')),
            ]),

            Section::make($s('finances'))->columns(2)->columnSpan(2)->schema([
                TextEntry::make('monthly_income')->label(self::l('income'))->formatStateUsing(self::money()),
                TextEntry::make('regular_expenses')->label(self::l('expenses'))->formatStateUsing(self::money()),
                TextEntry::make('employment_status')->label(self::l('employment'))->formatStateUsing(self::opt('employment')),
                TextEntry::make('company_sector')->label(self::l('sector'))->formatStateUsing(self::opt('company_sector')),
                IconEntry::make('defaulter_list')->label(self::l('defaulter'))->boolean()->trueColor('danger')->falseColor('success'),
                IconEntry::make('has_debt')->label(self::l('debts'))->boolean()->trueColor('warning')->falseColor('success'),
                IconEntry::make('has_car')->label(self::l('car'))->boolean(),
                TextEntry::make('car_number')->label(self::l('plate'))->placeholder('—'),
            ]),

            Section::make($s('payout'))->columnSpan(1)->schema([
                TextEntry::make('payout_method')->label(self::l('method'))->formatStateUsing(self::opt('payout_method')),
                TextEntry::make('bank_account')->label(self::l('iban'))->copyable()->placeholder('—')
                    ->visible(fn (Lead $record) => $record->payout_method !== 'mobile_money'),
                TextEntry::make('mm_operator')->label(self::l('operator'))->placeholder('—')
                    ->visible(fn (Lead $record) => $record->payout_method === 'mobile_money'),
                TextEntry::make('mm_number')->label(self::l('mm_number'))->copyable()->placeholder('—')
                    ->visible(fn (Lead $record) => $record->payout_method === 'mobile_money'),
            ]),

            Section::make('Documents Officiels Générés & Accès Client')
                ->columns(4)
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('contracts_count')
                        ->label('Contrats de prêt')
                        ->state(function (Lead $record) {
                            $contracts = $record->loanContracts;
                            if ($contracts->isEmpty()) {
                                return 'Aucun contrat généré';
                            }
                            return $contracts->map(fn ($c) => "{$c->reference} ({$c->created_at->format('d/m/Y')})")->implode(', ');
                        })
                        ->badge(fn (Lead $record) => $record->loanContracts->isNotEmpty())
                        ->color('success'),

                    TextEntry::make('insurance_count')
                        ->label('Assurances de crédit')
                        ->state(function (Lead $record) {
                            $docs = $record->insuranceDocuments;
                            if ($docs->isEmpty()) {
                                return 'Aucune assurance générée';
                            }
                            return $docs->map(fn ($d) => "{$d->reference} ({$d->created_at->format('d/m/Y')})")->implode(', ');
                        })
                        ->badge(fn (Lead $record) => $record->insuranceDocuments->isNotEmpty())
                        ->color('info'),

                    TextEntry::make('approval_count')
                        ->label('Accords de financement')
                        ->state(function (Lead $record) {
                            $approvals = $record->loanApprovalDocuments;
                            if ($approvals->isEmpty()) {
                                return 'Aucun accord généré';
                            }
                            return $approvals->map(fn ($a) => "{$a->reference} ({$a->created_at->format('d/m/Y')})")->implode(', ');
                        })
                        ->badge(fn (Lead $record) => $record->loanApprovalDocuments->isNotEmpty())
                        ->color('warning'),

                    TextEntry::make('bank_client_status')
                        ->label('Espace Bancaire en Ligne')
                        ->state(function (Lead $record) {
                            $client = $record->bankClient;
                            if (! $client) {
                                return 'Non créé';
                            }
                            return "{$client->account_number} ({$client->status})";
                        })
                        ->badge(fn (Lead $record) => (bool) $record->bankClient)
                        ->color(fn (Lead $record) => $record->bankClient ? 'success' : 'gray'),
                ]),

            Section::make($s('consent'))->columns(3)->columnSpanFull()->collapsed()->schema([
                TextEntry::make('consent_at')->label(self::l('consent_at'))->dateTime('d/m/Y H:i'),
                TextEntry::make('consent_version')->label(self::l('consent_version')),
                TextEntry::make('utm')->label(self::l('utm'))->placeholder('—')
                    ->state(fn (Lead $record) => $record->utm ? collect($record->utm)->map(fn ($v, $k) => "$k=$v")->implode(' · ') : null),
            ]),
        ]);
    }
}
