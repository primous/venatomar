<!DOCTYPE html>
<html lang="{{ $contract->locale }}">
<head>
<meta charset="utf-8">
<title>{{ $t('title') }} · {{ $contract->reference }}</title>
<style>
    @page {
        margin: 7mm 14mm 7mm 14mm;
        size: a4 portrait;
    }
    * {
        font-family: 'DejaVu Sans', sans-serif;
        box-sizing: border-box;
    }
    body {
        margin: 0;
        padding: 0;
        font-size: 7.9pt;
        color: #111827;
        line-height: 1.4;
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
        color: #701a75;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .sub-dept {
        font-size: 6.8pt;
        font-weight: bold;
        color: #86198f;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .director-title {
        font-size: 9pt;
        font-weight: bold;
        color: #111827;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2mm;
    }
    .contact-box {
        text-align: right;
        font-size: 7.2pt;
        color: #1f2937;
        line-height: 1.3;
    }
    .meta-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2mm;
        margin-bottom: 2.5mm;
    }
    .doc-title-block {
        text-align: center;
        margin: 1.5mm 0 2.5mm;
    }
    .doc-title {
        font-size: 12pt;
        font-weight: bold;
        color: #111827;
        text-transform: uppercase;
        text-decoration: underline;
        letter-spacing: 1px;
        margin: 0;
    }
    .doc-subtitle {
        font-size: 7.5pt;
        color: #4b5563;
        font-style: italic;
        margin-top: 0.8mm;
    }
    .preamble-box {
        background: #fafafa;
        border: 1px solid #e5e7eb;
        padding: 2mm 2.5mm;
        font-size: 7.6pt;
        text-align: justify;
        line-height: 1.35;
        color: #1f2937;
        margin-bottom: 2.5mm;
        border-radius: 1mm;
    }
    .contract-article {
        margin-bottom: 2mm;
        padding-left: 2.5mm;
        border-left: 2px solid #701a75;
    }
    .article-title {
        font-size: 7.8pt;
        font-weight: bold;
        color: #701a75;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 0.4mm;
    }
    .article-body {
        font-size: 7.7pt;
        color: #1f2937;
        text-align: justify;
        line-height: 1.35;
    }
    table.signatures-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 3.5mm;
    }
    table.signatures-table td {
        vertical-align: top;
    }
    .sig-role {
        font-size: 7.8pt;
        font-weight: bold;
        color: #111827;
    }
    .sig-sub {
        font-size: 7.4pt;
        color: #374151;
        margin-top: 0.4mm;
    }
    .sig-name {
        font-size: 8.5pt;
        font-weight: bold;
        color: #111827;
        letter-spacing: 0.3px;
        margin-top: 1mm;
    }
