<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\BankClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PortalProfileController extends Controller
{
    public function index(): View
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        return view('portal.profile', [
            'client' => $client,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var BankClient $client */
        $client = Auth::guard('client')->user();

        $action = $request->input('action', 'profile');

        if ($action === 'password') {
            $request->validate([
                'current_password' => ['required', 'string'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ], [
                'current_password.required' => 'Votre mot de passe actuel est obligatoire.',
                'password.min' => 'Le nouveau mot de passe doit comporter au moins 8 caractères.',
                'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            ]);

            if (! Hash::check($request->input('current_password'), $client->password)) {
                return back()->withErrors(['current_password' => trans('portal.password_invalid_error')]);
            }

            $client->update([
                'password' => Hash::make($request->input('password')),
            ]);

            return back()->with('status', trans('portal.password_updated_success'));
        }

        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        $client->update($validated);

        return back()->with('status', trans('portal.profile_updated_success'));
    }
}
