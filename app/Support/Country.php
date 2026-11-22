<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Read-only view over config/countries.php for one country.
 */
final class Country
{
    public readonly string $code;
    public readonly string $locale;
    public readonly string $name;
    public readonly string $currency;
    public readonly string $numberLocale;
    public readonly string $phonePrefix;
    public readonly string $phoneRegex;
    public readonly string $postalRegex;
    public readonly ?array $document;
    public readonly array $bank;
    public readonly string $creditBureau;
    public readonly array $dateOrder;
    public readonly string $dateSeparator;
    public readonly array $addressOrder;
    public readonly string $plateRegex;
    public readonly array $config;

    public function __construct(string $code)
    {
        $config = config('countries.'.$code);
        if (! is_array($config)) {
            throw new InvalidArgumentException("Unknown country [$code].");
        }

        $this->code = $code;
        $this->locale = (string) (array_search($code, Venatomar::locales(), true) ?: $code);
        $this->config = $config;
        $this->name = $config['name'];
        $this->currency = $config['currency'];
        $this->numberLocale = $config['number_locale'];
        $this->phonePrefix = $config['phone_prefix'];
        $this->phoneRegex = $config['phone_regex'];
        $this->postalRegex = $config['postal_regex'];
        $this->document = $config['document'];
        $this->bank = $config['bank'];
        $this->creditBureau = $config['credit_bureau'];
        $this->dateOrder = $config['date_order'];
        $this->dateSeparator = $config['date_separator'];
        $this->addressOrder = $config['address_order'];
        $this->plateRegex = $config['plate_regex'];
    }

    public static function forLocale(string $locale): self
    {
        $code = Venatomar::locales()[$locale] ?? ($locale === 'fr' ? 'fr' : null);
        if ($code === null) {
            throw new InvalidArgumentException("Unknown locale [$locale].");
        }

        return new self($code);
    }

    public static function current(): self
    {
        $locale = app()->getLocale();

        return self::forLocale(Venatomar::isLocale($locale) ? $locale : config('venatomar.default_locale'));
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }

    /** @return array<string,string> code => label */
    public function regions(): array
    {
        return config('regions.'.$this->code, []);
    }

    /** Translated option list for a group (employment, education, marital, home...). */
    public function options(string $group): array
    {
        $keys = match ($group) {
            'employment', 'education', 'marital', 'home' => $this->config[$group],
            'gender' => ['female', 'male', 'other'],
            'loan_purpose' => ['help', 'celebration', 'car', 'shopping', 'debts', 'renovation', 'education', 'bills', 'travel', 'unexpected', 'medical', 'other'],
            'company_sector' => ['agriculture', 'industry', 'construction', 'retail', 'hospitality', 'transport', 'finance', 'technology', 'health', 'education', 'public', 'services', 'other'],
            'payout_method' => ['transferencia', 'western_union', 'redpagos', 'abitab', 'midinero'],
            default => throw new InvalidArgumentException("Unknown option group [$group]."),
        };

        $out = [];
        foreach ($keys as $key) {
            $out[$key] = __('opt.'.$group.'.'.$key);
        }

        return $out;
    }

    public function documentRequired(): bool
    {
        return (bool) ($this->document['required'] ?? false);
    }

    public function hasDocument(): bool
    {
        return $this->document !== null;
    }

    public function isMobileMoneyCountry(): bool
    {
        return ($this->bank['type'] ?? null) === 'mg';
    }

    public function loan(): array
    {
        return Venatomar::loan($this->code);
    }

    /** Slider range for the calculator: configured loan bounds, else the per-currency defaults. */
    public function calculator(): array
    {
        $loan = $this->loan();
        $d = config('venatomar.calculator.'.$this->currency, config('venatomar.calculator.EUR'));
        $hasAmount = $loan['amount_min'] !== null && $loan['amount_max'] !== null;
        $hasTerm = $loan['term_min'] !== null && $loan['term_max'] !== null;
        $c = [
            'amount_min' => (int) ($hasAmount ? $loan['amount_min'] : $d['amount_min']),
            'amount_max' => (int) ($hasAmount ? $loan['amount_max'] : $d['amount_max']),
            'amount_step' => max(1, (int) ($hasAmount ? ($loan['amount_step'] ?? $d['amount_step']) : $d['amount_step'])),
            'term_min' => (int) ($hasTerm ? $loan['term_min'] : $d['term_min']),
            'term_max' => (int) ($hasTerm ? $loan['term_max'] : $d['term_max']),
        ];
        $c['amount'] = max($c['amount_min'], min($c['amount_max'], (int) $d['amount_default']));
        $c['term'] = max($c['term_min'], min($c['term_max'], (int) $d['term_default']));

        return $c;
    }

    public function example(): ?array
    {
        return Venatomar::example($this->code);
    }
}
