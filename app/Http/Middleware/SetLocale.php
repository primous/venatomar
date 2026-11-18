<?php

namespace App\Http\Middleware;

use App\Support\Country;
use App\Support\Venatomar;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Validates the {locale} URL prefix, applies it, and shares $country with views.
 * Unknown locale => 404.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        $isPortal = $request->is('*/portal*') || $request->is('portal*');

        if ($isPortal) {
            if (! in_array($locale, ['es', 'fr', 'en', 'de', 'it', 'pt', 'nl'], true)) {
                abort(404);
            }
        } else {
            if (! Venatomar::isLocale($locale)) {
                abort(404);
            }
        }

        self::apply($locale);

        // Keep route() helpers short: the locale parameter is implicit.
        $request->route()?->forgetParameter('locale');

        return $next($request);
    }

    public static function apply(string $locale): Country
    {
        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);
        $country = Country::forLocale($locale);
        app()->instance(Country::class, $country);
        View::share('country', $country);

        return $country;
    }
}
