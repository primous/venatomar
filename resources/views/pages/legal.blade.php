@php
    $titles = [
        'terms-and-conditions' => __('footer.terms'),
        'privacy-policy' => __('footer.privacy'),
        'cookie-policy' => __('footer.cookies'),
    ];
@endphp
<x-layouts::app :title="$titles[$page].' · '.config('venatomar.brand')" :description="__('legal.meta.'.$page)">
    <div class="rounded-b-[2rem] bg-gradient-to-b from-mint-200 to-mint-100 py-8 md:py-12">
        <div class="container-pf">
            <nav aria-label="{{ __('legal.nav') }}" class="text-small mb-4">
                <a href="{{ route('home') }}" class="hover:text-brand-700 hover:underline">{{ config('venatomar.brand') }}</a>
                <span aria-hidden="true"> / </span>
                <span aria-current="page">{{ $titles[$page] }}</span>
            </nav>
            <h1 class="h1">{{ $titles[$page] }}</h1>
            <p class="text-small mt-2">{{ __('legal.updated', ['date' => \Illuminate\Support\Carbon::parse(config('venatomar.consent_version'))->locale(app()->getLocale())->isoFormat('LL')]) }}</p>
        </div>
    </div>
    <section class="py-10 md:py-14">
        <div class="container-pf">
            <article class="prose-pf">
                @include($content, ['legal' => new \App\Support\LegalData($country)])
            </article>
        </div>
    </section>
</x-layouts::app>
