@props(['name', 'class' => 'size-5'])
{{-- Local line icons: 24px grid, 1.5px stroke, round caps/joins. Decorative by default. --}}
<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @switch($name)
        @case('star') <path d="M12 3.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.9l-5.2 2.7 1-5.8L3.5 9.7l5.9-.9z"/> @break
        @case('check') <path d="M5 12.5l4.5 4.5L19 7.5"/> @break
        @case('check-circle') <circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.8 2.8L16.5 9.5"/> @break
        @case('chevron-down') <path d="M6 9l6 6 6-6"/> @break
        @case('chevron-left') <path d="M15 6l-6 6 6 6"/> @break
        @case('arrow-right') <path d="M5 12h14M13 6l6 6-6 6"/> @break
        @case('menu') <path d="M4 7h16M4 12h16M4 17h16"/> @break
        @case('x') <path d="M6 6l12 12M18 6L6 18"/> @break
        @case('globe') <circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3z"/> @break
        @case('clock') <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/> @break
        @case('bank') <path d="M3 10l9-5 9 5M5 10v8M9.5 10v8M14.5 10v8M19 10v8M3 20h18"/> @break
        @case('device') <rect x="7" y="3" width="10" height="18" rx="2.5"/><path d="M11 17.5h2"/> @break
        @case('paper-off') <path d="M7 3h7l4 4v8M18 19a2 2 0 01-2 2H8a2 2 0 01-2-2V5M3 3l18 18"/> @break
        @case('gift') <rect x="4" y="9" width="16" height="11" rx="2"/><path d="M12 9v11M4 13h16M12 9c-1.5-3-5-3.5-5-1.5S10 9 12 9zm0 0c1.5-3 5-3.5 5-1.5S14 9 12 9z"/> @break
        @case('shield') <path d="M12 3l7 3v5.5c0 4.3-3 8-7 9.5-4-1.5-7-5.2-7-9.5V6l7-3z"/><path d="M9 12l2.2 2.2L15.5 10"/> @break
        @case('lock') <rect x="5" y="10.5" width="14" height="10" rx="2.5"/><path d="M8 10.5V8a4 4 0 018 0v2.5"/> @break
        @case('mail') <rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M4 7l8 6 8-6"/> @break
        @case('user') <circle cx="12" cy="8.5" r="3.5"/><path d="M5 20c.8-3.6 3.6-5.5 7-5.5s6.2 1.9 7 5.5"/> @break
        @case('logout') <path d="M14 4h4a2 2 0 012 2v12a2 2 0 01-2 2h-4M10 16l-4-4 4-4M6 12h10"/> @break
        @case('trash') <path d="M4 7h16M10 11v6M14 11v6M6 7l1 12a2 2 0 002 2h6a2 2 0 002-2l1-12M9 7V4.5h6V7"/> @break
        @case('form') <rect x="5" y="3" width="14" height="18" rx="2.5"/><path d="M9 8h6M9 12h6M9 16h3"/> @break
        @case('chat') <path d="M5 18l-1 3 4-1.5A8.5 8.5 0 1012 3.5 8.5 8.5 0 005 18z"/><path d="M8.5 12h.01M12 12h.01M15.5 12h.01"/> @break
        @case('sign') <path d="M4 20h16M6 16l9.5-9.5a2 2 0 012.8 2.8L8.8 18.8 5 19.5l1-3.5z"/> @break
        @case('info') <circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/> @break
        @case('alert') <circle cx="12" cy="12" r="9"/><path d="M12 7.5v5.5M12 16.5h.01"/> @break
        @case('percent') <path d="M19 5L5 19"/><circle cx="7" cy="7" r="2.5"/><circle cx="17" cy="17" r="2.5"/> @break
        @case('send') <path d="M21 3L10 14M21 3l-7 18-4-7-7-4 18-7z"/> @break
        @case('plus') <path d="M12 5v14M5 12h14"/> @break
        @case('file-check') <path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8l-5-5z"/><path d="M14 3v5h5M9 14l2 2 4-4"/> @break
        @case('spinner') <path d="M12 3a9 9 0 109 9" class="origin-center animate-spin"/> @break
    @endswitch
</svg>
