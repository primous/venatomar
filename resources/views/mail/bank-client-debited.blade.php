<x-mail-layout :message="$message ?? null" :title="__('mail.portal.debited_subject', ['account' => $accountNumber, 'amount' => $amount])" :preheader="__('mail.portal.debited_preheader', ['amount' => $amount])">
    <div style="text-align:center;margin:0 0 16px;">
        <span style="display:inline-block;padding:6px 14px;background:#FFF3E8;border:1px solid rgba(242,107,29,0.28);border-radius:999px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-size:11px;font-weight:700;color:#B93E07;text-transform:uppercase;letter-spacing:0.6px;">
            ! {{ __('mail.portal.debited_badge') }}
        </span>
    </div>

    <h1 style="margin:0 0 14px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-size:25px;line-height:1.25;color:#0B1F3A;font-weight:800;letter-spacing:-0.02em;text-align:center;">
        {{ __('mail.portal.welcome_title', ['name' => $name]) }}
    </h1>

    <p style="margin:0 0 26px;font-size:15.5px;line-height:1.6;color:#334155;text-align:center;">
        {{ __('mail.portal.debited_intro') }}
    </p>

    {{-- Primary CTA --}}
    <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0 0 14px;">
        <tr>
            <td align="center">
                <a href="{{ $loginUrl }}" target="_blank" rel="noopener noreferrer" class="cta-btn" style="display:inline-block;min-width:240px;padding:15px 32px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-weight:700;font-size:15px;color:#FFFFFF;text-decoration:none;border-radius:10px;background:#0FA57E;text-align:center;">
                    {{ __('mail.portal.btn_access') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 26px;font-size:12.5px;line-height:1.45;color:#64748B;text-align:center;">
        {{ __('mail.portal.link_fallback') }}<br>
        <a href="{{ $loginUrl }}" target="_blank" rel="noopener noreferrer" style="color:#0A5E4E;word-break:break-all;text-decoration:underline;font-weight:600;">{{ $loginUrl }}</a>
    </p>

    {{-- Debit details recap --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F6F8FB;border:1px solid #E6EBF1;border-radius:14px;font-size:14.5px;line-height:1.5;margin:0 0 22px;">
        <tr>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;color:#5B6B82;">{{ __('mail.portal.account_number') }}</td>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;text-align:right;font-weight:700;color:#0B1F3A;font-family:monospace;">{{ $accountNumber }}</td>
        </tr>
        <tr>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;color:#5B6B82;">{{ __('mail.portal.amount_debited') }}</td>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;text-align:right;font-weight:800;color:#B93E07;font-size:18px;">- {{ $amount }}</td>
        </tr>
        <tr>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;color:#5B6B82;">{{ __('mail.portal.description') }}</td>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;text-align:right;font-weight:600;color:#0B1F3A;">{{ $description }}</td>
        </tr>
        <tr>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;color:#5B6B82;">{{ __('mail.portal.operation_date') }}</td>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;text-align:right;font-weight:600;color:#5B6B82;">{{ $date }}</td>
        </tr>
        <tr>
            <td style="padding:13px 18px;color:#5B6B82;">{{ __('mail.portal.new_balance') }}</td>
            <td style="padding:13px 18px;text-align:right;font-weight:800;color:#0B1F3A;font-size:16px;">{{ $newBalance }}</td>
        </tr>
    </table>

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
