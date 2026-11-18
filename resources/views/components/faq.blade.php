@props(['items' => [], 'title'])
{{-- Native <details>/<summary> accordion, one section <h2>. --}}
<div {{ $attributes }}>
    <h2 class="text-center font-display text-2xl font-bold text-ink-900 md:text-[1.75rem]">{{ $title }}</h2>
    <div class="mx-auto mt-8 max-w-2xl space-y-3">
        @foreach ($items as $item)
            <details class="group rounded-md bg-white px-4 ring-1 ring-line open:shadow-card md:px-5">
                <summary class="flex min-h-14 cursor-pointer list-none items-center justify-between gap-4 py-3 text-[0.9375rem] font-semibold text-ink-900">
                    <span>{{ $item['q'] }}</span>
                    <x-icon name="plus" class="size-5 shrink-0 text-ink-500 transition-transform duration-200 group-open:rotate-45 motion-reduce:transition-none" />
                </summary>
                <p class="prose-pf pb-4 text-sm text-ink-700">{{ $item['a'] }}</p>
            </details>
        @endforeach
    </div>
</div>
