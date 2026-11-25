@props(["message" => null, "title" => null, "preheader" => null, "footer" => null])
@php
    use App\Support\Venatomar as PF;
    $brand = config('venatomar.brand');
    $logo = is_file(public_path('images/logo.png')) ? asset('images/logo.png') : null;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $title ?? $brand }}</title>
    <style>
        @media (max-width: 600px) {
            .container { width: 100% !important; }
            .pad { padding: 28px 18px 24px !important; }
            .cta-btn { display: block !important; width: auto !important; }
            h1 { font-size: 23px !important; }
        }
        a { color: #0A5E4E; }
    </style>
</head>
<body style="margin:0;padding:0;background:#FFFFFF;font-family:Inter,'Segoe UI',Helvetica,Arial,sans-serif;color:#1E293B;-webkit-font-smoothing:antialiased;">
    @isset($preheader)
        <div style="display:none;max-height:0;overflow:hidden;opacity:0;mso-hide:all;">{{ $preheader }}</div>
    @endisset
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#FFFFFF;">
        <tr>
            <td align="center" style="padding:32px 14px 40px;">
                <table role="presentation" class="container" width="540" cellpadding="0" cellspacing="0" style="width:540px;max-width:540px;">
                    {{-- Centered brand mark --}}
                    <tr>
                        <td align="center" style="padding:4px 0 24px;">
                            @if ($logo)
                                <img src="{{ $logo }}" width="40" height="40" alt="{{ $brand }}" style="display:block;margin:0 auto;border:0;border-radius:10px;">
                            @else
                                <span style="display:inline-block;width:40px;height:40px;line-height:40px;border-radius:10px;background:#0FA57E;color:#FFFFFF;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-size:20px;font-weight:800;text-align:center;">V</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Main centered clean canvas --}}
                    <tr>
                        <td class="pad" style="background:#FFFFFF;padding:0 12px 28px;">
                            {{ $slot }}
                        </td>
                    </tr>

                    {{-- Minimalist centered footer --}}
                    <tr>
                        <td align="center" style="padding:26px 12px 8px;border-top:1px solid #EAEFF4;text-align:center;">
                            <p style="margin:0 0 10px;font-family:Manrope,'Segoe UI',Helvetica,Arial,sans-serif;font-size:15px;font-weight:800;color:#8A97A8;letter-spacing:-0.01em;">
                                {{ $brand }}
                            </p>
                            @if (!empty($footer))
                                <div style="font-size:12px;line-height:1.6;color:#64748B;margin:0 0 6px;">
                                    {{ $footer }}
                                </div>
                            @endif
                            <p style="margin:4px 0 0;font-size:11.5px;line-height:1.5;color:#94A3B8;">
                                {{ PF::legal('company_name') }} · {{ PF::legal('address') }} · {{ PF::legal('tax_id') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
