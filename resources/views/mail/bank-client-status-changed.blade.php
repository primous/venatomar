<x-mail-layout :message="$message ?? null" :title="__('mail.portal.status_subject', ['account' => $accountNumber])" :preheader="__('mail.portal.status_preheader')">
    <div style="text-align:center;margin:0 0 16px;">
        <span style="display:inline-block;padding:6px 14px;background:{{ $isFrozen ? '#FFF3E8' : '#E6F5F1' }};border:1px solid {{ $isFrozen ? 'rgba(242,107,29,0.28)' : 'rgba(15,165,126,0.28)' }};border-radius:999px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-size:11px;font-weight:700;color:{{ $isFrozen ? '#B93E07' : '#0A5E4E' }};text-transform:uppercase;letter-spacing:0.6px;">
            ● {{ __('mail.portal.status_badge') }} : {{ $isFrozen ? __('mail.portal.status_frozen_label') : __('mail.portal.status_active_label') }}
        </span>
    </div>

    <h1 style="margin:0 0 18px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-size:25px;line-height:1.25;color:#0B1F3A;font-weight:800;letter-spacing:-0.02em;text-align:center;">
        {{ __('mail.portal.welcome_title', ['name' => $name]) }}
    </h1>

    @if($isFrozen)
        <div style="margin:0 0 24px;padding:18px 20px;background:#FFF9E6;border:1px solid #FDE68A;border-left:4px solid #F26B1D;border-radius:10px;font-size:14.5px;line-height:1.6;color:#78350F;text-align:center;">
            <p style="margin:0 0 10px;font-weight:700;">{{ __('mail.portal.status_frozen_intro') }}</p>
            @if(!empty($reason))
                <p style="margin:0 0 10px;font-size:13.5px;color:#78350F;"><em>{{ $reason }}</em></p>
            @endif
            <p style="margin:0;">{{ __('mail.portal.status_frozen_action') }}</p>
        </div>
    @else
        <div style="margin:0 0 24px;padding:18px 20px;background:#E6F5F1;border:1px solid rgba(15,165,126,0.28);border-left:4px solid #0FA57E;border-radius:10px;font-size:14.5px;line-height:1.6;color:#0A5E4E;font-weight:600;text-align:center;">
            <p style="margin:0;">{{ __('mail.portal.status_active_intro') }}</p>
        </div>
    @endif

    <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0 0 14px;">
        <tr>
            <td align="center">
                <a href="{{ $loginUrl }}" target="_blank" rel="noopener noreferrer" class="cta-btn" style="display:inline-block;min-width:240px;padding:15px 32px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-weight:700;font-size:15px;color:#FFFFFF;text-decoration:none;border-radius:10px;background:#0FA57E;text-align:center;">
                    {{ __('mail.portal.btn_access') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 22px;font-size:12.5px;line-height:1.45;color:#64748B;text-align:center;">
        {{ __('mail.portal.link_fallback') }}<br>
        <a href="{{ $loginUrl }}" target="_blank" rel="noopener noreferrer" style="color:#0A5E4E;word-break:break-all;text-decoration:underline;font-weight:600;">{{ $loginUrl }}</a>
    </p>

    <p style="margin:0;font-size:14px;line-height:1.5;color:#64748B;text-align:center;">
        {{ __('mail.accepted.closing') }} <strong style="color:#0B1F3A;">{{ __('mail.accepted.service') }}</strong>
    </p>

    <x-slot:footer>
        <p style="margin:0 0 6px;">
            <a href="{{ $privacyUrl }}" style="color:#475569;font-weight:700;text-decoration:none;">{{ __('footer.privacy') }}</a>
            &nbsp;·&nbsp;
            <a href="mailto:{{ $contactEmail }}" style="color:#475569;font-weight:700;text-decoration:none;">{{ __('mail.footer.contact') }}</a>
        </p>
        <p style="margin:0;color:#94A3B8;">{{ __('mail.footer.why', ['brand' => config('venatomar.brand')]) }}</p>
    </x-slot:footer>
</x-mail-layout>
