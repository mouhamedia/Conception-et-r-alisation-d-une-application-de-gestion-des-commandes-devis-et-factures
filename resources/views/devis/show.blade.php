@extends('layouts.app')
@section('title', $devis->numero)
@section('page-title', 'Devis ' . $devis->numero)

@section('topbar-actions')
<div style="display:flex;align-items:center;gap:8px;">
    @if($devis->statut === 'brouillon')
    <a href="{{ route('devis.edit', $devis) }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--card);border:1px solid var(--border);border-radius:9px;color:var(--muted);font-size:13px;font-weight:500;text-decoration:none;">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Modifier
    </a>
    <form method="POST" action="{{ route('devis.envoyer', $devis) }}">
        @csrf @method('PATCH')
        <button type="submit" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--card);border:1px solid var(--border);border-radius:9px;color:var(--text2);font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            Marquer envoyé
        </button>
    </form>
    @endif
    @if(in_array($devis->statut, ['brouillon','envoye']))
    <form method="POST" action="{{ route('devis.valider', $devis) }}">
        @csrf @method('PATCH')
        <button type="submit" class="btn-primary" style="background:#16a34a;padding:8px 18px;">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Valider le devis
        </button>
    </form>
    @endif
</div>
@endsection

@section('content')
@php
$statutColors = [
    'brouillon' => ['bg'=>'rgba(100,116,139,.12)','color'=>'var(--muted)'],
    'envoye'    => ['bg'=>'var(--accent-bg)','color'=>'var(--accent-t)'],
    'accepte'   => ['bg'=>'rgba(34,197,94,.14)','color'=>'#4ade80'],
    'refuse'    => ['bg'=>'rgba(239,68,68,.14)','color'=>'#f87171'],
];
$sc = $statutColors[$devis->statut] ?? $statutColors['brouillon'];
$labels = ['brouillon'=>'Brouillon','envoye'=>'Envoyé','accepte'=>'Accepté','refuse'=>'Refusé'];
$expireBientot = $devis->date_expiration && $devis->date_expiration->diffInDays(now()) <= 7 && $devis->date_expiration->isFuture() && $devis->statut === 'envoye';
@endphp

<style>
.dv-layout{display:grid;grid-template-columns:1fr 290px;gap:22px;align-items:start;}
@media(max-width:1000px){.dv-layout{grid-template-columns:1fr;}}
.dv-card{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;transition:background .25s;}
.dv-doc{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:32px;transition:background .25s;}
.divider{border:none;border-top:1px solid var(--border);margin:22px 0;}
.doc-th{font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;text-align:left;padding:9px 14px;background:var(--card2);border-bottom:1px solid var(--border);}
.doc-td{padding:13px 14px;font-size:13px;color:var(--text2);border-bottom:1px solid var(--border2,rgba(255,255,255,.04));}
.doc-td:last-child,.doc-tr:last-child .doc-td{border-bottom:none;}
.doc-tr:hover .doc-td{background:var(--accent-bg);}
.side-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;transition:background .25s;}
.side-row{display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid var(--border2,rgba(255,255,255,.04));font-size:13px;}
.side-row:last-child{border-bottom:none;}
</style>

{{-- Alerte expiration --}}
@if($expireBientot)
<div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.25);border-radius:10px;margin-bottom:18px;">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#fbbf24" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <span style="font-size:13px;color:#fbbf24;font-weight:600;">Ce devis expire le {{ $devis->date_expiration->format('d/m/Y') }}</span>
</div>
@endif

