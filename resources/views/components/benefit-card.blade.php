@props(['icon', 'title', 'delay' => 0])
<div class="reveal rounded-md bg-white p-5 shadow-card ring-1 ring-line" x-data x-intersect.once="setTimeout(() => $el.classList.add('is-visible'), {{ (int) $delay }})">
    <x-icon :name="$icon" class="size-7 text-brand-600" />
    <h3 class="mt-4 font-display text-base font-bold text-ink-900">{{ $title }}</h3>
    <p class="mt-1 text-sm text-ink-500">{{ $slot }}</p>
</div>
