@extends('layouts.app')

@section('title', 'Produits')
@section('page-title', 'Catalogue produits')

@section('topbar-actions')
<a href="{{ route('produits.create') }}" class="btn btn-primary">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Nouveau produit
</a>
@endsection

@section('content')
@php
$totalProduits   = $produits->total();
$stockFaible     = $produits->getCollection()->filter(fn($p) => $p->stock_actuel <= $p->stock_minimum)->count();
$actifs          = $produits->getCollection()->filter(fn($p) => $p->actif)->count();
$inactifs        = $produits->getCollection()->filter(fn($p) => !$p->actif)->count();
$catActive       = request('categorie', '');
@endphp

<style>
.sp-actif   { background: var(--c-green-bg);  color: var(--c-green); }
.sp-inactif { background: var(--c-gray-bg);   color: var(--c-gray);  border:1px solid var(--c-gray-b); }
.stock-bar { width:60px;height:4px;background:var(--border);border-radius:2px;display:inline-block;vertical-align:middle;overflow:hidden; }
.stock-bar-inner { height:100%;border-radius:2px; }
.act-btn { font-size:12px;font-weight:600;padding:5px 10px;border-radius:7px;border:none;cursor:pointer;font-family:inherit;text-decoration:none;display:inline-flex;align-items:center;gap:4px;transition:all .15s; }
.act-edit { background:var(--primary-bg);color:var(--primary); }
.act-edit:hover { background:var(--primary);color:white; }
.act-del  { background:var(--c-red-bg);color:var(--c-red);border:1px solid var(--c-red-b); }
.act-del:hover { background:var(--c-red);color:white; }
</style>

{{-- Stats --}}
<div class="pg-stats">
    <div class="ps">
        <div class="ps-icon" style="background:var(--primary-bg);">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div><div class="ps-val">{{ $totalProduits }}</div><div class="ps-lbl">Produits au total</div></div>
    </div>
    <div class="ps">
        <div class="ps-icon" style="background:var(--c-green-bg);">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="var(--c-green)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="ps-val" style="color:var(--c-green);">{{ $actifs }}</div><div class="ps-lbl">Actifs</div></div>
    </div>
    <div class="ps">
        <div class="ps-icon" style="background:var(--c-gray-bg);">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="var(--c-gray)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        </div>
        <div><div class="ps-val" style="color:var(--c-gray);">{{ $inactifs }}</div><div class="ps-lbl">Inactifs</div></div>
    </div>
    <div class="ps" style="{{ $stockFaible > 0 ? 'border-color:var(--c-red-b);' : '' }}">
        <div class="ps-icon" style="background:var(--c-red-bg);">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="var(--c-red)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div><div class="ps-val" style="{{ $stockFaible > 0 ? 'color:var(--c-red);' : '' }}">{{ $stockFaible }}</div><div class="ps-lbl">Alertes stock</div></div>
    </div>
</div>

{{-- Pills catégories --}}
@if($categories->isNotEmpty())
<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
    <span style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;flex-shrink:0;">Catégorie :</span>
    <a href="{{ route('produits.index') }}"
       style="display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;transition:all .15s;
              {{ !$catActive ? 'background:var(--accent);color:#fff;' : 'background:var(--card2);color:var(--muted);border:1px solid var(--border);' }}">
        Tous
    </a>
    @foreach($categories as $cat)
    <a href="{{ route('produits.index', ['categorie' => $cat]) }}"
       style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;transition:all .15s;
              {{ $catActive === $cat ? 'background:var(--accent);color:#fff;' : 'background:var(--card2);color:var(--muted);border:1px solid var(--border);' }}">
        <svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        {{ $cat }}
    </a>
    @endforeach
</div>
@endif

{{-- Toolbar --}}
<div class="toolbar">
    <div class="search-box">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="var(--muted2)" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Rechercher un produit, SKU…" oninput="filterTable()">
    </div>
    <select class="filter-sel" id="filterStatut" onchange="filterTable()">
        <option value="">Tous les statuts</option>
        <option value="actif">Actifs</option>
        <option value="inactif">Inactifs</option>
    </select>
    <select class="filter-sel" id="filterStock" onchange="filterTable()">
        <option value="">Tout le stock</option>
        <option value="faible">Stock faible</option>
    </select>
    <span id="countLabel" style="font-size:12px;color:var(--muted);white-space:nowrap;">{{ $totalProduits }} produit(s)</span>
