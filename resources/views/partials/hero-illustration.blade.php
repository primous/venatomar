{{-- Hero artwork: phone with a validated request and coins (original, inline SVG, decorative). --}}
<svg class="{{ $class ?? '' }}" viewBox="0 0 260 230" fill="none" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="pf-hero-coin" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#FFE38A"/><stop offset=".55" stop-color="#F5B300"/><stop offset="1" stop-color="#C98A00"/>
        </linearGradient>
    </defs>
    <ellipse cx="130" cy="226" rx="96" ry="10" fill="#063F30" opacity=".25"/>
    {{-- phone --}}
    <rect x="78" y="22" width="104" height="204" rx="18" fill="#0B1F3A"/>
    <rect x="86" y="34" width="88" height="180" rx="11" fill="#fff"/>
    <rect x="114" y="27" width="32" height="4" rx="2" fill="#24324A"/>
    <rect x="98" y="52" width="40" height="7" rx="3.5" fill="#C6F3DF"/>
    <rect x="98" y="68" width="64" height="20" rx="6" fill="#E3FAF0"/>
    <rect x="104" y="76" width="30" height="4" rx="2" fill="#0E9F77"/>
    <rect x="98" y="96" width="64" height="6" rx="3" fill="#E3E8EF"/>
    <rect x="98" y="96" width="38" height="6" rx="3" fill="#F26B1D"/>
    <circle cx="130" cy="148" r="24" fill="#10B981"/>
    <path d="M118 148l8 8 16-16" stroke="#fff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
    <rect x="98" y="186" width="64" height="16" rx="8" fill="#F26B1D"/>
    {{-- coins --}}
    <g><circle cx="44" cy="70" r="26" fill="#B87D00"/><circle cx="44" cy="66" r="26" fill="url(#pf-hero-coin)"/><circle cx="44" cy="66" r="18" stroke="#FFF3C4" stroke-opacity=".7" stroke-width="2.5"/></g>
    <g><circle cx="216" cy="44" r="22" fill="#B87D00"/><circle cx="216" cy="40" r="22" fill="url(#pf-hero-coin)"/><circle cx="216" cy="40" r="15" stroke="#FFF3C4" stroke-opacity=".7" stroke-width="2.5"/></g>
    <g><circle cx="222" cy="128" r="17" fill="#B87D00"/><circle cx="222" cy="125" r="17" fill="url(#pf-hero-coin)"/><circle cx="222" cy="125" r="11" stroke="#FFF3C4" stroke-opacity=".7" stroke-width="2"/></g>
    <path d="M40 140l4 9 9 4-9 4-4 9-4-9-9-4 9-4z" fill="#fff" opacity=".7"/>
    <path d="M200 190l3 6 6 3-6 3-3 6-3-6-6-3 6-3z" fill="#fff" opacity=".6"/>
</svg>
