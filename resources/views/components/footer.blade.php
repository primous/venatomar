@props(['alternates' => []])
@php
    use App\Support\Venatomar as PF;
    $email = request()->routeIs('solicitar*') ? 'contact@venatomar.com' : PF::legal('contact_email');
    $locale = app()->getLocale();
    $link = 'inline-flex min-h-11 items-center text-sm text-ink-900 underline underline-offset-4 decoration-ink-500 hover:text-brand-700 hover:decoration-brand-600';
@endphp
<footer class="mt-8 border-t border-line bg-white">
    <div class="container-pf py-8">
        <div class="flex flex-col items-center gap-5 text-center lg:flex-row lg:items-center lg:justify-between lg:text-left">
            <div class="flex flex-col items-center gap-3 lg:flex-row lg:gap-8">
                <x-logo />
                <nav aria-label="{{ __('footer.legal_title') }}">
                    <ul class="grid grid-cols-2 justify-items-center gap-x-6 sm:flex sm:flex-wrap sm:justify-center">
                        <li><a class="{{ $link }}" href="{{ route('legal', ['page' => 'terms-and-conditions']) }}">{{ __('footer.terms') }}</a></li>
                        <li><a class="{{ $link }}" href="{{ route('legal', ['page' => 'privacy-policy']) }}">{{ __('footer.privacy') }}</a></li>
                        <li><a class="{{ $link }}" href="{{ route('legal', ['page' => 'cookie-policy']) }}">{{ __('footer.cookies') }}</a></li>
                        <li><button type="button" x-data @click="$store.consent.open()" class="{{ $link }}">{{ __('footer.cookie_settings') }}</button></li>
                    </ul>
                </nav>
            </div>
            <address class="text-xs leading-[1.6] text-ink-500 not-italic lg:text-right">
                <p>{{ now()->year }} © {{ PF::legal('company_name') }}</p>
                <p>{{ PF::legal('address') }}</p>
                <p><a class="hover:text-brand-700 hover:underline" href="mailto:{{ $email }}">{{ $email }}</a></p>
            </address>
        </div>

        @if (count($alternates) > 1)
            <nav class="mt-6 flex flex-wrap items-center justify-center gap-1 lg:justify-start" aria-label="{{ __('nav.language') }}">
                @foreach ($alternates as $code => $url)
                    <a href="{{ $url }}" hreflang="{{ $code }}" lang="{{ $code }}" title="{{ __('lang.native.'.$code) }}" @if ($code === $locale) aria-current="true" @endif
                        class="inline-flex min-h-11 items-center gap-1.5 rounded-sm px-2 text-xs font-semibold text-ink-500 uppercase hover:bg-surface-2 hover:text-ink-900 aria-[current]:text-brand-700">
                        <x-flag :locale="$code" /> {{ $code }}
                    </a>
                @endforeach
            </nav>
        @endif

        <div class="text-small mt-6 space-y-1 border-t border-line pt-5 text-center lg:text-left">
            <p>{{ __('footer.disclaimer', ['brand' => config('venatomar.brand')]) }}</p>
            <p>{{ __('footer.tax_id') }}: {{ PF::legal('tax_id') }} · {{ __('footer.credit_register') }}: {{ PF::legal('credit_intermediary_number') }}</p>
        </div>
    </div>
</footer>
