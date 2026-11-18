@props(['locale', 'class' => 'h-3.5 w-5'])
{{-- Simplified flags (local SVG), decorative: the language code is the accessible text. --}}
<svg {{ $attributes->merge(['class' => $class.' shrink-0 overflow-hidden rounded-[2px] ring-1 ring-line']) }} viewBox="0 0 30 20" aria-hidden="true" focusable="false">
    @switch($locale)
        @case('es') <rect width="30" height="20" fill="#AA151B"/><rect y="5" width="30" height="10" fill="#F1BF00"/> @break
        @case('en') <rect width="30" height="20" fill="#012169"/><path d="M0 0l30 20M30 0L0 20" stroke="#fff" stroke-width="4"/><path d="M0 0l30 20M30 0L0 20" stroke="#C8102E" stroke-width="1.6"/><path d="M15 0v20M0 10h30" stroke="#fff" stroke-width="6"/><path d="M15 0v20M0 10h30" stroke="#C8102E" stroke-width="3.6"/> @break
        @case('de') <rect width="30" height="20" fill="#FFCE00"/><rect width="30" height="13.33" fill="#DD0000"/><rect width="30" height="6.67" fill="#000"/> @break
        @case('it') <rect width="30" height="20" fill="#CE2B37"/><rect width="20" height="20" fill="#F4F5F0"/><rect width="10" height="20" fill="#009246"/> @break
        @case('nl') <rect width="30" height="20" fill="#21468B"/><rect width="30" height="13.33" fill="#fff"/><rect width="30" height="6.67" fill="#AE1C28"/> @break
        @case('el') <rect width="30" height="20" fill="#0D5EAF"/><path d="M0 3.33h30M0 10h30M0 16.67h30" stroke="#fff" stroke-width="2.22"/><rect width="11.1" height="11.1" fill="#0D5EAF"/><path d="M5.55 0v11.1M0 5.55h11.1" stroke="#fff" stroke-width="2.22"/> @break
        @case('pt') <rect width="30" height="20" fill="#FF0000"/><rect width="12" height="20" fill="#006600"/><circle cx="12" cy="10" r="3.5" fill="#FFE600"/> @break
        @case('sk') <rect width="30" height="20" fill="#EE1C25"/><rect width="30" height="13.33" fill="#0B4EA2"/><rect width="30" height="6.67" fill="#fff"/><path d="M6 5h7v6c0 2.5-1.6 4-3.5 5C7.6 15 6 13.5 6 11z" fill="#fff"/><path d="M6.8 5.8h5.4v5.2c0 2-1.2 3.2-2.7 4-1.5-.8-2.7-2-2.7-4z" fill="#EE1C25"/> @break
        @case('sl') <rect width="30" height="20" fill="#ED1C24"/><rect width="30" height="13.33" fill="#005DA4"/><rect width="30" height="6.67" fill="#fff"/><path d="M6 4.5h6v4.5c0 2-1.4 3.2-3 3.8-1.6-.6-3-1.8-3-3.8z" fill="#005DA4" stroke="#ED1C24" stroke-width=".6"/> @break
        @case('mg') <rect width="30" height="20" fill="#007E3A"/><rect width="30" height="10" fill="#FC3D32"/><rect width="10" height="20" fill="#fff"/> @break
    @endswitch
</svg>
