<!DOCTYPE html>
<html lang="{{ $doc->locale }}">
<head>
<meta charset="utf-8">
<title>{{ $t('title') }} · {{ $doc->reference }}</title>
<style>
    @page {
        margin: 8mm 14mm 8mm 14mm;
        size: a4 portrait;
    }
    * {
        font-family: 'DejaVu Sans', sans-serif;
        box-sizing: border-box;
    }
    body {
        margin: 0;
        padding: 0;
        font-size: 8.3pt;
        color: #111827;
        line-height: 1.45;
        background: #ffffff;
    }
    .header-table {
        width: 100%;
        border-collapse: collapse;
    }
    .brand-title {
        font-size: 8.5pt;
        font-weight: bold;
        color: #111827;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .dept-title {
        font-size: 7.2pt;
        font-weight: bold;
        color: #1e3a8a;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .sub-dept {
        font-size: 6.8pt;
        font-weight: bold;
        color: #0369a1;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .director-title {
        font-size: 9.2pt;
        font-weight: bold;
        color: #111827;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2.5mm;
    }
    .contact-box {
        text-align: right;
        font-size: 7.3pt;
        color: #1f2937;
        line-height: 1.35;
    }
    .meta-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2.5mm;
        margin-bottom: 3.5mm;
    }
    .doc-title-block {
        text-align: center;
        margin: 2mm 0 3.5mm;
    }
    .doc-title {
        font-size: 12.5pt;
        font-weight: bold;
        color: #111827;
        text-transform: uppercase;
        text-decoration: underline;
        letter-spacing: 1px;
        margin: 0;
    }
    .doc-subtitle {
        font-size: 7.8pt;
        color: #4b5563;
        font-style: italic;
        margin-top: 1mm;
    }
    .lead-communique {
        text-indent: 10mm;
        font-weight: 600;
        margin-bottom: 3.5mm;
        font-size: 8.5pt;
        color: #111827;
    }
    .decision-block {
        margin-bottom: 3.2mm;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 3.5px solid #1d4ed8;
        padding: 2.2mm 3mm;
        border-radius: 1mm;
    }
    .decision-title {
        font-size: 8.2pt;
        font-weight: bold;
        color: #1e40af;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 0.8mm;
    }
    .decision-body {
        font-size: 8.1pt;
        color: #1f2937;
        text-align: justify;
        line-height: 1.45;
    }
    table.signatures-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 4.5mm;
    }
    table.signatures-table td {
        vertical-align: top;
    }
    .sig-role {
        font-size: 8pt;
        font-weight: bold;
        color: #111827;
    }
    .sig-sub {
        font-size: 7.6pt;
        color: #374151;
        margin-top: 0.5mm;
    }
    .sig-name {
        font-size: 8.8pt;
        font-weight: bold;
        color: #111827;
        letter-spacing: 0.3px;
        margin-top: 1mm;
    }
