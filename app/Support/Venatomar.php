<?php

namespace App\Support;

use NumberFormatter;

/**
 * Single access point to business values. A value written as a placeholder
 * ("[...]"), null or an empty string is considered ABSENT: callers hide the
 * block that would display it. Nothing is ever invented.
 */
class Venatomar
{
    public static function isPlaceholder(mixed $value): bool
    {
        return $value === null || $value === ''
            || (is_string($value) && preg_match('/^\[.*\]$/', trim($value)) === 1);
    }

    /** Return the value, or null when it is a placeholder. */
    public static function filled(mixed $value): mixed
    {
        return self::isPlaceholder($value) ? null : $value;
    }

    public static function config(string $key): mixed
    {
        return self::filled(config('venatomar.'.$key));
    }

    /** Numeric business value (int|float) or null. */
    public static function number(string $key): int|float|null
    {
        $value = self::config($key);

        return is_numeric($value) ? $value + 0 : null;
    }

    /**
     * Loan parameters for a country; every missing value is null.
     *
     * @return array{amount_min:?int,amount_max:?int,amount_step:int,term_min:?int,term_max:?int,apr_min:int|float|null,apr_max:int|float|null,debt_threshold:int|float|null}
     */
    public static function loan(string $cc): array
    {
        $p = 'loan.'.$cc.'.';
        $int = fn ($k) => ($n = self::number($p.$k)) === null ? null : (int) $n;

        return [
            'amount_min' => $int('amount_min'),
            'amount_max' => $int('amount_max'),
            'amount_step' => $int('amount_step') ?: 1,
            'term_min' => $int('term_min'),
            'term_max' => $int('term_max'),
            'apr_min' => self::number($p.'apr_min'),
            'apr_max' => self::number($p.'apr_max'),
            'debt_threshold' => self::number($p.'debt_threshold'),
        ];
    }

    /** Representative example, only when EVERY figure is filled. */
    public static function example(string $cc): ?array
    {
        $loan = self::loan($cc);
        $example = [];
        foreach (['amount', 'term', 'net', 'total', 'apr'] as $k) {
            $example[$k] = self::number('loan.'.$cc.'.example.'.$k);
        }

        $all = $example + $loan;
        foreach (['amount', 'term', 'net', 'total', 'apr', 'term_min', 'term_max', 'apr_min', 'apr_max'] as $k) {
            if ($all[$k] === null) {
                return null;
            }
        }

        return $all;
    }

    /** Legal identity value; placeholders are returned as-is so legal templates stay visibly incomplete. */
    public static function legal(string $key): string
    {
        return (string) config('venatomar.legal.'.$key, '');
    }

    public static function money(int|float|null $amount, ?Country $country = null, int $decimals = 0): string
    {
        if ($amount === null) {
            return '';
        }
        $country ??= Country::current();
        $fmt = new NumberFormatter($country->numberLocale, NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);

        return $fmt->formatCurrency($amount, $country->currency);
    }

    public static function decimal(int|float|null $value, ?Country $country = null, int $decimals = 2): string
    {
        if ($value === null) {
            return '';
        }
        $country ??= Country::current();
        $fmt = new NumberFormatter($country->numberLocale, NumberFormatter::DECIMAL);
        $fmt->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 0);
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimals);

        return $fmt->format($value);
    }

    /** Locale code => country code. */
    public static function locales(): array
    {
        return config('venatomar.locales');
    }

    public static function isLocale(?string $locale): bool
    {
        return $locale !== null && array_key_exists($locale, self::locales());
    }

    public static function reference(int $id, ?\DateTimeInterface $date = null): string
    {
        return sprintf('VN-%s-%06d', ($date ?? now())->format('Y'), $id);
    }

    public static function maskEmail(string $email): string
    {
        [$user, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visible = mb_substr($user, 0, min(2, max(1, mb_strlen($user) - 1)));

        return $visible.str_repeat('•', max(3, mb_strlen($user) - mb_strlen($visible))).'@'.$domain;
    }

    public static function maskTail(?string $value, int $visible = 4): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        $clean = preg_replace('/\s+/', '', $value);

        return '•••• '.mb_substr($clean, -$visible);
    }
}
