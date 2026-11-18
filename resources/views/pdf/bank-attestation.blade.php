<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Attestation de Solde Bancaire · {{ $client->account_number }}</title>
<style>
    @page { margin: 12mm 15mm; size: a4 portrait; }
    * { font-family: 'DejaVu Sans', sans-serif; box-sizing: border-box; }
    body { margin: 0; padding: 0; font-size: 8.8pt; color: #1e293b; line-height: 1.5; background: #ffffff; }
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .tricolor { height: 4px; width: 100%; margin: 10px 0 25px 0; }
    .tricolor td { height: 4px; padding: 0; }
    .ref-line { font-size: 8.5pt; font-weight: bold; color: #0f172a; margin-bottom: 25px; }
    .doc-title { text-align: center; font-size: 13pt; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 30px; border-bottom: 2px solid #0fa57e; padding-bottom: 8px; }
    .content-p { margin-bottom: 18px; text-align: justify; font-size: 9pt; }
    .highlight-card { background: #f8fafc; border: 1px solid #cbd5e1; border-left: 4px solid #0fa57e; padding: 14px 18px; margin: 22px 0; border-radius: 4px; }
    .signatures-table { width: 100%; margin-top: 50px; border-collapse: collapse; }
    .stamp-box { border: 1px dashed #94a3b8; border-radius: 6px; padding: 12px; height: 100px; text-align: center; }
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
            <div style="font-size: 7.5pt; color: #64748b; text-transform: uppercase;">Direction de la Conformité & des Titularisations</div>
        </td>
        <td style="width: 45%; text-align: right; font-size: 8pt; color: #475569; vertical-align: middle;">
            Venatomar Financial Services<br>
            Carrer d’Estruc, 9, 08002 Barcelone<br>
            Date : <strong>{{ $date }}</strong>
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

<div class="ref-line">
    N° Réf. : ATT-SOLDE/{{ date('Y') }}/{{ strtoupper(substr($client->account_number, -6)) }}
</div>

<div class="doc-title">
    ATTESTATION DE SOLDE ET DE BONNE TENUE DE COMPTE
</div>

<p class="content-p">
    La Direction des Opérations Bancaires et Financières de <strong>VENATOMAR FINANCIAL SERVICES S.A.</strong>, établissement de crédit agréé, certifie par la présente que :
</p>

<p class="content-p" style="padding-left: 20px;">
    <strong>Monsieur / Madame :</strong> {{ strtoupper($client->fullName()) }}<br>
    <strong>N° de Titulaire :</strong> {{ $client->account_number }}<br>
    <strong>Résidence déclarée :</strong> {{ $client->address ?: 'Adresse certifiée' }}, {{ $client->city }} ({{ $client->country }})<br>
    <strong>IBAN :</strong> {{ $client->iban }} · <strong>BIC :</strong> {{ $client->bic }}
</p>

<p class="content-p">
    Est titulaire d'un compte de dépôt auprès de notre établissement financier, ouvert et géré selon les normes réglementaires en vigueur.
</p>

<div class="highlight-card">
    <div style="font-size: 8pt; color: #64748b; text-transform: uppercase; font-weight: bold;">Situation Actuelle Certifiée :</div>
    <div style="font-size: 13pt; font-weight: bold; color: #0fa57e; margin-top: 4px;">
        Solde créditeur disponible : {{ $client->formattedBalance() }}
    </div>
    @if($client->savings_balance > 0)
        <div style="font-size: 9.5pt; color: #334155; margin-top: 4px;">
            Compte d'épargne rattaché : <strong>{{ $client->formattedSavingsBalance() }}</strong>
        </div>
    @endif
</div>

<p class="content-p">
    Ce compte fonctionne à ce jour à la parfaite satisfaction de notre établissement. Aucune opposition judiciaire, saisie ou incident de paiement n'est enregistré sur ce compte.
</p>

<p class="content-p">
    La présente attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit auprès des administrations ou organismes habilités.
</p>

<table class="signatures-table">
    <tr>
        <td style="width: 50%; padding-right: 20px;">
            <div class="stamp-box">
                <div style="font-size: 7.5pt; color: #64748b; font-weight: bold;">SCEAU DE CONFORMITÉ BANCAIRE</div>
                <div style="font-size: 9.5pt; font-weight: bold; color: #0fa57e; margin-top: 25px;">VENATOMAR BANKING</div>
                <div style="font-size: 7pt; color: #94a3b8;">Contrôle Réglementaire Validé</div>
            </div>
        </td>
        <td style="width: 50%; padding-left: 20px;">
            <div class="stamp-box">
                <div style="font-size: 7.5pt; color: #64748b; font-weight: bold;">LA DIRECTION GÉNÉRALE</div>
                <div style="font-size: 9.5pt; font-weight: bold; color: #1e293b; margin-top: 25px;">Signé Électroniquement</div>
                <div style="font-size: 7pt; color: #94a3b8;">Certificat eIDAS · Barcelone</div>
            </div>
        </td>
    </tr>
</table>

</body>
</html>
