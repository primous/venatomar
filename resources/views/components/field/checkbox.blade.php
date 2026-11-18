@props(['name'])
@php
    $id = 'f-'.str_replace(['.', '_'], '-', $name);
    $hasError = $errors->has($name);
@endphp
<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    <div class="flex items-start gap-3">
        <input type="checkbox" id="{{ $id }}" name="{{ $name }}" wire:model.live="{{ $name }}"
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            aria-required="true"
            @class([
                'mt-0.5 size-6 shrink-0 cursor-pointer rounded-[6px] border-2 bg-white accent-brand-600',
                'border-danger-600' => $hasError,
                'border-ink-300' => ! $hasError,
            ])>
        <label for="{{ $id }}" class="text-[0.9375rem] leading-[1.5] text-ink-700">{{ $slot }}</label>
    </div>
    <div aria-live="polite">
        @error($name)
            <p id="{{ $id }}-error" role="alert" class="flex items-start gap-1.5 text-sm font-medium text-danger-600"><x-icon name="alert" class="mt-0.5 size-4 shrink-0" />{{ $message }}</p>
        @enderror
    </div>
</div>
