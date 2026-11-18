@php
    use App\Support\Venatomar as PF;
    $loan = $country->loan();
    $hasBounds = $loan['amount_min'] !== null && $loan['amount_max'] !== null;
    $max = $hasBounds ? PF::money($loan['amount_max'], $country) : null;
    $brand = config('venatomar.brand');
    $locale = app()->getLocale();
    $hasReviews = \App\Models\Review::published($locale)->exists();
    $summary = $hasReviews ? \App\Models\Review::summary($locale) : null;
    // Proof lines next to the average: real figures only, placeholders are skipped.
    $highlights = array_values(array_filter((array) config('venatomar.reviews.highlights'), fn ($h) => is_string($h) && $h !== '' && ! str_starts_with($h, '[')));

    $c = $country->calculator();
    $calc = [
        'amount' => $c['amount'], 'term' => $c['term'],
        'aMin' => $c['amount_min'], 'aMax' => $c['amount_max'], 'tMin' => $c['term_min'], 'tMax' => $c['term_max'],
        'locale' => str_replace('_', '-', $country->numberLocale), 'currency' => $country->currency,
        'months' => __('form.months_short'), 'cta' => __('home.calc.cta', ['amount' => '__AMOUNT__']),
        'url' => route('apply'),
    ];
    $pct = fn ($v, $min, $max) => round(($v - $min) / max(1, $max - $min) * 100, 2);
    // Built here: Blade would compile a literal "@ context" key as a directive.
    $faqSchema = json_encode([
        '@'.'context' => 'https://schema.org',
        '@'.'type' => 'FAQPage',
        'mainEntity' => $faq->map(fn ($i) => [
            '@'.'type' => 'Question',
            'name' => $i['q'],
            'acceptedAnswer' => ['@'.'type' => 'Answer', 'text' => $i['a']],
        ])->all(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
    $h2 = 'font-display text-[1.375rem] font-bold text-ink-900 md:text-2xl';
@endphp
<x-layouts::app
    :title="$hasBounds ? __('home.meta.title', ['brand' => $brand, 'max' => $max]) : __('home.meta.title_short', ['brand' => $brand])"
    :description="__('home.meta.description')">

    @push('head')
        <script type="application/ld+json">{!! $faqSchema !!}</script>
    @endpush

    {{-- HERO --}}
    <section class="rounded-b-[2rem] bg-gradient-to-b from-mint-200 to-mint-100">
        <div class="container-pf grid items-start gap-5 py-6 md:py-12 lg:grid-cols-12 lg:gap-8">

            {{-- Green card --}}
            <div class="relative flex flex-col overflow-hidden rounded-xl bg-gradient-to-br from-[#1CCB9E] to-[#0FA57E] p-5 text-white shadow-card md:p-9 lg:col-span-7 lg:min-h-[23rem]">
                @if ($summary)
                    <a href="#reviews-title" class="inline-flex w-fit items-center gap-2 rounded-full bg-black/10 px-2.5 py-1 text-xs font-bold text-white hover:bg-black/20">
                        <span class="tracking-[-0.1em] text-[#FFD43B]" aria-hidden="true">★★★★★</span>
                        {{ PF::decimal($summary['rating'], $country, 1) }}/5
                    </a>
                @endif
                <h1 class="mt-3 max-w-[14ch] font-display text-[clamp(1.75rem,4vw,2.625rem)] leading-[1.1] font-extrabold tracking-tight text-white">{{ __('home.hero.headline') }}</h1>
                <ul class="mt-4 flex flex-wrap gap-x-5 gap-y-1.5 text-sm font-semibold">
                    @foreach ([__('home.hero.point1'), __('home.hero.point2')] as $point)
                        <li class="inline-flex items-center gap-1.5"><x-icon name="check" class="size-4 stroke-[2.5]" /> {{ $point }}</li>
                    @endforeach
                </ul>
                @include('partials.hero-illustration', ['class' => 'pointer-events-none mx-auto mt-6 -mb-9 hidden w-60 sm:block lg:mt-auto lg:w-64'])
            </div>

            {{-- Calculator --}}
            <div class="lg:col-span-5">
                <div class="relative pt-3">
                    <p class="absolute top-0 left-1/2 z-10 inline-flex max-w-[calc(100%-1rem)] -translate-x-1/2 items-center gap-1.5 whitespace-nowrap rounded-full bg-white px-3 py-1 text-xs font-semibold text-cta-700 shadow-card ring-1 ring-cta-100 lg:right-6 lg:left-auto lg:translate-x-0">
                        <x-icon name="clock" class="size-4" /> {{ __('home.hero.eyebrow') }}
                    </p>

                    <div x-data="loanCalc(@js($calc))" class="rounded-lg bg-white p-5 pt-7 shadow-pop md:p-6 md:pt-8">
                        <div class="flex items-center justify-between gap-3">
                            <label for="calc-amount" class="text-[0.9375rem] font-medium text-ink-900">{{ __('form.amount') }}</label>
                            <output for="calc-amount" class="min-w-24 rounded-sm px-3 py-1.5 text-right text-sm font-medium text-ink-900 tabular-nums ring-1 ring-line" x-text="money(amount)">{{ PF::money($c['amount'], $country) }}</output>
                        </div>
                        <input id="calc-amount" type="range" class="pf-range mt-4" x-model.number="amount" :style="fill(amount, aMin, aMax)" style="--fill: {{ $pct($c['amount'], $c['amount_min'], $c['amount_max']) }}%"
                            min="{{ $c['amount_min'] }}" max="{{ $c['amount_max'] }}" step="{{ $c['amount_step'] }}" value="{{ $c['amount'] }}" :aria-valuetext="money(amount)">
                        <div class="mt-2 flex justify-between text-xs text-ink-500"><span>{{ PF::money($c['amount_min'], $country) }}</span><span>{{ PF::money($c['amount_max'], $country) }}</span></div>

                        <div class="mt-5 flex items-center justify-between gap-3">
                            <label for="calc-term" class="text-[0.9375rem] font-medium text-ink-900">{{ __('form.term') }}</label>
                            <output for="calc-term" class="min-w-24 rounded-sm px-3 py-1.5 text-right text-sm font-medium text-ink-900 tabular-nums ring-1 ring-line" x-text="term + ' ' + months">{{ $c['term'] }} {{ $calc['months'] }}</output>
                        </div>
                        <input id="calc-term" type="range" class="pf-range mt-4" x-model.number="term" :style="fill(term, tMin, tMax)" style="--fill: {{ $pct($c['term'], $c['term_min'], $c['term_max']) }}%"
                            min="{{ $c['term_min'] }}" max="{{ $c['term_max'] }}" step="1" value="{{ $c['term'] }}" :aria-valuetext="term + ' ' + months">
                        <div class="mt-2 flex justify-between text-xs text-ink-500"><span>{{ $c['term_min'] }} {{ $calc['months'] }}</span><span>{{ $c['term_max'] }} {{ $calc['months'] }}</span></div>

                        <x-button size="lg" block class="mt-6" :href="route('apply', ['amount' => $c['amount'], 'term' => $c['term']])" x-bind:href="href()">
                            <span x-text="cta()">{{ __('home.calc.cta', ['amount' => PF::money($c['amount'], $country)]) }}</span>
                        </x-button>
                    </div>
                </div>

                <x-legal-example compact plain class="mt-4" />
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="py-10 md:py-16" aria-labelledby="how-title">
        <div class="container-pf">
            <h2 id="how-title" class="{{ $h2 }} md:text-center">{{ __('home.how.title') }}</h2>
            <x-steps class="mt-6" :steps="[
                ['title' => __('home.how.s1.title'), 'text' => __('home.how.s1.text')],
                ['title' => __('home.how.s2.title'), 'text' => __('home.how.s2.text')],
                ['title' => __('home.how.s3.title'), 'text' => __('home.how.s3.text')],
            ]" />
            <div class="mt-6 text-center">
                <x-button :href="route('apply')" class="w-full sm:w-auto">{{ __('nav.apply') }}</x-button>
            </div>
        </div>
    </section>

    {{-- REVIEWS: real published reviews only, plus the form to leave one --}}
    <section class="bg-surface-2 py-10 md:py-16" aria-labelledby="reviews-title">
        <div class="container-pf">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 id="reviews-title" class="{{ $h2 }}">{{ __('home.reviews.title') }}</h2>
                    <p class="mt-1 text-xs text-ink-500">{{ __('home.reviews.subtitle', ['brand' => $brand]) }}</p>
                </div>
                <a href="#leave-review" class="inline-flex min-h-11 items-center gap-1.5 font-semibold text-brand-700 underline underline-offset-2 hover:text-brand-600">
                    <x-icon name="star" class="size-4" /> {{ __('reviews.form.cta') }}
                </a>
            </div>
            <div class="mt-5 flex flex-col gap-3 md:flex-row md:items-center md:gap-10">
                @if ($summary)
                    <p class="inline-flex flex-wrap items-center gap-2 text-ink-700">
                        <x-star-tiles :rating="$summary['rating']" size="lg" />
                        <span><strong class="font-bold text-ink-900 tabular-nums">{{ PF::decimal($summary['rating'], $country, 1) }}/5</strong>
                        · {{ trans_choice('reviews.count', $summary['count'], ['count' => PF::decimal($summary['count'], $country, 0)]) }}</span>
                    </p>
                @endif
                @if ($highlights)
                    <ul class="space-y-1 text-xs text-ink-500">
                        @foreach ($highlights as $line)<li>{{ $line }}</li>@endforeach
                    </ul>
                @endif
            </div>
            @if ($hasReviews)
                <livewire:reviews-list :locale="$locale" />
            @endif
            <div class="mx-auto mt-10 max-w-3xl">
                <livewire:leave-review :locale="$locale" />
            </div>
        </div>
    </section>

    {{-- BENEFITS --}}
    <section class="py-10 md:py-16" aria-labelledby="benefits-title">
        <div class="container-pf">
            <h2 id="benefits-title" class="{{ $h2 }} md:text-center">{{ __('home.benefits.title') }}</h2>
            <div class="mt-6 grid gap-3 md:grid-cols-3 md:gap-4">
                <x-benefit-card icon="percent" :title="__('home.benefits.b2.title')" :delay="0">{{ __('home.benefits.b2.text') }}</x-benefit-card>
                <x-benefit-card icon="file-check" :title="__('home.benefits.b1.title')" :delay="60">{{ __('home.benefits.b1.text') }}</x-benefit-card>
                <x-benefit-card icon="send" :title="__('home.benefits.b3.title')" :delay="120">{{ __('home.benefits.b3.text') }}</x-benefit-card>
            </div>
            <div class="mt-6 text-center">
                <x-button :href="route('apply')" class="w-full sm:w-auto">{{ __('nav.apply') }}</x-button>
            </div>
        </div>
    </section>

    <x-cta-banner :title="__('home.cta.title')" :text="__('home.cta.text')" :cta="__('home.hero.cta')" :href="route('apply')" />

    {{-- FAQ --}}
    <section class="py-10 md:py-16">
        <div class="container-pf">
            <x-faq :title="__('home.faq.title')" :items="$faq" />
        </div>
    </section>
</x-layouts::app>
