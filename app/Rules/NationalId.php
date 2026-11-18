<?php

namespace App\Rules;

use App\Support\Country;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Validates the identity document configured for the country. */
class NationalId implements ValidationRule
{
    public function __construct(private readonly Country $country) {}

    public static function normalize(?string $value): string
    {
        return strtoupper(preg_replace('/[\s.\-]/', '', (string) $value));
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::check((string) ($this->country->document['type'] ?? ''), self::normalize($value))) {
            $fail(__('validation.document_number', ['example' => $this->country->document['example'] ?? '']));
        }
    }

    public static function check(string $type, string $v): bool
    {
        return match ($type) {
            'es_dni_nie' => Checksums::spanishDniNie($v),
            'gb_nino' => Checksums::ukNino($v),
            'it_codice_fiscale' => Checksums::italianCodiceFiscale($v),
            'gr_afm' => Checksums::greekAfm($v),
            'pt_nif' => Checksums::portugueseNif($v),
            'sk_rodne_cislo' => Checksums::slovakBirthNumber($v),
            'si_emso' => Checksums::sloveneEmso($v),
            'mg_cin' => Checksums::malagasyCin($v),
            'sv_dui' => Checksums::svDui($v),
            'uy_ci' => Checksums::uyCi($v),
            default => false,
        };
    }
}
