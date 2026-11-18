@props(['compact' => false, 'plain' => false])
@php
    use App\Support\Venatomar as PF;
    $country = app(\App\Support\Country::class);
    $ex = $country->example();
    $brand = config('venatomar.brand');
@endphp
{{-- Representative example. Figures come ONLY from config/venatomar.php; the
     figure sentences are omitted while any value is still a placeholder. --}}
<aside {{ $attributes->merge(['class' => ($plain ? 'px-2 text-center text-ink-500 ' : 'rounded-lg bg-surface-2 p-5 text-ink-700 ').($compact ? 'text-xs leading-[1.5]' : 'text-sm leading-[1.6]')]) }}
    aria-label="{{ __('legal_example.aria') }}">
    <p>
        {{ __('legal_example.intro', ['brand' => $brand]) }}
        @if ($ex)
            {{ __('legal_example.terms', [
                'term_min' => $ex['term_min'], 'term_max' => $ex['term_max'],
                'apr_min' => PF::decimal($ex['apr_min'], $country), 'apr_max' => PF::decimal($ex['apr_max'], $country),
            ]) }}
            {{ __('legal_example.example', [
                'amount' => PF::money($ex['amount'], $country), 'term' => $ex['term'],
                'net' => PF::money($ex['net'], $country, 2), 'total' => PF::money($ex['total'], $country, 2),
                'apr' => PF::decimal($ex['apr'], $country),
            ]) }}
        @endif
        {{ __('legal_example.outro') }}
    </p>
</aside>
