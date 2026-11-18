@props(['rating' => 0, 'size' => 'sm'])
@php
    $full = (int) round($rating);
    $box = $size === 'lg' ? 'size-6' : 'size-[18px]';
    $tile = $full >= 4 ? 'bg-star-500' : ($full === 3 ? 'bg-accent-500' : 'bg-cta-500');
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-0.5']) }} role="img" aria-label="{{ __('reviews.stars_aria', ['rating' => $rating]) }}">
    @for ($i = 1; $i <= 5; $i++)
        <span @class([$box, 'inline-flex items-center justify-center rounded-[3px]', $tile => $i <= $full, 'bg-ink-300/60' => $i > $full]) aria-hidden="true">
            <svg class="size-[75%] text-white" viewBox="0 0 20 20" fill="currentColor"><path d="M10 1.8l2.5 5.2 5.7.8-4.1 4 1 5.6L10 14.8l-5.1 2.6 1-5.6-4.1-4 5.7-.8z"/></svg>
        </span>
    @endfor
</span>
