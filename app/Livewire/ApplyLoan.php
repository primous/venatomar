<?php

namespace App\Livewire;

use App\Actions\SubmitLead;
use App\Http\Middleware\SetLocale;
use App\Rules\AdultBirthday;
use App\Rules\CountryFormat;
use App\Rules\Iban;
use App\Rules\MalagasyRib;
use App\Rules\NationalId;
use App\Support\Country;
use App\Support\Venatomar;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * 5-step loan request. Each step is validated server-side before moving on;
 * the whole request is re-validated on submit (never trust the client).
 * The draft lives in the server session (not in the database).
 */
class ApplyLoan extends Component
{
    public const STEPS = 5;

    /** Weight of each step in "Perfil completado" (sums to 100). */
    public const STEP_WEIGHTS = [1 => 20, 2 => 30, 3 => 20, 4 => 15, 5 => 15];

    /** Fields that are kept in the session draft. */
    public const DRAFT_FIELDS = [
        'selected_country',
        'amount', 'term_months', 'monthly_income', 'employment_status', 'defaulter_list',
        'first_name', 'last_name', 'document_number', 'birthday', 'email', 'phone', 'accept_privacy',
        'geo_zip', 'geo_province', 'geo_city', 'home_street', 'home_number', 'apartment_floor',
        'payout_method', 'bank_account', 'sort_code', 'uk_account_number', 'mm_operator', 'mm_number',
        'gender', 'loan_purpose', 'marital_status', 'company_sector', 'education', 'home_status',
        'regular_expenses', 'has_debt', 'has_car', 'car_number',
    ];

    public string $selected_country = '';

    #[Locked]
    public string $locale = 'es';

    #[Locked]
    public string $admin_email = 'info@venatomar.com';

    #[Locked]
    public string $channel = 'info';

    #[Locked]
    public string $thanks_route = 'apply.thanks';

    #[Locked]
    public int $startedAt = 0;

    #[Locked]
    public int $maxStep = 1;

    public int $step = 1;

    /** Honeypot: hidden from humans, must stay empty. */
    public string $website = '';

    // Step 1 · Ingresos
    public $amount = null;
    public $term_months = null;
    public $monthly_income = null;
    public string $employment_status = '';
    public $defaulter_list = null;

    // Step 2 · Datos personales
    public string $first_name = '';
    public string $last_name = '';
    public string $document_number = '';
    public array $birthday = ['d' => '', 'm' => '', 'y' => ''];
    public string $email = '';
    public string $phone = '';
    public bool $accept_privacy = false;

    // Step 3 · Domicilio
    public string $geo_zip = '';
    public string $geo_province = '';
    public string $geo_city = '';
    public string $home_street = '';
    public string $home_number = '';
    public string $apartment_floor = '';

    // Step 4 · Cuenta bancaria / Método de pago
    public string $payout_method = 'transferencia';
    public string $bank_account = '';
    public string $sort_code = '';
    public string $uk_account_number = '';
    public string $mm_operator = '';
    public string $mm_number = '';

    // Step 5 · Información adicional
    public string $gender = '';
    public string $loan_purpose = '';
    public string $marital_status = '';
    public string $company_sector = '';
    public string $education = '';
    public string $home_status = '';
    public $regular_expenses = null;
    public $has_debt = null;
    public $has_car = null;
    public string $car_number = '';

    public function mount(?string $locale = null, ?string $channel = null, ?string $adminEmail = null, ?string $thanksRoute = null): void
    {
        $this->locale = $locale ?? app()->getLocale();
        if ($channel) {
            $this->channel = $channel;
        }
        if ($adminEmail) {
            $this->admin_email = $adminEmail;
        } elseif ($this->channel === 'contact') {
            $this->admin_email = 'contact@venatomar.com';
        } elseif ($this->channel === 'ayuda') {
            $this->admin_email = 'ayuda@venatomar.com';
        }
        if ($thanksRoute) {
            $this->thanks_route = $thanksRoute;
        }
        $this->startedAt = now()->getTimestamp();
        $country = $this->country();
        if (! array_key_exists($this->payout_method, $this->payoutOptions())) {
            $this->payout_method = 'transferencia';
        }

        $this->restoreDraft();
        if (empty($this->selected_country)) {
            $code = Venatomar::locales()[$this->locale] ?? $this->locale;
            $this->selected_country = config('countries.'.$code) ? $code : 'es';
        }
        if (empty($this->payout_method) || ! array_key_exists($this->payout_method, $this->payoutOptions())) {
            $this->payout_method = 'transferencia';
        }

        $this->prefillFromCalculator();
    }

