<div>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($reviews as $review)
            <x-review-card :review="$review" wire:key="review-{{ $review->id }}" />
        @endforeach
    </div>
    @if ($hasMore)
        <div class="mt-8 text-center">
            <x-button variant="secondary" wire:click="more" wire-target="more">{{ __('home.reviews.more') }}</x-button>
        </div>
    @endif
</div>
