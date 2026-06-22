<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Facture {{ $facture->numero }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 11px;
    color: #1a1a2e;
    background: #fff;
    line-height: 1.5;
}

/* ─── Bande top ─────────────────────────────────────── */
.top-bar {
    background: #1E3A8A;
    height: 8px;
    width: 100%;
}

/* ─── Header ─────────────────────────────────────────── */
.header-table {
    width: 100%;
    padding: 32px 48px 24px;
    border-bottom: 2px solid #E8EEF8;
}
.logo-cell {
    width: 180px;
    vertical-align: top;
}
.logo-cell img {
    max-width: 160px;
    max-height: 70px;
}
.doc-title-cell {
    vertical-align: top;
    padding-left: 16px;
}
.doc-type {
    font-size: 36px;
    font-weight: 900;
    color: #1E3A8A;
    letter-spacing: 4px;
    text-transform: uppercase;
    line-height: 1;
}
.doc-num {
    font-size: 13px;
    color: #6B7280;
    font-weight: 600;
    margin-top: 6px;
    letter-spacing: 1px;
}
.company-cell {
    text-align: right;
    vertical-align: top;
    width: 220px;
}
.company-name {
    font-size: 15px;
    font-weight: 800;
    color: #1a1a2e;
}
.company-meta {
    font-size: 10px;
    color: #6B7280;
    margin-top: 5px;
    line-height: 1.7;
}
.stamp {
    display: inline-block;
    border: 2.5px solid currentColor;
    border-radius: 5px;
    padding: 3px 12px;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-top: 10px;
}
.stamp-payee    { color: #059669; border-color: #059669; }
.stamp-envoyee  { color: #1E3A8A; border-color: #1E3A8A; }
.stamp-retard   { color: #DC2626; border-color: #DC2626; }
.stamp-brouillon{ color: #9CA3AF; border-color: #9CA3AF; }

/* ─── Parties ────────────────────────────────────────── */
.parties-section {
    width: 100%;
    padding: 22px 48px;
    border-bottom: 1px solid #E8EEF8;
}
.parties-table { width: 100%; }
.party-cell {
    width: 50%;
    vertical-align: top;
    padding-right: 32px;
}
.party-cell + .party-cell {
    padding-right: 0;
    padding-left: 32px;
    border-left: 1px solid #E8EEF8;
}
.party-label {
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .14em;
    color: #9CA3AF;
    margin-bottom: 7px;
}
.party-name {
    font-size: 14px;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 4px;
}
.party-detail {
    font-size: 10.5px;
    color: #6B7280;
    line-height: 1.7;
}

/* ─── Meta bar ───────────────────────────────────────── */
.meta-section {
    background: #F0F4FF;
    border-top: 1px solid #C7D4F0;
    border-bottom: 1px solid #C7D4F0;
    padding: 14px 48px;
}
.meta-table { width: 100%; }
.meta-cell { vertical-align: top; }
.mi-label {
    font-size: 8.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: #9CA3AF;
    margin-bottom: 4px;
}
.mi-val {
    font-size: 12px;
    font-weight: 700;
    color: #1a1a2e;
}
.mi-val.blue  { color: #1E3A8A; }
.mi-val.red   { color: #DC2626; }

/* ─── Tableau produits ───────────────────────────────── */
.table-section { padding: 24px 48px 0; }
table.products { width: 100%; border-collapse: collapse; }
table.products thead tr {
    background: #1E3A8A;
    color: #fff;
}
table.products thead th {
    padding: 10px 12px;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    text-align: left;
}
table.products thead th.r { text-align: right; }
table.products thead th.c { text-align: center; }

table.products tbody tr:nth-child(even) { background: #F7F9FF; }
table.products tbody tr { border-bottom: 1px solid #E8EEF8; }
table.products tbody tr:last-child { border-bottom: 2px solid #C7D4F0; }

table.products td { padding: 11px 12px; vertical-align: middle; }
table.products td.r { text-align: right; }
table.products td.c { text-align: center; }

.prod-name { font-weight: 700; color: #1a1a2e; font-size: 12px; }
.prod-ref  { font-size: 9.5px; color: #9CA3AF; font-family: monospace; margin-top: 2px; }
.qty-badge {
    display: inline-block;
    background: #EFF6FF;
    color: #1E3A8A;
    border: 1px solid #BFDBFE;
    border-radius: 4px;
    padding: 2px 10px;
    font-size: 11px;
    font-weight: 700;
}
.col-ht   { color: #6B7280; font-size: 11px; }
.col-sub  { font-weight: 700; color: #1a1a2e; font-size: 12px; }

/* ─── Totaux ─────────────────────────────────────────── */
.totals-section {
    padding: 18px 48px 28px;
    text-align: right;
}
.totals-inner {
    display: inline-block;
    width: 270px;
    text-align: left;
}
table.totals { width: 100%; border-collapse: collapse; }
table.totals td {
    padding: 7px 4px;
    font-size: 12px;
    border-bottom: 1px solid #E8EEF8;
}
table.totals tr:last-of-type td { border-bottom: none; }
table.totals .t-label { color: #6B7280; }
table.totals .t-val   { text-align: right; font-weight: 600; color: #1a1a2e; }

.ttc-row {
    background: #1E3A8A;
    border-radius: 7px;
    margin-top: 6px;
}
.ttc-row td {
    padding: 11px 14px !important;
    border: none !important;
}
.ttc-row .t-label { color: rgba(255,255,255,.8); font-weight: 700; font-size: 13px; }
.ttc-row .t-val   { color: #fff; font-size: 18px; font-weight: 900; text-align: right; }

.restant-row td { border-bottom: none !important; }
.restant-row .t-label { color: #DC2626; font-size: 11px; font-weight: 600; }
.restant-row .t-val   { color: #DC2626; font-size: 14px; font-weight: 800; text-align: right; }

/* ─── Paiement confirmé ──────────────────────────────── */
.payment-box {
    margin: 0 48px 20px;
    padding: 14px 18px;
    background: #F0FDF4;
    border: 1.5px solid #BBF7D0;
    border-radius: 7px;
}
.payment-box .pi-title { font-size: 13px; font-weight: 700; color: #065F46; }
.payment-box .pi-sub   { font-size: 10.5px; color: #059669; margin-top: 3px; }

/* ─── Notes ──────────────────────────────────────────── */
.notes-box {
    margin: 0 48px 20px;
    padding: 12px 16px;
    background: #FFFBEB;
    border: 1px solid #FDE68A;
    border-radius: 6px;
    font-size: 10.5px;
    color: #92400E;
}
.notes-box .notes-label {
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #D97706;
    margin-bottom: 5px;
}

/* ─── Footer ─────────────────────────────────────────── */
.footer-table {
    width: 100%;
    padding: 16px 48px;
    border-top: 2px solid #E8EEF8;
    margin-top: 10px;
}
.footer-left  { vertical-align: middle; font-size: 10px; color: #9CA3AF; line-height: 1.7; }
.footer-right { vertical-align: middle; text-align: right; font-size: 10px; color: #9CA3AF; }
.footer-brand { font-size: 12px; font-weight: 800; color: #1E3A8A; letter-spacing: .5px; }

/* ─── Bande laterale gauche ─────────────────────────── */
.side-bar {
    position: fixed;
    left: 0;
    top: 0;
    width: 5px;
    height: 100%;
    background: #1E3A8A;
}
</style>
</head>
<body>

@php
    $commande       = $facture->commande;
    $totalTTC       = (float)($commande?->total_ttc ?? 0);
    $sousTotal      = (float)($commande?->sous_total_ht ?? 0);
    $tauxTVA        = (float)($commande?->tva ?? 18);
    $montantTVA     = $sousTotal * $tauxTVA / 100;
    $montantPaye    = (float)$facture->montant_paye;
    $restant        = max(0, $totalTTC - $montantPaye);
    $lignes         = $commande ? $commande->lignes : collect();
    $echeanceRetard = $facture->date_echeance && $facture->date_echeance->isPast() && $facture->statut !== 'payee';

    $logoBase64 = null;
    if ($facture->entreprise->logo) {
        $logoPath = storage_path('app/public/' . $facture->entreprise->logo);
        if (file_exists($logoPath)) {
            $ext  = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $mime = $ext === 'svg' ? 'image/svg+xml' : 'image/' . $ext;
            $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
    }
@endphp

{{-- Bande gauche --}}
<div class="side-bar"></div>

{{-- Bande top --}}
<div class="top-bar"></div>

{{-- ═══ HEADER ═══ --}}
<table class="header-table" cellpadding="0" cellspacing="0">
    <tr>
        {{-- Logo --}}
        <td class="logo-cell">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="{{ $facture->entreprise->nom }}">
            @endif
        </td>

        {{-- Titre document --}}
        <td class="doc-title-cell">
            <div class="doc-type">Facture</div>
            <div class="doc-num">{{ $facture->numero }}</div>
        </td>

        {{-- Entreprise + stamp --}}
        <td class="company-cell">
            <div class="company-name">{{ $facture->entreprise->nom }}</div>
            <div class="company-meta">
                @if($facture->entreprise->adresse){{ $facture->entreprise->adresse }}<br>@endif
                @if($facture->entreprise->ville){{ $facture->entreprise->ville }}<br>@endif
                @if($facture->entreprise->email){{ $facture->entreprise->email }}<br>@endif
                @if($facture->entreprise->telephone){{ $facture->entreprise->telephone }}<br>@endif
                @if($facture->entreprise->siret)NINEA : {{ $facture->entreprise->siret }}@endif
            </div>
            <br>
            @if($facture->statut === 'payee')
                <span class="stamp stamp-payee">Payée</span>
            @elseif($facture->statut === 'en_retard' || $echeanceRetard)
                <span class="stamp stamp-retard">En retard</span>
            @elseif($facture->statut === 'envoyee')
                <span class="stamp stamp-envoyee">À payer</span>
            @else
                <span class="stamp stamp-brouillon">Brouillon</span>
            @endif
        </td>
    </tr>
</table>

{{-- ═══ PARTIES ═══ --}}
<div class="parties-section">
    <table class="parties-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="party-cell">
                <div class="party-label">Émetteur</div>
                <div class="party-name">{{ $facture->entreprise->nom }}</div>
                <div class="party-detail">
                    @if($facture->entreprise->adresse){{ $facture->entreprise->adresse }}<br>@endif
                    @if($facture->entreprise->email){{ $facture->entreprise->email }}<br>@endif
                    @if($facture->entreprise->telephone){{ $facture->entreprise->telephone }}@endif
                </div>
            </td>
            <td class="party-cell">
                <div class="party-label">Facturé à</div>
                <div class="party-name">{{ $commande?->client_nom ?? '—' }}</div>
                <div class="party-detail">
                    @if($commande?->client_email){{ $commande->client_email }}<br>@endif
                    @if($commande?->client_telephone){{ $commande->client_telephone }}@endif
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- ═══ META BAR ═══ --}}
<div class="meta-section">
    <table class="meta-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="meta-cell">
                <div class="mi-label">N° Facture</div>
                <div class="mi-val">{{ $facture->numero }}</div>
            </td>
            <td class="meta-cell">
                <div class="mi-label">Date d'émission</div>
                <div class="mi-val">{{ $facture->created_at->format('d/m/Y') }}</div>
            </td>
            @if($facture->date_echeance)
            <td class="meta-cell">
                <div class="mi-label">Échéance</div>
                <div class="mi-val {{ $echeanceRetard ? 'red' : '' }}">
                    {{ $facture->date_echeance->format('d/m/Y') }}
                    @if($echeanceRetard) ⚠@endif
                </div>
            </td>
            @endif
            @if($commande)
            <td class="meta-cell">
                <div class="mi-label">Commande liée</div>
                <div class="mi-val">{{ $commande->numero }}</div>
            </td>
            @endif
            <td class="meta-cell" style="text-align:right;">
                <div class="mi-label">Montant TTC</div>
                <div class="mi-val blue">{{ number_format($totalTTC, 0, ',', ' ') }} FCFA</div>
            </td>
        </tr>
    </table>
</div>

{{-- ═══ TABLE PRODUITS ═══ --}}
<div class="table-section">
    <table class="products" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:42%;">Produit / Service</th>
                <th class="r" style="width:18%;">Prix unit. HT</th>
                <th class="c" style="width:10%;">Qté</th>
                <th class="r" style="width:15%;">Total HT</th>
                <th class="r" style="width:15%;">Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lignes as $ligne)
            <tr>
                <td>
                    <div class="prod-name">{{ $ligne->produit?->nom ?? 'Produit supprimé' }}</div>
                    @if($ligne->produit?->reference_sku)
                    <div class="prod-ref">Réf. {{ $ligne->produit->reference_sku }}</div>
                    @endif
                </td>
                <td class="r col-ht">{{ number_format($ligne->prix_unitaire_snapshot, 0, ',', ' ') }} FCFA</td>
                <td class="c"><span class="qty-badge">{{ $ligne->quantite }}</span></td>
                <td class="r col-ht">{{ number_format($ligne->sous_total, 0, ',', ' ') }} FCFA</td>
                <td class="r col-sub">{{ number_format($ligne->sous_total, 0, ',', ' ') }} FCFA</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;color:#9CA3AF;padding:24px;">Aucun article</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ═══ TOTAUX ═══ --}}
<div class="totals-section">
    <table class="totals" cellpadding="0" cellspacing="0" style="width:270px;margin-left:auto;">
        <tr>
            <td class="t-label">Sous-total HT</td>
            <td class="t-val">{{ number_format($sousTotal, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td class="t-label">TVA ({{ $tauxTVA }}%)</td>
            <td class="t-val">{{ number_format($montantTVA, 0, ',', ' ') }} FCFA</td>
        </tr>
        @if($montantPaye > 0 && $facture->statut !== 'payee')
        <tr>
            <td class="t-label" style="color:#059669;">Déjà payé</td>
            <td class="t-val" style="color:#059669;text-align:right;">− {{ number_format($montantPaye, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endif
        <tr class="ttc-row">
            <td class="t-label">Total TTC</td>
            <td class="t-val">{{ number_format($totalTTC, 0, ',', ' ') }} FCFA</td>
        </tr>
        @if($restant > 0 && $facture->statut !== 'payee')
        <tr class="restant-row">
            <td class="t-label">Restant dû</td>
            <td class="t-val">{{ number_format($restant, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endif
    </table>
</div>

{{-- ═══ PAIEMENT CONFIRMÉ ═══ --}}
@if($facture->statut === 'payee')
<div class="payment-box">
    <div class="pi-title">✓ &nbsp;Paiement intégralement reçu</div>
    @if($facture->payee_at)
    <div class="pi-sub">Le {{ $facture->payee_at->format('d/m/Y à H:i') }}</div>
    @endif
</div>
@endif

{{-- ═══ NOTES ═══ --}}
@if($commande?->notes)
<div class="notes-box">
    <div class="notes-label">Notes</div>
    <div>{{ $commande->notes }}</div>
</div>
@endif

{{-- ═══ FOOTER ═══ --}}
<table class="footer-table" cellpadding="0" cellspacing="0">
    <tr>
        <td class="footer-left">
            <div class="footer-brand">{{ $facture->entreprise->nom }}</div>
            <div>
                @if($facture->entreprise->adresse){{ $facture->entreprise->adresse }} &mdash; @endif
                @if($facture->entreprise->email){{ $facture->entreprise->email }}@endif
            </div>
        </td>
        <td class="footer-right">
            <div>Document généré par <strong style="color:#1E3A8A;">GestiPro</strong></div>
            <div style="margin-top:3px;">{{ now()->format('d/m/Y') }}</div>
        </td>
    </tr>
</table>

</body>
</html>