    public function updatedSelectedCountry(string $value): void
    {
        if (config('countries.'.$value)) {
            $this->selected_country = $value;
            $newCountry = new Country($value);
            $calc = $newCountry->calculator();
            $this->amount = $calc['amount'];
            $this->term_months = $calc['term'];
            $this->geo_province = '';
            $this->payout_method = $newCountry->isMobileMoneyCountry() ? 'mobile_money' : 'transferencia';
            $this->saveDraft();
        }
    }

    /** Home calculator link (?amount=&term=): applied only inside the calculator range. */
    private function prefillFromCalculator(): void
    {
        $c = $this->country()->calculator();
        $amount = filter_var(request()->query('amount'), FILTER_VALIDATE_INT);
        $term = filter_var(request()->query('term'), FILTER_VALIDATE_INT);

        if ($amount !== false && $amount >= $c['amount_min'] && $amount <= $c['amount_max']) {
            $this->amount = $amount;
        }
        if ($term !== false && $term >= $c['term_min'] && $term <= $c['term_max']) {
            $this->term_months = $term;
        }
    }

    public function hydrate(): void
    {
        SetLocale::apply($this->locale);
    }

    public function country(): Country
    {
        if (! empty($this->selected_country) && config('countries.'.$this->selected_country)) {
            return new Country($this->selected_country);
        }

        $code = Venatomar::locales()[$this->locale] ?? $this->locale;
        if (config('countries.'.$code)) {
            return new Country($code);
        }

        return Country::forLocale($this->locale);
    }

    /* ------------------------------------------------------------------ */
    /*  Rules                                                              */
    /* ------------------------------------------------------------------ */

    public function rulesForStep(int $step): array
    {
        $c = $this->country();
        $loan = $c->loan();

        return match ($step) {
            1 => [
                'amount' => ['required', 'integer',
                    'min:'.($loan['amount_min'] ?? 1),
                    'max:'.($loan['amount_max'] ?? 100000000)],
                'term_months' => ['required', 'integer',
                    'min:'.($loan['term_min'] ?? 1),
                    'max:'.($loan['term_max'] ?? 600)],
                'monthly_income' => ['required', 'integer', 'min:0', 'max:100000000'],
                'employment_status' => ['required', Rule::in(array_keys($c->options('employment')))],
                'defaulter_list' => ['required', Rule::in(['0', '1', 0, 1])],
            ],
            2 => array_filter([
                'first_name' => ['required', 'string', 'min:2', 'max:80', 'regex:/^[\pL\pM\' .\-]+$/u'],
                'last_name' => ['required', 'string', 'min:2', 'max:120', 'regex:/^[\pL\pM\' .\-]+$/u'],
                'document_number' => $c->hasDocument()
                    ? [$c->documentRequired() ? 'required' : 'nullable', 'string', 'max:20', new NationalId($c)]
                    : null,
                'birthday' => ['required', 'array', new AdultBirthday],
                'email' => ['required', 'string', 'max:180', 'email:rfc'],
                'phone' => ['required', 'string', 'max:24', CountryFormat::phone($c)],
                'accept_privacy' => ['accepted'],
            ]),
            3 => [
                'geo_zip' => ['required', 'string', 'max:12', CountryFormat::postal($c)],
                'geo_province' => ['required', Rule::in(array_map('strval', array_keys($c->regions())))],
                'geo_city' => ['required', 'string', 'min:2', 'max:120'],
                'home_street' => ['required', 'string', 'min:2', 'max:160'],
                'home_number' => ['required', 'string', 'max:20'],
                'apartment_floor' => ['nullable', 'string', 'max:40'],
            ],
            4 => $this->payoutRules($c),
            5 => [
                'gender' => ['required', Rule::in(array_keys($c->options('gender')))],
                'loan_purpose' => ['required', Rule::in(array_keys($c->options('loan_purpose')))],
                'marital_status' => ['required', Rule::in(array_keys($c->options('marital')))],
                'company_sector' => ['required', Rule::in(array_keys($c->options('company_sector')))],
                'education' => ['required', Rule::in(array_keys($c->options('education')))],
                'home_status' => ['required', Rule::in(array_keys($c->options('home')))],
                'regular_expenses' => ['required', 'integer', 'min:0', 'max:100000000'],
                'has_debt' => ['required', Rule::in(['0', '1', 0, 1])],
                'has_car' => ['required', Rule::in(['0', '1', 0, 1])],
                'car_number' => (string) $this->has_car === '1'
                    ? ['required', 'string', 'max:20', CountryFormat::plate($c)]
                    : ['nullable'],
            ],
        };
    }

