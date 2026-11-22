@php
    use \App\Support\Venatomar as PF;
    $currencySymbol = (new \NumberFormatter($country->numberLocale.'@currency='.$country->currency, \NumberFormatter::CURRENCY))->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);
    $hasAmountBounds = $loan['amount_min'] !== null && $loan['amount_max'] !== null;
    $hasTermBounds = $loan['term_min'] !== null && $loan['term_max'] !== null;
    $isMg = $country->isMobileMoneyCountry();
    $threshold = $loan['debt_threshold'];
    $calc = $country->calculator();
@endphp
<div>
    <div class="rounded-lg bg-white p-5 shadow-pop md:p-8">
        <x-form.progress :percent="$progress" :step="$step" :total="5" />

        <form wire:submit="next" novalidate class="mt-6">
            {{-- Honeypot (hidden from humans and assistive tech) --}}
            <div class="absolute -left-[9999px] size-px overflow-hidden" aria-hidden="true">
                <label for="f-website">{{ __('form.website') }}</label>
                <input type="text" id="f-website" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            <div wire:key="step-{{ $step }}" class="step-enter space-y-6">
                <h2 id="step-heading" tabindex="-1" class="h3 focus:outline-none">{{ __('form.step'.$step.'.title') }}</h2>

                @if ($errors->has('form'))
                    <div role="alert" class="flex gap-3 rounded-md bg-danger-100 p-4 text-sm font-medium text-danger-600">
                        <x-icon name="alert" class="size-5 shrink-0" /> {{ $errors->first('form') }}
                    </div>
                @endif

                {{-- STEP 1 · Ingresos --}}
                @if ($step === 1)
                    <div class="space-y-2">
                        <label for="f-country" class="block text-xs font-semibold uppercase tracking-wider text-ink-700">{{ __('form.select_country') }}</label>
                        <select id="f-country" wire:model.live="selected_country" class="w-full h-11 px-3 bg-surface-1 border border-line rounded-lg text-sm font-medium text-ink-900 focus:bg-white focus:border-brand-600 focus:ring-1 focus:ring-brand-600 focus:outline-none">
                            <option value="es">🇪🇸 España (EUR €)</option>
                            <option value="sv">🇸🇻 El Salvador (USD $)</option>
                            <option value="uy">🇺🇾 Uruguay (UYU $)</option>
                            <option value="fr">🇫🇷 France (EUR €)</option>
                            <option value="gb">🇬🇧 United Kingdom (GBP £)</option>
                            <option value="de">🇩🇪 Deutschland (EUR €)</option>
                            <option value="it">🇮🇹 Italia (EUR €)</option>
                            <option value="pt">🇵🇹 Portugal (EUR €)</option>
                            <option value="nl">🇳🇱 Nederland (EUR €)</option>
                            <option value="sk">🇸🇰 Slovensko (EUR €)</option>
                            <option value="si">🇸🇮 Slovenija (EUR €)</option>
                            <option value="gr">🇬🇷 Ελλάδα (EUR €)</option>
                            <option value="mg">🇲🇬 Madagascar (MGA)</option>
                        </select>
                    </div>

                    <div class="space-y-3">
                        <x-field.input name="amount" :label="__('form.amount')" inputmode="numeric" :suffix="$currencySymbol"
                            :help="$hasAmountBounds ? __('form.amount_help', ['min' => PF::money($loan['amount_min'], $country), 'max' => PF::money($loan['amount_max'], $country)]) : null" />
                        <input type="range" wire:model.live.debounce.150ms="amount" min="{{ $calc['amount_min'] }}" max="{{ $calc['amount_max'] }}" step="{{ $calc['amount_step'] }}"
                            aria-label="{{ __('form.amount') }}" class="pf-range" style="--fill: {{ is_numeric($amount) ? max(0, min(100, ($amount - $calc['amount_min']) / max(1, $calc['amount_max'] - $calc['amount_min']) * 100)) : 0 }}%">
                        <div class="flex justify-between text-xs text-ink-500"><span>{{ PF::money($calc['amount_min'], $country) }}</span><span>{{ PF::money($calc['amount_max'], $country) }}</span></div>
                    </div>
                    <div class="space-y-3">
                        <x-field.input name="term_months" :label="__('form.term')" inputmode="numeric" :suffix="__('form.months_short')"
                            :help="$hasTermBounds ? __('form.term_help', ['min' => $loan['term_min'], 'max' => $loan['term_max']]) : null" />
                        <input type="range" wire:model.live.debounce.150ms="term_months" min="{{ $calc['term_min'] }}" max="{{ $calc['term_max'] }}" step="1"
                            aria-label="{{ __('form.term') }}" class="pf-range" style="--fill: {{ is_numeric($term_months) ? max(0, min(100, ($term_months - $calc['term_min']) / max(1, $calc['term_max'] - $calc['term_min']) * 100)) : 0 }}%">
                        <div class="flex justify-between text-xs text-ink-500"><span>{{ trans_choice('form.months', $calc['term_min'], ['count' => $calc['term_min']]) }}</span><span>{{ trans_choice('form.months', $calc['term_max'], ['count' => $calc['term_max']]) }}</span></div>
                    </div>
                    <x-field.input name="monthly_income" :label="__('form.monthly_income')" inputmode="numeric" :suffix="$currencySymbol" :help="__('form.monthly_income_help')" />
                    <x-field.select name="employment_status" :label="__('form.employment_status')" :options="$country->options('employment')" />
                    <x-field.radio-cards name="defaulter_list" :label="__('form.defaulter_list', ['bureau' => $country->creditBureau])" :help="__('form.defaulter_list_help')" />
                @endif

                {{-- STEP 2 · Datos personales --}}
                @if ($step === 2)
                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-field.input name="first_name" :label="__('form.first_name')" autocomplete="given-name" />
                        <x-field.input name="last_name" :label="__('form.last_name')" autocomplete="family-name" />
                    </div>
                    @if ($country->hasDocument())
                        <x-field.input name="document_number" :label="__('form.document.'.$country->code)" :optional="! $country->documentRequired()"
                            :help="__('form.document_help', ['example' => $country->document['example']])" autocomplete="off" autocapitalize="characters" spellcheck="false" />
                    @endif
                    <x-field.date name="birthday" :label="__('form.birthday')" :help="__('form.birthday_help')" />
                    <x-field.input name="email" type="email" inputmode="email" :label="__('form.email')" autocomplete="email" :help="__('form.email_help')" />
                    <x-field.input name="phone" type="tel" inputmode="tel" :label="__('form.phone')" :prefix="$country->phonePrefix" autocomplete="tel-national"
                        :placeholder="$country->get('phone_example')" />
                    <x-field.checkbox name="accept_privacy">
                        {!! __('form.accept_privacy', [
                            'terms' => '<a href="'.e(route('legal', ['page' => 'terms-and-conditions'])).'" target="_blank" rel="noopener" class="font-semibold text-brand-700 underline underline-offset-2">'.e(__('footer.terms')).'</a>',
                            'privacy' => '<a href="'.e(route('legal', ['page' => 'privacy-policy'])).'" target="_blank" rel="noopener" class="font-semibold text-brand-700 underline underline-offset-2">'.e(__('footer.privacy')).'</a>',
                        ]) !!}
                        <span class="sr-only">({{ __('form.new_tab') }})</span>
                    </x-field.checkbox>
                @endif

                {{-- STEP 3 · Domicilio (order from the country config) --}}
                @if ($step === 3)
                    @foreach ($country->addressOrder as $field)
                        @switch($field)
                            @case('geo_zip')
                                <x-field.input name="geo_zip" :label="__('form.geo_zip')" autocomplete="postal-code" :placeholder="$country->get('postal_example')"
                                    :inputmode="preg_match('/[A-Z]/', (string) $country->get('postal_example')) ? 'text' : 'numeric'" />
                                @break
                            @case('geo_province')
                                <x-field.select name="geo_province" :label="__('form.geo_province.'.$country->code)" :options="$country->regions()" />
                                @break
                            @case('geo_city')
                                <x-field.input name="geo_city" :label="__('form.geo_city')" autocomplete="address-level2" />
                                @break
                            @case('home_street')
                                <x-field.input name="home_street" :label="$country->get('street_label') === 'lot' ? __('form.home_street_lot') : __('form.home_street')" autocomplete="address-line1" />
                                @break
                            @case('home_number')
                                <x-field.input name="home_number" :label="__('form.home_number')" autocomplete="off" />
                                @break
                            @case('apartment_floor')
                                <x-field.input name="apartment_floor" :label="__('form.apartment_floor')" optional autocomplete="address-line2" />
                                @break
                        @endswitch
                    @endforeach
                @endif

                {{-- STEP 4 · Cuenta bancaria / Método de recepción de fondos --}}
                @if ($step === 4)
                    <p class="text-ink-500">{{ __('form.payout_method_intro') }}</p>

                    <x-field.radio-cards name="payout_method" :label="__('form.payout_method_question')" :options="$this->payoutOptions()" />

                    @if ($payout_method === 'mobile_money')
                        <div class="mt-4 space-y-4">
                            <x-field.select name="mm_operator" :label="__('form.mm_operator')" :options="$country->get('mobile_money_operators')" />
                            <x-field.input name="mm_number" type="tel" inputmode="tel" :label="__('form.mm_number')" :prefix="$country->phonePrefix" :placeholder="$country->get('phone_example')" />
                        </div>
                    @elseif ($payout_method === 'western_union')
                        <div class="mt-4 space-y-4">
                            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-ink-800">
                                <div class="flex items-center gap-2 font-bold text-amber-900 mb-1">
                                    <x-icon name="check-circle" class="size-5 text-amber-600 shrink-0" />
                                    <span>Western Union — Retiro en efectivo</span>
                                </div>
                                <p class="text-xs text-ink-600 leading-relaxed">
                                    {{ __('form.western_union_notice') }}
                                </p>
                            </div>
                            <x-field.input name="bank_account" :label="__('form.agency_city_optional')" placeholder="Ej: Sucursal Centro / Tu ciudad" />
                        </div>
                    @elseif ($payout_method === 'redpagos')
                        <div class="mt-4 space-y-4">
                            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-ink-800">
                                <div class="flex items-center gap-2 font-bold text-emerald-900 mb-1">
                                    <x-icon name="check-circle" class="size-5 text-emerald-600 shrink-0" />
                                    <span>REDPAGOS — Cobro en efectivo</span>
                                </div>
                                <p class="text-xs text-ink-600 leading-relaxed">
                                    {{ __('form.redpagos_notice') }}
                                </p>
                            </div>
                            <x-field.input name="bank_account" :label="__('form.redpagos_optional')" placeholder="Ej: Retiro en sucursal o N° de cuenta / tarjeta" />
                        </div>
                    @elseif ($payout_method === 'abitab')
                        <div class="mt-4 space-y-4">
                            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-sm text-ink-800">
                                <div class="flex items-center gap-2 font-bold text-blue-900 mb-1">
                                    <x-icon name="check-circle" class="size-5 text-blue-600 shrink-0" />
                                    <span>ABITAB — Cobro en agencia</span>
                                </div>
                                <p class="text-xs text-ink-600 leading-relaxed">
                                    {{ __('form.abitab_notice') }}
                                </p>
                            </div>
                            <x-field.input name="bank_account" :label="__('form.abitab_optional')" placeholder="Ej: Sucursal Abitab Centro o cualquier local" />
                        </div>
                    @elseif ($payout_method === 'midinero')
                        <div class="mt-4 space-y-4">
                            <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-200 text-sm text-ink-800">
                                <div class="flex items-center gap-2 font-bold text-indigo-900 mb-1">
                                    <x-icon name="check-circle" class="size-5 text-indigo-600 shrink-0" />
                                    <span>MIDINERO — Abono a tarjeta / cuenta</span>
                                </div>
                                <p class="text-xs text-ink-600 leading-relaxed">
                                    {{ __('form.midinero_notice') }}
                                </p>
                            </div>
                            <x-field.input name="bank_account" :label="__('form.midinero_number')" :help="__('form.midinero_number_help')" placeholder="Ej: 16 dígitos de tu tarjeta MiDinero" maxlength="34" />
                        </div>
                    @elseif ($country->code === 'gb' && $payout_method === 'uk_account')
                        <div class="grid gap-6 sm:grid-cols-2 mt-4">
                            <x-field.input name="sort_code" inputmode="numeric" :label="__('form.sort_code')" placeholder="12-34-56" maxlength="8" />
                            <x-field.input name="uk_account_number" inputmode="numeric" :label="__('form.uk_account_number')" maxlength="8" />
                        </div>
                    @else
                        {{-- Transferencia bancaria (por defecto) --}}
                        <div class="mt-4 space-y-4">
                            <x-field.input name="bank_account" :label="__('form.account_or_iban')" :help="__('form.account_or_iban_help')"
                                placeholder="Introduce tu número de cuenta bancaria o IBAN"
                                spellcheck="false" maxlength="42" />
                        </div>
                    @endif

                    <p class="text-small flex items-start gap-2 mt-4"><x-icon name="lock" class="size-4 shrink-0 text-brand-600" /> {{ __('form.bank_secure') }}</p>
                @endif

                {{-- STEP 5 · Información adicional --}}
                @if ($step === 5)
                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-field.select name="gender" :label="__('form.gender')" :options="$country->options('gender')" />
                        <x-field.select name="marital_status" :label="__('form.marital_status')" :options="$country->options('marital')" />
                    </div>
                    <x-field.select name="loan_purpose" :label="__('form.loan_purpose')" :options="$country->options('loan_purpose')" />
                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-field.select name="company_sector" :label="__('form.company_sector')" :options="$country->options('company_sector')" />
                        <x-field.select name="education" :label="__('form.education')" :options="$country->options('education')" />
                    </div>
                    <x-field.select name="home_status" :label="__('form.home_status')" :options="$country->options('home')" />
                    <x-field.input name="regular_expenses" inputmode="numeric" :label="__('form.regular_expenses')" :suffix="$currencySymbol" :help="__('form.regular_expenses_help')" />
                    <x-field.radio-cards name="has_debt"
                        :label="$threshold !== null ? __('form.has_debt', ['amount' => PF::money($threshold, $country)]) : __('form.has_debt_generic')" />
                    <x-field.radio-cards name="has_car" :label="$country->get('vehicle_includes_motorbike') ? __('form.has_car_moto') : __('form.has_car')" />
                    @if ((string) $has_car === '1')
                        <x-field.input name="car_number" :label="__('form.car_number')" :placeholder="$country->get('plate_example')" autocapitalize="characters" spellcheck="false" />
                    @endif
                @endif

                <x-form.nav :step="$step" :last="$step === 5" />
            </div>
        </form>
    </div>

    <p class="text-small mt-4 flex items-center justify-center gap-2"><x-icon name="lock" class="size-4 text-brand-600" /> {{ __('form.secure_note') }}</p>
    <x-legal-example compact class="mt-6" />
</div>
