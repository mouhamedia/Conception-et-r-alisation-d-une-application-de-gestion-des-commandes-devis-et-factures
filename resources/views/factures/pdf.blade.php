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
            color: #111827;
            background: #fff;
        }

        /* ── Bande latérale gauche ── */
        .page-wrap {
            min-height: 100vh;
            border-left: 6px solid #1E3A8A;
            padding: 0;
        }

        /* ── Header ── */
        .header {
            padding: 36px 44px 28px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid #E5ECF6;
        }
        .header-left .doc-type {
            font-size: 32px;
            font-weight: 900;
            color: #1E3A8A;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .header-left .doc-num {
            font-size: 13px;
            color: #6B7280;
            margin-top: 4px;
            font-weight: 500;
        }
        .header-right {
            text-align: right;
        }
        .company-name {
            font-size: 16px;
            font-weight: 800;
            color: #111827;
        }
        .company-meta {
            font-size: 11px;
            color: #6B7280;
            margin-top: 4px;
            line-height: 1.6;
        }

        /* ── Status stamp ── */
        .stamp {
            display: inline-block;
            border: 3px solid currentColor;
            border-radius: 6px;
            padding: 4px 14px;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
            transform: rotate(-5deg);
            opacity: 0.85;
        }
        .stamp-payee    { color: #059669; }
        .stamp-envoyee  { color: #1E3A8A; }
        .stamp-retard   { color: #DC2626; }
        .stamp-brouillon{ color: #9CA3AF; }

        /* ── Info parties ── */
        .parties {
            display: flex;
            gap: 0;
            padding: 28px 44px;
            border-bottom: 1px solid #E5ECF6;
        }
        .party { flex: 1; }
        .party + .party { border-left: 1px solid #E5ECF6; padding-left: 32px; }
        .party-label {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: #9CA3AF;
            margin-bottom: 8px;
        }
        .party-name {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 3px;
        }
        .party-detail {
            font-size: 11px;
            color: #6B7280;
            line-height: 1.6;
        }

        /* ── Dates & numéros ── */
        .meta-bar {
            background: #F8FAFF;
            border-bottom: 1px solid #E5ECF6;
            padding: 14px 44px;
            display: flex;
            gap: 48px;
        }
        .meta-item .mi-label {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #9CA3AF;
            margin-bottom: 3px;
        }
        .meta-item .mi-val {
            font-size: 12px;
            font-weight: 700;
            color: #111827;
        }
        .meta-item .mi-val.red { color: #DC2626; }

        /* ── Table produits ── */
        .table-wrap { padding: 28px 44px 0; }
        table { width: 100%; border-collapse: collapse; }
        thead tr {
            background: #1E3A8A;
        }
        thead th {
            padding: 10px 14px;
            font-size: 9px;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: .1em;
            text-align: left;
        }
        thead th.r { text-align: right; }
        thead th.c { text-align: center; }

        tbody tr:nth-child(even) { background: #F8FAFF; }
        tbody tr { border-bottom: 1px solid #E5ECF6; }
        tbody tr:last-child { border-bottom: 2px solid #E5ECF6; }

        td { padding: 12px 14px; vertical-align: middle; }
        td.r { text-align: right; }
        td.c { text-align: center; }
        .prod-name { font-weight: 600; color: #111827; font-size: 12px; }
        .prod-ref  { font-size: 10px; color: #9CA3AF; margin-top: 2px; font-family: monospace; }
        .qty-badge {
            display: inline-block;
            background: #EFF6FF;
            color: #1E3A8A;
            border: 1px solid #BFDBFE;
            border-radius: 4px;
            padding: 2px 9px;
            font-size: 11px;
            font-weight: 700;
        }
        .price-ht { color: #6B7280; font-size: 11px; }
        .subtotal { font-weight: 700; color: #111827; font-size: 12px; }

        /* ── Totaux ── */
        .totals-wrap {
            padding: 20px 44px 32px;
            display: flex;
            justify-content: flex-end;
        }
        .totals-box {
            width: 280px;
        }
        .tot-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 0;
            border-bottom: 1px solid #E5ECF6;
            font-size: 12px;
        }
        .tot-row:last-child { border-bottom: none; }
        .tot-row .tl { color: #6B7280; }
        .tot-row .tv { font-weight: 600; color: #111827; }
        .tot-row.total-ttc {
            background: #1E3A8A;
            border-radius: 8px;
            padding: 12px 14px;
            margin-top: 6px;
            border: none;
        }
        .tot-row.total-ttc .tl { color: rgba(255,255,255,.8); font-weight: 700; font-size: 13px; }
        .tot-row.total-ttc .tv { color: #fff; font-size: 18px; font-weight: 900; }

        /* ── Notes ── */
        .notes-wrap {
            margin: 0 44px 28px;
            padding: 14px 18px;
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 6px;
            font-size: 11px;
            color: #92400E;
        }
        .notes-label {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #D97706;
            margin-bottom: 5px;
        }

        /* ── Paiement info ── */
        .payment-info {
            margin: 0 44px 28px;
            padding: 16px 20px;
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .payment-info .pi-icon {
            width: 36px;
            height: 36px;
            background: #D1FAE5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .payment-info .pi-title { font-size: 13px; font-weight: 700; color: #065F46; }
        .payment-info .pi-sub   { font-size: 11px; color: #059669; margin-top: 2px; }

        /* ── Footer ── */
        .footer {
            border-top: 2px solid #E5ECF6;
            padding: 18px 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .footer-left {
            font-size: 10px;
            color: #9CA3AF;
            line-height: 1.7;
        }
        .footer-right {
            text-align: right;
            font-size: 10px;
            color: #9CA3AF;
        }
        .footer-brand {
            font-size: 12px;
            font-weight: 800;
            color: #1E3A8A;
            letter-spacing: .5px;
        }
        .divider-dot { color: #D1D5DB; margin: 0 6px; }
    </style>
</head>
<body>
<div class="page-wrap">

    @php
        $commande    = $facture->commande;
        $totalTTC    = (float)($commande?->total_ttc ?? 0);
        $sousTotal   = (float)($commande?->sous_total_ht ?? 0);
        $tauxTVA     = (float)($commande?->tva ?? 18);
        $montantTVA  = $sousTotal * $tauxTVA / 100;
        $montantPaye = (float)$facture->montant_paye;
        $restant     = max(0, $totalTTC - $montantPaye);
        $lignes      = $commande ? $commande->lignes : collect();
        $echeanceRetard = $facture->date_echeance && $facture->date_echeance->isPast() && $facture->statut !== 'payee';
    @endphp

    @php
        $logoBase64 = null;
        if ($facture->entreprise->logo) {
            $logoPath = storage_path('app/public/' . $facture->entreprise->logo);
            if (file_exists($logoPath)) {
                $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                $mime = $ext === 'svg' ? 'image/svg+xml' : 'image/' . $ext;
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }
    @endphp

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-left">
            @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="{{ $facture->entreprise->nom }}"
                 style="max-height:64px;max-width:200px;object-fit:contain;margin-bottom:14px;display:block;">
            @endif
            <div class="doc-type">Facture</div>
            <div class="doc-num">{{ $facture->numero }}</div>
        </div>
        <div class="header-right" style="display:flex;flex-direction:column;align-items:flex-end;gap:12px;">
            <div>
                <div class="company-name">{{ $facture->entreprise->nom }}</div>
                <div class="company-meta">
                    @if($facture->entreprise->adresse){{ $facture->entreprise->adresse }}@endif
                    @if($facture->entreprise->ville)<br>{{ $facture->entreprise->ville }}@endif
                    @if($facture->entreprise->email)<br>{{ $facture->entreprise->email }}@endif
                    @if($facture->entreprise->telephone)<br>{{ $facture->entreprise->telephone }}@endif
                    @if($facture->entreprise->siret)<br>NINEA : {{ $facture->entreprise->siret }}@endif
                </div>
            </div>
            {{-- Stamp statut --}}
            @if($facture->statut === 'payee')
                <span class="stamp stamp-payee">Payée</span>
            @elseif($facture->statut === 'en_retard' || $echeanceRetard)
                <span class="stamp stamp-retard">En retard</span>
            @elseif($facture->statut === 'envoyee')
                <span class="stamp stamp-envoyee">À payer</span>
            @else
                <span class="stamp stamp-brouillon">Brouillon</span>
            @endif
        </div>
    </div>

    {{-- ── PARTIES ── --}}
    <div class="parties">
        <div class="party">
            <div class="party-label">Émetteur</div>
            <div class="party-name">{{ $facture->entreprise->nom }}</div>
            <div class="party-detail">
                @if($facture->entreprise->adresse){{ $facture->entreprise->adresse }}<br>@endif
                @if($facture->entreprise->email){{ $facture->entreprise->email }}<br>@endif
                @if($facture->entreprise->telephone){{ $facture->entreprise->telephone }}@endif
            </div>
        </div>
        <div class="party">
            <div class="party-label">Facturé à</div>
            <div class="party-name">{{ $commande?->client_nom ?? '—' }}</div>
            @if($commande?->client_email || $commande?->client_telephone)
            <div class="party-detail">
                @if($commande?->client_email){{ $commande->client_email }}<br>@endif
                @if($commande?->client_telephone){{ $commande->client_telephone }}@endif
            </div>
            @endif
        </div>
    </div>

    {{-- ── META BAR ── --}}
    <div class="meta-bar">
        <div class="meta-item">
            <div class="mi-label">N° Facture</div>
            <div class="mi-val">{{ $facture->numero }}</div>
        </div>
        <div class="meta-item">
            <div class="mi-label">Date d'émission</div>
            <div class="mi-val">{{ $facture->created_at->format('d/m/Y') }}</div>
        </div>
        @if($facture->date_echeance)
        <div class="meta-item">
            <div class="mi-label">Échéance</div>
            <div class="mi-val {{ $echeanceRetard ? 'red' : '' }}">
                {{ $facture->date_echeance->format('d/m/Y') }}
                {{ $echeanceRetard ? ' ⚠ Dépassée' : '' }}
            </div>
        </div>
        @endif
        @if($commande)
        <div class="meta-item">
            <div class="mi-label">Commande liée</div>
            <div class="mi-val">{{ $commande->numero }}</div>
        </div>
        @endif
        <div class="meta-item">
            <div class="mi-label">Montant TTC</div>
            <div class="mi-val" style="color:#1E3A8A;">{{ number_format($totalTTC, 0, ',', ' ') }} FCFA</div>
        </div>
    </div>

    {{-- ── TABLE PRODUITS ── --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:42%;">Produit / Service</th>
                    <th class="r" style="width:20%;">Prix unit. HT</th>
                    <th class="c" style="width:12%;">Qté</th>
                    <th class="r" style="width:14%;">Total HT</th>
                    <th class="r" style="width:12%;">Sous-total</th>
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
                    <td class="r price-ht">{{ number_format($ligne->prix_unitaire_snapshot, 0, ',', ' ') }} FCFA</td>
                    <td class="c"><span class="qty-badge">{{ $ligne->quantite }}</span></td>
                    <td class="r price-ht">{{ number_format($ligne->sous_total, 0, ',', ' ') }} FCFA</td>
                    <td class="r subtotal">{{ number_format($ligne->sous_total, 0, ',', ' ') }} FCFA</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#9CA3AF;padding:24px;">Aucun article</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── TOTAUX ── --}}
    <div class="totals-wrap">
        <div class="totals-box">
            <div class="tot-row">
                <span class="tl">Sous-total HT</span>
                <span class="tv">{{ number_format($sousTotal, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="tot-row">
                <span class="tl">TVA ({{ $tauxTVA }}%)</span>
                <span class="tv">{{ number_format($montantTVA, 0, ',', ' ') }} FCFA</span>
            </div>
            @if($montantPaye > 0 && $facture->statut !== 'payee')
            <div class="tot-row">
                <span class="tl" style="color:#059669;">Déjà payé</span>
                <span class="tv" style="color:#059669;">− {{ number_format($montantPaye, 0, ',', ' ') }} FCFA</span>
            </div>
            @endif
            <div class="tot-row total-ttc">
                <span class="tl">Total TTC</span>
                <span class="tv">{{ number_format($totalTTC, 0, ',', ' ') }} FCFA</span>
            </div>
            @if($restant > 0 && $facture->statut !== 'payee')
            <div class="tot-row" style="margin-top:8px;padding:8px 0 0;">
                <span style="font-size:11px;color:#DC2626;font-weight:600;">Restant dû</span>
                <span style="font-size:13px;font-weight:800;color:#DC2626;">{{ number_format($restant, 0, ',', ' ') }} FCFA</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ── PAIEMENT CONFIRMÉ ── --}}
    @if($facture->statut === 'payee')
    <div class="payment-info">
        <div class="pi-icon">✓</div>
        <div>
            <div class="pi-title">Paiement intégralement reçu</div>
            @if($facture->payee_at)
            <div class="pi-sub">Le {{ $facture->payee_at->format('d/m/Y à H:i') }}</div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── NOTES ── --}}
    @if($commande?->notes)
    <div class="notes-wrap">
        <div class="notes-label">Notes</div>
        <div>{{ $commande->notes }}</div>
    </div>
    @endif

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-left">
            <div class="footer-brand">{{ $facture->entreprise->nom }}</div>
            <div style="margin-top:4px;">
                @if($facture->entreprise->adresse){{ $facture->entreprise->adresse }} — @endif
                @if($facture->entreprise->email){{ $facture->entreprise->email }}@endif
            </div>
        </div>
        <div class="footer-right">
            <div>Document généré par <strong>GestiPro</strong></div>
            <div style="margin-top:3px;color:#D1D5DB;">{{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

</div>
</body>
</html>
