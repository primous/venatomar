<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\BankClient;
use App\Models\BankClientLoan;
use App\Models\BankSupportMessage;
use App\Models\BankTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalLoanController extends Controller
{
    public function index(): View
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        $activeLoan = $client->activeLoan();
        $allLoans = $client->loans()->get();

        return view('portal.loans', [
            'client' => $client,
            'activeLoan' => $activeLoan,
            'allLoans' => $allLoans,
        ]);
    }

    public function transferSavings(Request $request): RedirectResponse
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        if ($client->isFrozen()) {
            return back()->with('error', trans('portal.account_frozen_error'));
        }

        $validated = $request->validate([
            'direction' => ['required', 'in:to_savings,from_savings'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $amount = (float) $validated['amount'];

        if ($validated['direction'] === 'to_savings') {
            if ($client->balance < $amount) {
                return back()->with('error', trans('portal.insufficient_funds_error'));
            }
            $client->decrement('balance', $amount);
            $client->increment('savings_balance', $amount);

            BankTransaction::create([
                'bank_client_id' => $client->id,
                'type' => 'savings_deposit',
                'category' => 'epargne',
                'amount' => $amount,
                'currency' => $client->currency,
                'status' => 'completed',
                'target_bank_name' => 'Compte Épargne Venatomar',
                'target_account_holder' => $client->fullName(),
                'target_account_number' => $client->account_number . '-EP',
                'description' => 'Virement interne vers compte épargne',
                'approved_by_admin' => true,
                'completed_at' => now(),
            ]);

            return back()->with('status', trans('portal.savings_transfer_success') . " ({$client->formatMoney($amount)})");
        } else {
            if ($client->savings_balance < $amount) {
                return back()->with('error', trans('portal.insufficient_funds_error'));
            }
            $client->decrement('savings_balance', $amount);
            $client->increment('balance', $amount);

            BankTransaction::create([
                'bank_client_id' => $client->id,
                'type' => 'savings_withdrawal',
                'category' => 'epargne',
                'amount' => $amount,
                'currency' => $client->currency,
                'status' => 'completed',
                'target_bank_name' => 'Compte Courant Venatomar',
                'target_account_holder' => $client->fullName(),
                'target_account_number' => $client->account_number,
                'description' => 'Virement interne depuis compte épargne',
                'approved_by_admin' => true,
                'completed_at' => now(),
            ]);

            return back()->with('status', trans('portal.savings_transfer_success') . " ({$client->formatMoney($amount)})");
        }
    }

    public function applyLoan(Request $request): RedirectResponse
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:500', 'max:500000'],
            'term_months' => ['required', 'integer', 'min:6', 'max:120'],
            'title' => ['required', 'string', 'max:100'],
            'project_description' => ['nullable', 'string', 'max:500'],
        ]);

        $amount = (float) $validated['amount'];
        $months = (int) $validated['term_months'];
        $annualRate = 3.50;
        $monthlyRate = ($annualRate / 100) / 12;

        // Standard amortization monthly payment formula
        $monthlyPayment = ($amount * $monthlyRate) / (1 - pow(1 + $monthlyRate, -$months));
        $totalRepayable = $monthlyPayment * $months;

        $loan = BankClientLoan::create([
            'bank_client_id' => $client->id,
            'title' => $validated['title'],
            'amount' => $amount,
            'remaining_amount' => $amount,
            'term_months' => $months,
            'interest_rate' => $annualRate,
            'monthly_payment' => round($monthlyPayment, 2),
            'total_repayable' => round($totalRepayable, 2),
            'status' => 'pending_approval',
        ]);

        BankSupportMessage::create([
            'bank_client_id' => $client->id,
            'category' => 'loan_inquiry',
            'subject' => "Demande de financement : {$validated['title']} ({$client->formatMoney($amount)})",
            'message' => "Le client a sollicité un prêt de {$client->formatMoney($amount)} sur {$months} mois. Description du projet : " . ($validated['project_description'] ?: 'Aucune précision'),
            'status' => 'open',
        ]);

        return back()->with('status', trans('portal.loan_applied_success') . " ({$client->formatMoney($amount)}). Ref: {$loan->reference}");
    }
}
