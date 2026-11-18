<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Iban implements ValidationRule
{
    public function __construct(private readonly ?string $country = null, private readonly ?int $length = null) {}

    public static function normalize(?string $value): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $value));
    }

    /** Groups of 4 characters, as shown in the input mask. */
    public static function format(?string $value): string
    {
        return trim(chunk_split(self::normalize($value), 4, ' '));
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Checksums::iban(self::normalize($value), $this->country, $this->length)) {
            $fail(__('validation.iban', ['country' => $this->country ?? '', 'length' => $this->length ?? '']));
        }
    }
}
