<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class Seo
{
    /** Routes that may appear in the sitemap / carry hreflang alternates. */
    public const INDEXABLE = ['home', 'apply', 'legal'];

    /**
     * Same page in every locale: [locale => absolute URL]. Query strings are
     * never carried over (they could contain campaign or personal data).
     *
     * @return array<string,string>
     */
    public static function alternates(): array
    {
        $route = Route::current();
        $name = $route?->getName();
        if ($name === null || ! Route::has($name) || ! in_array('locale', $route->parameterNames(), true)) {
            $name = 'home';
            $params = [];
        } else {
            $params = $route->parameters();
            unset($params['locale'], $params['token']);
        }

        $urls = [];
        foreach (array_keys(Venatomar::locales()) as $locale) {
            $urls[$locale] = route($name, ['locale' => $locale] + $params);
        }

        return $urls;
    }

    /** hreflang value: language-COUNTRY. */
    public static function hreflang(string $locale): string
    {
        return $locale.'-'.strtoupper(Venatomar::locales()[$locale]);
    }

    public static function canonical(): string
    {
        return self::alternates()[app()->getLocale()] ?? url()->current();
    }
}