    public function payoutOptions(): array
    {
        $c = $this->country();
        if ($c->isMobileMoneyCountry()) {
            return [
                'transferencia' => __('opt.payout_method.transferencia'),
                'mobile_money' => __('opt.payout_method.mobile_money'),
                'western_union' => 'Western Union',
            ];
        }

        return [
            'transferencia' => __('opt.payout_method.transferencia'),
            'western_union' => 'Western Union',
            'redpagos' => 'REDPAGOS',
            'abitab' => 'ABITAB',
            'midinero' => 'MIDINERO',
        ];
    }

    private function payoutRules(Country $c): array
    {
        $allowed = array_unique(array_merge(
            array_keys($this->payoutOptions()),
            ['transferencia', 'western_union', 'redpagos', 'abitab', 'midinero', 'iban', 'account', 'rib', 'uk_account', 'mobile_money']
        ));

        return [
            'payout_method' => ['required', Rule::in($allowed)],
        ];
    }

    public function allRules(): array
    {
        $rules = [];
        for ($s = 1; $s <= self::STEPS; $s++) {
            $rules += $this->rulesForStep($s);
        }

        return $rules;
    }

    protected function validationAttributes(): array
    {
        $attrs = [];
        foreach (self::DRAFT_FIELDS as $field) {
            $attrs[$field] = __('attr.'.$field);
        }

        return $attrs;
    }

    /* ------------------------------------------------------------------ */
    /*  Live validation & normalisation                                    */
    /* ------------------------------------------------------------------ */

    public function updated(string $property): void
    {
        $this->normalize($property);

        $root = explode('.', $property)[0];
        $rules = $this->rulesForStep($this->step);

        if ($root === 'birthday') {
            if (collect($this->birthday)->every(fn ($v) => $v !== '' && $v !== null)) {
                $this->validateOnly('birthday', $rules);
            }
        } elseif (array_key_exists($root, $rules)) {
            $this->validateOnly($root, $rules);
        }

        if ($root === 'has_car' && (string) $this->has_car === '0') {
            $this->car_number = '';
            $this->resetErrorBag('car_number');
        }
        if ($root === 'payout_method') {
            $this->resetErrorBag(['bank_account', 'sort_code', 'uk_account_number', 'mm_operator', 'mm_number']);
        }

        $this->saveDraft();
    }

    private function normalize(string $property): void
    {
        $c = $this->country();
        match (explode('.', $property)[0]) {
            'document_number' => $this->document_number = NationalId::normalize($this->document_number),
            'bank_account' => $this->bank_account = in_array($this->payout_method, ['transferencia', 'iban'], true) && preg_match('/^[A-Za-z]{2}\d/i', trim($this->bank_account))
                ? Iban::format($this->bank_account)
                : trim(preg_replace('/\s+/', ' ', (string) $this->bank_account)),
            'geo_zip' => $this->geo_zip = CountryFormat::normalizePostal($this->geo_zip),
            'car_number' => $this->car_number = CountryFormat::normalizePlate($this->car_number),
            'email' => $this->email = mb_strtolower(trim($this->email)),
            'first_name', 'last_name', 'geo_city', 'home_street', 'home_number', 'apartment_floor', 'phone', 'mm_number' => $this->{$property} = trim(preg_replace('/\s+/', ' ', (string) $this->{$property})),
            'amount', 'term_months', 'monthly_income', 'regular_expenses' => $this->{$property} = self::toInt($this->{$property}),
            default => null,
        };
    }

