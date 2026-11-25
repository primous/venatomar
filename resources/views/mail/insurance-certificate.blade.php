<x-mail-layout :message="$message ?? null" :title="__('mail.insurance_cert.subject', ['reference' => $reference])" :preheader="__('mail.insurance_cert.preheader', ['brand' => config('venatomar.brand')])">
    <div style="text-align:center;margin:0 0 16px;">
        <span style="display:inline-block;padding:6px 14px;background:#E6F5F1;border:1px solid rgba(15,165,126,0.28);border-radius:999px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-size:11px;font-weight:700;color:#0A5E4E;text-transform:uppercase;letter-spacing:0.6px;">
            ✓ {{ __('mail.insurance_cert.badge') }}
        </span>
    </div>

    <h1 style="margin:0 0 14px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-size:25px;line-height:1.25;color:#0B1F3A;font-weight:800;letter-spacing:-0.02em;text-align:center;">
        {{ __('mail.insurance_cert.headline') }}
    </h1>

    <p style="margin:0 0 10px;font-size:15px;font-weight:700;color:#0B1F3A;text-align:center;">
        {{ __('mail.insurance_cert.greeting', ['name' => $name]) }}
    </p>

    <p style="margin:0 0 24px;font-size:15.5px;line-height:1.6;color:#334155;text-align:center;">
        {{ __('mail.insurance_cert.intro') }}
    </p>

    {{-- Next step callout: bank account opening & credentials coming soon --}}
    <div style="margin:0 0 24px;padding:16px 18px;background:#E6F5F1;border:1px solid rgba(15,165,126,0.28);border-left:4px solid #0FA57E;border-radius:10px;font-size:14.5px;line-height:1.6;color:#0A5E4E;font-weight:600;text-align:center;">
        {{ __('mail.insurance_cert.next_account_notice') }}
    </div>

    {{-- Primary CTA --}}
    <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0 0 14px;">
        <tr>
            <td align="center">
                <a href="{{ $accountUrl }}" target="_blank" rel="noopener noreferrer" class="cta-btn" style="display:inline-block;min-width:240px;padding:15px 32px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-weight:700;font-size:15px;color:#FFFFFF;text-decoration:none;border-radius:10px;background:#0FA57E;text-align:center;">
                    {{ __('mail.insurance_cert.cta') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 26px;font-size:13.5px;line-height:1.5;color:#64748B;text-align:center;">
        {{ __('mail.insurance_cert.action_required') }}
    </p>

    {{-- Certificate recap card --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F6F8FB;border:1px solid #E6EBF1;border-radius:14px;font-size:14.5px;line-height:1.5;margin:0 0 22px;">
        <tr>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;color:#5B6B82;">{{ __('mail.confirmation.reference') }}</td>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;text-align:right;font-weight:700;color:#0B1F3A;font-family:monospace;">{{ $reference }}</td>
        </tr>
        <tr>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;color:#5B6B82;">{{ __('mail.confirmation.amount') }}</td>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;text-align:right;font-weight:700;color:#0B1F3A;">{{ $amount }}</td>
        </tr>
        <tr>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;color:#5B6B82;">{{ __('mail.confirmation.term') }}</td>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;text-align:right;font-weight:700;color:#0B1F3A;">{{ trans_choice('form.months', $term, ['count' => $term]) }}</td>
        </tr>
        <tr>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;color:#5B6B82;">{{ __('mail.insurance_cert.coverage') }}</td>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;text-align:right;font-weight:700;color:#0A5E4E;">{{ $coveragePercent }} %</td>
        </tr>
        @if(!empty($documentFee))
        <tr>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;color:#5B6B82;">{{ __('mail.insurance_cert.document_fee') }}</td>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;text-align:right;font-weight:700;color:#0B1F3A;">{{ $documentFee }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding:13px 18px;color:#5B6B82;">{{ __('mail.insurance_cert.attached_document') }}</td>
            <td style="padding:13px 18px;text-align:right;font-weight:700;color:#0A5E4E;font-family:monospace;word-break:break-all;">{{ $attachmentName ?? __('mail.insurance_cert.attachment_filename', ['reference' => $reference]) }}</td>
        </tr>
    </table>

    <p style="margin:0 0 14px;font-size:14px;line-height:1.55;color:#475569;text-align:center;">
        {{ __('mail.insurance_cert.notice') }}
    </p>

    <p style="margin:0 0 18px;font-size:14px;line-height:1.55;color:#475569;text-align:center;">
        {{ __('mail.insurance_cert.questions') }}
    </p>

    <p style="margin:0;font-size:14px;line-height:1.5;color:#64748B;text-align:center;">
        {{ __('mail.insurance_cert.closing') }} <strong style="color:#0B1F3A;">{{ __('mail.insurance_cert.service') }}</strong>
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
