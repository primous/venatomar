<?php

namespace App\Support;

use Illuminate\Support\Facades\Lang;

/**
 * Back-office texts (lang/{fr,es,en}/admin.php). French is the default.
 * Kept apart from the public site's JSON files, which have no French.
 */
class AdminText
{
    public const LOCALES = ['fr' => 'Français', 'es' => 'Español', 'en' => 'English'];

    public const DEFAULT = 'fr';

    public static function t(string $key, array $replace = []): string
    {
        return trans('admin.'.$key, $replace);
    }

    /** Label of a stored select value: admin file first, then the site's JSON, then the raw value. */
    public static function opt(string $group, ?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $key = "admin.opt.$group.$value";
        if (Lang::has($key, null, false)) {
            return trans($key);
        }
        $json = "opt.$group.$value";
        $label = trans($json);

        return $label === $json ? $value : $label;
    }
}