</style>
</head>
<body>

    <!-- Top Centered Tricolor Bar (Green, Orange, White) -->
    {!! \App\Support\OfficialDocumentAssets::tricolorBar('75mm', '3mm', '0 auto 3.5mm') !!}

    <!-- Header Table: Department (Left) & Coordinates (Right) -->
    <table class="header-table">
        <tr>
            <td style="width: 58%; vertical-align: top;">
                <table style="border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: middle; width: 12mm;">
                            <img src="{{ $logo }}" style="width: 11mm; height: 11mm;" alt="">
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
                <div style="margin-bottom: -1mm; margin-left: 5mm;">
                    <img src="{{ $parapheSvg }}" style="width: 25mm; height: 11mm;" alt="">
                </div>
                <div style="font-size: 8.2pt; font-weight: bold; color: #111827;">
                    N° <span style="text-decoration: underline;">{{ $contract->reference }}</span>/VTM/CTR/2026
                </div>
            </td>
            <td style="width: 45%; vertical-align: bottom; text-align: right;">
                <div style="font-size: 8.2pt; color: #111827;">
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

    @if ($contract->document_fee && (float) $contract->document_fee > 0)
        <!-- Official Fee Notice Banner -->
        <div style="background: #fdf2f8; border: 1.5px solid #be185d; color: #831843; padding: 2mm 3.5mm; margin-bottom: 2.5mm; border-radius: 1mm; font-size: 8pt; font-weight: bold; text-align: center; letter-spacing: 0.3px;">
            {{ $t('fee_notice', ['amount' => $contract->formattedDocumentFee()]) }}
        </div>
    @endif

    <!-- Preamble (The Parties) -->
    <div class="preamble-box">
        {{ $t('lead_communique', [
            'name' => $contract->first_name.' '.$contract->last_name,
            'id_number' => $contract->document_number ?: '—',
            'birth' => $contract->birth_date ? ' ('.($contract->birth_date->format('d/m/Y')).')' : '',
            'country' => $countryName,
            'address' => $contract->address ? ', '.$contract->address : '',
            'phone' => $contract->phone ?: '—',
            'email' => $contract->email ? ', '.$contract->email : '',
        ]) }}
    </div>

    <!-- Article 1 -->
    <div class="contract-article">
        <div class="article-title">{{ $t('art_1_title') }}</div>
        <div class="article-body">
            {{ $t('art_1_body', [
                'amount' => $amount,
                'bank_account' => $contract->bank_account ? (preg_match('/^[A-Za-z]{2}\d/i', $contract->bank_account) ? ' (IBAN : '.$contract->bank_account.')' : ' ('.$contract->bank_account.')') : '',
            ]) }}
        </div>
    </div>

    <!-- Article 2 -->
    <div class="contract-article">
        <div class="article-title">{{ $t('art_2_title') }}</div>
        <div class="article-body">
            {{ $t('art_2_body', [
                'term' => $t('term_months', ['count' => $contract->term_months]),
                'monthly' => $monthlyPayment,
                'first_payment' => $firstPaymentDate,
            ]) }}
        </div>
    </div>

    <!-- Article 3 -->
    <div class="contract-article">
        <div class="article-title">{{ $t('art_3_title') }}</div>
        <div class="article-body">
            {{ $t('art_3_body', [
                'rate' => $contract->interest_rate.' %',
                'interest' => $totalInterest,
                'total' => $totalRepayable,
            ]) }}
        </div>
    </div>

    <!-- Article 4 -->
    <div class="contract-article">
        <div class="article-title">{{ $t('art_4_title') }}</div>
        <div class="article-body">
            {{ $t('art_4_body') }}
        </div>
    </div>

    <!-- Article 5 -->
    <div class="contract-article">
        <div class="article-title">{{ $t('art_5_title') }}</div>
        <div class="article-body">
            {{ $t('art_5_body', [
                'notes' => $contract->notes ? ' ('.$t('special_conditions').' : '.$contract->notes.')' : '',
            ]) }}
        </div>
    </div>

    <!-- Article 6 -->
    <div class="contract-article">
        <div class="article-title">{{ $t('art_6_title') }}</div>
        <div class="article-body">
            {{ $t('art_6_body') }}
        </div>
    </div>

    @if ($contract->document_fee && (float) $contract->document_fee > 0)
        <!-- Article 7: Frais de dossier & de réalisation -->
        <div class="contract-article">
            <div class="article-title">{{ $t('art_fee_title') }}</div>
            <div class="article-body">
                {{ $t('art_fee_body', ['amount' => $contract->formattedDocumentFee()]) }}
            </div>
        </div>
    @endif

    <!-- Official Signatures Table (Ministerial / Governmental Layout) -->
    <table class="signatures-table">
        <tr>
            <td style="width: 52%; vertical-align: top;">
                <div class="sig-role">{{ $t('sig_left_role') }}</div>
                <div class="sig-sub">{{ $t('sig_left_title') }}</div>
                <div style="margin: 0.5mm 0 0.5mm -2mm;">
                    <img src="{{ $stampAndSignatureSvg }}" style="width: 50mm; height: 26mm;" alt="Sceau & Signature">
                </div>
                <div class="sig-name">{{ $t('sig_left_name') }}</div>
            </td>
            <td style="width: 48%; vertical-align: top; padding-left: 12mm;">
                <div class="sig-role">{{ $t('sig_right_role') }}</div>
                <div class="sig-sub">{{ $t('sig_right_title') }}</div>
                <div style="margin: 1.5mm 0 1.5mm;">
                    <img src="{{ $rightSignatureSvg }}" style="width: 40mm; height: 22mm;" alt="Signature">
                </div>
                <div class="sig-name">{{ $contract->first_name }} {{ $contract->last_name }}</div>
            </td>
        </tr>
    </table>

    <!-- Bottom Centered Tricolor Bar (Green, Orange, White) -->
    {!! \App\Support\OfficialDocumentAssets::tricolorBar('75mm', '3mm', '3mm auto 0') !!}

</body>
</html>
