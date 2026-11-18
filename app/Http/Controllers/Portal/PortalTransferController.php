<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\BankClient;
use App\Models\BankTransaction;
use App\Support\BankDirectory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalTransferController extends Controller
{
    public function index(Request $request): View
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        $banks = BankDirectory::banksForCountry($client->country);
        if (empty($banks)) {
            $banks = BankDirectory::banksForCountry('France');
        }

        $tab = $request->query('tab', 'all');

        $query = $client->transactions();

        if ($tab === 'sent') {
            $query->whereIn('type', ['transfer_out', 'fee']);
        } elseif ($tab === 'received') {
            $query->whereIn('type', ['transfer_in', 'loan_payout']);
        } elseif ($tab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($tab === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        $transactions = $query->latest('id')->paginate(15);
        $pendingTx = $client->transactions()->where('status', 'pending')->latest('id')->first();

        return view('portal.transfers', [
            'client' => $client,
            'banks' => $banks,
            'transactions' => $transactions,
            'tab' => $tab,
            'pendingTx' => $pendingTx,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        if ($client->isFrozen()) {
            return back()->with('error', trans('portal.account_frozen_error'));
        }

        $validated = $request->validate([
            'target_bank_name' => ['required', 'string', 'max:120'],
            'target_account_holder' => ['required', 'string', 'max:120'],
            'target_account_number' => ['required', 'string', 'max:60'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $amount = (float) $validated['amount'];
        $availableFunds = (float) $client->balance + (float) $client->authorized_overdraft;

        if ($amount > $availableFunds) {
            return back()->with('error', trans('portal.insufficient_funds_error') . ' (' . $client->formattedBalance() . ')');
        }

        // Create transaction in 'pending' status
        $tx = BankTransaction::create([
            'bank_client_id' => $client->id,
            'type' => 'transfer_out',
            'category' => 'virement',
            'amount' => $amount,
            'currency' => $client->currency,
            'status' => 'pending',
            'target_bank_name' => $validated['target_bank_name'],
            'target_account_holder' => $validated['target_account_holder'],
            'target_account_number' => $validated['target_account_number'],
            'description' => $validated['description'] ?: 'Virement bancaire sortant',
            'is_otp_authorized' => $client->can_receive_otp_email,
        ]);

        if ($client->can_receive_otp_email) {
            return redirect()->route('portal.transfers', ['locale' => $client->locale ?? app()->getLocale()])
                ->with('active_otp_tx', $tx->id)
                ->with('otp_notice', 'code_sent_email')
                ->with('status', trans('portal.otp_email_desc'));
        }

        return redirect()->route('portal.transfers', ['locale' => $client->locale ?? app()->getLocale()])
            ->with('active_otp_tx', $tx->id)
            ->with('otp_notice', 'code_held_by_admin')
            ->with('warning', trans('portal.otp_admin_desc'));
    }

    public function verifyOtp(Request $request, BankTransaction $transaction): RedirectResponse
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        if ($transaction->bank_client_id !== $client->id || ! $transaction->isPending()) {
            return redirect()->route('portal.transfers', ['locale' => $client->locale ?? app()->getLocale()])->with('error', trans('portal.otp_invalid_error'));
        }

        $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $code = trim($request->input('otp_code'));

        if ($transaction->otp_code !== $code) {
            return back()->with('active_otp_tx', $transaction->id)
                ->with('error', trans('portal.otp_invalid_error'));
        }

        // Valid OTP: Deduct balance and complete transfer
        $amount = (float) $transaction->amount;
        $client->decrement('balance', $amount);

        $transaction->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('portal.transfers', ['locale' => $client->locale ?? app()->getLocale()])
            ->with('status', trans('portal.otp_verified_success') . " ({$client->formatMoney($amount)})");
    }

    public function cancel(Request $request, BankTransaction $transaction): RedirectResponse
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        if ($transaction->bank_client_id !== $client->id || ! $transaction->isPending()) {
            return redirect()->route('portal.transfers', ['locale' => $client->locale ?? app()->getLocale()])->with('error', trans('portal.otp_invalid_error'));
        }

        $transaction->update(['status' => 'cancelled']);

        return redirect()->route('portal.transfers', ['locale' => $client->locale ?? app()->getLocale()])->with('status', trans('portal.transfer_cancelled_success'));
    }
}
