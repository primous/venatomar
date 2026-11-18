<?php

namespace App\Http\Middleware;

use App\Models\BankClient;
use App\Support\Venatomar;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientPortalLocale
{
    /**
     * Handle an incoming request.
     * Enforces that an authenticated banking client accesses their portal strictly
     * in the language assigned by the administrator upon account creation.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var BankClient|null $client */
        $client = Auth::guard('client')->user();

        if ($client && filled($client->locale)) {
            $targetLocale = $client->locale;
            $currentLocale = app()->getLocale();

            if ($currentLocale !== $targetLocale && in_array($targetLocale, ['es', 'fr', 'en', 'de', 'it', 'pt', 'nl'], true)) {
                session(['locale' => $targetLocale]);
                app()->setLocale($targetLocale);

                $segments = $request->segments();
                if (! empty($segments)) {
                    $segments[0] = $targetLocale;
                    $targetPath = '/' . implode('/', $segments);
                    if ($queryString = $request->getQueryString()) {
                        $targetPath .= '?' . $queryString;
                    }

                    return redirect()->to($targetPath);
                }

                return redirect()->route('portal.dashboard', ['locale' => $targetLocale]);
            }
        }

        return $next($request);
    }
}
