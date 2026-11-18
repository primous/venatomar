@props(['alternates' => []])
@php
    $locale = app()->getLocale();
    $applyRoute = request()->routeIs('solicitar*') ? route('solicitar') : route('apply');
    $accountRoute = request()->routeIs('solicitar*') ? route('solicitar.account') : route('account');
@endphp
<header x-data="stickyHeader" :class="scrolled ? 'border-line' : 'border-transparent'"
    class="sticky top-0 z-40 h-16 rounded-b-2xl border-b border-transparent bg-white/95 shadow-[0_2px_12px_rgb(11_31_58/.05)] backdrop-blur supports-[backdrop-filter]:bg-white/85 transition-colors">
    <div class="container-pf flex h-16 items-center justify-between gap-3">
        <x-logo />

        <div class="flex items-center gap-2 md:gap-3">
            {{-- Language switcher (desktop) --}}
            <div class="relative hidden md:block" x-data="{ open: false }" @keydown.escape.window="if (open) { open = false; $refs.langBtn.focus() }" @click.outside="open = false">
                <button type="button" x-ref="langBtn" @click="open = !open; if (open) $nextTick(() => $refs.langMenu.querySelector('[aria-current]')?.focus())"
                    @keydown.arrow-down.prevent="open = true; $nextTick(() => $refs.langMenu.querySelector('a')?.focus())"
                    :aria-expanded="open" aria-haspopup="true" aria-controls="lang-menu"
                    class="inline-flex h-11 items-center gap-2 rounded-sm px-3 text-sm font-semibold text-ink-900 hover:bg-surface-2">
                    <x-flag :locale="$locale" />
                    <span class="uppercase">{{ $locale }}</span>
                    <span class="sr-only">{{ __('nav.language') }}</span>
                    <x-icon name="chevron-down" class="size-4 transition-transform" ::class="open && 'rotate-180'" />
                </button>
                <div x-cloak x-show="open" x-transition.opacity.duration.150ms x-ref="langMenu" id="lang-menu"
                    @keydown.arrow-down.prevent="const l = [...$el.querySelectorAll('a')]; l[(l.indexOf(document.activeElement) + 1) % l.length].focus()"
                    @keydown.arrow-up.prevent="const l = [...$el.querySelectorAll('a')]; l[(l.indexOf(document.activeElement) - 1 + l.length) % l.length].focus()"
                    class="absolute right-0 mt-2 w-56 rounded-md bg-white p-1.5 shadow-pop ring-1 ring-line">
                    <ul class="max-h-[70vh] overflow-auto">
                        @foreach ($alternates as $code => $url)
                            <li>
                                <a href="{{ $url }}" hreflang="{{ $code }}" lang="{{ $code }}" @if ($code === $locale) aria-current="true" @endif
                                    class="flex min-h-11 items-center gap-3 rounded-sm px-3 text-sm text-ink-700 hover:bg-surface-2 focus:bg-surface-2 aria-[current]:font-semibold aria-[current]:text-brand-700">
                                    <x-flag :locale="$code" />
                                    <span class="flex-1">{{ __('lang.native.'.$code) }}</span>
                                    <span class="text-xs uppercase text-ink-500">{{ $code }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <x-button variant="cta" :href="$applyRoute" size="md" class="hidden md:inline-flex whitespace-nowrap px-4 text-sm md:px-5">{{ __('nav.apply') }}</x-button>

            <a href="{{ $accountRoute }}" title="{{ __('nav.account') }}"
                class="inline-flex size-11 shrink-0 items-center justify-center rounded-md bg-white text-ink-900 ring-1 ring-line hover:bg-surface-2">
                <x-icon name="user" class="size-5" />
                <span class="sr-only">{{ __('nav.account') }}</span>
            </a>
        </div>
    </div>
</header>
