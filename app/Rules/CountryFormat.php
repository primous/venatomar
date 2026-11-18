<?php

namespace App\Rules;

use App\Support\Country;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Country-specific formats: postal code, mobile phone (national format) and
 * number plate. The regexes live in config/countries.php.
 */
class CountryFormat implements ValidationRule
{
    public function __construct(private readonly Country $country, private readonly string $kind) {}

    public static function postal(Country $c): self
    {
        return new self($c, 'postal');
    }

    public static function phone(Country $c): self
    {
        return new self($c, 'phone');
    }

    public static function plate(Country $c): self
    {
        return new self($c, 'plate');
    }

    public static function normalizePostal(?string $v): string
    {
        return strtoupper(trim(preg_replace('/\s+/', ' ', (string) $v)));
    }

    /** Strip spaces, separators, the international prefix and one trunk "0". */
    public static function normalizePhone(?string $v, Country $c): string
    {
        $digits = preg_replace('/[^\d+]/', '', (string) $v);
        $prefix = $c->phonePrefix;
        if (str_starts_with($digits, $prefix)) {
            $digits = substr($digits, strlen($prefix));
        } elseif (str_starts_with($digits, '00'.ltrim($prefix, '+'))) {
            $digits = substr($digits, strlen($prefix) + 1);
        }
        $digits = ltrim($digits, '+');

        return str_starts_with($digits, '0') ? substr($digits, 1) : $digits;
    }

    public static function normalizePlate(?string $v): string
    {
        return mb_strtoupper(trim(preg_replace('/\s+/', ' ', (string) $v)));
    }

    public static function matches(Country $c, string $kind, ?string $value): bool
    {
        return match ($kind) {
            'postal' => preg_match($c->postalRegex, self::normalizePostal($value)) === 1,
            'phone' => preg_match($c->phoneRegex, self::normalizePhone($value, $c)) === 1,
            'plate' => preg_match($c->plateRegex, self::normalizePlate($value)) === 1,
        };
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::matches($this->country, $this->kind, (string) $value)) {
            $fail(__('validation.format_'.$this->kind, [
                'example' => $this->country->get($this->kind.'_example', ''),
            ]));
        }
    }
}
