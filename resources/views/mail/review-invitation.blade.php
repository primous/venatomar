{{-- Review invitation: sent once, no personal data besides the first name and reference. --}}
<x-mail-layout :message="$message ?? null" :title="__('mail.review.subject', ['brand' => config('venatomar.brand')])" :preheader="__('mail.review.preheader')">
    <h1 style="margin:0 0 14px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-size:26px;line-height:1.25;color:#0B1F3A;font-weight:800;letter-spacing:-0.02em;text-align:center;">
        {{ __('mail.review.title', ['name' => $firstName]) }}
    </h1>

    <p style="margin:0 0 28px;font-size:15.5px;line-height:1.6;color:#334155;text-align:center;">
        {{ __('mail.review.intro', ['reference' => $reference, 'brand' => config('venatomar.brand')]) }}
    </p>

    <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0 0 16px;">
        <tr>
            <td align="center">
                <a href="{{ $reviewUrl }}" class="cta-btn" style="display:inline-block;min-width:240px;padding:15px 32px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-weight:700;font-size:15px;color:#FFFFFF;text-decoration:none;border-radius:10px;background:#0FA57E;text-align:center;">
                    {{ __('mail.review.button') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:13.5px;line-height:1.5;color:#64748B;text-align:center;">
        {{ __('mail.review.once') }}
    </p>

    <x-slot:footer>
        <p style="margin:0 0 6px;">
            <a href="{{ $privacyUrl }}" style="color:#475569;font-weight:700;text-decoration:none;">{{ __('footer.privacy') }}</a>
        </p>
        <p style="margin:0;color:#94A3B8;">{{ __('mail.footer.why', ['brand' => config('venatomar.brand')]) }}</p>
    </x-slot:footer>
</x-mail-layout>