</style>
</head>
<body>

    <!-- Top Centered Tricolor Bar (Green, Orange, White) -->
    {!! \App\Support\OfficialDocumentAssets::tricolorBar('75mm', '3.2mm', '0 auto 4mm') !!}

    <!-- Header Table: Department (Left) & Coordinates (Right) -->
    <table class="header-table">
        <tr>
            <td style="width: 58%; vertical-align: top;">
                <table style="border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: middle; width: 13mm;">
                            <img src="{{ $logo }}" style="width: 12mm; height: 12mm;" alt="">
                        </td>
                        <td style="vertical-align: middle; padding-left: 2.5mm;">
                            <div class="brand-title">{{ $t('header_brand') }}</div>
                            <div class="dept-title">{{ $t('header_dept') }}</div>
                            {!! \App\Support\OfficialDocumentAssets::subTricolorBar() !!}
                            <div class="sub-dept">{{ $t('header_sub_dept') }}</div>
                        </td>
                    </tr>
                </table>
                <div class="director-title">{{ $t('header_director') }}</div>
            </td>
            <td style="width: 42%; vertical-align: top;">
                <div class="contact-box">
                    <div>{{ $issuerAddress }}</div>
                    <div>Tél : {{ $issuerPhone }}</div>
                    <div>{{ $contactEmail ?? 'info@venatomar.com' }}</div>
                    <div style="font-weight: bold; color: #0E7C66;">www.venatomar.com</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Reference & Stamped Date Row -->
    <table class="meta-table">
        <tr>
            <td style="width: 55%; vertical-align: bottom;">
                <div style="margin-bottom: -1mm; margin-left: 6mm;">
                    <img src="{{ $parapheSvg }}" style="width: 27mm; height: 12mm;" alt="">
                </div>
                <div style="font-size: 8.4pt; font-weight: bold; color: #111827;">
                    N° <span style="text-decoration: underline;">{{ $doc->reference }}</span>/VTM/VAL/2026
                </div>
            </td>
            <td style="width: 45%; vertical-align: bottom; text-align: right;">
                <div style="font-size: 8.4pt; color: #111827;">
                    {{ $t('city_prefix', ['city' => $issuerCity]) }} &nbsp;<span style="color: #1d4ed8; font-weight: bold; letter-spacing: 0.8px;">{{ $dateUpper }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Title (Underlined & Centered) -->
    <div class="doc-title-block">
        <h1 class="doc-title">{{ $t('title') }}</h1>
        <div class="doc-subtitle">{{ $t('subtitle') }}</div>
    </div>

    <!-- Official Narrative Lead -->
    <div class="lead-communique">
        {{ $t('lead_communique') }}
    </div>

    <!-- Decision 1: Avis Favorable et Octroi -->
    <div class="decision-block">
        <div class="decision-title">{{ $t('dec_1_title') }}</div>
        <div class="decision-body">
            {{ $t('dec_1_body', [
                'name' => $doc->first_name.' '.$doc->last_name,
                'id_number' => $doc->document_number ?: '—',
                'birth' => $doc->birth_date ? ' ('.($doc->birth_date->format('d/m/Y')).')' : '',
                'country' => $countryName,
                'address' => $doc->address ? ' ('.$doc->address.')' : '',
                'phone' => $doc->phone ?: '—',
                'email' => $doc->email ? ', '.$doc->email : '',
            ]) }}
        </div>
    </div>

    <!-- Decision 2: Conditions Financières Arrêtées -->
    <div class="decision-block">
        <div class="decision-title">{{ $t('dec_2_title') }}</div>
        <div class="decision-body">
            {{ $t('dec_2_body', [
                'amount' => $amount,
                'term' => $t('term_months', ['count' => $doc->term_months]),
                'monthly' => $monthlyPayment,
                'rate' => ($doc->interest_rate ? $doc->interest_rate.' %' : '3.50 %'),
                'total' => $totalRepayable,
                'lender' => ($doc->lender ?: $t('lender_default')),
            ]) }}
        </div>
    </div>

    <!-- Decision 3: Déblocage des fonds -->
    <div class="decision-block">
        <div class="decision-title">{{ $t('dec_3_title') }}</div>
        <div class="decision-body">
            {{ $t('dec_3_body', [
                'expiry_date' => $expiryDate,
            ]) }}
        </div>
    </div>

    <!-- Decision 4: Force Exécutoire -->
    <div class="decision-block">
        <div class="decision-title">{{ $t('dec_4_title') }}</div>
        <div class="decision-body">
            {{ $t('dec_4_body') }}
        </div>
    </div>

    <!-- Official Signatures Table (Ministerial / Governmental Layout) -->
    <table class="signatures-table">
        <tr>
            <td style="width: 52%; vertical-align: top;">
                <div class="sig-role">{{ $t('sig_left_role') }}</div>
                <div class="sig-sub">{{ $t('sig_left_title') }}</div>
                <div style="margin: 0.5mm 0 0.5mm -2mm;">
                    <img src="{{ $stampAndSignatureSvg }}" style="width: 52mm; height: 27mm;" alt="Sceau & Signature">
                </div>
                <div class="sig-name">{{ $t('sig_left_name') }}</div>
            </td>
            <td style="width: 48%; vertical-align: top; padding-left: 14mm;">
                <div class="sig-role">{{ $t('sig_right_role') }}</div>
                <div class="sig-sub">{{ $t('sig_right_title') }}</div>
                <div style="margin: 1.5mm 0 1.5mm;">
                    <img src="{{ $rightSignatureSvg }}" style="width: 42mm; height: 23mm;" alt="Signature">
                </div>
                <div class="sig-name">{{ $t('sig_right_name') }}</div>
            </td>
        </tr>
    </table>

    <!-- Bottom Centered Tricolor Bar (Green, Orange, White) -->
    {!! \App\Support\OfficialDocumentAssets::tricolorBar('75mm', '3.2mm', '4mm auto 0') !!}

</body>
</html>
