<x-mail-layout :message="$message ?? null" :title="$emailSubject" :preheader="$emailSubject">
    <div style="text-align:center;margin:0 0 16px;">
        <span style="display:inline-block;padding:6px 14px;background:#E6F5F1;border:1px solid rgba(15,165,126,0.28);border-radius:999px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-size:11px;font-weight:700;color:#0A5E4E;text-transform:uppercase;letter-spacing:0.6px;">
            {{ $reference }}
        </span>
    </div>

    <p style="margin:0 0 14px;font-size:15.5px;font-weight:700;color:#0B1F3A;">
        {{ __('mail.accepted.greeting', ['name' => $firstName]) }}
    </p>

    <div style="margin:0 0 24px;padding:18px 20px;background:#F6F8FB;border:1px solid #E6EBF1;border-radius:14px;font-size:15px;line-height:1.65;color:#1E293B;">
        {!! nl2br(e($messageBody)) !!}
    </div>

    <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0 0 22px;">
        <tr>
            <td align="center">
                <a href="{{ $accountUrl }}" target="_blank" rel="noopener noreferrer" class="cta-btn" style="display:inline-block;min-width:220px;padding:14px 28px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-weight:700;font-size:14.5px;color:#FFFFFF;text-decoration:none;border-radius:10px;background:#0FA57E;text-align:center;">
                    {{ __('mail.confirmation.cta') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:13.5px;line-height:1.55;color:#5B6B82;text-align:center;">
        {!! __('mail.confirmation.questions', ['email' => '<a href="mailto:'.$contactEmail.'" style="color:#0A5E4E;font-weight:700;text-decoration:underline;">'.$contactEmail.'</a>']) !!}
    </p>

    <p style="margin:0;font-size:14px;line-height:1.5;color:#64748B;text-align:center;">
        {{ __('mail.confirmation.closing') }} <strong style="color:#0B1F3A;">{{ __('mail.confirmation.team', ['brand' => config('venatomar.brand')]) }}</strong>
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
