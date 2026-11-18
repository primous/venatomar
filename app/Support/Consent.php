<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Reads the "pf_consent" cookie written by the cookie banner (plain JSON, not
 * encrypted so the browser script can read it). A consent given for an older
 * policy version is ignored.
 */
final class Consent
{
    public const COOKIE = 'pf_consent';

    private function __construct(private readonly array $choices) {}

    public static function fromRequest(Request $request): self
    {
        $raw = $request->cookies->get(self::COOKIE);
        $data = is_string($raw) ? json_decode(rawurldecode($raw), true) : null;

        if (! is_array($data) || ($data['v'] ?? null) !== config('venatomar.cookie_policy_version')) {
            return new self([]);
        }

        return new self([
            'statistics' => ($data['statistics'] ?? false) === true,
            'marketing' => ($data['marketing'] ?? false) === true,
        ]);
    }

    public function allows(string $category): bool
    {
        return $category === 'necessary' || ($this->choices[$category] ?? false);
    }
}
