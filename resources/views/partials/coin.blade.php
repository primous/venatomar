{{-- One gold coin seen at an angle (original inline SVG, decorative). --}}
<svg class="{{ $class ?? '' }}" viewBox="0 0 120 120" fill="none" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="pf-coin-face" x1=".15" y1=".1" x2=".85" y2=".95">
            <stop offset="0" stop-color="#FFF0B3"/><stop offset=".35" stop-color="#FFD45C"/><stop offset=".75" stop-color="#F0A800"/><stop offset="1" stop-color="#C98400"/>
        </linearGradient>
        <linearGradient id="pf-coin-edge" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#D99400"/><stop offset="1" stop-color="#8F5E00"/>
        </linearGradient>
    </defs>
    <ellipse cx="64" cy="64" rx="46" ry="50" fill="url(#pf-coin-edge)"/>
    <ellipse cx="58" cy="58" rx="46" ry="50" fill="url(#pf-coin-face)"/>
    <ellipse cx="58" cy="58" rx="36" ry="39.5" stroke="#FFF6D6" stroke-opacity=".75" stroke-width="3"/>
    <ellipse cx="58" cy="58" rx="36" ry="39.5" stroke="#C98400" stroke-opacity=".35" stroke-width="1" transform="translate(1.5 1.5)"/>
    <text x="58" y="74" text-anchor="middle" font-family="Manrope, Arial, sans-serif" font-size="46" font-weight="800" fill="#C27F00" opacity=".9">{{ $symbol ?? '€' }}</text>
    <path d="M26 40c6-14 18-22 32-22" stroke="#fff" stroke-opacity=".7" stroke-width="4" stroke-linecap="round"/>
</svg>
