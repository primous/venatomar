<?php

namespace App\Rules;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Value: ['d' => .., 'm' => .., 'y' => ..]. Must be a real date, age 18 to 100. */
class AdultBirthday implements ValidationRule
{
    public function __construct(private readonly int $minAge = 18, private readonly int $maxAge = 100) {}

    public static function toDate(mixed $value): ?CarbonImmutable
    {
        if (! is_array($value)) {
            return null;
        }
        $d = (int) ($value['d'] ?? 0);
        $m = (int) ($value['m'] ?? 0);
        $y = (int) ($value['y'] ?? 0);
        if ($y < 1900 || ! checkdate($m, $d, $y)) {
            return null;
        }

        return CarbonImmutable::create($y, $m, $d)->startOfDay();
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = self::toDate($value);
        if ($date === null) {
            $fail(__('validation.birthday_invalid'));

            return;
        }
        $age = $date->diffInYears(CarbonImmutable::today());
        if ($age < $this->minAge) {
            $fail(__('validation.birthday_minor'));
        } elseif ($age > $this->maxAge) {
            $fail(__('validation.birthday_invalid'));
        }
    }
}