{{-- Commande liée --}}
@if($devis->commande)
<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 18px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);border-radius:12px;margin-bottom:18px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:34px;height:34px;border-radius:9px;background:rgba(34,197,94,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#4ade80" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div style="font-size:13px;font-weight:700;color:#4ade80;">Commande générée</div>
            <div style="font-size:12px;color:rgba(74,222,128,.7);">{{ $devis->commande->reference }}</div>
        </div>
    </div>
    <a href="{{ route('commandes.show', $devis->commande) }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:rgba(34,197,94,.2);border:1px solid rgba(34,197,94,.4);border-radius:9px;color:#4ade80;font-size:12px;font-weight:700;text-decoration:none;">
        Voir la commande →
    </a>
</div>
@endif

<div class="dv-layout">
    {{-- Document principal --}}
    <div class="dv-doc">

        {{-- En-tête document --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
            <div>
                <div style="font-size:28px;font-weight:800;color:var(--text);letter-spacing:-.5px;">{{ $devis->numero }}</div>
                <div style="font-size:12px;color:var(--muted);margin-top:5px;display:flex;flex-direction:column;gap:2px;">
                    <span>Émis le {{ $devis->date_emission->format('d/m/Y') }}</span>
                    @if($devis->date_expiration)
                    <span style="color:{{ $devis->date_expiration->isPast() && $devis->statut!=='accepte' ? '#f87171' : 'var(--muted)' }};">
                        Expire le {{ $devis->date_expiration->format('d/m/Y') }}
                    </span>
                    @endif
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px;">
                <span style="display:inline-flex;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
                    {{ $labels[$devis->statut] ?? $devis->statut }}
                </span>
                <div style="font-size:24px;font-weight:800;color:var(--accent-t);">{{ number_format($devis->total_ttc,0,',',' ') }} FCFA</div>
            </div>
        </div>

        {{-- DE / POUR --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;padding:20px;background:var(--card2);border-radius:12px;margin-bottom:24px;">
            <div>
                <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px;">De</div>
                <div style="font-size:14px;font-weight:700;color:var(--text);">{{ $devis->entreprise->nom ?? '—' }}</div>
                @if($devis->entreprise?->email)
                <div style="font-size:12px;color:var(--muted);margin-top:3px;">{{ $devis->entreprise->email }}</div>
                @endif
                @if($devis->entreprise?->telephone)
                <div style="font-size:12px;color:var(--muted);">{{ $devis->entreprise->telephone }}</div>
                @endif
            </div>
            <div>
                <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px;">Pour</div>
                <div style="font-size:14px;font-weight:700;color:var(--text);">{{ $devis->client_nom }}</div>
                @if($devis->client_email)<div style="font-size:12px;color:var(--muted);margin-top:3px;">{{ $devis->client_email }}</div>@endif
                @if($devis->client_telephone)<div style="font-size:12px;color:var(--muted);">{{ $devis->client_telephone }}</div>@endif
            </div>
        </div>

        {{-- Lignes produits --}}
        <div style="border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:20px;">
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
                @foreach($devis->lignes as $ligne)
                <tr class="doc-tr">
                    <td class="doc-td">
                        <div style="font-weight:600;color:var(--text);">{{ $ligne->produit?->nom ?? 'Produit supprimé' }}</div>
                        @if($ligne->produit?->reference_sku)
                        <div style="font-size:11px;color:var(--muted);font-family:monospace;margin-top:2px;">{{ $ligne->produit->reference_sku }}</div>
                        @endif
                    </td>
                    <td class="doc-td" style="text-align:right;color:var(--muted);">{{ number_format($ligne->prix_unitaire_snapshot,0,',',' ') }} FCFA</td>
                    <td class="doc-td" style="text-align:center;">
                        <span style="display:inline-flex;padding:2px 10px;background:var(--card2);border:1px solid var(--border);border-radius:20px;font-size:12px;font-weight:600;color:var(--text2);">{{ $ligne->quantite }}</span>
                    </td>
                    <td class="doc-td" style="text-align:right;font-weight:700;color:var(--text);">{{ number_format($ligne->sous_total,0,',',' ') }} FCFA</td>
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
                    <span style="color:var(--text2);font-weight:600;">{{ number_format($devis->sous_total_ht,0,',',' ') }} FCFA</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:11px 16px;border-bottom:1px solid var(--border);font-size:13px;">
                    <span style="color:var(--muted);">TVA ({{ $devis->tva }}%)</span>
                    <span style="color:var(--text2);font-weight:600;">{{ number_format($devis->sous_total_ht*$devis->tva/100,0,',',' ') }} FCFA</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:var(--accent-bg);">
                    <span style="font-size:14px;font-weight:700;color:var(--text);">Total TTC</span>
                    <span style="font-size:18px;font-weight:800;color:var(--accent-t);">{{ number_format($devis->total_ttc,0,',',' ') }} FCFA</span>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($devis->notes)
        <div style="margin-top:22px;padding:16px;background:var(--card2);border:1px solid var(--border);border-radius:12px;">
            <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px;">Notes</div>
            <p style="font-size:13px;color:var(--text2);line-height:1.6;">{{ $devis->notes }}</p>
        </div>
        @endif
    </div>

    {{-- Sidebar info --}}
    <div style="display:flex;flex-direction:column;gap:14px;position:sticky;top:82px;">

        {{-- Récupération : accepté sans commande --}}
        @if($devis->statut === 'accepte' && !$devis->commande)
        <div class="side-card" style="border-color:rgba(245,158,11,.3);background:rgba(245,158,11,.05);">
            <div style="font-size:12px;font-weight:700;color:#fbbf24;text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">Commande non générée</div>
            <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">Le devis a été accepté mais la commande n'a pas pu être créée. Cliquez pour relancer la génération.</p>
            <form method="POST" action="{{ route('devis.valider', $devis) }}">
                @csrf @method('PATCH')
                <button type="submit" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:10px;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.4);border-radius:10px;color:#fbbf24;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Générer la commande
                </button>
            </form>
        </div>
        @endif

        {{-- Actions --}}
        @if(in_array($devis->statut, ['brouillon','envoye']))
        <div class="side-card">
            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">Actions</div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                @if($devis->statut === 'brouillon')
                <form method="POST" action="{{ route('devis.envoyer', $devis) }}">
                    @csrf @method('PATCH')
                    <button type="submit" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:10px;background:var(--card2);border:1px solid var(--border);border-radius:10px;color:var(--text2);font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;" onmouseover="this.style.background='var(--accent-bg)'" onmouseout="this.style.background='var(--card2)'">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Marquer envoyé
                    </button>
                </form>
                <a href="{{ route('devis.edit', $devis) }}" style="display:flex;align-items:center;justify-content:center;gap:7px;padding:10px;background:var(--card2);border:1px solid var(--border);border-radius:10px;color:var(--text2);font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;" onmouseover="this.style.background='var(--accent-bg)'" onmouseout="this.style.background='var(--card2)'">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Modifier
                </a>
                @endif
                <form method="POST" action="{{ route('devis.valider', $devis) }}">
                    @csrf @method('PATCH')
                    <button type="submit" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:10px;background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.3);border-radius:10px;color:#4ade80;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Valider le devis
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Résumé --}}
        <div class="side-card">
            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">Résumé</div>
            <div class="side-row">
                <span style="color:var(--muted);">Statut</span>
                <span style="font-weight:700;padding:2px 8px;border-radius:20px;font-size:11px;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">{{ $labels[$devis->statut]??$devis->statut }}</span>
            </div>
            <div class="side-row">
                <span style="color:var(--muted);">Créé par</span>
                <span style="font-weight:600;color:var(--text2);font-size:12px;">{{ $devis->user?->prenom }} {{ $devis->user?->name }}</span>
            </div>
            <div class="side-row">
                <span style="color:var(--muted);">Émis le</span>
                <span style="color:var(--text2);">{{ $devis->date_emission->format('d/m/Y') }}</span>
            </div>
            @if($devis->date_expiration)
            <div class="side-row">
                <span style="color:var(--muted);">Expire le</span>
                <span style="color:{{ $devis->date_expiration->isPast()&&$devis->statut!=='accepte'?'#f87171':'var(--text2)' }};font-weight:{{ $devis->date_expiration->isPast()&&$devis->statut!=='accepte'?'700':'400' }};">
                    {{ $devis->date_expiration->format('d/m/Y') }}
                </span>
            </div>
            @endif
            <div class="side-row">
                <span style="color:var(--muted);">Lignes</span>
                <span style="color:var(--text2);">{{ $devis->lignes->count() }}</span>
            </div>
            <div class="side-row" style="border:none;padding-top:10px;margin-top:4px;border-top:1px solid var(--border);">
                <span style="color:var(--muted);">Total TTC</span>
                <span style="font-size:16px;font-weight:800;color:var(--accent-t);">{{ number_format($devis->total_ttc,0,',',' ') }}<span style="font-size:11px;margin-left:3px;">FCFA</span></span>
            </div>
        </div>

        {{-- Client --}}
        <div class="side-card">
            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">Client</div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                <div style="width:36px;height:36px;border-radius:50%;background:var(--accent-bg);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--accent-t);flex-shrink:0;">
                    {{ strtoupper(substr($devis->client_nom,0,2)) }}
                </div>
                <div>
                    <div style="font-weight:700;font-size:13px;color:var(--text);">{{ $devis->client_nom }}</div>
                </div>
            </div>
            @if($devis->client_email)
            <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted);margin-bottom:5px;">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                {{ $devis->client_email }}
            </div>
            @endif
            @if($devis->client_telephone)
            <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted);">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                {{ $devis->client_telephone }}
            </div>
            @endif
        </div>

        {{-- Navigation --}}
        <a href="{{ route('devis.index') }}" style="display:flex;align-items:center;justify-content:center;gap:7px;padding:10px;background:var(--card);border:1px solid var(--border);border-radius:10px;color:var(--muted);font-size:13px;font-weight:500;text-decoration:none;transition:all .15s;" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour aux devis
        </a>
    </div>
</div>
@endsection
