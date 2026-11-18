@props([
    'title' => config('venatomar.brand'),
    'description' => null,
    'noindex' => false,
])
@php
    use App\Support\Seo;
    $alternates = Seo::alternates();
    $locale = app()->getLocale();
    $clientConfig = [
        'policyVersion' => config('venatomar.cookie_policy_version'),
        'tracking' => array_filter([
            'ga4' => config('venatomar.tracking.ga4_id'),
            'ads' => config('venatomar.tracking.google_ads_id'),
            'meta' => config('venatomar.tracking.meta_pixel_id'),
        ]),
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif
    @if ($noindex)
        <meta name="robots" content="noindex, nofollow">
    @else
        <link rel="canonical" href="{{ Seo::canonical() }}">
        @foreach ($alternates as $alt => $url)
            <link rel="alternate" hreflang="{{ Seo::hreflang($alt) }}" href="{{ $url }}">
        @endforeach
        <link rel="alternate" hreflang="x-default" href="{{ $alternates[config('venatomar.default_locale')] }}">
    @endif
    <meta name="theme-color" content="#0E7C66">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    @if ($description)<meta property="og:description" content="{{ $description }}">@endif
    <meta property="og:locale" content="{{ str_replace('-', '_', Seo::hreflang($locale)) }}">
    <link rel="icon" href="/favicon.ico" sizes="48x48">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon-96x96.png" type="image/png" sizes="96x96">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="apple-mobile-web-app-title" content="{{ config('venatomar.brand') }}">
    <meta property="og:site_name" content="{{ config('venatomar.brand') }}">
    <meta property="og:image" content="{{ url('/icon-512.png') }}">
    <link rel="preload" href="/fonts/manrope-latin.woff2" as="font" type="font/woff2" crossorigin>
    @include('partials.fonts')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script type="application/json" id="pf-config">@json($clientConfig)</script>
    @stack('head')
</head>
<body class="flex min-h-screen flex-col">
    <a href="#main" class="sr-only z-[60] rounded-md bg-brand-600 px-4 py-3 font-semibold text-white focus:not-sr-only focus:fixed focus:top-3 focus:left-3">
        {{ __('nav.skip') }}
    </a>

    <x-header :alternates="$alternates" />

    <main id="main" tabindex="-1" class="flex-1 focus:outline-none">
        {{ $slot }}
    </main>

    <x-footer :alternates="$alternates" />
    <x-cookie-banner />

    @livewireScriptConfig
</body>
</html>
