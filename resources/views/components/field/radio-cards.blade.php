@props(['name', 'label', 'options' => null, 'help' => null])
@php
    $id = 'f-'.str_replace(['.', '_'], '-', $name);
    $options ??= ['1' => __('form.yes'), '0' => __('form.no')];
    $hasError = $errors->has($name);
@endphp
<fieldset {{ $attributes->merge(['class' => 'space-y-1.5']) }}
    @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif>
    <legend class="mb-1.5 block text-[0.9375rem] leading-[1.4] font-semibold text-ink-900">{{ $label }}</legend>
    @if ($help)
        <p class="text-small -mt-1 mb-2">{{ $help }}</p>
    @endif
    <div class="grid grid-cols-2 gap-3">
        @foreach ($options as $value => $text)
            <label for="{{ $id }}-{{ $value }}" @class([
                'relative flex min-h-14 cursor-pointer items-center justify-center gap-2 rounded-md bg-white px-4 text-center font-semibold text-ink-900 ring-1 transition',
                'has-[:checked]:bg-cta-100 has-[:checked]:text-cta-700 has-[:checked]:ring-2 has-[:checked]:ring-cta-600',
                'has-[:focus-visible]:outline-2 has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-brand-600',
                'ring-danger-600' => $hasError,
                'ring-line hover:ring-ink-300' => ! $hasError,
            ])>
                <input type="radio" id="{{ $id }}-{{ $value }}" name="{{ $name }}" value="{{ $value }}" wire:model.live="{{ $name }}" class="peer sr-only">
                <x-icon name="check-circle" class="hidden size-5 peer-checked:block" />
                {{ $text }}
            </label>
        @endforeach
    </div>
    <div aria-live="polite">
        @error($name)
            <p id="{{ $id }}-error" role="alert" class="flex items-start gap-1.5 text-sm font-medium text-danger-600"><x-icon name="alert" class="mt-0.5 size-4 shrink-0" />{{ $message }}</p>
        @enderror
    </div>
</fieldset>
