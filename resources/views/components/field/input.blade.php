@props([
    'name',                 // Livewire property
    'label',
    'help' => null,
    'type' => 'text',
    'inputmode' => null,    // numeric | tel | email | text
    'prefix' => null,
    'suffix' => null,       // e.g. currency
    'autocomplete' => 'off',
    'live' => false,        // wire:model.live (debounced) instead of .blur
    'optional' => false,
    'hiddenLabel' => false, // label only for screen readers, shown as placeholder
])
@php
    $id = 'f-'.str_replace(['.', '_'], '-', $name);
    $hasError = $errors->has($name);
    $describedBy = trim(($help ? $id.'-help ' : '').($hasError ? $id.'-error' : ''));
    $model = $live ? 'wire:model.live.debounce.400ms' : 'wire:model.live.blur';
@endphp
<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    <label for="{{ $id }}" class="{{ $hiddenLabel ? 'sr-only' : 'block text-[0.9375rem] leading-[1.4] font-semibold text-ink-900' }}">
        {{ $label }}
        @if ($optional)<span class="font-normal text-ink-500">({{ __('form.optional') }})</span>@endif
    </label>
    <div @class([
        'flex h-14 items-stretch overflow-hidden rounded-sm border-b-2 bg-field transition focus-within:bg-white focus-within:ring-1 focus-within:ring-line',
        'border-danger-600' => $hasError,
        'border-ink-300/50 hover:border-ink-300 focus-within:border-brand-600' => ! $hasError,
    ])>
        @if ($prefix)
            <span class="flex items-center pl-4 pr-1 text-ink-500 tabular-nums" aria-hidden="true">{{ $prefix }}</span>
        @endif
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" {{ $model }}="{{ $name }}"
            @if ($hiddenLabel) placeholder="{{ $label }}" @endif
            @if ($inputmode) inputmode="{{ $inputmode }}" @endif
            autocomplete="{{ $autocomplete }}"
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @unless ($optional) aria-required="true" @endunless
            {{ $attributes->except('class') }}
            class="min-w-0 flex-1 bg-transparent px-4 text-base text-ink-900 placeholder:text-ink-500 focus:outline-none {{ $inputmode === 'numeric' ? 'tabular-nums' : '' }}">
        @if ($suffix)
            <span class="flex items-center pr-4 pl-1 font-semibold text-ink-500" aria-hidden="true">{{ $suffix }}</span>
        @endif
    </div>
    @if ($help)
        <p id="{{ $id }}-help" class="text-small">{{ $help }}</p>
    @endif
    <div aria-live="polite">
        @error($name)
            <p id="{{ $id }}-error" role="alert" class="flex items-start gap-1.5 text-sm font-medium text-danger-600">
                <x-icon name="alert" class="mt-0.5 size-4 shrink-0" />{{ $message }}
            </p>
        @enderror
    </div>
</div>
