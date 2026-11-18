<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Relevé d'Identité Bancaire · {{ $client->account_number }}</title>
<style>
    @page { margin: 12mm 15mm; size: a4 portrait; }
    * { font-family: 'DejaVu Sans', sans-serif; box-sizing: border-box; }
    body { margin: 0; padding: 0; font-size: 9pt; color: #1e293b; line-height: 1.5; background: #ffffff; }
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .tricolor { height: 4px; width: 100%; margin: 12px 0 25px 0; }
    .tricolor td { height: 4px; padding: 0; }
    .title-banner { background: #f8fafc; border: 1px solid #e2e8f0; border-left: 5px solid #0fa57e; padding: 12px 16px; margin-bottom: 25px; }
    .title-banner h1 { margin: 0; font-size: 14pt; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; }
    .title-banner p { margin: 3px 0 0 0; font-size: 8pt; color: #64748b; }
    .box { border: 1px solid #cbd5e1; border-radius: 6px; padding: 16px; margin-bottom: 20px; background: #ffffff; }
    .box-title { font-size: 10pt; font-weight: bold; color: #0f172a; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px; }
    .rib-grid { width: 100%; border-collapse: collapse; }
    .rib-grid td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
    .rib-label { width: 32%; color: #64748b; font-size: 8.5pt; text-transform: uppercase; font-weight: bold; }
    .rib-value { width: 68%; color: #0f172a; font-size: 10pt; font-weight: bold; font-family: 'DejaVu Sans Mono', monospace; }
    .notice { font-size: 8pt; color: #64748b; line-height: 1.4; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 12px; }
    .footer-table { width: 100%; margin-top: 40px; border-collapse: collapse; }
    .signature-box { border: 1px dashed #cbd5e1; border-radius: 6px; padding: 12px; text-align: center; height: 90px; }
</style>
</head>
<body>

<table class="header-table">
    <tr>
        <td style="width: 55%; vertical-align: middle;">
            @if($logo)
                <img src="{{ $logo }}" style="height: 38px; vertical-align: middle;" alt="Venatomar">
            @endif
            <div style="font-size: 12pt; font-weight: bold; color: #0fa57e; margin-top: 4px;">VENATOMAR BANKING</div>
            <div style="font-size: 7.5pt; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Département des Opérations Bancaires & Prêts</div>
        </td>
        <td style="width: 45%; text-align: right; font-size: 8pt; color: #475569; vertical-align: middle;">
            <strong>Venatomar Financial Services</strong><br>
            Carrer d’Estruc, 9, 08002 Barcelone<br>
            contact@venatomar.com · www.venatomar.com<br>
            Date d'édition : <strong>{{ $date }}</strong>
        </td>
    </tr>
</table>

<table class="tricolor" cellpadding="0" cellspacing="0">
    <tr>
        <td style="background: #0FA57E; width: 33%;"></td>
        <td style="background: #F26B1D; width: 34%;"></td>
        <td style="background: #E2E8F0; width: 33%;"></td>
    </tr>
</table>

<div class="title-banner">
    <h1>Relevé d'Identité Bancaire (RIB / IBAN)</h1>
    <p>Document officiel attestant des coordonnées bancaires du titulaire auprès de Venatomar.</p>
</div>

<div class="box">
    <div class="box-title">TITULAIRE DU COMPTE</div>
    <table class="rib-grid">
        <tr>
            <td class="rib-label">Nom et Prénom :</td>
            <td class="rib-value" style="font-family: 'DejaVu Sans', sans-serif;">{{ strtoupper($client->fullName()) }}</td>
        </tr>
        <tr>
            <td class="rib-label">Adresse :</td>
            <td class="rib-value" style="font-family: 'DejaVu Sans', sans-serif; font-size: 9pt;">
                {{ $client->address ?: 'Non renseignée' }}, {{ $client->postal_code }} {{ $client->city }} ({{ $client->country }})
            </td>
        </tr>
        <tr>
            <td class="rib-label">N° Client :</td>
            <td class="rib-value">{{ $client->account_number }}</td>
        </tr>
    </table>
</div>

<div class="box">
    <div class="box-title">COORDONNÉES BANCAIRES NATIONALES & INTERNATIONALES</div>
    <table class="rib-grid">
        <tr>
            <td class="rib-label">IBAN / Compte :</td>
            <td class="rib-value" style="color: #0fa57e; font-size: 11pt;">{{ $client->iban }}</td>
        </tr>
        <tr>
            <td class="rib-label">Code BIC / SWIFT :</td>
            <td class="rib-value">{{ $client->bic }}</td>
        </tr>
        <tr>
            <td class="rib-label">Établissement :</td>
            <td class="rib-value" style="font-family: 'DejaVu Sans', sans-serif; font-size: 9.5pt;">VENATOMAR BANK S.A.</td>
        </tr>
        <tr>
            <td class="rib-label">Domiciliation :</td>
            <td class="rib-value" style="font-family: 'DejaVu Sans', sans-serif; font-size: 9.5pt;">Siège Européen Barcelone - Agence Centrale</td>
        </tr>
        <tr>
            <td class="rib-label">Devise du compte :</td>
            <td class="rib-value" style="font-family: 'DejaVu Sans', sans-serif;">{{ $client->currency }}</td>
        </tr>
    </table>
</div>

<table class="footer-table">
    <tr>
        <td style="width: 50%; padding-right: 15px;">
            <div class="signature-box">
                <div style="font-size: 8pt; font-weight: bold; color: #64748b; margin-bottom: 30px;">Cachet de la Banque</div>
                <div style="font-size: 9pt; font-weight: bold; color: #0fa57e;">VENATOMAR OPERATIONS</div>
                <div style="font-size: 7.5pt; color: #94a3b8;">Certifié Conforme</div>
            </div>
        </td>
        <td style="width: 50%; padding-left: 15px;">
            <div class="signature-box">
                <div style="font-size: 8pt; font-weight: bold; color: #64748b; margin-bottom: 30px;">La Direction des Engagements</div>
                <div style="font-size: 9pt; font-weight: bold; color: #1e293b;">Département Juridique</div>
                <div style="font-size: 7.5pt; color: #94a3b8;">Visa Électronique Sécurisé</div>
            </div>
        </td>
    </tr>
</table>

<div class="notice">
    <strong>Usage du document :</strong> Ce relevé d'identité bancaire est destiné à être remis à vos débiteurs ou créanciers pour faciliter le règlement de vos opérations par virement ou prélèvement. Il garantit la bonne transmission des flux financiers sur votre compte bancaire Venatomar.
</div>

</body>
</html>
