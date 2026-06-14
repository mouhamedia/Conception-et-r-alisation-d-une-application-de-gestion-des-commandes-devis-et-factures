@extends('layouts.app')
@section('title', 'Devis ' . $devis->numero)
@section('page-title', 'Devis reçu')

@section('content')
<div style="max-width:760px;margin:0 auto;">

    @if(session('success'))
    <div style="background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.3);border-radius:12px;padding:14px 18px;margin-bottom:20px;color:#4ade80;font-size:14px;font-weight:600;">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);border-radius:12px;padding:14px 18px;margin-bottom:20px;color:#f87171;font-size:14px;font-weight:600;">
        {{ session('error') }}
    </div>
    @endif

    {{-- En-tête --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:28px;margin-bottom:16px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap;">
            <div>
                <div style="font-size:22px;font-weight:800;color:var(--text);">{{ $devis->numero }}</div>
                <div style="font-size:13px;color:var(--muted);margin-top:4px;">De : <strong style="color:var(--text2);">{{ $devis->entreprise->nom ?? '—' }}</strong></div>
                <div style="font-size:13px;color:var(--muted);margin-top:2px;">Émis le {{ $devis->date_emission->format('d/m/Y') }} · Expire le {{ $devis->date_expiration->format('d/m/Y') }}</div>
            </div>
            <div>
                @php
                $statutColors = [
                    'brouillon' => ['bg'=>'rgba(148,163,184,.15)','color'=>'#94a3b8','label'=>'Brouillon'],
                    'envoye'    => ['bg'=>'rgba(14,165,233,.15)','color'=>'#38bdf8','label'=>'En attente de réponse'],
                    'accepte'   => ['bg'=>'rgba(34,197,94,.15)','color'=>'#4ade80','label'=>'Accepté'],
                    'refuse'    => ['bg'=>'rgba(239,68,68,.15)','color'=>'#f87171','label'=>'Refusé'],
                    'expire'    => ['bg'=>'rgba(245,158,11,.15)','color'=>'#fbbf24','label'=>'Expiré'],
                ];
                $s = $statutColors[$devis->statut] ?? $statutColors['brouillon'];
                @endphp
                <span style="display:inline-block;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;background:{{ $s['bg'] }};color:{{ $s['color'] }};">
                    {{ $s['label'] }}
                </span>
            </div>
        </div>

        {{-- Adressé à --}}
        <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--border);display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div>
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);font-weight:600;margin-bottom:6px;">Adressé à</div>
                <div style="font-size:14px;font-weight:700;color:var(--text);">{{ $devis->client_nom }}</div>
                @if($devis->client_email)<div style="font-size:13px;color:var(--muted);">{{ $devis->client_email }}</div>@endif
                @if($devis->client_telephone)<div style="font-size:13px;color:var(--muted);">{{ $devis->client_telephone }}</div>@endif
            </div>
            <div>
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);font-weight:600;margin-bottom:6px;">Expéditeur</div>
                <div style="font-size:14px;font-weight:700;color:var(--text);">{{ $devis->entreprise->nom ?? '—' }}</div>
                @if($devis->entreprise?->email)<div style="font-size:13px;color:var(--muted);">{{ $devis->entreprise->email }}</div>@endif
            </div>
        </div>
    </div>

    {{-- Lignes produits --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;margin-bottom:16px;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--border);background:var(--card2);">
                    <th style="text-align:left;padding:12px 20px;font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);font-weight:600;">Produit</th>
                    <th style="text-align:center;padding:12px 16px;font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);font-weight:600;">Qté</th>
                    <th style="text-align:right;padding:12px 16px;font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);font-weight:600;">Prix unitaire</th>
                    <th style="text-align:right;padding:12px 20px;font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);font-weight:600;">Sous-total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($devis->lignes as $ligne)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:14px 20px;font-size:14px;color:var(--text);font-weight:500;">{{ $ligne->produit->nom ?? '—' }}</td>
                    <td style="padding:14px 16px;text-align:center;font-size:14px;color:var(--muted);">{{ $ligne->quantite }}</td>
                    <td style="padding:14px 16px;text-align:right;font-size:14px;color:var(--muted);">{{ number_format($ligne->prix_unitaire_snapshot, 0, ',', ' ') }} DZD</td>
                    <td style="padding:14px 20px;text-align:right;font-size:14px;font-weight:600;color:var(--text);">{{ number_format($ligne->sous_total, 0, ',', ' ') }} DZD</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totaux --}}
        <div style="padding:16px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;">
            <div style="min-width:240px;">
                <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;color:var(--muted);">
                    <span>Sous-total HT</span>
                    <span>{{ number_format($devis->sous_total_ht, 0, ',', ' ') }} DZD</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;color:var(--muted);">
                    <span>TVA ({{ $devis->tva }}%)</span>
                    <span>{{ number_format($devis->total_ttc - $devis->sous_total_ht, 0, ',', ' ') }} DZD</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0 0;border-top:1px solid var(--border);font-size:16px;font-weight:800;color:var(--text);">
                    <span>Total TTC</span>
                    <span>{{ number_format($devis->total_ttc, 0, ',', ' ') }} DZD</span>
                </div>
            </div>
        </div>
    </div>

    @if($devis->notes)
    <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px 20px;margin-bottom:16px;">
        <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:8px;">Notes</div>
        <div style="font-size:14px;color:var(--text2);line-height:1.6;">{{ $devis->notes }}</div>
    </div>
    @endif

    {{-- Actions client --}}
    @if($devis->statut === 'envoye')
    <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-size:14px;font-weight:700;color:var(--text);">Votre réponse</div>
            <div style="font-size:13px;color:var(--muted);margin-top:3px;">Acceptez ou refusez ce devis. Le fournisseur sera notifié immédiatement.</div>
        </div>
        <div style="display:flex;gap:10px;">
            <form method="POST" action="{{ route('devis.refuser-client', $devis) }}">
                @csrf @method('PATCH')
                <button type="submit"
                    style="padding:10px 22px;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);border-radius:10px;color:#f87171;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .15s;"
                    onmouseover="this.style.background='rgba(239,68,68,.22)'" onmouseout="this.style.background='rgba(239,68,68,.12)'"
                    onclick="return confirm('Confirmer le refus du devis {{ $devis->numero }} ?')">
                    Refuser
                </button>
            </form>
            <form method="POST" action="{{ route('devis.accepter-client', $devis) }}">
                @csrf @method('PATCH')
                <button type="submit"
                    style="padding:10px 22px;background:rgba(34,197,94,.85);border:1px solid rgba(34,197,94,.3);border-radius:10px;color:#fff;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .15s;"
                    onmouseover="this.style.background='rgba(34,197,94,1)'" onmouseout="this.style.background='rgba(34,197,94,.85)'"
                    onclick="return confirm('Confirmer l\'acceptation du devis {{ $devis->numero }} ?')">
                    Accepter le devis
                </button>
            </form>
        </div>
    </div>
    @elseif($devis->statut === 'accepte')
    <div style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.25);border-radius:14px;padding:18px 22px;text-align:center;color:#4ade80;font-size:14px;font-weight:700;">
        Vous avez accepté ce devis. La commande a été générée automatiquement.
    </div>
    @elseif($devis->statut === 'refuse')
    <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:14px;padding:18px 22px;text-align:center;color:#f87171;font-size:14px;font-weight:700;">
        Vous avez refusé ce devis.
    </div>
    @endif

</div>
@endsection