</div>

{{-- Table --}}
<div class="tc">
    @if($produits->isEmpty())
    <div style="padding:60px 20px;text-align:center;">
        <div style="width:56px;height:56px;background:var(--card2);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px;">Aucun produit dans le catalogue</div>
        <div style="font-size:13px;color:var(--muted);margin-bottom:20px;">Commencez par ajouter votre premier produit ou service.</div>
        <a href="{{ route('produits.create') }}" class="btn btn-primary" style="margin:0 auto;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Ajouter un produit
        </a>
    </div>
    @else
    <table class="tbl" id="produitsTable">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Catégorie</th>
                <th style="text-align:right;">Prix HT</th>
                <th style="text-align:center;">Stock</th>
                <th style="text-align:center;">Statut</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produits as $produit)
            @php
            $stockOk  = $produit->stock_actuel > $produit->stock_minimum;
            $stockPct = $produit->stock_minimum > 0
                ? min(100, round(($produit->stock_actuel / ($produit->stock_minimum * 3)) * 100))
                : ($produit->stock_actuel > 0 ? 100 : 0);
            @endphp
            <tr data-statut="{{ $produit->actif ? 'actif' : 'inactif' }}"
                data-stock="{{ $stockOk ? 'ok' : 'faible' }}"
                data-search="{{ strtolower($produit->nom . ' ' . $produit->reference_sku . ' ' . $produit->categorie) }}">
                <td>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:36px;height:36px;border-radius:9px;background:var(--primary-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <div>
                            <div style="font-weight:600;color:var(--text);">{{ $produit->nom }}</div>
                            @if($produit->reference_sku)
                            <div style="font-size:11px;color:var(--muted);margin-top:1px;font-family:monospace;">{{ $produit->reference_sku }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    @if($produit->categorie)
                    <span class="badge badge-gray">{{ $produit->categorie }}</span>
                    @else
                    <span style="color:var(--muted2);">—</span>
                    @endif
                </td>
                <td style="text-align:right;font-weight:700;color:var(--text);">
                    {{ number_format($produit->prix_unitaire, 0, ',', ' ') }}
                    <span style="font-size:11px;font-weight:400;color:var(--muted);">DZD</span>
                </td>
                <td style="text-align:center;">
                    <div style="display:inline-flex;flex-direction:column;align-items:center;gap:4px;">
                        <span style="font-weight:700;color:{{ $stockOk ? 'var(--text)' : 'var(--c-red)' }};font-size:15px;">{{ $produit->stock_actuel }}</span>
                        <div style="display:flex;align-items:center;gap:5px;">
                            <div class="stock-bar">
                                <div class="stock-bar-inner" style="width:{{ $stockPct }}%;background:{{ $stockOk ? 'var(--primary)' : 'var(--c-red)' }};"></div>
                            </div>
                            <span style="font-size:10px;color:var(--muted);">min {{ $produit->stock_minimum }}</span>
                        </div>
                    </div>
                </td>
                <td style="text-align:center;">
                    <span class="sp {{ $produit->actif ? 'sp-actif' : 'sp-inactif' }}">
                        {{ $produit->actif ? 'Actif' : 'Inactif' }}
                    </span>
                </td>
                <td style="text-align:right;">
                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                        <a href="{{ route('produits.edit', $produit) }}" class="act-btn act-edit">
                            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Modifier
                        </a>
                        <form method="POST" action="{{ route('produits.destroy', $produit) }}" style="display:inline;"
                              onsubmit="return confirm('Supprimer {{ addslashes($produit->nom) }} ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="act-btn act-del">
                                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($produits->hasPages())
    <div style="padding:14px 20px;border-top:1px solid var(--border2);">{{ $produits->links() }}</div>
    @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
function filterTable() {
    const q      = document.getElementById('searchInput').value.toLowerCase();
    const statut = document.getElementById('filterStatut').value;
    const stock  = document.getElementById('filterStock').value;
    const rows   = document.querySelectorAll('#produitsTable tbody tr');
    let visible  = 0;
    rows.forEach(row => {
        const show = (!q || row.dataset.search.includes(q)) &&
                     (!statut || row.dataset.statut === statut) &&
                     (!stock  || row.dataset.stock  === stock);
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const lbl = document.getElementById('countLabel');
    if (lbl) lbl.textContent = visible + ' produit(s)';
}
</script>
@endpush
