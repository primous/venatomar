<?php

namespace App\Support;

/**
 * Values injected in the legal templates (resources/views/legal/{locale}).
 * Identity placeholders ("[...]") are printed as-is so that missing legal
 * information stays visible until it is filled in config/venatomar.php.
 */
class LegalData
{
    public function __construct(public readonly Country $country) {}

    public function __get(string $key): string
    {
        return e(Venatomar::legal($key));
    }

    public function brand(): string
    {
        return e(config('venatomar.brand'));
    }

    public function url(): string
    {
        return e(rtrim(config('app.url'), '/'));
    }

    public function authority(): string
    {
        $a = $this->country->get('authority');

        return '<a href="'.e($a['url']).'" target="_blank" rel="noopener">'.e($a['name']).'</a>';
    }

    public function sessionMinutes(): int
    {
        return (int) config('session.lifetime');
    }

    public function utmDays(): int
    {
        return (int) config('venatomar.utm_cookie_days');
    }

    public function sessionCookie(): string
    {
        return e(config('session.cookie'));
    }

    public function link(string $page, string $label): string
    {
        return '<a href="'.e(route('legal', ['page' => $page])).'">'.e($label).'</a>';
    }

    public function accountLink(string $label): string
    {
        return '<a href="'.e(route('account')).'">'.e($label).'</a>';
    }

    /** Representative example sentence (same source as the home page), or null. */
    public function example(): ?array
    {
        return $this->country->example();
    }

    public function money(int|float|null $v, int $decimals = 0): string
    {
        return Venatomar::money($v, $this->country, $decimals);
    }

    public function decimal(int|float|null $v): string
    {
        return Venatomar::decimal($v, $this->country);
    }

    public function trackingEnabled(string $key): bool
    {
        return filled(config('venatomar.tracking.'.$key));
    }
}
