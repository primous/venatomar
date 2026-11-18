@props(["message" => null, "title" => null, "preheader" => null, "footer" => null])
@php
    use App\Support\Venatomar as PF;
    $brand = config('venatomar.brand');
    $logoPath = public_path('images/logo.png');
    $logo = isset($message) && is_file($logoPath) ? $message->embed($logoPath) : null; // cid: inline attachment
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $title ?? $brand }}</title>
    <style>
        @media (max-width: 600px) { .container { width: 100% !important; } .pad { padding: 24px 20px !important; } }
        a { color: #0A5E4E; }
    </style>
</head>
<body style="margin:0;padding:0;background:#F5F7FA;font-family:Inter,Segoe UI,Helvetica,Arial,sans-serif;color:#24324A;">
    @isset($preheader)
        <div style="display:none;max-height:0;overflow:hidden;opacity:0;">{{ $preheader }}</div>
    @endisset
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F7FA;">
        <tr><td align="center" style="padding:24px 12px;">
            <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:600px;">
                <tr><td style="padding:8px 4px 20px;">
                    <table role="presentation" cellpadding="0" cellspacing="0"><tr>
                        @if ($logo)<td style="padding-right:10px;"><img src="{{ $logo }}" width="32" height="32" alt="" style="display:block;border:0;"></td>@endif
                        <td style="font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:20px;font-weight:800;color:#0B1F3A;">{{ $brand }}</td>
                    </tr></table>
                </td></tr>
                <tr><td class="pad" style="background:#FFFFFF;border:1px solid #E3E8EF;border-radius:16px;padding:32px;">
                    {{ $slot }}
                </td></tr>
                <tr><td style="padding:20px 8px;font-size:12px;line-height:1.5;color:#5B6B82;">
                    {{ $footer ?? '' }}
                    <p style="margin:8px 0 0;">{{ PF::legal('company_name') }} · {{ PF::legal('address') }} · {{ PF::legal('tax_id') }}</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
