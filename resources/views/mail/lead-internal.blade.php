{{-- INTERNAL e-mail, Spanish only, contains sensitive data in clear. --}}
<x-mail-layout :message="$message ?? null" :title="__('mail.internal.title', ['reference' => $lead->reference])">
    <h1 style="margin:0 0 8px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:22px;color:#0B1F3A;">{{ __('mail.internal.title', ['reference' => $lead->reference]) }}</h1>
    <p style="margin:0 0 24px;font-size:14px;color:#5B6B82;">{{ __('mail.internal.reply_hint') }}</p>
    @foreach ($rows as $section => $fields)
        <h2 style="margin:24px 0 8px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:16px;color:#0A5E4E;">{{ $section }}</h2>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
            @foreach ($fields as $label => $value)
                <tr>
                    <td style="padding:8px 12px 8px 0;border-bottom:1px solid #E3E8EF;color:#5B6B82;width:42%;vertical-align:top;">{{ $label }}</td>
                    <td style="padding:8px 0;border-bottom:1px solid #E3E8EF;color:#0B1F3A;font-weight:600;word-break:break-word;">{{ $value }}</td>
                </tr>
            @endforeach
        </table>
    @endforeach
</x-mail-layout>
