@props(['rating' => 0])
<span class="inline-flex items-center gap-0.5" role="img" aria-label="{{ __('reviews.stars_aria', ['rating' => $rating]) }}">
    @for ($i = 1; $i <= 5; $i++)
        <svg @class(['size-4', 'text-accent-500' => $i <= round($rating), 'text-ink-300' => $i > round($rating)]) viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10 1.8l2.5 5.2 5.7.8-4.1 4 1 5.6L10 14.8l-5.1 2.6 1-5.6-4.1-4 5.7-.8z"/>
        </svg>
    @endfor
</span>
