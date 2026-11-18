@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'loading' => false,      // static loading state
    'wireTarget' => null,    // Livewire action: shows the spinner while it runs
    'block' => false,
])
@php
    // Complete class strings only (no dynamic concatenation of utilities).
    $variants = [
        'primary' => 'bg-gradient-to-b from-[#F47A2C] to-cta-600 text-white hover:from-cta-600 hover:to-cta-700 shadow-[0_6px_16px_rgb(208_74_10/.25)]',
        'brand' => 'bg-brand-600 text-white hover:bg-brand-700 shadow-sm',
        'secondary' => 'bg-white text-ink-900 ring-1 ring-inset ring-line hover:bg-surface-2',
        'ghost' => 'bg-transparent text-ink-900 hover:bg-surface-2',
        'white' => 'bg-white text-brand-700 hover:bg-brand-100 shadow-sm',
        'cta' => 'bg-gradient-to-b from-[#F47A2C] to-cta-600 text-white hover:from-cta-600 hover:to-cta-700 shadow-[0_6px_16px_rgb(208_74_10/.25)]',
    ];
    $sizes = [
        'md' => 'h-11 px-5 rounded-md text-[0.9375rem]',
        'lg' => 'h-14 px-7 rounded-lg text-base',
    ];
    $classes = implode(' ', [
        'inline-flex items-center justify-center gap-2 font-semibold leading-none select-none',
        'transition duration-150 hover:-translate-y-px active:translate-y-0 motion-reduce:transform-none',
        'disabled:cursor-not-allowed disabled:opacity-60 aria-busy:cursor-wait',
        'min-h-11',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
        $block ? 'w-full' : '',
    ]);
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if ($loading) aria-busy="true" disabled @endif
        @if ($wireTarget) wire:loading.attr="aria-busy" wire:loading.class="pointer-events-none opacity-80" wire:target="{{ $wireTarget }}" @endif
    >
        @if ($loading)
            <x-icon name="spinner" class="size-5" />
        @elseif ($wireTarget)
            <span wire:loading wire:target="{{ $wireTarget }}"><x-icon name="spinner" class="size-5" /></span>
        @endif
        {{ $slot }}
    </button>
@endif
