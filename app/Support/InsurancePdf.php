<?php

namespace App\Support;

use App\Models\InsuranceDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

/** Renders an InsuranceDocument as an official Certificate or Information sheet, in the document's own language. */
final class InsurancePdf
{
    /** Languages a document can be written in (site locales + French). */
    public const LOCALES = [
        'fr' => 'Français', 'es' => 'Español', 'en' => 'English', 'de' => 'Deutsch', 'it' => 'Italiano',
        'nl' => 'Nederlands', 'el' => 'Ελληνικά', 'pt' => 'Português', 'sk' => 'Slovenčina', 'sl' => 'Slovenščina', 'mg' => 'Malagasy',
    ];

    public const NUMBER_LOCALES = [
        'fr' => 'fr_FR', 'es' => 'es_ES', 'en' => 'en_GB', 'de' => 'de_DE', 'it' => 'it_IT',
        'nl' => 'nl_NL', 'el' => 'el_GR', 'pt' => 'pt_PT', 'sk' => 'sk_SK', 'sl' => 'sl_SI', 'mg' => 'mg_MG',
    ];

    /** Suggestions for the currency field (any ISO 4217 code is accepted). */
    public const CURRENCIES = ['EUR', 'GBP', 'MGA', 'USD', 'CHF', 'XOF', 'XAF', 'MAD', 'CAD', 'MUR', 'CDF', 'TND', 'DZD', 'PLN', 'CZK', 'HUF', 'RON', 'SEK', 'NOK', 'DKK'];

    public static function render(InsuranceDocument $doc, ?string $type = null): string
    {
        $locale = $doc->locale;
        $t = fn (string $key, array $replace = []) => trans('insurance.'.$key, $replace + ['brand' => Venatomar::config('brand')], $locale);

        // dompdf does not render SVG gradients: use the solid brand green.
        $svg = str_replace('url(#g)', '#0FA57E', (string) file_get_contents(public_path('images/logo.svg')));
        $logo = 'data:image/svg+xml;base64,'.base64_encode($svg);

        $targetType = $type ?? ($doc->document_type ?: 'certificate');
        $view = $targetType === 'information' ? 'pdf.insurance' : 'pdf.certificate';

        $createdAt = $doc->created_at ?? now();
        $effectiveDate = $doc->effective_date ? Carbon::parse($doc->effective_date) : $createdAt;
        $expiryDate = $doc->expiryDate();

        $guaranteeNames = trans('admin.insurance.guarantee_names');
        $guaranteesList = implode(', ', array_map(fn ($k) => $guaranteeNames[$k] ?? ucfirst($k), $doc->guarantees ?? []));

        return Pdf::loadView($view, [
            'doc' => $doc,
            't' => $t,
            'logo' => $logo,
            'countryName' => $doc->country,
            'amount' => $doc->money(),
            'date' => $createdAt->locale($locale)->isoFormat('LL'),
            'dateUpper' => strtoupper($createdAt->locale($locale)->isoFormat('LL')),
            'effectiveDate' => $effectiveDate ? $effectiveDate->locale($locale)->isoFormat('LL') : $createdAt->locale($locale)->isoFormat('LL'),
            'expiryDate' => $expiryDate ? $expiryDate->locale($locale)->isoFormat('LL') : null,
            'contactEmail' => $doc->admin_email ?: Venatomar::legal('contact_email'),
            'company' => Venatomar::filled(config('venatomar.legal.company_name')),
            'address' => Venatomar::filled(config('venatomar.legal.address')),
            'issuerAddress' => $doc->issuer_address ?: (Venatomar::legal('address') ?: 'Carrer d’Estruc, 9, 08002 Barcelone'),
            'issuerPhone' => $doc->issuer_phone ?: '+34 910 000 000',
            'issuerCity' => $doc->issuer_city ?: 'Barcelone',
            'authCode' => $doc->authCode(),
            'guaranteesList' => $guaranteesList,
            'parapheSvg' => OfficialDocumentAssets::parapheSvg(),
            'stampAndSignatureSvg' => OfficialDocumentAssets::stampAndSignatureSvg('DIRECTION DES ASSURANCES', 'VISA ASSURANCE'),
            'rightSignatureSvg' => OfficialDocumentAssets::rightSignatureSvg(),
            'sealSvg' => OfficialDocumentAssets::stampAndSignatureSvg('DIRECTION DES ASSURANCES', 'VISA ASSURANCE'),
            'signatureSvg' => OfficialDocumentAssets::rightSignatureSvg(),
        ])->setPaper('a4')->output();
    }

    public static function sealSvg(): string
    {
        return OfficialDocumentAssets::stampAndSignatureSvg('DIRECTION DES ASSURANCES', 'VISA ASSURANCE');
    }

    public static function signatureSvg(): string
    {
        return OfficialDocumentAssets::rightSignatureSvg();
    }
}
