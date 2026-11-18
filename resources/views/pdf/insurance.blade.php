<!DOCTYPE html>
<html lang="{{ $doc->locale }}">
<head>
<meta charset="utf-8">
<title>{{ $t('info.title') }} · {{ $doc->reference }}</title>
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
        font-size: 8.2pt;
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
        margin-top: 2.5mm;
        margin-bottom: 3.5mm;
    }
    .doc-title-block {
        text-align: center;
        margin: 2mm 0 3.5mm;
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
        font-size: 7.8pt;
        color: #4b5563;
        font-style: italic;
        margin-top: 1mm;
    }
    .lead-communique {
        text-indent: 8mm;
        font-weight: 600;
        margin-bottom: 3mm;
        font-size: 8.3pt;
        color: #111827;
    }
    .section-title {
        font-size: 8.2pt;
        font-weight: bold;
        color: #0E7C66;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin: 2.5mm 0 1.5mm;
        padding-bottom: 0.8mm;
        border-bottom: 1px solid #e5e7eb;
    }
    .grid-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 7.8pt;
    }
    .grid-table td {
        padding: 1.2mm 2mm;
        border: 1px solid #e5e7eb;
        vertical-align: middle;
    }
    .grid-table td.lbl {
        width: 40%;
        background: #f8fafc;
        color: #374151;
        font-weight: bold;
    }
    .grid-table td.val {
        color: #111827;
    }
    .box-shaded {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 3px solid #0E7C66;
        padding: 2mm 2.5mm;
        font-size: 7.8pt;
        line-height: 1.35;
        color: #1f2937;
        margin-bottom: 2.5mm;
    }
    .box-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 1.5mm;
        padding: 2mm 2.5mm;
        font-size: 7.6pt;
        line-height: 1.35;
    }
    table.guarantees-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 7.4pt;
        margin-top: 1.5mm;
    }
    table.guarantees-table th {
        background: #0E7C66;
        color: #ffffff;
        font-weight: bold;
        text-transform: uppercase;
        padding: 1.4mm 2mm;
        text-align: left;
        font-size: 7.2pt;
        border: 1px solid #0E7C66;
    }
    table.guarantees-table td {
        padding: 1.4mm 2mm;
        border: 1px solid #e5e7eb;
        vertical-align: top;
    }
    .badge-on {
        display: inline-block;
        background: #ecfdf5;
        color: #065f46;
        font-weight: bold;
        padding: 0.5mm 1.5mm;
        border: 0.5px solid #a7f3d0;
        border-radius: 1mm;
        font-size: 6.8pt;
        text-transform: uppercase;
    }
    .badge-off {
        display: inline-block;
        background: #f3f4f6;
        color: #6b7280;
        font-weight: normal;
        padding: 0.5mm 1.5mm;
        border: 0.5px solid #e5e7eb;
        border-radius: 1mm;
        font-size: 6.8pt;
        text-transform: uppercase;
    }
    ul, ol {
        margin: 0;
        padding-left: 4.5mm;
    }
    li {
        margin-bottom: 0.8mm;
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
    .page-break {
        page-break-before: always;
    }
</style>
</head>
<body>

    <!-- ==================== PAGE 1 ==================== -->

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
                            <div class="brand-title">{{ $t('info.header_brand') }}</div>
                            <div class="dept-title">{{ $t('info.header_dept') }}</div>
                            {!! \App\Support\OfficialDocumentAssets::subTricolorBar() !!}
                            <div class="sub-dept">{{ $t('info.header_sub_dept') }}</div>
                        </td>
                    </tr>
                </table>
                <div class="director-title">{{ $t('info.header_director') }}</div>
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
                    N° <span style="text-decoration: underline;">{{ $doc->reference }}</span>/VTM/INF/2026
                </div>
            </td>
            <td style="width: 45%; vertical-align: bottom; text-align: right;">
                <div style="font-size: 8.2pt; color: #111827;">
                    {{ $t('info.city_prefix', ['city' => $issuerCity]) }} &nbsp;<span style="color: #1d4ed8; font-weight: bold; letter-spacing: 0.8px;">{{ $dateUpper }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Title (Underlined & Centered) -->
    <div class="doc-title-block">
        <h1 class="doc-title">{{ $t('info.title') }}</h1>
        <div class="doc-subtitle">{{ $t('subtitle') }}</div>
    </div>

    @if ($doc->document_fee && (float) $doc->document_fee > 0)
        <!-- Official Fee Notice Banner -->
        <div style="background: #f0fdf4; border: 1.5px solid #059669; color: #065f46; padding: 2.2mm 3.5mm; margin-bottom: 3.5mm; border-radius: 1mm; font-size: 8.3pt; font-weight: bold; text-align: center; letter-spacing: 0.3px;">
            {{ $t('info.fee_notice', ['amount' => $doc->formattedDocumentFee()]) }}
        </div>
    @endif

    <!-- Official Communiqué Intro -->
    <div class="lead-communique">
        {{ $t('info.lead_communique') }}
    </div>

    <!-- Section 1 & 2: Client & Loan Details (Side by side 2 columns) -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 2.5mm;">
        <tr>
            <td style="width: 49%; vertical-align: top; padding-right: 2mm;">
                <div class="section-title" style="margin-top: 0;">1. {{ $t('sections.client') }}</div>
                <table class="grid-table">
                    <tr><td class="lbl">{{ $t('fields.name') }}</td><td class="val"><strong>{{ $doc->first_name }} {{ $doc->last_name }}</strong></td></tr>
                    <tr><td class="lbl">{{ $t('fields.id_number') }}</td><td class="val">{{ $doc->document_number ?: '—' }}</td></tr>
                    <tr><td class="lbl">{{ $t('cert.insured_birth') }}</td><td class="val">{{ $doc->birth_date ? $doc->birth_date->format('d/m/Y') : '—' }}</td></tr>
                    <tr><td class="lbl">{{ $t('fields.country') }}</td><td class="val">{{ $countryName }}</td></tr>
                    <tr><td class="lbl">{{ $t('fields.phone') }}</td><td class="val">{{ $doc->phone ?: '—' }}</td></tr>
                </table>
            </td>
            <td style="width: 49%; vertical-align: top; padding-left: 2mm;">
                <div class="section-title" style="margin-top: 0;">2. {{ $t('sections.loan') }}</div>
                <table class="grid-table">
                    <tr><td class="lbl">{{ $t('fields.amount') }}</td><td class="val"><strong style="color: #0E7C66;">{{ $amount }}</strong></td></tr>
                    <tr><td class="lbl">{{ $t('fields.term') }}</td><td class="val">{{ $t('fields.months', ['count' => $doc->term_months]) }}</td></tr>
                    <tr><td class="lbl">{{ $t('fields.lender') }}</td><td class="val">{{ $doc->lender ?: $t('fields.lender_unknown') }}</td></tr>
                    <tr><td class="lbl">{{ $t('fields.coverage') }}</td><td class="val"><strong>{{ $doc->coverage_percent }} %</strong></td></tr>
                    <tr><td class="lbl">{{ $t('cert.effective_date') }}</td><td class="val">{{ $effectiveDate }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Section 3: Qu'est-ce que l'assurance de prêt ? -->
    <div class="section-title">3. {{ $t('sections.what') }}</div>
    <div class="box-shaded">
        {{ $t('what_text') }}
    </div>

    <!-- Section 4: Tableau complet des garanties -->
    <div class="section-title">4. {{ $t('sections.guarantees') }} &amp; {{ $t('selected') }}</div>
    <table class="guarantees-table">
        <thead>
            <tr>
                <th style="width: 32%;">{{ $t('cert.th_guarantee') }}</th>
                <th style="width: 20%; text-align: center;">{{ $t('cert.th_status') }}</th>
                <th style="width: 48%;">{{ $t('cert.th_benefit') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach (\App\Models\InsuranceDocument::GUARANTEES as $key)
                @php($on = in_array($key, $doc->guarantees ?? [], true))
                @php([$name, $desc] = $t('guarantees.'.$key))
                <tr>
                    <td style="font-weight: bold; color: #111827;">{{ $name }}</td>
                    <td style="text-align: center;">
                        @if ($on)
                            <span class="badge-on">{{ $t('cert.status_covered') }}</span>
                        @else
                            <span class="badge-off">{{ $t('not_selected') }}</span>
                        @endif
                    </td>
                    <td style="color: #374151; font-size: 7.2pt; line-height: 1.3;">{{ $desc }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Bottom Centered Tricolor Bar (Page 1) -->
    {!! \App\Support\OfficialDocumentAssets::tricolorBar('75mm', '2.5mm', '4mm auto 0') !!}


    <!-- ==================== PAGE 2 ==================== -->
    <div class="page-break"></div>

    <!-- Top Centered Tricolor Bar (Page 2) -->
    {!! \App\Support\OfficialDocumentAssets::tricolorBar('75mm', '3mm', '0 auto 3.5mm') !!}

    <!-- Page 2 Header Reminder -->
    <table class="header-table" style="margin-bottom: 2mm;">
        <tr>
            <td style="width: 55%; vertical-align: middle;">
                <table style="border-collapse: collapse;">
                    <tr>
                        <td style="width: 9mm; vertical-align: middle;">
                            <img src="{{ $logo }}" style="width: 8mm; height: 8mm;" alt="">
                        </td>
                        <td style="vertical-align: middle; padding-left: 2mm;">
                            <div style="font-size: 7.5pt; font-weight: bold; color: #111827;">{{ $t('info.header_brand') }}</div>
                            <div style="font-size: 6.8pt; color: #047857; font-weight: bold;">{{ $t('info.header_sub_dept') }}</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%; vertical-align: middle; text-align: right; font-size: 7.2pt; color: #6b7280;">
                <div>N° <strong>{{ $doc->reference }}</strong>/VTM/INF/2026 · {{ $t('info.title') }}</div>
                <div style="color: #0E7C66; font-weight: bold;">Document officiel précontractuel (Page 2 / 2)</div>
            </td>
        </tr>
    </table>

    <!-- Section 5: Quotité & Déclaration de santé -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 2.5mm;">
        <tr>
            <td style="width: 49%; vertical-align: top; padding-right: 2mm;">
                <div class="section-title" style="margin-top: 0;">5. {{ $t('sections.coverage') }}</div>
                <div class="box-card" style="min-height: 20mm;">
                    {{ $t('coverage_text', ['percent' => $doc->coverage_percent]) }}
                </div>
            </td>
            <td style="width: 49%; vertical-align: top; padding-left: 2mm;">
                <div class="section-title" style="margin-top: 0;">6. {{ $t('sections.health') }}</div>
                <div class="box-card" style="min-height: 20mm;">
                    {{ $t('health_text') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Section 6: Documents & Exclusions -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 2.5mm;">
        <tr>
            <td style="width: 49%; vertical-align: top; padding-right: 2mm;">
                <div class="section-title" style="margin-top: 0;">7. {{ $t('sections.documents') }}</div>
                <div class="box-card" style="min-height: 24mm;">
                    <ul>
                        @foreach ($t('documents') as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </td>
            <td style="width: 49%; vertical-align: top; padding-left: 2mm;">
                <div class="section-title" style="margin-top: 0;">8. {{ $t('sections.exclusions') }}</div>
                <div class="box-card" style="min-height: 24mm;">
                    <ul>
                        @foreach ($t('exclusions') as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </td>
        </tr>
    </table>

    <!-- Section 7: Démarches & Étapes -->
    <div class="section-title">9. {{ $t('sections.steps') }}</div>
    <div class="box-card" style="margin-bottom: 2.5mm;">
        <ol style="margin: 0; padding-left: 5mm;">
            @foreach ($t('steps') as $item)
                <li style="margin-bottom: 0.8mm;">{{ $item }}</li>
            @endforeach
        </ol>
    </div>

    <!-- Section 8: Cadre légal et notice -->
    <div style="background: #fffbeb; border: 1px solid #fde68a; border-left: 3px solid #f59e0b; padding: 2mm 2.5mm; font-size: 7.2pt; color: #78350f; line-height: 1.35; margin-bottom: 3mm;">
        <div><strong>Notice légale :</strong> {{ $t('notice') }}</div>
        <div style="margin-top: 1mm; font-size: 6.8pt; color: #92400e;">
            {{ $t('cert.auth_code') }} : <strong>{{ $authCode ?? $doc->authCode() }}</strong> · Inscrit au registre officiel des assurances :brand.
        </div>
    </div>

    <!-- Official Signatures Table (Ministerial / Governmental Layout) -->
    <table class="signatures-table">
        <tr>
            <td style="width: 52%; vertical-align: top;">
                <div class="sig-role">{{ $t('info.sig_left_role') }}</div>
                <div class="sig-sub">{{ $t('info.sig_left_title') }}</div>
                <div style="margin: 0.5mm 0 0.5mm -2mm;">
                    <img src="{{ $stampAndSignatureSvg }}" style="width: 50mm; height: 26mm;" alt="Sceau & Signature">
                </div>
                <div class="sig-name">{{ $t('info.sig_left_name') }}</div>
            </td>
            <td style="width: 48%; vertical-align: top; padding-left: 12mm;">
                <div class="sig-role">{{ $t('info.sig_right_role') }}</div>
                <div class="sig-sub">{{ $t('info.sig_right_title') }}</div>
                <div style="margin: 1.5mm 0 1.5mm;">
                    <img src="{{ $rightSignatureSvg }}" style="width: 40mm; height: 22mm;" alt="Signature">
                </div>
                <div class="sig-name">{{ $doc->first_name }} {{ $doc->last_name }}</div>
            </td>
        </tr>
    </table>

    <!-- Bottom Centered Tricolor Bar (Page 2) -->
    {!! \App\Support\OfficialDocumentAssets::tricolorBar('75mm', '3mm', '3mm auto 0') !!}

</body>
</html>
