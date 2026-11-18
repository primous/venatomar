<x-layouts::app :title="__('thanks.title').' · '.config('venatomar.brand')" noindex>
    <section class="rounded-b-[2rem] bg-gradient-to-b from-mint-200 to-mint-100/30 py-10 md:py-16">
        <div class="mx-auto w-full max-w-[560px] px-5">
            <div class="rounded-lg bg-white p-6 text-center shadow-pop md:p-10">
                <span class="mx-auto inline-flex size-16 items-center justify-center rounded-full bg-gradient-to-br from-[#1CCB9E] to-[#0FA57E] text-white shadow-card">
                    <x-icon name="check" class="size-8" />
                </span>
                <h1 class="h2 mt-6">{{ __('thanks.title') }}</h1>
                <p class="mt-3 text-ink-500">{{ __('thanks.reference') }}</p>
                <p class="mt-1 font-display text-2xl font-extrabold tracking-wide text-ink-900 tabular-nums">{{ $reference }}</p>
                <p class="mt-6">{{ __('thanks.email_sent', ['email' => $maskedEmail]) }}</p>

                <div class="mt-8 rounded-lg bg-surface-2 p-5 text-left">
                    <h2 class="font-display text-lg font-bold text-ink-900">{{ __('thanks.next_title') }}</h2>
                    <ol class="mt-3 space-y-3">
                        @foreach ([__('thanks.next1'), __('thanks.next2'), __('thanks.next3')] as $i => $line)
                            <li class="flex gap-3">
                                <span class="inline-flex size-7 shrink-0 items-center justify-center rounded-full bg-[#0FA57E] text-sm font-bold text-white" aria-hidden="true">{{ $i + 1 }}</span>
                                <span>{{ $line }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <x-button :href="route('account')" size="lg">{{ __('nav.account') }}</x-button>
                    <x-button :href="route('home')" variant="secondary" size="lg">{{ __('thanks.home') }}</x-button>
                </div>
            </div>
        </div>
    </section>
</x-layouts::app>
