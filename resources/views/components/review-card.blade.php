@props(['review'])
@php
    // Soft avatar colours, picked from the initial (complete class strings for Tailwind).
    $palette = ['bg-cta-100 text-cta-700', 'bg-mint-100 text-brand-700', 'bg-surface-2 text-ink-700', 'bg-[#FDF3D7] text-[#7A5A00]', 'bg-[#E8EEFF] text-[#2F4AA8]'];
    $avatar = $palette[mb_ord($review->initial() ?: 'A') % count($palette)];
@endphp
<article {{ $attributes->merge(['class' => 'flex h-full flex-col overflow-hidden rounded-md bg-white p-5 shadow-card ring-1 ring-line break-words']) }}>
    <header class="flex items-center gap-3">
        <span @class(['inline-flex size-8 shrink-0 items-center justify-center rounded-full text-sm font-bold', $avatar]) aria-hidden="true">{{ $review->initial() }}</span>
        <p class="min-w-0 flex-1 truncate text-sm font-semibold text-ink-900">{{ $review->author_name }}</p>
        @if ($review->is_verified)
            <span class="inline-flex shrink-0 items-center gap-1 text-xs font-medium text-[#1D72C9]">
                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1.5l2.1 1.5 2.6-.1.8 2.5 2.1 1.5-.8 2.5.8 2.5-2.1 1.5-.8 2.5-2.6-.1L10 18.5l-2.1-1.5-2.6.1-.8-2.5-2.1-1.5.8-2.5-.8-2.5 2.1-1.5.8-2.5 2.6.1L10 1.5zm3.3 6.2a.75.75 0 00-1.1-1l-3 3.3-1.4-1.4a.75.75 0 10-1.1 1l2 2a.75.75 0 001.1 0l3.5-3.9z" clip-rule="evenodd"/></svg>
                {{ __('reviews.verified') }}
            </span>
        @endif
    </header>

    <x-star-tiles class="mt-3" :rating="$review->rating" />

    @if ($review->title)
        <h3 class="mt-3 text-sm font-bold text-ink-900">{{ $review->title }}</h3>
    @endif
    <p class="mt-2 flex-1 text-sm leading-[1.6] text-ink-700">{{ $review->body }}</p>

    <p class="mt-4 text-xs text-ink-500">
        {{ __('reviews.date') }}
        <time datetime="{{ $review->reviewed_at->toDateString() }}">{{ $review->reviewed_at->format('d/m/y') }}</time>
    </p>
</article>
