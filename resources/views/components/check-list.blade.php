@props(['items' => []])
<ul {{ $attributes->merge(['class' => 'space-y-3']) }}>
    @foreach ($items as $item)
        <li class="flex items-start gap-3">
            <span class="mt-0.5 inline-flex size-6 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-600">
                <x-icon name="check" class="size-4" />
            </span>
            <span class="font-medium text-ink-900">{{ $item }}</span>
        </li>
    @endforeach
</ul>
