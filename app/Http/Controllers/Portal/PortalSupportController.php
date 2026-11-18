<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\BankClient;
use App\Models\BankSupportMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalSupportController extends Controller
{
    public function index(): View
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        $messages = $client->supportMessages()->latest('id')->paginate(10);
        $transactions = $client->transactions()->latest('id')->limit(20)->get();

        return view('portal.support', [
            'client' => $client,
            'messages' => $messages,
            'transactions' => $transactions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'category' => ['required', 'in:general,fraud_alert,transaction_dispute,loan_inquiry,technical'],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:3000'],
            'bank_transaction_id' => ['nullable', 'exists:bank_transactions,id'],
        ], [
            'subject.required' => 'L\'objet de votre message est obligatoire.',
            'message.required' => 'Veuillez saisir votre message ou explication.',
        ]);

        BankSupportMessage::create([
            'bank_client_id' => $client->id,
            'bank_transaction_id' => $validated['bank_transaction_id'] ?? null,
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        return redirect()->route('portal.support', ['locale' => $client->locale ?? app()->getLocale()])
            ->with('status', trans('portal.support_message_sent'));
    }
}
