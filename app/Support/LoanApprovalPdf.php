<?php

namespace App\Support;

use App\Models\LoanApprovalDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

/** Renders a LoanApprovalDocument as an official loan validation certificate in the document's locale. */
final class LoanApprovalPdf
{
    public static function render(LoanApprovalDocument $doc): string
    {
        $locale = in_array($doc->locale, ['sv', 'uy'], true) ? 'es' : ($doc->locale ?: 'es');
        $t = fn (string $key, array $replace = []) => trans('loan_approval.'.$key, $replace + ['brand' => Venatomar::config('brand')], $locale);

        $svg = str_replace('url(#g)', '#0FA57E', (string) file_get_contents(public_path('images/logo.svg')));
        $logo = 'data:image/svg+xml;base64,'.base64_encode($svg);

        $createdAt = $doc->created_at ?? now();
        $approvalDate = $doc->approval_date ? Carbon::parse($doc->approval_date) : $createdAt;
        $expiryDate = $doc->expiry_date ? Carbon::parse($doc->expiry_date) : $approvalDate->copy()->addDays(30);

        return Pdf::loadView('pdf.loan-approval', [
            'doc' => $doc,
            't' => $t,
            'logo' => $logo,
            'countryName' => $doc->country,
            'amount' => $doc->money(),
            'monthlyPayment' => $doc->monthlyPaymentFormatted(),
            'totalRepayable' => $doc->totalRepayableFormatted(),
            'date' => $createdAt->locale($locale)->isoFormat('LL'),
            'dateUpper' => strtoupper($createdAt->locale($locale)->isoFormat('LL')),
            'approvalDate' => $approvalDate->locale($locale)->isoFormat('LL'),
            'expiryDate' => $expiryDate->locale($locale)->isoFormat('LL'),
            'contactEmail' => $doc->admin_email ?: Venatomar::legal('contact_email'),
            'company' => Venatomar::filled(config('venatomar.legal.company_name')),
            'address' => Venatomar::filled(config('venatomar.legal.address')),
            'issuerAddress' => $doc->issuer_address ?: (Venatomar::legal('address') ?: 'Carrer d’Estruc, 9, 08002 Barcelone'),
            'issuerPhone' => $doc->issuer_phone ?: '+34 910 000 000',
            'issuerCity' => $doc->issuer_city ?: 'Barcelone',
            'authCode' => $doc->authCode(),
            'parapheSvg' => OfficialDocumentAssets::parapheSvg(),
            'stampAndSignatureSvg' => OfficialDocumentAssets::stampAndSignatureSvg('DIRECTION DES ENGAGEMENTS', 'ACCORD DEFINITIF'),
            'rightSignatureSvg' => OfficialDocumentAssets::rightSignatureSvg(),
            'sealSvg' => OfficialDocumentAssets::stampAndSignatureSvg('DIRECTION DES ENGAGEMENTS', 'ACCORD DEFINITIF'),
            'signatureSvg' => OfficialDocumentAssets::rightSignatureSvg(),
        ])->setPaper('a4')->output();
    }

    public static function sealSvg(): string
    {
        return OfficialDocumentAssets::stampAndSignatureSvg('DIRECTION DES ENGAGEMENTS', 'ACCORD DEFINITIF');
    }

    public static function signatureSvg(): string
    {
        return OfficialDocumentAssets::rightSignatureSvg();
    }
}
