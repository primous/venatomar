<?php

namespace App\Support;

use App\Models\BankClient;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

/**
 * PDF Engine for automated client portal banking documents:
 * - RIB / IBAN / BIC Statement
 * - Monthly Account Statement (Relevé de compte)
 * - Balance & Solvency Certificate (Attestation bancaire)
 */
final class BankPdfEngine
{
    private static function getLogo(): string
    {
        $logoPath = public_path('images/logo.svg');
        if (file_exists($logoPath)) {
            $svg = str_replace('url(#g)', '#0FA57E', (string) file_get_contents($logoPath));
            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        }
        return '';
    }

    public static function renderRib(BankClient $client): string
    {
        $logo = self::getLogo();
        $date = now()->format('d/m/Y');

        return Pdf::loadView('pdf.bank-rib', [
            'client' => $client,
            'logo' => $logo,
            'date' => $date,
        ])->output();
    }

    public static function renderStatement(BankClient $client): string
    {
        $logo = self::getLogo();
        $date = now()->format('d/m/Y');
        $transactions = $client->transactions()->orderBy('created_at', 'desc')->limit(50)->get();

        return Pdf::loadView('pdf.bank-statement', [
            'client' => $client,
            'logo' => $logo,
            'date' => $date,
            'transactions' => $transactions,
            'period' => Carbon::now()->subMonth()->format('d/m/Y') . ' - ' . now()->format('d/m/Y'),
        ])->output();
    }

    public static function renderAttestation(BankClient $client): string
    {
        $logo = self::getLogo();
        $date = now()->format('d/m/Y');

        return Pdf::loadView('pdf.bank-attestation', [
            'client' => $client,
            'logo' => $logo,
            'date' => $date,
        ])->output();
    }
}
