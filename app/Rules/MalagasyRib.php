<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MalagasyRib implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Checksums::malagasyRib(preg_replace('/\D/', '', (string) $value))) {
            $fail(__('validation.rib'));
        }
    }
}
