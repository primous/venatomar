<?php

namespace App\Http\Controllers;

use App\Models\MagicLoginToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    private function accountRouteName(Request $request, string $suffix = ''): string
    {
        $prefix = $request->routeIs('solicitar*') ? 'solicitar.account' : 'account';

        return $suffix === '' ? $prefix : $prefix.'.'.$suffix;
    }

    public function show(Request $request): View|RedirectResponse
    {
        if (Auth::guard('client')->check()) {
            $locale = Auth::guard('client')->user()->locale ?: app()->getLocale();

            return redirect()->route('portal.dashboard', ['locale' => $locale]);
        }

        if (Auth::guard('applicant')->check()) {
            return redirect()->route($this->accountRouteName($request, 'dashboard'));
        }

        return view('pages.account');
    }

    /** Signed, single-use, 15-minute link. Expired or reused => refused. */
    public function login(Request $request, string $token): RedirectResponse
    {
        $magic = MagicLoginToken::findValid($token);

        if ($magic === null || ! $magic->consume()) {
            return redirect()->route($this->accountRouteName($request))->with('account_error', __('account.link_invalid'));
        }

        Auth::guard('applicant')->login($magic->applicant);
        $request->session()->regenerate();
        $magic->applicant->forceFill(['last_login_at' => now()])->save();

        return redirect()->route($this->accountRouteName($request, 'dashboard'));
    }

    public function dashboard(): View
    {
        return view('pages.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $target = $this->accountRouteName($request);
        Auth::guard('applicant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($target)->with('account_status', __('account.logged_out'));
    }
}
