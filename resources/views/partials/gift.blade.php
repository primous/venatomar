{{-- Gift box with a validated card and a coin (original inline SVG, decorative). --}}
<svg class="{{ $class ?? '' }}" viewBox="0 0 220 170" fill="none" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="pf-gift-box" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2FD3A2"/><stop offset="1" stop-color="#0A8A66"/></linearGradient>
        <linearGradient id="pf-gift-gold" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#FFE38A"/><stop offset=".6" stop-color="#F5B300"/><stop offset="1" stop-color="#B87D00"/></linearGradient>
    </defs>
    <rect x="96" y="14" width="80" height="96" rx="12" fill="#11B386" transform="rotate(10 136 62)"/>
    <rect x="102" y="20" width="68" height="84" rx="9" fill="#24C796" transform="rotate(10 136 62)"/>
    <path d="M122 60l10 10 20-20" stroke="#fff" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
    <rect x="34" y="78" width="112" height="86" rx="8" fill="url(#pf-gift-box)"/>
    <rect x="26" y="62" width="128" height="26" rx="6" fill="#2FD3A2"/>
    <rect x="80" y="62" width="20" height="102" fill="url(#pf-gift-gold)"/>
    <path d="M90 62c-14-26-42-28-40-10 2 12 26 12 40 10zm0 0c14-26 42-28 40-10-2 12-26 12-40 10z" fill="url(#pf-gift-gold)"/>
    <circle cx="162" cy="140" r="22" fill="#B87D00"/>
    <circle cx="159" cy="137" r="22" fill="url(#pf-gift-gold)"/>
    <text x="159" y="146" text-anchor="middle" font-family="Manrope, Arial, sans-serif" font-size="24" font-weight="800" fill="#B87D00">€</text>
</svg>
