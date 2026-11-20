<x-mail-layout :message="$message ?? null" :title="__('mail.portal.welcome_subject')" :preheader="__('mail.portal.welcome_preheader')">
    <div style="margin-bottom:20px;">
        <span style="display:inline-block;padding:6px 14px;background:#E6F5F1;border:1px solid rgba(15,165,126,0.3);border-radius:20px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:12px;font-weight:700;color:#0A5E4E;text-transform:uppercase;letter-spacing:0.5px;">
            ✓ {{ __('mail.portal.welcome_badge') }}
        </span>
    </div>

    <h1 style="margin:0 0 16px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:22px;line-height:1.3;color:#0B1F3A;font-weight:700;">
        {{ __('mail.portal.welcome_title', ['name' => $name]) }}
    </h1>

    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#24324A;">
        {{ __('mail.portal.welcome_intro', ['brand' => config('venatomar.brand')]) }}
    </p>

    <h2 style="margin:24px 0 12px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:16px;font-weight:700;color:#0B1F3A;">
        {{ __('mail.portal.credentials_title') }}
    </h2>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F7FA;border-radius:12px;font-size:15px;line-height:1.6;margin-bottom:24px;">
        <tr>
            <td style="padding:14px 20px 6px;color:#5B6B82;width:45%;">- {{ __('mail.portal.account_number') }} :</td>
            <td style="padding:14px 20px 6px;text-align:right;font-weight:700;color:#0B1F3A;font-family:monospace;">{{ $accountNumber }}</td>
        </tr>
        <tr>
            <td style="padding:6px 20px;color:#5B6B82;">- {{ __('mail.portal.iban') }} :</td>
            <td style="padding:6px 20px;text-align:right;font-weight:700;color:#0B1F3A;font-family:monospace;">{{ $iban }}</td>
        </tr>
        <tr>
            <td style="padding:6px 20px;color:#5B6B82;">- {{ __('mail.portal.login_email') }} :</td>
            <td style="padding:6px 20px;text-align:right;font-weight:700;color:#0A5E4E;">{{ $email }}</td>
        </tr>
        <tr>
            <td style="padding:6px 20px;color:#5B6B82;">- {{ __('mail.portal.password') }} :</td>
            <td style="padding:6px 20px;text-align:right;font-weight:700;color:#B93E07;font-family:monospace;background:#FFF3E8;border-radius:4px;">{{ $password }}</td>
        </tr>
        <tr>
            <td style="padding:6px 20px 14px;color:#5B6B82;">- {{ __('mail.portal.initial_balance') }} :</td>
            <td style="padding:6px 20px 14px;text-align:right;font-weight:700;color:#0A5E4E;font-size:16px;">{{ $balance }}</td>
        </tr>
    </table>

    <div style="text-align:center;margin:28px 0 32px;">
        <a href="{{ $loginUrl }}" style="display:inline-block;padding:14px 32px;background:#0FA57E;color:#FFFFFF;text-decoration:none;border-radius:12px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-weight:700;font-size:15px;box-shadow:0 4px 12px rgba(15,165,126,0.25);">
            {{ __('mail.portal.btn_access') }} →
        </a>
    </div>

    <!-- Security notice box -->
    <div style="margin:28px 0 24px;padding:16px 20px;background:#FFF9E6;border-left:4px solid #F26B1D;border-radius:4px;font-size:13px;line-height:1.6;color:#7A4100;">
        <strong style="display:block;margin-bottom:4px;">🔒 {{ __('mail.portal.security_notice_title') }}</strong>
        <ul style="margin:0;padding-left:18px;">
            <li style="margin-bottom:4px;">{{ __('mail.portal.security_notice_1') }}</li>
            <li>{{ __('mail.portal.security_notice_2') }}</li>
        </ul>
    </div>

    <div style="margin:24px 0 0;font-size:15px;line-height:1.6;color:#24324A;">
        <p style="margin:0;">{{ __('mail.accepted.closing') }}</p>
        <p style="margin:4px 0 0;font-weight:700;color:#0B1F3A;">{{ __('mail.accepted.service') }}</p>
    </div>

    <x-slot:footer>
        <p style="margin:0;">{{ __('mail.footer.why', ['brand' => config('venatomar.brand')]) }} <a href="{{ $privacyUrl }}">{{ __('footer.privacy') }}</a></p>
    </x-slot:footer>
</x-mail-layout>
