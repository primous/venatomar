<?php

namespace App\Rules;

/**
 * Pure checksum algorithms for national identifiers and bank accounts.
 * Every method expects an already normalised value (upper case, no spaces).
 */
final class Checksums
{
    /** Spain: DNI (8 digits + letter) or NIE (X/Y/Z + 7 digits + letter), mod 23. */
    public static function spanishDniNie(string $v): bool
    {
        if (! preg_match('/^(\d{8}|[XYZ]\d{7})([A-Z])$/', $v, $m)) {
            return false;
        }
        $number = (int) strtr($m[1], ['X' => '0', 'Y' => '1', 'Z' => '2']);

        return 'TRWAGMYFPDXBNJZSQVHLCKE'[$number % 23] === $m[2];
    }

    /** IBAN: country prefix, exact length and ISO 13616 mod-97 == 1. */
    public static function iban(string $v, ?string $country = null, ?int $length = null): bool
    {
        if (! preg_match('/^[A-Z]{2}\d{2}[A-Z0-9]{10,30}$/', $v)) {
            return false;
        }
        if ($country !== null && ! str_starts_with($v, $country)) {
            return false;
        }
        if ($length !== null && strlen($v) !== $length) {
            return false;
        }

        return self::mod97(substr($v, 4).substr($v, 0, 4)) === 1;
    }

    /** Portugal: NIF, 9 digits, mod 11. */
    public static function portugueseNif(string $v): bool
    {
        if (! preg_match('/^[1235689]\d{8}$/', $v) && ! preg_match('/^(45|70|71|72|74|75|77|79|90|91|98|99)\d{7}$/', $v)) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum += (int) $v[$i] * (9 - $i);
        }
        $check = 11 - ($sum % 11);

        return ($check >= 10 ? 0 : $check) === (int) $v[8];
    }

    /** Greece: ΑΦΜ, 9 digits, powers of 2, mod 11 mod 10. */
    public static function greekAfm(string $v): bool
    {
        if (! preg_match('/^\d{9}$/', $v) || $v === '000000000') {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum += (int) $v[$i] * (2 ** (8 - $i));
        }

        return (($sum % 11) % 10) === (int) $v[8];
    }

    /** Italy: codice fiscale, 16 characters, odd/even control character. */
    public static function italianCodiceFiscale(string $v): bool
    {
        if (! preg_match('/^[A-Z]{6}[0-9LMNPQRSTUV]{2}[ABCDEHLMPRST][0-9LMNPQRSTUV]{2}[A-Z][0-9LMNPQRSTUV]{3}[A-Z]$/', $v)) {
            return false;
        }
        $odd = [
            '0' => 1, '1' => 0, '2' => 5, '3' => 7, '4' => 9, '5' => 13, '6' => 15, '7' => 17, '8' => 19, '9' => 21,
            'A' => 1, 'B' => 0, 'C' => 5, 'D' => 7, 'E' => 9, 'F' => 13, 'G' => 15, 'H' => 17, 'I' => 19, 'J' => 21,
            'K' => 2, 'L' => 4, 'M' => 18, 'N' => 20, 'O' => 11, 'P' => 3, 'Q' => 6, 'R' => 8, 'S' => 12, 'T' => 14,
            'U' => 16, 'V' => 10, 'W' => 22, 'X' => 25, 'Y' => 24, 'Z' => 23,
        ];
        $sum = 0;
        for ($i = 0; $i < 15; $i++) {
            $c = $v[$i];
            if ($i % 2 === 0) { // 1st, 3rd... (odd positions, 1-based)
                $sum += $odd[$c];
            } else {
                $sum += ctype_digit($c) ? (int) $c : ord($c) - 65;
            }
        }

        return chr(65 + ($sum % 26)) === $v[15];
    }

    /**
     * Slovakia: rodné číslo. 10 digits => divisible by 11 (legacy rule: remainder
     * 10 with check digit 0, numbers issued before 1985). 9 digits => born before 1954.
     * The date part must be plausible (month +50 for women, +20/+70 since 2004).
     */
    public static function slovakBirthNumber(string $v): bool
    {
        $v = str_replace('/', '', $v);
        if (! preg_match('/^\d{9,10}$/', $v)) {
            return false;
        }

        $yy = (int) substr($v, 0, 2);
        $mm = (int) substr($v, 2, 2);
        $dd = (int) substr($v, 4, 2);
        foreach ([70, 50, 20, 0] as $offset) {
            if ($mm > $offset && $mm <= $offset + 12) {
                $mm -= $offset;
                break;
            }
        }
        if ($mm < 1 || $mm > 12 || $dd < 1 || $dd > 31) {
            return false;
        }

        if (strlen($v) === 9) {
            return $yy < 54;
        }

        $base = (int) substr($v, 0, 9);
        $check = (int) $v[9];
        $mod = $base % 11;

        return ((int) $v % 11 === 0) || ($mod === 10 && $check === 0);
    }

    /** Slovenia: EMŠO, 13 digits, weights 7..2 twice, mod 11. */
    public static function sloveneEmso(string $v): bool
    {
        if (! preg_match('/^\d{13}$/', $v)) {
            return false;
        }
        $dd = (int) substr($v, 0, 2);
        $mm = (int) substr($v, 2, 2);
        if ($dd < 1 || $dd > 31 || $mm < 1 || $mm > 12) {
            return false;
        }
        $weights = [7, 6, 5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        foreach ($weights as $i => $w) {
            $sum += (int) $v[$i] * $w;
        }
        $m = $sum % 11;
        if ($m === 1) { // control would be 10: invalid
            return false;
        }

        return ($m === 0 ? 0 : 11 - $m) === (int) $v[12];
    }

    /** United Kingdom: National Insurance number format (no public checksum). */
    public static function ukNino(string $v): bool
    {
        return preg_match('/^(?!BG|GB|NK|KN|TN|NT|ZZ)[A-CEGHJ-PR-TW-Z][A-CEGHJ-NPR-TW-Z]\d{6}[A-D]$/', $v) === 1;
    }

    /** Madagascar: CIN, 12 digits. */
    public static function malagasyCin(string $v): bool
    {
        return preg_match('/^\d{12}$/', $v) === 1;
    }

    /**
     * Madagascar: RIB, 23 digits = bank (5) + branch (5) + account (11) + key (2),
     * key = 97 - ((89 * bank + 15 * branch + 3 * account) mod 97).
     */
    public static function malagasyRib(string $v): bool
    {
        if (! preg_match('/^(\d{5})(\d{5})(\d{11})(\d{2})$/', $v, $m)) {
            return false;
        }

        return self::ribKey($m[1], $m[2], $m[3]) === (int) $m[4];
    }

    public static function ribKey(string $bank, string $branch, string $account): int
    {
        $n = (89 * (int) $bank + 15 * (int) $branch + 3 * self::mod97($account)) % 97;

        return 97 - $n;
    }

    /** Mod 97 of an alphanumeric string (letters A=10 ... Z=35), computed in chunks. */
    public static function mod97(string $v): int
    {
        $digits = '';
        foreach (str_split($v) as $c) {
            $digits .= ctype_alpha($c) ? (string) (ord($c) - 55) : $c;
        }
        $rest = 0;
        foreach (str_split($digits, 7) as $chunk) {
            $rest = (int) ($rest.$chunk) % 97;
        }

        return $rest;
    }
}
