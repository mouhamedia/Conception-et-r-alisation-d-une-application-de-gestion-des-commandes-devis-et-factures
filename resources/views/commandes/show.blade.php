@extends('layouts.app')
@section('title', $commande->numero)
@section('page-title', 'Commande ' . $commande->numero)

@section('topbar-actions')
@php
$statutsActions = [
    'en_attente' => ['label'=>'En attente','color'=>'rgba(245,158,11,.15)','border'=>'rgba(245,158,11,.35)','text'=>'#fbbf24'],
    'en_cours'   => ['label'=>'En cours','color'=>'rgba(14,165,233,.15)','border'=>'rgba(14,165,233,.35)','text'=>'#38bdf8'],
    'livree'     => ['label'=>'Livrée','color'=>'rgba(34,197,94,.15)','border'=>'rgba(34,197,94,.35)','text'=>'#4ade80'],
    'annulee'    => ['label'=>'Annulée','color'=>'rgba(239,68,68,.15)','border'=>'rgba(239,68,68,.35)','text'=>'#f87171'],
];
@endphp
<div style="display:flex;align-items:center;gap:8px;">
    @foreach($statutsActions as $statut => $cfg)
    @if($commande->statut !== $statut)
    <form method="POST" action="{{ route('commandes.statut', [$commande, $statut]) }}">
        @csrf @method('PATCH')
        <button type="submit" style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:{{ $cfg['color'] }};border:1px solid {{ $cfg['border'] }};border-radius:9px;color:{{ $cfg['text'] }};font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .15s;">
            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            {{ $cfg['label'] }}
        </button>
    </form>
    @endif
    @endforeach
</div>
@endsection

@section('content')
@php
$badges = [
    'en_attente' => ['bg'=>'rgba(245,158,11,.15)','color'=>'#fbbf24','label'=>'En attente'],
    'en_cours'   => ['bg'=>'rgba(14,165,233,.15)','color'=>'#38bdf8','label'=>'En cours'],
    'livree'     => ['bg'=>'rgba(34,197,94,.15)','color'=>'#4ade80','label'=>'Livrée'],
    'annulee'    => ['bg'=>'rgba(239,68,68,.15)','color'=>'#f87171','label'=>'Annulée'],
];
$b = $badges[$commande->statut] ?? ['bg'=>'rgba(148,163,184,.15)','color'=>'#94a3b8','label'=>$commande->statut];
@endphp

@if(session('success'))
<div style="display:flex;align-items:center;gap:10px;padding:13px 18px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);border-radius:12px;margin-bottom:18px;">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#4ade80" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span style="font-size:13px;color:#4ade80;font-weight:600;">{{ session('success') }}</span>
</div>
@endif

<style>
.cmd-layout{display:grid;grid-template-columns:1fr 290px;gap:22px;align-items:start;}
@media(max-width:1000px){.cmd-layout{grid-template-columns:1fr;}}
.cmd-doc{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:32px;transition:background .25s;}
.side-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;transition:background .25s;}
.side-row{display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid var(--border2,rgba(255,255,255,.04));font-size:13px;}
.side-row:last-child{border-bottom:none;}
.doc-th{font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;text-align:left;padding:9px 14px;background:var(--card2);border-bottom:1px solid var(--border);}
.doc-td{padding:13px 14px;font-size:13px;color:var(--text2);border-bottom:1px solid var(--border2,rgba(255,255,255,.04));}
.doc-tr:last-child .doc-td{border-bottom:none;}
.doc-tr:hover .doc-td{background:var(--accent-bg);}
</style>