    public static function toInt(mixed $v): int|string|null
    {
        if ($v === null || $v === '') {
            return null;
        }
        $digits = preg_replace('/[^\d]/', '', (string) $v);

        return $digits === '' ? (string) $v : (int) $digits;
    }

    /* ------------------------------------------------------------------ */
    /*  Navigation                                                         */
    /* ------------------------------------------------------------------ */

    public function next(): void
    {
        $this->validate($this->rulesForStep($this->step));

        if ($this->step >= self::STEPS) {
            $this->submit();

            return;
        }

        $this->step++;
        $this->maxStep = max($this->maxStep, $this->step);
        $this->saveDraft();
        $this->dispatch('step-changed', step: $this->step);
    }

    public function back(): void
    {
        if ($this->step > 1) {
            $this->step--;
            $this->resetErrorBag();
            $this->saveDraft();
            $this->dispatch('step-changed', step: $this->step);
        }
    }

    public function submit(): void
    {
        // 1. Anti-spam: honeypot + minimal delay since the form was displayed.
        if ($this->website !== '' || (now()->getTimestamp() - $this->startedAt) < (int) config('venatomar.antispam.min_seconds')) {
            $this->addError('form', __('form.error_generic'));

            return;
        }

        // 2. Re-validate EVERYTHING; jump to the first step in error.
        for ($s = 1; $s <= self::STEPS; $s++) {
            try {
                $this->validate($this->rulesForStep($s));
            } catch (ValidationException $e) {
                $this->step = $s;
                $this->dispatch('step-changed', step: $s);
                throw $e;
            }
        }

        // 3. Rate limits (per IP and per e-mail).
        $ipKey = 'apply:ip:'.request()->ip();
        $emailKey = 'apply:email:'.hash('sha256', mb_strtolower($this->email));
        if (RateLimiter::tooManyAttempts($ipKey, (int) config('venatomar.antispam.per_ip_per_hour'))
            || RateLimiter::tooManyAttempts($emailKey, (int) config('venatomar.antispam.per_email_per_day'))) {
            $this->addError('form', __('form.error_rate_limited'));

            return;
        }

        // 4. Persist FIRST, then queue the e-mails (see SubmitLead).
        $lead = app(SubmitLead::class)->handle($this->country(), $this->payload(), [
            'locale' => Venatomar::isLocale($this->locale) ? $this->locale : $this->country()->locale,
            'ip_address' => request()->ip(),
            'user_agent' => mb_substr((string) request()->userAgent(), 0, 500),
            'utm' => session('utm'),
            'admin_email' => $this->admin_email,
            'channel' => $this->channel,
        ]);

        RateLimiter::hit($ipKey, 3600);
        RateLimiter::hit($emailKey, 86400);

        session()->forget($this->draftKey());
        session()->put('last_lead_id', $lead->id);

        $this->redirectRoute($this->thanks_route, ['locale' => $this->locale]);
    }

