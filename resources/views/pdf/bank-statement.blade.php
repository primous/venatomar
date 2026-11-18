<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Relevé de Compte · {{ $client->account_number }}</title>
<style>
    @page { margin: 10mm 14mm; size: a4 portrait; }
    * { font-family: 'DejaVu Sans', sans-serif; box-sizing: border-box; }
    body { margin: 0; padding: 0; font-size: 8.5pt; color: #1e293b; line-height: 1.45; background: #ffffff; }
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    .tricolor { height: 4px; width: 100%; margin: 8px 0 18px 0; }
    .tricolor td { height: 4px; padding: 0; }
    .summary-box { width: 100%; border-collapse: collapse; margin-bottom: 20px; background: #f8fafc; border: 1px solid #e2e8f0; }
    .summary-box td { padding: 10px 14px; vertical-align: top; }
    .tx-table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 8pt; }
    .tx-table th { background: #0fa57e; color: #ffffff; text-align: left; padding: 8px 10px; font-weight: bold; font-size: 7.5pt; text-transform: uppercase; }
    .tx-table td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    .tx-table tr:nth-child(even) td { background: #f8fafc; }
    .amount-pos { color: #0fa57e; font-weight: bold; text-align: right; }
    .amount-neg { color: #dc2626; font-weight: bold; text-align: right; }
</style>
</head>
<body>

<table class="header-table">
    <tr>
        <td style="width: 55%; vertical-align: middle;">
            @if($logo)
                <img src="{{ $logo }}" style="height: 36px; vertical-align: middle;" alt="Venatomar">
            @endif
            <div style="font-size: 11pt; font-weight: bold; color: #0fa57e; margin-top: 3px;">VENATOMAR BANKING</div>
            <div style="font-size: 7.5pt; color: #64748b;">RELEVÉ DE COMPTE OFFICIEL</div>
        </td>
        <td style="width: 45%; text-align: right; font-size: 8pt; color: #475569; vertical-align: middle;">
            Période : <strong>{{ $period }}</strong><br>
            Édité le : <strong>{{ $date }}</strong><br>
            Devise : <strong>{{ $client->currency }}</strong>
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

<table class="summary-box">
    <tr>
        <td style="width: 50%; border-right: 1px solid #e2e8f0;">
            <div style="font-size: 7.5pt; color: #64748b; text-transform: uppercase; font-weight: bold;">Titulaire</div>
            <div style="font-size: 10pt; font-weight: bold; color: #0f172a; margin-top: 2px;">{{ strtoupper($client->fullName()) }}</div>
            <div style="font-size: 8pt; color: #475569; margin-top: 2px;">{{ $client->address }}, {{ $client->city }} ({{ $client->country }})</div>
            <div style="font-size: 8pt; color: #0fa57e; font-weight: bold; margin-top: 4px;">N° Compte : {{ $client->account_number }}</div>
        </td>
        <td style="width: 50%;">
            <div style="font-size: 7.5pt; color: #64748b; text-transform: uppercase; font-weight: bold;">Situation du Compte</div>
            <div style="font-size: 12pt; font-weight: bold; color: #0fa57e; margin-top: 2px;">
                Solde Courant : {{ $client->formattedBalance() }}
            </div>
            <div style="font-size: 8.5pt; color: #475569; margin-top: 2px;">
                Épargne disponible : <strong>{{ $client->formattedSavingsBalance() }}</strong>
            </div>
            <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">
                IBAN : {{ $client->iban }} · BIC : {{ $client->bic }}
            </div>
        </td>
    </tr>
</table>

<div style="font-size: 9pt; font-weight: bold; color: #0f172a; margin-top: 15px; text-transform: uppercase; letter-spacing: 0.5px;">
    Historique des Mouvements et Opérations
</div>

<table class="tx-table">
    <thead>
        <tr>
            <th style="width: 15%;">Date</th>
            <th style="width: 20%;">Réf. / Opération</th>
            <th style="width: 45%;">Détails & Bénéficiaire / Émetteur</th>
            <th style="width: 20%; text-align: right;">Montant</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $tx)
            <tr>
                <td>{{ $tx->created_at->format('d/m/Y') }}</td>
                <td>
                    <strong>{{ $tx->reference }}</strong><br>
                    <span style="font-size: 7pt; color: #64748b;">
                        {{ in_array($tx->type, ['transfer_out', 'fee']) ? 'Débit' : 'Crédit' }}
                    </span>
                </td>
                <td>
                    <strong>{{ $tx->target_bank_name ?: 'Opération bancaire' }}</strong>
                    @if($tx->target_account_holder)
                        — {{ $tx->target_account_holder }}
                    @endif
                    @if($tx->description)
                        <div style="font-size: 7.5pt; color: #64748b;">{{ $tx->description }}</div>
                    @endif
                </td>
                <td class="{{ in_array($tx->type, ['transfer_out', 'fee']) ? 'amount-neg' : 'amount-pos' }}">
                    {{ $tx->formattedAmount() }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px; color: #64748b;">
                    Aucune transaction enregistrée pour cette période.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 25px; font-size: 7.5pt; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px;">
    Relevé certifié par le système central de télétransmission Venatomar Banking. En cas de contestation, veuillez contacter le service client sous un délai de 30 jours à compter de la date d'émission du présent relevé.
</div>

</body>
</html>
