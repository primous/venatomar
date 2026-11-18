<x-mail-layout :message="$message ?? null" :title="__('mail.deletion.title')">
    <h1 style="margin:0 0 12px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:22px;color:#0B1F3A;">{{ __('mail.deletion.title') }}</h1>
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;">{{ __('mail.deletion.intro') }}</p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
        @foreach ($rows as $label => $value)
            <tr>
                <td style="padding:8px 12px 8px 0;border-bottom:1px solid #E3E8EF;color:#5B6B82;width:42%;">{{ $label }}</td>
                <td style="padding:8px 0;border-bottom:1px solid #E3E8EF;color:#0B1F3A;font-weight:600;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>
</x-mail-layout>
