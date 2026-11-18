@props(['step' => 1, 'last' => false, 'action' => 'next'])
<div {{ $attributes->merge(['class' => 'space-y-3 pt-2']) }}>
    <x-button type="submit" size="lg" block :wire-target="$action">
        {{ $last ? __('form.submit') : __('form.continue') }}
        <x-icon name="arrow-right" class="size-5" wire:loading.remove wire:target="{{ $action }}" />
    </x-button>
    @if ($step > 1)
        <div class="text-center">
            <button type="button" wire:click="back" class="inline-flex min-h-11 items-center gap-1 rounded-sm px-3 font-semibold text-ink-700 hover:text-ink-900">
                <x-icon name="chevron-left" class="size-4" /> {{ __('form.back') }}
            </button>
        </div>
    @endif
</div>
