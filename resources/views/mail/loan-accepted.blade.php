@php
    use App\Support\Venatomar as PF;

    $introText = e(__('mail.accepted.intro'));
    $reqText = e(__('mail.accepted.insurance_requirement'));
    $actionText = e(__('mail.accepted.action_required'));

    $highlightTerms = [
        'acceptée et validée', 'acceptada y validada', 'accepted, and validated', 'accepted and validated',
        'akzeptiert und bestätigt', 'accettata e convalidata', 'aceite e validada',
        'geaccepteerd en goedgekeurd', 'prijatá a schválená', 'sprejeta in potrjena',
        'έγινε αποδεκτή και εγκρίθηκε', 'nekena',
        'assurance crédit', 'seguro de crédito', 'credit insurance', 'Kreditversicherung',
        'assicurazione credito', 'kredietverzekering', 'ασφάλιση πίστωσης', 'poistenie úveru', 'zavarovanje kredita',
        '24 heures', '24 horas', '24 hours', '24 Stunden', '24 ore', '24 uur',
        '24 hodín', '24 ur', '24 ωρών', '24 ora',
    ];

    foreach ($highlightTerms as $highlight) {
        $introText = str_ireplace($highlight, '<strong style="color:#0B1F3A;">' . $highlight . '</strong>', $introText);
        $reqText = str_ireplace($highlight, '<strong style="color:#0B1F3A;">' . $highlight . '</strong>', $reqText);
        $actionText = str_ireplace($highlight, '<strong style="color:#0B1F3A;">' . $highlight . '</strong>', $actionText);
    }
@endphp
<x-mail-layout :message="$message ?? null" :title="__('mail.accepted.subject', ['reference' => $reference])" :preheader="__('mail.accepted.preheader', ['brand' => config('venatomar.brand')])">
    <div style="text-align:center;margin:0 0 16px;">
        <span style="display:inline-block;padding:6px 14px;background:#E6F5F1;border:1px solid rgba(15,165,126,0.28);border-radius:999px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-size:11px;font-weight:700;color:#0A5E4E;text-transform:uppercase;letter-spacing:0.6px;">
            ✓ {{ __('mail.accepted.badge') }}
        </span>
    </div>

    <h1 style="margin:0 0 14px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-size:25px;line-height:1.25;color:#0B1F3A;font-weight:800;letter-spacing:-0.02em;text-align:center;">
        {{ __('mail.accepted.headline') }}
    </h1>

    <p style="margin:0 0 10px;font-size:15px;font-weight:700;color:#0B1F3A;text-align:center;">
        {{ __('mail.accepted.greeting', ['name' => $firstName]) }}
    </p>

    <p style="margin:0 0 14px;font-size:15.5px;line-height:1.6;color:#334155;text-align:center;">
        {!! $introText !!}
    </p>

    <p style="margin:0 0 24px;font-size:15.5px;line-height:1.6;color:#334155;text-align:center;">
        {!! $reqText !!}
    </p>

    {{-- Primary CTA --}}
    <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0 0 14px;">
        <tr>
            <td align="center">
                <a href="{{ $accountUrl }}" target="_blank" rel="noopener noreferrer" class="cta-btn" style="display:inline-block;min-width:240px;padding:15px 32px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-weight:700;font-size:15px;color:#FFFFFF;text-decoration:none;border-radius:10px;background:#0FA57E;text-align:center;">
                    {{ __('mail.accepted.cta') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 26px;font-size:13.5px;line-height:1.5;color:#64748B;text-align:center;">
        {!! $actionText !!}
    </p>

    {{-- Loan & insurance attachment recap --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F6F8FB;border:1px solid #E6EBF1;border-radius:14px;font-size:14.5px;line-height:1.5;margin:0 0 22px;">
        <tr>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;color:#5B6B82;">{{ __('mail.confirmation.reference') }}</td>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;text-align:right;font-weight:700;color:#0B1F3A;font-family:monospace;">{{ $reference }}</td>
        </tr>
        <tr>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;color:#5B6B82;">{{ __('mail.confirmation.amount') }}</td>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;text-align:right;font-weight:700;color:#0B1F3A;">{{ PF::money($amount, $country) }}</td>
        </tr>
        <tr>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;color:#5B6B82;">{{ __('mail.confirmation.term') }}</td>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;text-align:right;font-weight:700;color:#0B1F3A;">{{ trans_choice('form.months', $term, ['count' => $term]) }}</td>
        </tr>
        @if(!empty($documentFee))
        <tr>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;color:#5B6B82;">{{ __('mail.accepted.document_fee') }}</td>
            <td style="padding:13px 18px;border-bottom:1px solid #EAEFF4;text-align:right;font-weight:700;color:#0B1F3A;">{{ $documentFee }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding:13px 18px;color:#5B6B82;">{{ __('mail.accepted.attached_document') }}</td>
            <td style="padding:13px 18px;text-align:right;font-weight:700;color:#0A5E4E;font-family:monospace;word-break:break-all;">{{ $attachmentName ?? (($docReference ?: $reference).'.pdf') }}</td>
        </tr>
    </table>

    {{-- Serious review emphasis box --}}
    <div style="margin:0 0 22px;padding:16px 18px;background:#FFFBEB;border:1px solid #FDE68A;border-left:4px solid #F26B1D;border-radius:8px;font-size:14px;line-height:1.6;color:#78350F;">
        {{ __('mail.accepted.document_notice') }}
    </div>

    <p style="margin:0 0 18px;font-size:14px;line-height:1.55;color:#475569;text-align:center;">
        {{ __('mail.accepted.questions') }}
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
