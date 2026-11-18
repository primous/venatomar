@props(['steps' => []])
{{-- 3 numbered step cards: stacked on mobile, side by side on desktop. --}}
<ol {{ $attributes->merge(['class' => 'grid gap-4 md:grid-cols-3']) }}>
    @foreach ($steps as $i => $step)
        <li class="reveal rounded-md bg-white p-5 shadow-card ring-1 ring-line" x-data x-intersect.once="setTimeout(() => $el.classList.add('is-visible'), {{ $i * 60 }})">
            <span class="inline-flex rounded-sm bg-[#0FA57E] px-2 py-0.5 text-[0.6875rem] font-bold text-white">{{ __('home.how.step', ['n' => $i + 1]) }}</span>
            <h3 class="mt-3 font-display text-base font-bold text-ink-900">{{ $step['title'] }}</h3>
            <p class="mt-1 text-sm text-ink-500">{{ $step['text'] }}</p>
        </li>
    @endforeach
</ol>
