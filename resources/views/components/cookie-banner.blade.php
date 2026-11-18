{{-- First-visit banner + settings panel. State lives in Alpine.store('consent') (resources/js/consent.js). --}}
<div x-data x-cloak>
    {{-- Banner --}}
    <section x-show="!$store.consent.decided && !$store.consent.panelOpen" x-transition.opacity
        role="region" aria-labelledby="cookie-title"
        class="fixed inset-x-0 bottom-0 z-50 border-t border-line bg-white shadow-pop">
        <div class="container-pf flex flex-col gap-4 py-5 lg:flex-row lg:items-center lg:gap-8">
            <div class="flex-1">
                <h2 id="cookie-title" class="font-display text-base font-bold text-ink-900">{{ __('cookies.title') }}</h2>
                <p class="mt-1 text-sm text-ink-700">
                    {{ __('cookies.text') }}
                    <a href="{{ route('legal', ['page' => 'cookie-policy']) }}" class="font-medium text-brand-700 underline underline-offset-2">{{ __('cookies.policy_link') }}</a>
                </p>
            </div>
            {{-- Three buttons with the same visual weight --}}
            <div class="grid gap-2 sm:grid-cols-3 lg:w-[34rem]">
                <x-button variant="secondary" @click="$store.consent.rejectAll()">{{ __('cookies.only_necessary') }}</x-button>
                <x-button variant="secondary" @click="$store.consent.open()">{{ __('cookies.configure') }}</x-button>
                <x-button variant="secondary" @click="$store.consent.acceptAll()">{{ __('cookies.accept_all') }}</x-button>
            </div>
        </div>
    </section>

    {{-- Settings panel --}}
    <div x-show="$store.consent.panelOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-end justify-center bg-ink-900/40 p-0 sm:items-center sm:p-4"
        @keydown.escape.window="$store.consent.panelOpen = false">
        <div role="dialog" aria-modal="true" aria-labelledby="cookie-panel-title" @keydown.tab="const f = [...$el.querySelectorAll('button, input, a')]; if ($event.shiftKey && document.activeElement === f[0]) { $event.preventDefault(); f.at(-1).focus() } else if (! $event.shiftKey && document.activeElement === f.at(-1)) { $event.preventDefault(); f[0].focus() }"
            x-init="$watch('$store.consent.panelOpen', v => v && $nextTick(() => $el.querySelector('h2').focus()))"
            class="w-full max-w-lg rounded-t-xl bg-white p-6 shadow-pop sm:rounded-xl md:p-8">
            <div class="flex items-start justify-between gap-4">
                <h2 id="cookie-panel-title" tabindex="-1" class="h3 focus:outline-none">{{ __('cookies.panel_title') }}</h2>
                <button type="button" class="-m-2 inline-flex size-11 items-center justify-center rounded-sm hover:bg-surface-2" @click="$store.consent.panelOpen = false">
                    <x-icon name="x" /><span class="sr-only">{{ __('nav.close_menu') }}</span>
                </button>
            </div>

            <ul class="mt-5 divide-y divide-line">
                <li class="flex items-start justify-between gap-4 py-4">
                    <div>
                        <p class="font-semibold text-ink-900">{{ __('cookies.necessary') }}</p>
                        <p class="text-small mt-1">{{ __('cookies.necessary_desc') }}</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">{{ __('cookies.always_on') }}</span>
                </li>
                @foreach (['statistics', 'marketing'] as $cat)
                    <li class="flex items-start justify-between gap-4 py-4">
                        <div>
                            <label for="consent-{{ $cat }}" class="font-semibold text-ink-900">{{ __('cookies.'.$cat) }}</label>
                            <p class="text-small mt-1" id="consent-{{ $cat }}-desc">{{ __('cookies.'.$cat.'_desc') }}</p>
                        </div>
                        <input type="checkbox" id="consent-{{ $cat }}" role="switch" aria-describedby="consent-{{ $cat }}-desc"
                            x-model="$store.consent.{{ $cat }}"
                            class="peer relative mt-1 h-7 w-12 shrink-0 cursor-pointer appearance-none rounded-full bg-ink-300 transition-colors
                                   before:absolute before:top-1 before:left-1 before:size-5 before:rounded-full before:bg-white before:shadow before:transition-transform
                                   checked:bg-brand-600 checked:before:translate-x-5">
                    </li>
                @endforeach
            </ul>

            <div class="mt-6 grid gap-2 sm:grid-cols-3">
                <x-button variant="secondary" @click="$store.consent.rejectAll()">{{ __('cookies.only_necessary') }}</x-button>
                <x-button variant="secondary" @click="$store.consent.saveSelection()">{{ __('cookies.save') }}</x-button>
                <x-button variant="secondary" @click="$store.consent.acceptAll()">{{ __('cookies.accept_all') }}</x-button>
            </div>
        </div>
    </div>
</div>
