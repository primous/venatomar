<x-mail-layout :message="$message ?? null" :title="__('mail.portal.credited_subject', ['account' => $accountNumber, 'amount' => $amount])" :preheader="__('mail.portal.credited_preheader', ['amount' => $amount])">
    <div style="margin-bottom:20px;">
        <span style="display:inline-block;padding:6px 14px;background:#E6F5F1;border:1px solid rgba(15,165,126,0.3);border-radius:20px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:12px;font-weight:700;color:#0A5E4E;text-transform:uppercase;letter-spacing:0.5px;">
            ✓ {{ __('mail.portal.credited_badge') }}
        </span>
    </div>

    <h1 style="margin:0 0 16px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:22px;line-height:1.3;color:#0B1F3A;font-weight:700;">
        {{ __('mail.portal.welcome_title', ['name' => $name]) }}
    </h1>

    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#24324A;">
        {{ __('mail.portal.credited_intro') }}
    </p>

    <!-- Credit details recap -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F7FA;border-radius:12px;font-size:15px;line-height:1.6;margin-bottom:24px;">
        <tr>
            <td style="padding:14px 20px 6px;color:#5B6B82;width:45%;">- {{ __('mail.portal.account_number') }} :</td>
            <td style="padding:14px 20px 6px;text-align:right;font-weight:700;color:#0B1F3A;font-family:monospace;">{{ $accountNumber }}</td>
        </tr>
        <tr>
            <td style="padding:6px 20px;color:#5B6B82;">- {{ __('mail.portal.amount_credited') }} :</td>
            <td style="padding:6px 20px;text-align:right;font-weight:800;color:#0FA57E;font-size:18px;">+ {{ $amount }}</td>
        </tr>
        <tr>
            <td style="padding:6px 20px;color:#5B6B82;">- {{ __('mail.portal.description') }} :</td>
            <td style="padding:6px 20px;text-align:right;font-weight:600;color:#0B1F3A;">{{ $description }}</td>
        </tr>
        <tr>
            <td style="padding:6px 20px;color:#5B6B82;">- {{ __('mail.portal.operation_date') }} :</td>
            <td style="padding:6px 20px;text-align:right;font-weight:600;color:#5B6B82;">{{ $date }}</td>
        </tr>
        <tr>
            <td style="padding:6px 20px 14px;color:#5B6B82;">- {{ __('mail.portal.new_balance') }} :</td>
            <td style="padding:6px 20px 14px;text-align:right;font-weight:700;color:#0A5E4E;font-size:16px;">{{ $newBalance }}</td>
        </tr>
    </table>

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
