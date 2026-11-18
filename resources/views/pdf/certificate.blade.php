<!DOCTYPE html>
<html lang="{{ $doc->locale }}">
<head>
<meta charset="utf-8">
<title>{{ $t('cert.title') }} · {{ $doc->reference }}</title>
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
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .sub-dept {
        font-size: 6.8pt;
        font-weight: bold;
        color: #047857;
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
    .cert-section {
        margin-bottom: 3.2mm;
        padding-left: 3mm;
        border-left: 2.5px solid #0E7C66;
    }
    .cert-section-title {
        font-size: 8.2pt;
        font-weight: bold;
        color: #0E7C66;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 0.8mm;
    }
    .cert-section-body {
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
                            <div class="brand-title">{{ $t('cert.header_brand') }}</div>
                            <div class="dept-title">{{ $t('cert.header_dept') }}</div>
                            {!! \App\Support\OfficialDocumentAssets::subTricolorBar() !!}
                            <div class="sub-dept">{{ $t('cert.header_sub_dept') }}</div>
                        </td>
                    </tr>
                </table>
                <div class="director-title">{{ $t('cert.header_director') }}</div>
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
                    N° <span style="text-decoration: underline;">{{ $doc->reference }}</span>/VTM/ASS/2026
                </div>
            </td>
            <td style="width: 45%; vertical-align: bottom; text-align: right;">
                <div style="font-size: 8.4pt; color: #111827;">
                    {{ $t('cert.city_prefix', ['city' => $issuerCity]) }} &nbsp;<span style="color: #1d4ed8; font-weight: bold; letter-spacing: 0.8px;">{{ $dateUpper }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Title (Underlined & Centered) -->
    <div class="doc-title-block">
        <h1 class="doc-title">{{ $t('cert.title') }}</h1>
        <div class="doc-subtitle">{{ $t('cert.subtitle') }}</div>
    </div>

    @if ($doc->document_fee && (float) $doc->document_fee > 0)
        <!-- Official Fee Notice Banner -->
        <div style="background: #f0fdf4; border: 1.5px solid #059669; color: #065f46; padding: 2.2mm 3.5mm; margin-bottom: 3.5mm; border-radius: 1mm; font-size: 8.3pt; font-weight: bold; text-align: center; letter-spacing: 0.3px;">
            {{ $t('cert.fee_notice', ['amount' => $doc->formattedDocumentFee()]) }}
        </div>
    @endif

    <!-- Official Narrative Lead -->
    <div class="lead-communique">
        {{ $t('cert.lead_communique') }}
    </div>

    <!-- Section I: Identification de l'assuré -->
    <div class="cert-section">
        <div class="cert-section-title">{{ $t('cert.sec_1_title') }}</div>
        <div class="cert-section-body">
            {{ $t('cert.sec_1_body', [
                'name' => $doc->first_name.' '.$doc->last_name,
                'birth' => $doc->birth_date ? $doc->birth_date->format('d/m/Y') : '—',
                'id_number' => $doc->document_number ?: '—',
                'country' => $countryName,
                'address' => $doc->address ? ', '.$doc->address : '',
                'phone' => $doc->phone ?: '—',
                'email' => $doc->email ? ', '.$doc->email : '',
                'reference' => $doc->reference,
                'lender' => $doc->lender ?: $t('cert.loan_lender_default'),
            ]) }}
        </div>
    </div>

    <!-- Section II: Prêt couvert & Prise d'effet -->
    <div class="cert-section">
        <div class="cert-section-title">{{ $t('cert.sec_2_title') }}</div>
        <div class="cert-section-body">
            {{ $t('cert.sec_2_body', [
                'amount' => $amount,
                'term' => $t('cert.loan_months', ['count' => $doc->term_months]),
                'effective_date' => $effectiveDate,
                'expiry_date' => $expiryDate ?: '—',
            ]) }}
        </div>
    </div>

    <!-- Section III: Risques garantis & Quotité -->
    <div class="cert-section">
        <div class="cert-section-title">{{ $t('cert.sec_3_title') }}</div>
        <div class="cert-section-body">
            {{ $t('cert.sec_3_body', [
                'quota' => $doc->coverage_percent,
                'guarantees' => $guaranteesList,
            ]) }}
        </div>
    </div>

    <!-- Section IV: Clause bénéficiaire & Force probante -->
    <div class="cert-section">
        <div class="cert-section-title">{{ $t('cert.sec_4_title') }}</div>
        <div class="cert-section-body">
            {{ $t('cert.sec_4_body') }}
        </div>
    </div>

    @if ($doc->document_fee && (float) $doc->document_fee > 0)
        <!-- Section V: Frais d'émission et de gestion -->
        <div class="cert-section">
            <div class="cert-section-title">{{ $t('cert.sec_5_title') }}</div>
            <div class="cert-section-body">
                {{ $t('cert.sec_5_body', ['amount' => $doc->formattedDocumentFee()]) }}
            </div>
        </div>
    @endif

    <!-- Official Signatures Table (Ministerial / Governmental Layout) -->
    <table class="signatures-table">
        <tr>
            <td style="width: 52%; vertical-align: top;">
                <div class="sig-role">{{ $t('cert.sig_left_role') }}</div>
                <div class="sig-sub">{{ $t('cert.sig_left_title') }}</div>
                <div style="margin: 0.5mm 0 0.5mm -2mm;">
                    <img src="{{ $stampAndSignatureSvg }}" style="width: 52mm; height: 27mm;" alt="Sceau & Signature">
                </div>
                <div class="sig-name">{{ $t('cert.sig_left_name') }}</div>
            </td>
            <td style="width: 48%; vertical-align: top; padding-left: 14mm;">
                <div class="sig-role">{{ $t('cert.sig_right_role') }}</div>
                <div class="sig-sub">{{ $t('cert.sig_right_title') }}</div>
                <div style="margin: 1.5mm 0 1.5mm;">
                    <img src="{{ $rightSignatureSvg }}" style="width: 42mm; height: 23mm;" alt="Signature">
                </div>
                <div class="sig-name">{{ $doc->first_name }} {{ $doc->last_name }}</div>
            </td>
        </tr>
    </table>

    <!-- Bottom Centered Tricolor Bar (Green, Orange, White) -->
    {!! \App\Support\OfficialDocumentAssets::tricolorBar('75mm', '3.2mm', '4mm auto 0') !!}

</body>
</html>
