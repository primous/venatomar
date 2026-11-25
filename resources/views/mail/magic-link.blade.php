<x-mail-layout :message="$message ?? null" :title="__('mail.magic.subject', ['brand' => config('venatomar.brand')])">
    <h1 style="margin:0 0 14px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-size:26px;line-height:1.25;color:#0B1F3A;font-weight:800;letter-spacing:-0.02em;text-align:center;">
        {{ __('mail.magic.title') }}
    </h1>

    <p style="margin:0 0 28px;font-size:15.5px;line-height:1.6;color:#334155;text-align:center;">
        {{ __('mail.magic.intro', ['minutes' => $minutes]) }}
    </p>

    <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0 0 16px;">
        <tr>
            <td align="center">
                <a href="{{ $url }}" class="cta-btn" style="display:inline-block;min-width:240px;padding:15px 32px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-weight:700;font-size:15px;color:#FFFFFF;text-decoration:none;border-radius:10px;background:#0FA57E;text-align:center;">
                    {{ __('mail.magic.button') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:13.5px;line-height:1.5;color:#64748B;text-align:center;">
        {{ __('mail.magic.ignore') }}
    </p>

    <x-slot:footer>
        <p style="margin:0;">
            <a href="{{ $privacyUrl }}" style="color:#475569;font-weight:700;text-decoration:none;">{{ __('footer.privacy') }}</a>
        </p>
    </x-slot:footer>
</x-mail-layout>
