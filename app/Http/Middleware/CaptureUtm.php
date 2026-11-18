<?php

namespace App\Http\Middleware;

use App\Support\Consent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps campaign parameters (utm_*, gclid...) in the server session from the
 * first page view, so they can be attached to the loan request.
 * The long-lived "pf_utm" cookie is only written after statistics consent.
 * UTM values are campaign identifiers, never personal data.
 */
class CaptureUtm
{
    public const KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'];

    public function handle(Request $request, Closure $next): Response
    {
        $utm = [];
        foreach (self::KEYS as $key) {
            $value = $request->query($key);
            if (is_string($value) && $value !== '') {
                $utm[$key] = mb_substr(strip_tags($value), 0, 150);
            }
        }

        $consent = Consent::fromRequest($request);

        if ($utm !== [] && $request->hasSession()) {
            $request->session()->put('utm', $utm);
        } elseif ($request->hasSession() && ! $request->session()->has('utm') && $consent->allows('statistics')) {
            $stored = json_decode((string) $request->cookie('pf_utm'), true);
            if (is_array($stored)) {
                $request->session()->put('utm', array_intersect_key($stored, array_flip(self::KEYS)));
            }
        }

        $response = $next($request);

        if ($utm !== [] && $consent->allows('statistics')) {
            Cookie::queue('pf_utm', json_encode($utm), config('venatomar.utm_cookie_days') * 24 * 60);
        }

        return $response;
    }
}
