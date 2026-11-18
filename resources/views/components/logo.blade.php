@props(['link' => true, 'inverted' => false])
@php $brand = config('venatomar.brand'); @endphp
@if ($link)
<a href="{{ route('home') }}" class="inline-flex min-h-11 items-center gap-2 rounded-sm" aria-label="{{ __('nav.home_aria', ['brand' => $brand]) }}">
@else
<span class="inline-flex items-center gap-2">
@endif
    <img src="/images/logo.svg" alt="" width="32" height="32" class="size-8" decoding="async">
    <span @class(['font-display text-lg font-extrabold tracking-tight sm:text-xl', 'text-ink-900' => ! $inverted, 'text-white' => $inverted])>{{ $brand }}</span>
@if ($link)
</a>
@else
</span>
@endif