    /** Clean, normalised data for the Lead. */
    public function payload(): array
    {
        $c = $this->country();
        $birthday = AdultBirthday::toDate($this->birthday);
        $isMm = $this->payout_method === 'mobile_money';

        $rawBank = trim((string) $this->bank_account);
        $bank = match (true) {
            $rawBank === '' => null,
            $isMm => null,
            $this->payout_method === 'midinero' => 'MiDinero: '.$rawBank,
            $c->isMobileMoneyCountry() => preg_replace('/\D/', '', $rawBank),
            ($c->bank['type'] ?? 'iban') === 'account' => $rawBank,
            default => preg_match('/^[A-Za-z]{2}\d/i', $rawBank) ? Iban::normalize($rawBank) : $rawBank,
        };

        return [
            'amount' => (int) $this->amount,
            'term_months' => (int) $this->term_months,
            'monthly_income' => (int) $this->monthly_income,
            'employment_status' => $this->employment_status,
            'defaulter_list' => (string) $this->defaulter_list === '1',
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'document_number' => $c->hasDocument() && $this->document_number !== '' ? NationalId::normalize($this->document_number) : null,
            'birthday' => $birthday?->toDateString(),
            'email' => mb_strtolower($this->email),
            'phone' => $c->phonePrefix.CountryFormat::normalizePhone($this->phone, $c),
            'geo_zip' => CountryFormat::normalizePostal($this->geo_zip),
            'geo_province' => $this->geo_province,
            'geo_city' => $this->geo_city,
            'home_street' => $this->home_street,
            'home_number' => $this->home_number,
            'apartment_floor' => $this->apartment_floor !== '' ? $this->apartment_floor : null,
            'payout_method' => $this->payout_method,
            'bank_account' => $bank,
            'mm_operator' => $isMm && $this->mm_operator !== '' ? $this->mm_operator : null,
            'mm_number' => $isMm && $this->mm_number !== '' ? $c->phonePrefix.CountryFormat::normalizePhone($this->mm_number, $c) : null,
            'gender' => $this->gender,
            'loan_purpose' => $this->loan_purpose,
            'marital_status' => $this->marital_status,
            'company_sector' => $this->company_sector,
            'education' => $this->education,
            'home_status' => $this->home_status,
            'regular_expenses' => (int) $this->regular_expenses,
            'has_debt' => (string) $this->has_debt === '1',
            'has_car' => (string) $this->has_car === '1',
            'car_number' => (string) $this->has_car === '1' ? CountryFormat::normalizePlate($this->car_number) : null,
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  "Perfil completado"                                                */
    /* ------------------------------------------------------------------ */

    public function progress(): int
    {
        $data = $this->only(self::DRAFT_FIELDS);
        $total = 0.0;

        foreach (self::STEP_WEIGHTS as $step => $weight) {
            $rules = $this->rulesForStep($step);
            // Optional / preselected fields do not count until completed; car_number only counts when required.
            unset($rules['apartment_floor']);
            if ($step === 4 && $this->maxStep < 5) {
                unset($rules['payout_method']);
            }
            if ((string) $this->has_car !== '1') {
                unset($rules['car_number']);
            }
            if ($rules === []) {
                continue;
            }
            $per = $weight / count($rules);
            foreach ($rules as $field => $rule) {
                if (Validator::make($data, [$field => $rule])->passes()) {
                    $total += $per;
                }
            }
        }

        return (int) min(100, round($total));
    }

    /* ------------------------------------------------------------------ */
    /*  Session draft                                                      */
    /* ------------------------------------------------------------------ */

    private function draftKey(): string
    {
        return 'apply_draft.'.$this->channel.'.'.$this->locale;
    }

    private function saveDraft(): void
    {
        session()->put($this->draftKey(), [
            'fields' => $this->only(self::DRAFT_FIELDS),
            'step' => $this->step,
            'max_step' => $this->maxStep,
        ]);
    }

    private function restoreDraft(): void
    {
        $draft = session($this->draftKey());
        if (! is_array($draft)) {
            return;
        }
        foreach ($draft['fields'] ?? [] as $field => $value) {
            if (in_array($field, self::DRAFT_FIELDS, true)) {
                $this->{$field} = $value;
            }
        }
        $this->maxStep = max(1, min(self::STEPS, (int) ($draft['max_step'] ?? 1)));
        $this->step = max(1, min($this->maxStep, (int) ($draft['step'] ?? 1)));
    }

    public function render()
    {
        $c = $this->country();

        return view('livewire.apply-loan', [
            'country' => $c,
            'loan' => $c->loan(),
            'progress' => $this->progress(),
        ]);
    }
}
