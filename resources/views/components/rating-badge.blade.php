@php
    use App\Support\Venatomar as PF;
    // Shown ONLY when enabled and backed by real, configured values.
    $rating = PF::number('reviews.rating');
    $count = PF::number('reviews.count');
    $source = PF::config('reviews.source');
    $show = config('venatomar.reviews.enabled') === true && $rating !== null && $count !== null && $source !== null;
@endphp
@if ($show)
    <div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 text-sm text-ink-700']) }}>
        <x-stars :rating="$rating" />
        <span>
            <strong class="font-semibold text-ink-900 tabular-nums">{{ PF::decimal($rating, null, 1) }}/5</strong>
            · {{ trans_choice('reviews.count', (int) $count, ['count' => PF::decimal($count, null, 0)]) }} · {{ $source }}
        </span>
    </div>
@endif
