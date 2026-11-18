@props(['name', 'label', 'options' => [], 'help' => null, 'placeholder' => null])
@php
    $id = 'f-'.str_replace(['.', '_'], '-', $name);
    $hasError = $errors->has($name);
    $describedBy = trim(($help ? $id.'-help ' : '').($hasError ? $id.'-error' : ''));
@endphp
<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    <label for="{{ $id }}" class="block text-[0.9375rem] leading-[1.4] font-semibold text-ink-900">{{ $label }}</label>
    <div class="relative">
        <select id="{{ $id }}" name="{{ $name }}" wire:model.live="{{ $name }}"
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            aria-required="true"
            @class([
                'h-14 w-full appearance-none rounded-sm border-b-2 bg-field pr-10 pl-4 text-base text-ink-900 transition focus:bg-white focus:ring-1 focus:ring-line focus:outline-none',
                'border-danger-600' => $hasError,
                'border-ink-300/50 hover:border-ink-300 focus:border-brand-600' => ! $hasError,
            ])>
            <option value="">{{ $placeholder ?? __('form.select') }}</option>
            @foreach ($options as $value => $text)
                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
        </select>
        <x-icon name="chevron-down" class="pointer-events-none absolute top-1/2 right-3 size-5 -translate-y-1/2 text-ink-500" />
    </div>
    @if ($help)
        <p id="{{ $id }}-help" class="text-small">{{ $help }}</p>
    @endif
    <div aria-live="polite">
        @error($name)
            <p id="{{ $id }}-error" role="alert" class="flex items-start gap-1.5 text-sm font-medium text-danger-600"><x-icon name="alert" class="mt-0.5 size-4 shrink-0" />{{ $message }}</p>
        @enderror
    </div>
</div>