<div class="cmd-layout">
    {{-- Document principal --}}
    <div class="cmd-doc">

        {{-- En-tête --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
            <div>
                <div style="font-size:28px;font-weight:800;color:var(--text);letter-spacing:-.5px;">{{ $commande->numero }}</div>
                <div style="font-size:12px;color:var(--muted);margin-top:5px;display:flex;flex-direction:column;gap:3px;">
                    <span>Créée le {{ $commande->created_at->format('d/m/Y à H:i') }}</span>
                    @if($commande->devis)
                    <span>Devis origine :
                        <a href="{{ route('devis.show', $commande->devis) }}" style="color:var(--accent-t);font-weight:600;text-decoration:none;">
                            {{ $commande->devis->numero }}
                        </a>
                    </span>
                    @endif
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px;">
                <span style="display:inline-flex;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;background:{{ $b['bg'] }};color:{{ $b['color'] }};">
                    {{ $b['label'] }}
                </span>
                <div style="font-size:26px;font-weight:800;color:var(--accent-t);">{{ number_format($commande->total_ttc, 0, ',', ' ') }} DZD</div>
            </div>
        </div>

        {{-- Client --}}
        <div style="display:flex;align-items:center;gap:12px;padding:16px 18px;background:var(--card2);border:1px solid var(--border);border-radius:12px;margin-bottom:24px;">
            <div style="width:40px;height:40px;border-radius:50%;background:var(--accent-bg);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:var(--accent-t);flex-shrink:0;">
                {{ strtoupper(substr($commande->client_nom, 0, 2)) }}
            </div>
            <div>
                <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:2px;">Client</div>
                <div style="font-size:14px;font-weight:700;color:var(--text);">{{ $commande->client_nom }}</div>
            </div>
        </div>

        {{-- Lignes produits --}}
        <div style="border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:24px;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th class="doc-th">Produit / Service</th>
                        <th class="doc-th" style="text-align:right;">Prix unit. HT</th>
                        <th class="doc-th" style="text-align:center;">Qté</th>
                        <th class="doc-th" style="text-align:right;">Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($commande->lignes as $ligne)
                <tr class="doc-tr">
                    <td class="doc-td">
                        <div style="font-weight:600;color:var(--text);">{{ $ligne->produit?->nom ?? 'Produit supprimé' }}</div>
                        @if($ligne->produit?->reference_sku)
                        <div style="font-size:11px;color:var(--muted);font-family:monospace;margin-top:2px;">{{ $ligne->produit->reference_sku }}</div>
                        @endif
                    </td>
                    <td class="doc-td" style="text-align:right;color:var(--muted);">{{ number_format($ligne->prix_unitaire_snapshot, 0, ',', ' ') }} DZD</td>
                    <td class="doc-td" style="text-align:center;">
                        <span style="display:inline-flex;padding:2px 10px;background:var(--card2);border:1px solid var(--border);border-radius:20px;font-size:12px;font-weight:600;color:var(--text2);">{{ $ligne->quantite }}</span>
                    </td>
                    <td class="doc-td" style="text-align:right;font-weight:700;color:var(--text);">{{ number_format($ligne->sous_total, 0, ',', ' ') }} DZD</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totaux --}}
        <div style="display:flex;justify-content:flex-end;">
            <div style="width:280px;background:var(--card2);border:1px solid var(--border);border-radius:12px;overflow:hidden;">
                <div style="display:flex;justify-content:space-between;padding:11px 16px;border-bottom:1px solid var(--border);font-size:13px;">
                    <span style="color:var(--muted);">Sous-total HT</span>
                    <span style="color:var(--text2);font-weight:600;">{{ number_format($commande->sous_total_ht, 0, ',', ' ') }} DZD</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:11px 16px;border-bottom:1px solid var(--border);font-size:13px;">
                    <span style="color:var(--muted);">TVA ({{ $commande->tva }}%)</span>
                    <span style="color:var(--text2);font-weight:600;">{{ number_format($commande->sous_total_ht * $commande->tva / 100, 0, ',', ' ') }} DZD</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:var(--accent-bg);">
                    <span style="font-size:14px;font-weight:700;color:var(--text);">Total TTC</span>
                    <span style="font-size:18px;font-weight:800;color:var(--accent-t);">{{ number_format($commande->total_ttc, 0, ',', ' ') }} DZD</span>
                </div>
            </div>
        </div>

        @if($commande->notes)
        <div style="margin-top:22px;padding:16px;background:var(--card2);border:1px solid var(--border);border-radius:12px;">
            <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px;">Notes</div>
            <p style="font-size:13px;color:var(--text2);line-height:1.6;">{{ $commande->notes }}</p>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div style="display:flex;flex-direction:column;gap:14px;position:sticky;top:82px;">

        {{-- Changer de statut --}}
        <div class="side-card">
            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">Changer le statut</div>
            <div style="display:flex;flex-direction:column;gap:7px;">
                @foreach($badges as $statut => $cfg)
                @if($commande->statut !== $statut)
                <form method="POST" action="{{ route('commandes.statut', [$commande, $statut]) }}">
                    @csrf @method('PATCH')
                    <button type="submit" style="width:100%;display:flex;align-items:center;gap:8px;padding:9px 12px;background:{{ $cfg['bg'] }};border:1px solid rgba(255,255,255,.06);border-radius:10px;color:{{ $cfg['color'] }};font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;text-align:left;">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $cfg['color'] }};flex-shrink:0;"></span>
                        {{ $cfg['label'] }}
                    </button>
                </form>
                @else
                <div style="display:flex;align-items:center;gap:8px;padding:9px 12px;background:{{ $cfg['bg'] }};border:1px solid {{ str_replace(',.15)', ',.35)', $cfg['bg']) }};border-radius:10px;">
                    <span style="width:8px;height:8px;border-radius:50%;background:{{ $cfg['color'] }};flex-shrink:0;"></span>
                    <span style="font-size:13px;font-weight:700;color:{{ $cfg['color'] }};">{{ $cfg['label'] }} (actuel)</span>
                </div>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Résumé --}}
        <div class="side-card">
            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">Résumé</div>
            <div class="side-row">
                <span style="color:var(--muted);">Statut</span>
                <span style="font-weight:700;padding:2px 8px;border-radius:20px;font-size:11px;background:{{ $b['bg'] }};color:{{ $b['color'] }};">{{ $b['label'] }}</span>
            </div>
            <div class="side-row">
                <span style="color:var(--muted);">Lignes</span>
                <span style="color:var(--text2);font-weight:600;">{{ $commande->lignes->count() }}</span>
            </div>
            <div class="side-row">
                <span style="color:var(--muted);">Créée le</span>
                <span style="color:var(--text2);">{{ $commande->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="side-row" style="border:none;padding-top:10px;margin-top:4px;border-top:1px solid var(--border);">
                <span style="color:var(--muted);">Total TTC</span>
                <span style="font-size:16px;font-weight:800;color:var(--accent-t);">{{ number_format($commande->total_ttc, 0, ',', ' ') }}<span style="font-size:11px;margin-left:3px;">DZD</span></span>
            </div>
        </div>

        {{-- Devis origine --}}
        @if($commande->devis)
        <div class="side-card">
            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">Devis origine</div>
            <a href="{{ route('devis.show', $commande->devis) }}" style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--card2);border:1px solid var(--border);border-radius:10px;text-decoration:none;transition:all .15s;" onmouseover="this.style.background='var(--accent-bg)'" onmouseout="this.style.background='var(--card2)'">
                <div style="display:flex;align-items:center;gap:8px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="var(--accent-t)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span style="font-size:13px;font-weight:600;color:var(--accent-t);">{{ $commande->devis->numero }}</span>
                </div>
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        @endif

        {{-- Facture liée --}}
        @if($commande->facture)
        @php
        $factureColors = ['brouillon'=>['bg'=>'rgba(148,163,184,.12)','color'=>'#94a3b8'],'envoyee'=>['bg'=>'rgba(14,165,233,.12)','color'=>'#38bdf8'],'payee'=>['bg'=>'rgba(34,197,94,.12)','color'=>'#4ade80'],'en_retard'=>['bg'=>'rgba(239,68,68,.12)','color'=>'#f87171']];
        $fc = $factureColors[$commande->facture->statut] ?? ['bg'=>'rgba(148,163,184,.12)','color'=>'#94a3b8'];
        $factureLabels = ['brouillon'=>'Brouillon','envoyee'=>'Envoyée','payee'=>'Payée','en_retard'=>'En retard'];
        @endphp
        <div class="side-card" style="border-color:rgba(14,165,233,.25);">
            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">Facture associée</div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <span style="font-size:14px;font-weight:700;color:var(--text);">{{ $commande->facture->numero }}</span>
                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $fc['bg'] }};color:{{ $fc['color'] }};">{{ $factureLabels[$commande->facture->statut] ?? $commande->facture->statut }}</span>
            </div>
            <a href="{{ route('factures.show', $commande->facture) }}" style="display:flex;align-items:center;justify-content:center;gap:6px;padding:9px;background:var(--accent-bg);border:1px solid rgba(14,165,233,.3);border-radius:10px;color:var(--accent-t);font-size:13px;font-weight:700;text-decoration:none;transition:all .15s;" onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Voir la facture
            </a>
        </div>
        @endif

        {{-- Retour --}}
        <a href="{{ route('commandes.index') }}" style="display:flex;align-items:center;justify-content:center;gap:7px;padding:10px;background:var(--card);border:1px solid var(--border);border-radius:10px;color:var(--muted);font-size:13px;font-weight:500;text-decoration:none;transition:all .15s;" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour aux commandes
        </a>
    </div>
</div>
@endsection
