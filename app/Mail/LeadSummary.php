<?php

namespace App\Mail;

use App\Models\Lead;
use App\Support\Country;
use App\Support\Venatomar;

/**
 * Full, human-readable dump of a lead for the INTERNAL e-mail (Spanish).
 * Contains sensitive values in clear: never use it for the applicant.
 */
class LeadSummary
{
    public function __construct(private readonly Lead $lead) {}

    /** @return array<string, array<string,string>> section => [label => value] */
    public function sections(): array
    {
        $l = $this->lead;
        $c = new Country($l->country);
        $opt = fn (string $group, ?string $key) => $key === null ? '—' : __('opt.'.$group.'.'.$key, [], 'es');
        $yes = fn ($b) => $b ? 'Sí' : 'No';
        $money = fn ($v) => Venatomar::decimal($v, new Country('es'), 0).' '.$l->currency;
        $regions = $c->regions();

        $payout = match ($l->payout_method) {
            'mobile_money' => [
                'Método de cobro' => 'Mobile Money',
                'Operador' => $c->get('mobile_money_operators.'.$l->mm_operator, (string) $l->mm_operator),
                'Número Mobile Money' => (string) $l->mm_number,
            ],
            'western_union' => [
                'Método de cobro' => 'Western Union',
                'Modalidad' => 'Retiro en efectivo en agencia',
                'Preferencia / Agencia' => (string) ($l->bank_account ?: 'Cualquier sucursal Western Union'),
            ],
            'redpagos' => [
                'Método de cobro' => 'REDPAGOS',
                'Modalidad' => 'Cobro en sucursal / local',
                'Detalle / Cuenta' => (string) ($l->bank_account ?: 'Retiro en sucursal Redpagos'),
            ],
            'abitab' => [
                'Método de cobro' => 'ABITAB',
                'Modalidad' => 'Cobro en sucursal / agencia',
                'Detalle' => (string) ($l->bank_account ?: 'Retiro en sucursal Abitab'),
            ],
            'midinero' => [
                'Método de cobro' => 'MIDINERO',
                'Cuenta / Tarjeta' => (string) $l->bank_account,
            ],
            'transferencia' => [
                'Método de cobro' => 'Transferencia bancaria',
                'Cuenta / IBAN' => preg_match('/^[A-Za-z]{2}\d/i', (string) $l->bank_account) ? trim(chunk_split((string) $l->bank_account, 4, ' ')) : (string) $l->bank_account,
            ],
            'rib' => ['Método de cobro' => 'RIB', 'RIB' => (string) $l->bank_account],
            'uk_account' => ['Método de cobro' => 'Sort code + account number', 'Cuenta' => (string) $l->bank_account],
            default => ['Método de cobro' => 'Transferencia bancaria / IBAN', 'Cuenta / IBAN' => preg_match('/^[A-Za-z]{2}\d/i', (string) $l->bank_account) ? trim(chunk_split((string) $l->bank_account, 4, ' ')) : (string) $l->bank_account],
        };

        return [
            '1 · Ingresos' => [
                'Importe solicitado' => $money($l->amount),
                'Plazo' => $l->term_months.' meses',
                'Ingresos netos mensuales' => $money($l->monthly_income),
                'Situación laboral' => $opt('employment', $l->employment_status),
                'En fichero de morosos ('.$c->creditBureau.')' => $yes($l->defaulter_list),
            ],
            '2 · Datos personales' => array_filter([
                'Nombre' => $l->first_name,
                'Apellidos' => $l->last_name,
                'Documento de identidad' => $c->hasDocument() ? ($l->document_number ?: '—') : null,
                'Fecha de nacimiento' => $l->birthday ? date('d/m/Y', strtotime($l->birthday)) : '—',
                'E-mail' => $l->email,
                'Teléfono' => $l->phone,
            ], fn ($v) => $v !== null),
            '3 · Domicilio' => [
                'Código postal' => $l->geo_zip,
                'Provincia / región' => $regions[$l->geo_province] ?? $l->geo_province,
                'Localidad' => $l->geo_city,
                'Calle' => $l->home_street,
                'Número' => $l->home_number,
                'Piso / puerta' => $l->apartment_floor ?: '—',
            ],
            '4 · Cuenta bancaria' => $payout,
            '5 · Información adicional' => [
                'Sexo' => $opt('gender', $l->gender),
                'Finalidad' => $opt('loan_purpose', $l->loan_purpose),
                'Estado civil' => $opt('marital', $l->marital_status),
                'Sector de la empresa' => $opt('company_sector', $l->company_sector),
                'Estudios' => $opt('education', $l->education),
                'Vivienda' => $opt('home', $l->home_status),
                'Gastos mensuales' => $money($l->regular_expenses),
                'Deudas superiores al umbral' => $yes($l->has_debt),
                'Vehículo de menos de 10 años' => $yes($l->has_car),
                'Matrícula' => $l->car_number ?: '—',
            ],
            'Metadatos' => [
                'Referencia' => (string) $l->reference,
                'Canal de recepción' => $l->admin_email ?: 'info@venatomar.com',
                'Fecha' => $l->created_at->timezone('Europe/Madrid')->format('d/m/Y H:i').' (Madrid)',
                'Idioma / país' => $l->locale.' / '.strtoupper($l->country),
                'IP' => (string) $l->ip_address,
                'User-agent' => (string) $l->user_agent,
                'UTM' => $l->utm ? collect($l->utm)->map(fn ($v, $k) => "$k=$v")->implode(', ') : '—',
                'Consentimiento' => $l->consent_at->timezone('Europe/Madrid')->format('d/m/Y H:i').' · versión '.$l->consent_version,
            ],
        ];
    }
}
