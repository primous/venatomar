<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\BankClient;
use App\Support\BankPdfEngine;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalDocumentController extends Controller
{
    public function index(): View
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        return view('portal.documents', [
            'client' => $client,
        ]);
    }

    public function downloadRib(): Response
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        $pdf = BankPdfEngine::renderRib($client);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="RIB-IBAN-' . $client->account_number . '.pdf"',
        ]);
    }

    public function downloadStatement(): Response
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        $pdf = BankPdfEngine::renderStatement($client);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Releve-de-compte-' . $client->account_number . '.pdf"',
        ]);
    }

    public function downloadAttestation(): Response
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        $pdf = BankPdfEngine::renderAttestation($client);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Attestation-Solde-' . $client->account_number . '.pdf"',
        ]);
    }
}
