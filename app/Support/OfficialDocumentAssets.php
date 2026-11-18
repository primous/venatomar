<?php

namespace App\Support;

/**
 * Shared official visual assets inspired by ministerial / governmental letters:
 * authentic circular ink stamp with fountain-pen signature overlay, paraphes,
 * and prestige tricolor bar.
 */
final class OfficialDocumentAssets
{
    /** Returns a centered 3-color bar HTML (Green, Orange, White). */
    public static function tricolorBar(string $width = '75mm', string $height = '3.2mm', string $margin = '0 auto'): string
    {
        return '<table style="width: '.$width.'; margin: '.$margin.'; border-collapse: collapse; border-spacing: 0; border: 0.8px solid #cbd5e1;">'
            .'<tr>'
            .'<td style="width: 33.33%; height: '.$height.'; background-color: #0E7C66;"></td>'
            .'<td style="width: 33.34%; height: '.$height.'; background-color: #FF8200;"></td>'
            .'<td style="width: 33.33%; height: '.$height.'; background-color: #FFFFFF;"></td>'
            .'</tr>'
            .'</table>';
    }

    /** Small tricolor stripe under department title (Green, Orange, White). */
    public static function subTricolorBar(): string
    {
        return '<table style="width: 34mm; height: 1.8mm; margin: 1.2mm 0; border-collapse: collapse; border-spacing: 0; border: 0.6px solid #cbd5e1;">'
            .'<tr>'
            .'<td style="width: 33.33%; height: 1.8mm; background-color: #0E7C66;"></td>'
            .'<td style="width: 33.34%; height: 1.8mm; background-color: #FF8200;"></td>'
            .'<td style="width: 33.33%; height: 1.8mm; background-color: #FFFFFF;"></td>'
            .'</tr>'
            .'</table>';
    }

    /** Small handwritten blue ink flourish / paraphe above N°. */
    public static function parapheSvg(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 40" width="42" height="20">'
            .'<path d="M12,28 C20,12 30,8 38,15 C44,22 32,35 25,30 C20,25 26,14 44,16 C58,18 64,10 74,15" fill="none" stroke="#1d4ed8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
            .'<path d="M24,18 L50,12" fill="none" stroke="#1d4ed8" stroke-width="1.3" stroke-linecap="round"/>'
            .'</svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /** Authentic circular blue ink seal with overlapping cursive blue signature. */
    public static function stampAndSignatureSvg(string $dept = 'DIRECTION GENERALE', string $visa = 'VISA CONTRACTUEL'): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 130" width="185" height="98">'
            // Authentic circular blue stamp rotated -5 degrees
            .'<g transform="translate(65, 65) rotate(-5)">'
            .'<circle cx="0" cy="0" r="54" fill="none" stroke="#1d4ed8" stroke-width="2.2" opacity="0.9" />'
            .'<circle cx="0" cy="0" r="50" fill="none" stroke="#2563eb" stroke-width="1" stroke-dasharray="3,1.8" opacity="0.85" />'
            .'<circle cx="0" cy="0" r="38" fill="none" stroke="#1d4ed8" stroke-width="1.2" opacity="0.85" />'
            .'<circle cx="0" cy="0" r="35" fill="#eff6ff" fill-opacity="0.35" stroke="#3b82f6" stroke-width="0.6" />'
            .'<text x="0" y="-24" font-family="DejaVu Sans, sans-serif" font-size="6.5" font-weight="bold" fill="#1d4ed8" text-anchor="middle" letter-spacing="1.2">VENATOMAR</text>'
            .'<text x="0" y="-16" font-family="DejaVu Sans, sans-serif" font-size="4.6" font-weight="bold" fill="#2563eb" text-anchor="middle" letter-spacing="0.8">SERVICES FINANCIERS</text>'
            // Center shield / crest
            .'<g transform="translate(0, -1) scale(0.68)">'
            .'<path d="M-15,-13 C-15,-13 0,-18 0,-18 C0,-18 15,-13 15,-13 C15,6 8,18 0,23 C-8,18 -15,6 -15,-13 Z" fill="#1d4ed8" opacity="0.95" />'
            .'<path d="M-5,1 L-1,5 L7,-4" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />'
            .'</g>'
            .'<text x="0" y="19" font-family="DejaVu Sans, sans-serif" font-size="5" font-weight="bold" fill="#1d4ed8" text-anchor="middle" letter-spacing="0.8">'.$dept.'</text>'
            .'<text x="0" y="27" font-family="DejaVu Sans, sans-serif" font-size="4.2" font-weight="bold" fill="#2563eb" text-anchor="middle" letter-spacing="0.5">'.$visa.'</text>'
            .'<text x="0" y="34" font-family="DejaVu Sans, sans-serif" font-size="3.8" fill="#4b5563" text-anchor="middle">(VTM)</text>'
            .'</g>'
            // Blue fountain pen signature overlapping the stamp
            .'<g transform="translate(42, 10)">'
            .'<path d="M 5,60 C 22,35 34,18 52,24 C 64,28 42,65 35,58 C 28,52 36,30 65,34 C 85,37 100,22 112,30 C 122,36 105,62 135,46 C 152,36 170,30 185,34" fill="none" stroke="#1e3a8a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />'
            .'<path d="M 38,36 L 75,26" fill="none" stroke="#1e3a8a" stroke-width="1.6" stroke-linecap="round" />'
            .'<path d="M 25,68 C 70,64 120,62 175,56" fill="none" stroke="#1e3a8a" stroke-width="1.4" stroke-linecap="round" />'
            .'</g>'
            .'</svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /** Authentic blue ink signature for right column (approver / borrower). */
    public static function rightSignatureSvg(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 55" width="110" height="38">'
            .'<path d="M15,35 C32,20 42,12 55,18 C65,22 45,45 38,38 C32,32 40,16 65,22 C85,26 98,15 110,22 C120,28 105,48 130,34 C142,26 150,22 158,25" fill="none" stroke="#1e3a8a" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>'
            .'<path d="M38,24 L72,16" fill="none" stroke="#1e3a8a" stroke-width="1.4" stroke-linecap="round"/>'
            .'<path d="M28,44 C68,42 110,40 152,36" fill="none" stroke="#1e3a8a" stroke-width="1.2" stroke-linecap="round"/>'
            .'</svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
