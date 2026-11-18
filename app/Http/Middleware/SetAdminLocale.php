<?php

namespace App\Http\Middleware;

use App\Support\AdminText;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Back office language: chosen in the user menu, stored in the session, French by default. */
class SetAdminLocale
{
    public const SESSION_KEY = 'admin_locale';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get(self::SESSION_KEY, AdminText::DEFAULT);
        if (! array_key_exists($locale, AdminText::LOCALES)) {
            $locale = AdminText::DEFAULT;
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
