<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\BankClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalDashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        // Ensure portal opens in the language chosen by the admin at account creation
        if ($client && $client->locale && $client->locale !== app()->getLocale()) {
            return redirect()->route('portal.dashboard', ['locale' => $client->locale]);
        }

        $search = $request->query('q');

        $query = $client->transactions();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('target_bank_name', 'like', "%{$search}%")
                    ->orWhere('target_account_holder', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest('id')->limit(15)->get();
        $loan = $client->activeLoan();

        return view('portal.dashboard', [
            'client' => $client,
            'loan' => $loan,
            'transactions' => $transactions,
            'search' => $search,
        ]);
    }
}
