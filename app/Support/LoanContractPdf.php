<?php

namespace App\Support;

use App\Models\LoanContract;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

/** Renders a LoanContract as an official legal credit agreement in the contract's locale. */
final class LoanContractPdf
{
    public static function render(LoanContract $contract): string
    {
        $locale = in_array($contract->locale, ['sv', 'uy'], true) ? 'es' : ($contract->locale ?: 'es');
        $t = fn (string $key, array $replace = []) => trans('loan_contract.'.$key, $replace + ['brand' => Venatomar::config('brand')], $locale);

        $svg = str_replace('url(#g)', '#0FA57E', (string) file_get_contents(public_path('images/logo.svg')));
        $logo = 'data:image/svg+xml;base64,'.base64_encode($svg);

        $createdAt = $contract->created_at ?? now();
        $contractDate = $contract->contract_date ? Carbon::parse($contract->contract_date) : $createdAt;
        $firstPaymentDate = $contract->first_payment_date ? Carbon::parse($contract->first_payment_date) : $contractDate->copy()->addMonth();

        return Pdf::loadView('pdf.loan-contract', [
            'contract' => $contract,
            't' => $t,
            'logo' => $logo,
            'countryName' => $contract->country,
            'amount' => $contract->money(),
            'monthlyPayment' => $contract->monthlyPaymentFormatted(),
            'totalInterest' => $contract->totalInterestFormatted(),
            'totalRepayable' => $contract->totalRepayableFormatted(),
            'date' => $createdAt->locale($locale)->isoFormat('LL'),
            'dateUpper' => strtoupper($contractDate->locale($locale)->isoFormat('LL')),
            'contractDate' => $contractDate->locale($locale)->isoFormat('LL'),
            'firstPaymentDate' => $firstPaymentDate->locale($locale)->isoFormat('LL'),
            'contactEmail' => $contract->admin_email ?: Venatomar::legal('contact_email'),
            'company' => Venatomar::filled(config('venatomar.legal.company_name')),
            'address' => Venatomar::filled(config('venatomar.legal.address')),
            'issuerAddress' => $contract->issuer_address ?: (Venatomar::legal('address') ?: 'Carrer d’Estruc, 9, 08002 Barcelone'),
            'issuerPhone' => $contract->issuer_phone ?: '+34 910 000 000',
            'issuerCity' => $contract->issuer_city ?: 'Barcelone',
            'authCode' => $contract->authCode(),
            'parapheSvg' => OfficialDocumentAssets::parapheSvg(),
            'stampAndSignatureSvg' => OfficialDocumentAssets::stampAndSignatureSvg('DIRECTION JURIDIQUE', 'VISA CONTRACTUEL'),
            'rightSignatureSvg' => OfficialDocumentAssets::rightSignatureSvg(),
            'sealSvg' => OfficialDocumentAssets::stampAndSignatureSvg('DIRECTION JURIDIQUE', 'VISA CONTRACTUEL'),
            'lenderSignatureSvg' => OfficialDocumentAssets::stampAndSignatureSvg('DIRECTION JURIDIQUE', 'VISA CONTRACTUEL'),
            'borrowerSignatureSvg' => OfficialDocumentAssets::rightSignatureSvg(),
        ])->setPaper('a4')->output();
    }

    public static function sealSvg(): string
    {
        return OfficialDocumentAssets::stampAndSignatureSvg('DIRECTION JURIDIQUE', 'VISA CONTRACTUEL');
    }

    public static function lenderSignatureSvg(): string
    {
        return OfficialDocumentAssets::stampAndSignatureSvg('DIRECTION JURIDIQUE', 'VISA CONTRACTUEL');
    }

    public static function borrowerSignatureSvg(): string
    {
        return OfficialDocumentAssets::rightSignatureSvg();
    }
}
