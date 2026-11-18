@props(['percent' => 0, 'step' => 1, 'total' => 5])
{{-- "Perfil completado" — profile completeness, NEVER an approval probability. --}}
<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    <div class="flex items-center justify-between text-sm">
        <span class="font-semibold text-ink-900" id="progress-label">{{ __('form.progress', ['percent' => $percent]) }}</span>
        <span class="text-ink-500">{{ __('form.step_of', ['step' => $step, 'total' => $total]) }}</span>
    </div>
    <div class="h-2 overflow-hidden rounded-full bg-field" role="progressbar"
        aria-labelledby="progress-label" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $percent }}">
        <div class="h-full rounded-full bg-gradient-to-r from-teal-500 to-brand-600 transition-[width] duration-500 ease-(--ease-out-soft) motion-reduce:transition-none" style="width: {{ $percent }}%"></div>
    </div>
</div>
