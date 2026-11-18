<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\BankClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalAuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard('client')->check()) {
            $locale = Auth::guard('client')->user()->locale ?: app()->getLocale();
            return redirect()->route('portal.dashboard', ['locale' => $locale]);
        }

        return redirect()->route('account', ['locale' => app()->getLocale() ?: config('venatomar.default_locale', 'es')]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('client')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var BankClient $client */
            $client = Auth::guard('client')->user();

            if ($client->isBlocked()) {
                Auth::guard('client')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => __('portal.account_blocked'),
                ]);
            }

            // CRITICAL: Determine client's language selected by admin at creation
            $targetLocale = $client->locale ?: (session('locale') ?: config('venatomar.default_locale', 'es'));
            session(['locale' => $targetLocale]);
            app()->setLocale($targetLocale);

            $client->update(['last_login_at' => now()]);

            return redirect()->route('portal.dashboard', ['locale' => $targetLocale]);
        }

        return back()->withErrors([
            'email' => 'Identifiants bancaires incorrects. / Credenciales de acceso incorrectas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        $locale = Auth::guard('client')->user()?->locale ?? app()->getLocale();
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('account', ['locale' => $locale])->with('status', 'Session fermée avec succès. / Sesión cerrada con éxito.');
    }
}
