<x-mail-layout :message="$message ?? null" :title="__('mail.portal.status_subject', ['account' => $accountNumber])" :preheader="__('mail.portal.status_preheader')">
    <div style="margin-bottom:20px;">
        <span style="display:inline-block;padding:6px 14px;background:{{ $isFrozen ? '#FFF3E8' : '#E6F5F1' }};border:1px solid {{ $isFrozen ? 'rgba(242,107,29,0.3)' : 'rgba(15,165,126,0.3)' }};border-radius:20px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:12px;font-weight:700;color:{{ $isFrozen ? '#B93E07' : '#0A5E4E' }};text-transform:uppercase;letter-spacing:0.5px;">
            ● {{ __('mail.portal.status_badge') }} : {{ $isFrozen ? 'RESTRICTION' : 'ACTIF' }}
        </span>
    </div>

    <h1 style="margin:0 0 16px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:22px;line-height:1.3;color:#0B1F3A;font-weight:700;">
        {{ __('mail.portal.welcome_title', ['name' => $name]) }}
    </h1>

    @if($isFrozen)
        <div style="margin:20px 0;padding:18px 20px;background:#FFF9E6;border-left:4px solid #F26B1D;border-radius:4px;font-size:14px;line-height:1.6;color:#7A4100;">
            <p style="margin:0 0 10px;font-weight:700;">{{ __('mail.portal.status_frozen_intro') }}</p>
            @if(!empty($reason))
                <p style="margin:0 0 10px;font-size:13px;color:#7A4100;"><em>{{ $reason }}</em></p>
            @endif
            <p style="margin:0;">{{ __('mail.portal.status_frozen_action') }}</p>
        </div>
    @else
        <div style="margin:20px 0;padding:18px 20px;background:#E6F5F1;border-left:4px solid #0FA57E;border-radius:4px;font-size:14px;line-height:1.6;color:#0A5E4E;">
            <p style="margin:0;font-weight:600;">{{ __('mail.portal.status_active_intro') }}</p>
        </div>
    @endif

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin:28px auto 14px;text-align:center;">
        <tr>
            <td align="center" style="border-radius:12px;background:#0FA57E;">
                <a href="{{ $loginUrl }}" target="_blank" rel="noopener noreferrer" style="display:inline-block;padding:14px 32px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-weight:700;font-size:15px;color:#FFFFFF;text-decoration:none;border-radius:12px;background:#0FA57E;border:1px solid #0FA57E;">
                    {{ __('mail.portal.btn_access') }} &rarr;
                </a>
            </td>
        </tr>
    </table>
    <p style="margin:0 0 28px;text-align:center;font-size:12px;line-height:1.4;color:#5B6B82;">
        {{ __('mail.portal.link_fallback') }}<br>
        <a href="{{ $loginUrl }}" target="_blank" rel="noopener noreferrer" style="color:#0A5E4E;word-break:break-all;text-decoration:underline;font-weight:600;">{{ $loginUrl }}</a>
    </p>

    <div style="margin:24px 0 0;font-size:15px;line-height:1.6;color:#24324A;">
        <p style="margin:0;">{{ __('mail.accepted.closing') }}</p>
        <p style="margin:4px 0 0;font-weight:700;color:#0B1F3A;">{{ __('mail.accepted.service') }}</p>
    </div>

    <x-slot:footer>
        <p style="margin:0;">{{ __('mail.footer.why', ['brand' => config('venatomar.brand')]) }} <a href="{{ $privacyUrl }}">{{ __('footer.privacy') }}</a></p>
    </x-slot:footer>
</x-mail-layout>

