<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $facture->numero }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:system-ui,-apple-system,'Segoe UI',sans-serif;-webkit-font-smoothing:antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 20px;">
<tr><td align="center">

    {{-- ── Card principale ── --}}
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">

        {{-- Header --}}
        <tr>
            <td style="background:linear-gradient(135deg,#1E3A8A,#2563EB);padding:32px 36px 28px;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
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
                            @if($logoBase64)
                            <img src="{{ $logoBase64 }}" alt="{{ $facture->entreprise->nom }}"
                                 style="max-height:40px;max-width:140px;object-fit:contain;margin-bottom:16px;display:block;filter:brightness(0) invert(1);">
                            @else
                            <div style="font-size:13px;font-weight:800;color:rgba(255,255,255,.6);letter-spacing:.05em;text-transform:uppercase;margin-bottom:10px;">
                                {{ $facture->entreprise->nom }}
                            </div>
                            @endif
                            <div style="font-size:28px;font-weight:900;color:#ffffff;letter-spacing:-.5px;line-height:1.1;">
                                Facture {{ $facture->numero }}
                            </div>
                            <div style="font-size:13px;color:rgba(255,255,255,.7);margin-top:6px;">
                                Émise le {{ $facture->created_at->format('d/m/Y') }}
                                @if($facture->date_echeance)
                                · Échéance le {{ $facture->date_echeance->format('d/m/Y') }}
                                @endif
                            </div>
                        </td>
                        <td style="text-align:right;vertical-align:top;">
                            <div style="background:rgba(255,255,255,.15);border-radius:12px;padding:16px 20px;display:inline-block;">
                                <div style="font-size:11px;color:rgba(255,255,255,.65);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;">Total TTC</div>
                                @php
                                    $totalTTC = (float)($facture->commande?->total_ttc ?? 0);
                                @endphp
                                <div style="font-size:26px;font-weight:900;color:#ffffff;line-height:1;">
                                    {{ number_format($totalTTC, 0, ',', ' ') }}
                                </div>
                                <div style="font-size:12px;color:rgba(255,255,255,.7);margin-top:2px;">FCFA</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- Intro --}}
        <tr>
            <td style="padding:28px 36px 24px;">
                @php $clientNom = $facture->commande?->client_nom ?? 'Client'; @endphp
                <p style="font-size:15px;font-weight:700;color:#111;margin:0 0 8px;">Bonjour {{ $clientNom }},</p>
                <p style="font-size:14px;color:#4b5563;line-height:1.6;margin:0;">
                    Veuillez trouver ci-joint votre facture <strong style="color:#1E3A8A;">{{ $facture->numero }}</strong>
                    de la part de <strong>{{ $facture->entreprise->nom }}</strong>.
                    Le PDF est joint à cet email. Vous pouvez également le télécharger en cliquant sur le bouton ci-dessous.
                </p>
            </td>
        </tr>

        {{-- Récapitulatif --}}
        <tr>
            <td style="padding:0 36px 24px;">
                <table width="100%" cellpadding="0" cellspacing="0"
                       style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
                    <tr style="background:#f1f5f9;">
                        <td colspan="2" style="padding:12px 16px;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.1em;">
                            Détail de la facture
                        </td>
                    </tr>
                    @php
                        $sousTotal  = (float)($facture->commande?->sous_total_ht ?? 0);
                        $tva        = (float)($facture->commande?->tva ?? 18);
                        $montantTVA = $sousTotal * $tva / 100;
                        $lignes     = $facture->commande ? $facture->commande->lignes : collect();
                    @endphp
                    @foreach($lignes as $ligne)
                    <tr style="border-top:1px solid #e5e7eb;">
                        <td style="padding:10px 16px;font-size:13px;color:#374151;">
                            {{ $ligne->produit?->nom ?? 'Produit' }}
                            <span style="color:#9ca3af;font-size:11px;"> × {{ $ligne->quantite }}</span>
                        </td>
                        <td style="padding:10px 16px;font-size:13px;font-weight:600;color:#111;text-align:right;">
                            {{ number_format($ligne->sous_total, 0, ',', ' ') }} FCFA
                        </td>
                    </tr>
                    @endforeach
                    <tr style="border-top:1px solid #e5e7eb;">
                        <td style="padding:10px 16px;font-size:12px;color:#9ca3af;">Sous-total HT</td>
                        <td style="padding:10px 16px;font-size:12px;color:#6b7280;text-align:right;">{{ number_format($sousTotal, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr style="border-top:1px solid #e5e7eb;">
                        <td style="padding:10px 16px;font-size:12px;color:#9ca3af;">TVA ({{ $tva }}%)</td>
                        <td style="padding:10px 16px;font-size:12px;color:#6b7280;text-align:right;">{{ number_format($montantTVA, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr style="background:#1E3A8A;">
                        <td style="padding:13px 16px;font-size:14px;font-weight:700;color:rgba(255,255,255,.85);">Total TTC</td>
                        <td style="padding:13px 16px;font-size:18px;font-weight:900;color:#fff;text-align:right;">{{ number_format($totalTTC, 0, ',', ' ') }} FCFA</td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- CTA --}}
        <tr>
            <td style="padding:0 36px 32px;text-align:center;">
                <a href="{{ $downloadUrl }}"
                   style="display:inline-block;background:linear-gradient(135deg,#1E3A8A,#2563EB);color:#fff;font-size:14px;font-weight:700;padding:14px 36px;border-radius:12px;text-decoration:none;box-shadow:0 4px 16px rgba(37,99,235,.35);">
                    ⬇ Télécharger la facture PDF
                </a>
                <p style="font-size:11px;color:#9ca3af;margin-top:10px;">
                    Lien valable 30 jours · Le PDF est également joint à cet email
                </p>
            </td>
        </tr>

        {{-- Infos émetteur --}}
        <tr>
            <td style="background:#f8fafc;border-top:1px solid #e5e7eb;padding:20px 36px;">
                <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin:0 0 6px;">Émis par</p>
                <p style="font-size:13px;font-weight:700;color:#111;margin:0 0 3px;">{{ $facture->entreprise->nom }}</p>
                <p style="font-size:12px;color:#6b7280;margin:0;line-height:1.6;">
                    @if($facture->entreprise->adresse){{ $facture->entreprise->adresse }}<br>@endif
                    @if($facture->entreprise->email){{ $facture->entreprise->email }}@endif
                    @if($facture->entreprise->telephone) · {{ $facture->entreprise->telephone }}@endif
                </p>
            </td>
        </tr>

        {{-- Footer --}}
        <tr>
            <td style="background:#f1f5f9;border-top:1px solid #e5e7eb;padding:16px 36px;text-align:center;">
                <p style="font-size:11px;color:#9ca3af;margin:0;">
                    Cet email a été envoyé via <strong style="color:#1E3A8A;">GestiPro</strong> ·
                    En cas de question, contactez <a href="mailto:{{ $facture->entreprise->email ?? '' }}" style="color:#2563EB;text-decoration:none;">{{ $facture->entreprise->email ?? $facture->entreprise->nom }}</a>
                </p>
            </td>
        </tr>

    </table>
</td></tr>
</table>

</body>
</html>
