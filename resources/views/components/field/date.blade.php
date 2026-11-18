@props(['name', 'label', 'help' => null])
@php
    // Day / month / year inputs, ordered and separated as in the country config.
    $country = app(\App\Support\Country::class);
    $id = 'f-'.str_replace(['.', '_'], '-', $name);
    $hasError = $errors->has($name) || $errors->has($name.'.*');
    $parts = [
        'd' => ['label' => __('form.day'), 'max' => 2, 'ph' => __('form.day_ph'), 'ac' => 'bday-day', 'w' => 'w-16'],
        'm' => ['label' => __('form.month'), 'max' => 2, 'ph' => __('form.month_ph'), 'ac' => 'bday-month', 'w' => 'w-16'],
        'y' => ['label' => __('form.year'), 'max' => 4, 'ph' => __('form.year_ph'), 'ac' => 'bday-year', 'w' => 'w-24'],
    ];
@endphp
<fieldset {{ $attributes->merge(['class' => 'space-y-1.5']) }} @if ($hasError) aria-describedby="{{ $id }}-error" @endif>
    <legend class="mb-1.5 block text-[0.9375rem] leading-[1.4] font-semibold text-ink-900">{{ $label }}</legend>
    <div class="flex items-end gap-2">
        @foreach ($country->dateOrder as $i => $part)
            @if ($i > 0)
                <span class="pb-3 text-ink-300" aria-hidden="true">{{ $country->dateSeparator }}</span>
            @endif
            <div>
                <label for="{{ $id }}-{{ $part }}" class="sr-only">{{ $parts[$part]['label'] }}</label>
                <input id="{{ $id }}-{{ $part }}" type="text" inputmode="numeric" maxlength="{{ $parts[$part]['max'] }}"
                    autocomplete="{{ $parts[$part]['ac'] }}" placeholder="{{ $parts[$part]['ph'] }}"
                    wire:model.live.blur="{{ $name }}.{{ $part }}" aria-required="true"
                    @if ($hasError) aria-invalid="true" @endif
                    @class([
                        'h-14 rounded-sm border-b-2 bg-field px-3 text-center text-base text-ink-900 tabular-nums placeholder:text-ink-500 transition focus:bg-white focus:ring-1 focus:ring-line focus:outline-none',
                        $parts[$part]['w'],
                        'border-danger-600' => $hasError,
                        'border-ink-300/50 hover:border-ink-300 focus:border-brand-600' => ! $hasError,
                    ])>
            </div>
        @endforeach
    </div>
    @if ($help)
        <p class="text-small">{{ $help }}</p>
    @endif
    <div aria-live="polite">
        @error($name)
            <p id="{{ $id }}-error" role="alert" class="flex items-start gap-1.5 text-sm font-medium text-danger-600"><x-icon name="alert" class="mt-0.5 size-4 shrink-0" />{{ $message }}</p>
        @enderror
    </div>
</fieldset>
