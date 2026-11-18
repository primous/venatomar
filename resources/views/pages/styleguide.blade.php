{{-- Local-only component gallery (route registered only when APP_ENV=local). Uses demo data, never shown in production. --}}
<x-layouts::app :title="'Styleguide · '.config('venatomar.brand')" noindex>
    <div class="container-pf section space-y-16">
        <section>
            <h1 class="h1">{{ config('venatomar.brand') }} · Styleguide</h1>
            <p class="mt-2 text-ink-500">?lang=xx · {{ implode(' · ', array_keys(config('venatomar.locales'))) }}</p>
        </section>

        <section class="space-y-4">
            <h2 class="h2">Tokens</h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-8">
                @foreach (['bg-ink-900' => 'ink-900', 'bg-ink-700' => 'ink-700', 'bg-ink-500' => 'ink-500', 'bg-ink-300' => 'ink-300', 'bg-line' => 'line', 'bg-surface-2' => 'surface-2', 'bg-brand-700' => 'brand-700', 'bg-brand-600' => 'brand-600', 'bg-brand-500' => 'brand-500', 'bg-brand-100' => 'brand-100', 'bg-accent-500' => 'accent-500', 'bg-danger-600' => 'danger-600', 'bg-danger-100' => 'danger-100'] as $class => $label)
                    <div><div class="h-14 rounded-md ring-1 ring-line {{ $class }}"></div><p class="text-small mt-1">{{ $label }}</p></div>
                @endforeach
            </div>
            <p class="h1">H1 Manrope 800</p><p class="h2">H2 Manrope 700</p><p class="h3">H3 Manrope 700</p>
            <p class="prose-pf">Inter 400 · 1rem / 1.0625rem · 1.6</p><p class="text-small">Small / legal · ink-500</p>
        </section>

        <section class="space-y-4">
            <h2 class="h2">x-button</h2>
            <div class="flex flex-wrap gap-3">
                <x-button>primary md</x-button><x-button size="lg">primary lg</x-button>
                <x-button variant="secondary">secondary</x-button><x-button variant="ghost">ghost</x-button>
                <x-button loading>loading</x-button>
            </div>
        </section>

        <section class="grid gap-8 md:grid-cols-2">
            <div><h2 class="h2 mb-4">x-check-list</h2><x-check-list :items="[__('home.hero.point1'), __('home.hero.point2'), __('home.hero.point3')]" /></div>
            <div><h2 class="h2 mb-4">x-legal-example</h2><x-legal-example /></div>
        </section>

        <section><h2 class="h2 mb-6">x-steps</h2>
            <x-steps :steps="[['title' => __('home.how.s1.title'), 'text' => __('home.how.s1.text')], ['title' => __('home.how.s2.title'), 'text' => __('home.how.s2.text')], ['title' => __('home.how.s3.title'), 'text' => __('home.how.s3.text')]]" />
        </section>

        <section><h2 class="h2 mb-6">x-benefit-card · x-review-card (demo)</h2>
            <div class="grid gap-6 md:grid-cols-3">
                <x-benefit-card icon="paper-off" :title="__('home.benefits.b1.title')">{{ __('home.benefits.b1.text') }}</x-benefit-card>
                <x-benefit-card icon="shield" :title="__('home.benefits.b3.title')">{{ __('home.benefits.b3.text') }}</x-benefit-card>
                <x-review-card :review="new \App\Models\Review(['author_name' => 'Demo', 'rating' => 4, 'body' => 'Lorem ipsum dolor sit amet (demo, local only).', 'reviewed_at' => now()])" />
            </div>
        </section>

        <section><x-faq :title="'x-faq'" :items="[['q' => __('home.faq.what.q'), 'a' => __('home.faq.what.a')], ['q' => __('home.faq.grant.q'), 'a' => __('home.faq.grant.a')]]" /></section>

        <section class="max-w-[560px] space-y-4">
            <h2 class="h2">x-form.progress</h2>
            <x-form.progress :percent="45" :step="2" />
            <p class="text-small">x-field.* : see /{{ app()->getLocale() }}/apply-loan</p>
        </section>
    </div>
    <x-cta-banner :title="__('home.cta.title')" :text="__('home.cta.text')" :cta="__('home.hero.cta')" :href="route('apply')" />
</x-layouts::app>
