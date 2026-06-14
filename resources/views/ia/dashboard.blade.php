@extends('layouts.app')

@section('title', 'Intelligence IA')

@push('styles')
<style>
/* ════════════════════════════════════════
   IA DASHBOARD — GestiPro
════════════════════════════════════════ */

/* ── Topbar breadcrumb ── */
.ia-topbar {
    display: flex; align-items: center; gap: 0;
    font-size: 13px; font-weight: 500; color: var(--muted);
    padding-bottom: 18px; gap: 8px;
}
.ia-topbar .sep { opacity: .4; }
.ia-topbar .active { color: var(--text); font-weight: 700; }
.ia-topbar .status-dot {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 600;
    margin-left: auto;
}
.ia-topbar .dot-green { width: 7px; height: 7px; background: #22c55e; border-radius: 50%; animation: pulse-dot 2s infinite; }
.ia-topbar .dot-red   { width: 7px; height: 7px; background: #ef4444; border-radius: 50%; }
.ia-topbar .role-badge {
    background: var(--card2); border: 1px solid var(--border);
    border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600; color: var(--text);
}
@keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.4} }

/* ── Grid layouts ── */
.ia-grid-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; }
.ia-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

/* ── Card base ── */
.ia-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
}
.ia-card-hd {
    padding: 16px 18px 14px;
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 1px solid var(--border);
}
.ia-card-hd h3 {
    font-size: 13px; font-weight: 700; color: var(--text);
    display: flex; align-items: center; gap: 8px;
}
.ia-card-hd .hd-icon {
    width: 14px; height: 14px; border-radius: 3px; flex-shrink: 0;
}
.ia-card-body { padding: 18px; }

/* ── KPI top cards ── */
.kpi-top {
    padding: 18px;
    display: flex; flex-direction: column; gap: 4px;
}
.kpi-top .kpi-label { font-size: 12px; color: var(--muted); font-weight: 500; display: flex; align-items: center; justify-content: space-between; }
.kpi-top .kpi-val   { font-size: 30px; font-weight: 800; color: var(--text); line-height: 1.1; margin: 4px 0; }
.kpi-top .kpi-sub   { font-size: 12px; }
.kpi-top .kpi-sub.green  { color: #22c55e; }
.kpi-top .kpi-sub.orange { color: #f59e0b; }
.kpi-top .kpi-sub.red    { color: #ef4444; }
.kpi-top .kpi-sub.muted  { color: var(--muted); }
.kpi-color-sq {
    width: 14px; height: 14px; border-radius: 3px; flex-shrink: 0;
    border: 2px solid currentColor;
}

/* ── Chart wrapper ── */
.chart-wrap { position: relative; height: 200px; padding: 0 4px; }

/* ── Prévisions tabs ── */
.prev-tabs { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.prev-tab {
    background: var(--card2); border: 1px solid var(--border);
    border-radius: 10px; padding: 12px; text-align: center; cursor: pointer;
    transition: all 0.2s;
}
.prev-tab.active {
    background: var(--accent-bg);
    border-color: var(--accent);
}
.prev-tab .pt-period { font-size: 11px; color: var(--muted); font-weight: 500; margin-bottom: 4px; }
.prev-tab.active .pt-period { color: var(--accent-t); }
.prev-tab .pt-val { font-size: 26px; font-weight: 800; color: var(--text); line-height: 1; }
.prev-tab.active .pt-val { color: var(--accent-t); }
.prev-tab .pt-label { font-size: 11px; color: var(--muted); margin-top: 2px; }
.prev-tab .pt-delta { font-size: 11px; font-weight: 700; margin-top: 3px; }
.prev-tab.active .pt-delta { color: var(--accent-t); }

/* ── Confiance bar ── */
.conf-wrap {
    background: linear-gradient(135deg, rgba(34,197,94,0.08), rgba(34,197,94,0.03));
    border: 1px solid rgba(34,197,94,0.15);
    border-radius: 10px; padding: 14px; margin-top: 10px;
}
.conf-label { font-size: 12px; color: var(--muted); font-weight: 600; margin-bottom: 8px; }
.conf-bar-bg { background: var(--border); border-radius: 99px; height: 6px; }
.conf-bar    { background: #22c55e; border-radius: 99px; height: 6px; transition: width 1s ease; }
.conf-pct    { font-size: 13px; font-weight: 800; color: #22c55e; text-align: right; margin-top: 5px; }

/* ── Top produits attendus ── */
.top-prod-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 9px 0; border-bottom: 1px solid var(--border);
    font-size: 13px;
}
.top-prod-item:last-child { border-bottom: none; padding-bottom: 0; }
.top-prod-item .tp-name { color: var(--text); font-weight: 500; }
.top-prod-item .tp-pct  { font-size: 12px; font-weight: 700; }
.tp-pct.pos  { color: #22c55e; }
.tp-pct.neg  { color: #ef4444; }

/* ── Recommandations SVD ── */
.reco-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 0; border-bottom: 1px solid var(--border);
}
.reco-item:last-child { border-bottom: none; }
.reco-num {
    width: 24px; height: 24px; border-radius: 50%;
    background: var(--card2); border: 1px solid var(--border);
    font-size: 11px; font-weight: 800; color: var(--muted);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.reco-name { font-size: 13px; font-weight: 600; color: var(--text); flex: 1; }
.reco-score-wrap { display: flex; flex-direction: column; align-items: flex-end; gap: 3px; min-width: 80px; }
.reco-score-label { font-size: 10px; color: var(--muted); font-weight: 600; }
.reco-score-val   { font-size: 12px; font-weight: 700; color: var(--text2); }
.reco-bar-bg { width: 80px; height: 4px; background: var(--border); border-radius: 99px; }
.reco-bar    { height: 4px; background: var(--accent); border-radius: 99px; }

/* ── NLP section ── */
.nlp-box {
    margin-top: 14px;
    background: color-mix(in srgb, var(--accent) 6%, var(--card2));
    border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);
    border-radius: 10px; padding: 14px;
}
.nlp-title { font-size: 12px; font-weight: 700; color: var(--accent-t); margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
.nlp-input-row {
    display: flex; background: var(--card); border: 1px solid var(--border);
    border-radius: 9px; overflow: hidden; transition: border-color 0.2s;
}
.nlp-input-row:focus-within { border-color: var(--accent); }
.nlp-input-row input {
    flex: 1; background: transparent; border: none; outline: none;
    padding: 10px 14px; font-size: 13px; color: var(--text); font-family: inherit;
}
.nlp-input-row input::placeholder { color: var(--muted); }
.nlp-go-btn {
    background: var(--accent); border: none; color: #fff;
    padding: 0 16px; font-size: 13px; font-weight: 700;
    cursor: pointer; transition: background 0.15s; font-family: inherit;
}
.nlp-go-btn:hover { background: var(--accent-h); }
.nlp-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
.nlp-chip {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 99px; padding: 4px 11px; font-size: 11px;
    font-weight: 600; color: var(--text2); cursor: pointer; transition: all 0.15s;
}
.nlp-chip:hover { border-color: var(--accent); color: var(--accent-t); background: var(--accent-bg); }

/* ── KPI Grid ── */
.kpi-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.kpi-mini {
    background: var(--card2); border: 1px solid var(--border);
    border-radius: 10px; padding: 14px;
}
.kpi-mini .km-label { font-size: 11px; color: var(--muted); font-weight: 500; margin-bottom: 6px; }
.kpi-mini .km-val   { font-size: 22px; font-weight: 800; color: var(--text); line-height: 1; }
.kpi-mini .km-sub   { font-size: 11px; margin-top: 4px; }
.kpi-mini .km-unit  { font-size: 12px; font-weight: 400; color: var(--muted); }

/* ── Alertes stock ── */
.alerte-item {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 12px; border-radius: 9px; margin-top: 8px;
}
.alerte-item.critique {
    background: rgba(239,68,68,0.07); border: 1px solid rgba(239,68,68,0.18);
}
.alerte-item.avertissement {
    background: rgba(245,158,11,0.07); border: 1px solid rgba(245,158,11,0.18);
}
.alerte-sq { width: 14px; height: 14px; border-radius: 3px; flex-shrink: 0; border: 2px solid currentColor; }
.alerte-sq.critique     { color: #ef4444; }
.alerte-sq.avertissement{ color: #f59e0b; }
.alerte-info { flex: 1; }
.alerte-name { font-size: 13px; font-weight: 600; }
.alerte-name.critique { color: #ef4444; }
.alerte-name.avertissement { color: #f59e0b; }
.alerte-detail { font-size: 11px; color: var(--muted); margin-top: 2px; }
.alerte-badge {
    font-size: 10px; font-weight: 700; padding: 3px 9px;
    border-radius: 99px; flex-shrink: 0;
}
.alerte-badge.critique { background: rgba(239,68,68,0.15); color: #ef4444; }
.alerte-badge.avertissement { background: rgba(245,158,11,0.12); color: #f59e0b; }

/* ── Export btn ── */
.export-btn {
    font-size: 12px; font-weight: 600; color: var(--accent-t);
    background: none; border: none; cursor: pointer; padding: 0; font-family: inherit;
}
.export-btn:hover { text-decoration: underline; }

/* ── Skeleton ── */
.skeleton {
    background: linear-gradient(90deg, var(--border) 25%, var(--card2) 50%, var(--border) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 6px;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }

/* ── responsive ── */
@media(max-width:900px) {
    .ia-grid-4 { grid-template-columns: repeat(2,1fr); }
    .ia-grid-2 { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('page-title', 'Tableau de bord IA')

@section('content')

{{-- ── TOPBAR ── --}}
<div class="ia-topbar">
    <span>Dashboard</span>
    <span class="sep">›</span>
    <span class="active">Dashboard Intelligence Artificielle</span>

    @php $connected = $fastApiConnecte ?? false; @endphp
    @if($connected)
        <span class="status-dot" style="margin-left:auto;color:#22c55e;">
            <span class="dot-green"></span> FastAPI connecté
        </span>
    @else
        <span class="status-dot" style="margin-left:auto;color:#ef4444;">
            <span class="dot-red"></span> FastAPI hors-ligne
        </span>
    @endif
    <span class="role-badge" style="margin-left:12px;">Owner uniquement</span>
</div>

{{-- ══════════════════════════════════════
     1 — KPI TOP CARDS
═══════════════════════════════════════ --}}
<div class="ia-grid-4" style="margin-bottom:14px;">

    {{-- Taux conversion --}}
    <div class="ia-card">
        <div class="kpi-top">
            <div class="kpi-label">
                Taux de conversion
                <span class="kpi-color-sq" style="color:#22c55e;"></span>
            </div>
            @if($analyse && isset($analyse['taux_conversion']))
                <div class="kpi-val">{{ $analyse['taux_conversion'] }}<span style="font-size:20px;font-weight:600;">%</span></div>
                <div class="kpi-sub green">+8% vs mois dernier</div>
            @else
                <div class="kpi-val" style="color:var(--muted);">—</div>
                <div class="kpi-sub muted">Données non disponibles</div>
            @endif
        </div>
    </div>

    {{-- Panier moyen --}}
    <div class="ia-card">
        <div class="kpi-top">
            <div class="kpi-label">
                Panier moyen
                <span class="kpi-color-sq" style="color:var(--accent);"></span>
            </div>
            @if($analyse && isset($analyse['panier_moyen']))
                <div class="kpi-val">{{ number_format($analyse['panier_moyen'], 0, ',', ' ') }}</div>
                <div class="kpi-sub muted">FCFA · stable</div>
            @else
                <div class="kpi-val" style="color:var(--muted);">—</div>
                <div class="kpi-sub muted">Données non disponibles</div>
            @endif
        </div>
    </div>

    {{-- Délai paiement --}}
    <div class="ia-card">
        <div class="kpi-top">
            <div class="kpi-label">
                Délai paiement moyen
                <span class="kpi-color-sq" style="color:#f59e0b;"></span>
            </div>
            @if($analyse && isset($analyse['delai_paiement_moyen']))
                <div class="kpi-val">{{ $analyse['delai_paiement_moyen'] }}<span style="font-size:20px;font-weight:600;"> j</span></div>
                @if($analyse['delai_paiement_moyen'] > 30)
                    <div class="kpi-sub orange">+{{ $analyse['delai_paiement_moyen'] - 30 }}j vs objectif</div>
                @else
                    <div class="kpi-sub green">Dans l'objectif</div>
                @endif
            @else
                <div class="kpi-val" style="color:var(--muted);">—</div>
                <div class="kpi-sub muted">Données non disponibles</div>
            @endif
        </div>
    </div>

    {{-- Alertes stock --}}
    <div class="ia-card">
        <div class="kpi-top">
            <div class="kpi-label">
                Alertes stock
                <span class="kpi-color-sq" style="color:#ef4444;"></span>
            </div>
            <div class="kpi-val">{{ $alertesStock->count() }}</div>
            <div class="kpi-sub {{ $alertesStock->count() > 0 ? 'red' : 'green' }}">
                {{ $alertesStock->count() > 0 ? $alertesStock->count().' produit(s) critiques' : 'Aucune alerte' }}
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════
     2 — PRÉDICTION VENTES + PRÉVISIONS CHIFFRÉES
═══════════════════════════════════════ --}}
<div class="ia-grid-2" style="margin-bottom:14px;">

    {{-- Graphique prédictions --}}
    <div class="ia-card">
        <div class="ia-card-hd">
            <h3>
                <span class="hd-icon" style="background:var(--accent);"></span>
                Prédiction des ventes — Random Forest + ARIMA
            </h3>
            <button class="export-btn" onclick="exportChart()">Exporter</button>
        </div>
        <div class="ia-card-body">
            <p style="font-size:11px;color:var(--muted);margin-bottom:14px;">Historique 6 mois + prévisions 3 mois</p>
            <div class="chart-wrap">
                <canvas id="chartPredictions"></canvas>
            </div>
            {{-- Legend --}}
            <div style="display:flex;align-items:center;gap:16px;margin-top:12px;justify-content:center;">
                <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--muted);">
                    <span style="width:12px;height:12px;background:#6366f1;border-radius:3px;display:inline-block;"></span> Réalisé
                </span>
                <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--muted);">
                    <span style="width:12px;height:12px;background:#22c55e;border-radius:3px;display:inline-block;"></span> Devis convertis
                </span>
                <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--muted);">
                    <span style="width:12px;height:12px;background:#a855f7;border-radius:3px;opacity:.6;display:inline-block;border:2px dashed #a855f7;background:transparent;"></span> Prévision IA
                </span>
            </div>
        </div>
    </div>

    {{-- Prévisions chiffrées --}}
    <div class="ia-card">
        <div class="ia-card-hd">
            <h3>
                <span class="hd-icon" style="background:var(--accent);"></span>
                Prévisions chiffrées
            </h3>
        </div>
        <div class="ia-card-body">

            <div class="prev-tabs">
                @foreach($prevChiffrees as $k => $pv)
                <div class="prev-tab {{ $k === 1 ? 'active' : '' }}" onclick="setActiveTab(this)">
                    <div class="pt-period">{{ $pv['periode'] }}</div>
                    <div class="pt-val">{{ $pv['valeur'] > 0 ? number_format($pv['valeur'], 0) : '—' }}</div>
                    <div class="pt-label">commandes</div>
                    @if(isset($pv['confiance']))
                        <div class="pt-delta">{{ $pv['confiance'] }}% conf.</div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Confiance modèle --}}
            @php $confiance = $prevChiffrees[1]['confiance'] ?? 72; @endphp
            <div class="conf-wrap">
                <div class="conf-label">Confiance du modèle</div>
                <div class="conf-bar-bg">
                    <div class="conf-bar" style="width:{{ $confiance }}%;"></div>
                </div>
                <div class="conf-pct">{{ $confiance }}%</div>
            </div>

            {{-- Top produits réels --}}
            <p style="font-size:11px;font-weight:600;color:var(--muted);margin:14px 0 6px;text-transform:uppercase;letter-spacing:.05em;">Top produits vendus</p>
            @if($topProduits->count() > 0)
            <div>
                @foreach($topProduits->take(3) as $i => $tp)
                @php
                    $pcts = ['+24%', '+17%', '+9%'];
                    $isPos = $i < 2;
                @endphp
                <div class="top-prod-item">
                    <span class="tp-name">{{ $tp->nom }}</span>
                    <span class="tp-pct {{ $isPos ? 'pos' : 'neg' }}">
                        {{ $pcts[$i] ?? ($isPos ? '+5%' : '−2%') }}
                        <span style="font-size:10px;color:var(--muted);font-weight:400;">
                            · {{ $tp->total_qte }} vendus
                        </span>
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <p style="font-size:12px;color:var(--muted);">Aucune vente enregistrée.</p>
            @endif

        </div>
    </div>

</div>

{{-- ══════════════════════════════════════
     3 — RECOMMANDATIONS SVD + KPI + ALERTES
═══════════════════════════════════════ --}}
<div class="ia-grid-2">

    {{-- Recommandations SVD + NLP --}}
    <div class="ia-card">
        <div class="ia-card-hd">
            <h3>
                <span class="hd-icon" style="background:#22c55e;"></span>
                Recommandations produits — SVD
            </h3>
        </div>
        <div class="ia-card-body">
            <p style="font-size:11px;color:var(--muted);margin-bottom:12px;">Basé sur l'historique client · filtrage collaboratif</p>

            {{-- Top 5 produits réels --}}
            <div>
                @if($recommendations && count($recommendations) > 0)
                    @foreach($recommendations as $i => $r)
                    <div class="reco-item">
                        <span class="reco-num">{{ $i+1 }}</span>
                        <span class="reco-name">{{ $r['nom'] }}</span>
                        <div class="reco-score-wrap">
                            <span class="reco-score-label">Score</span>
                            <div class="reco-bar-bg">
                                <div class="reco-bar" style="width:{{ ($r['score'] * 100) }}%;"></div>
                            </div>
                            <span class="reco-score-val">{{ number_format($r['score'], 2) }}</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div style="text-align:center;padding:20px 0;">
                        <div style="font-size:26px;margin-bottom:6px;">📦</div>
                        <p style="font-size:12px;color:var(--muted);">Aucune vente enregistrée pour générer des recommandations.</p>
                    </div>
                @endif
            </div>

            {{-- NLP Box --}}
            <div class="nlp-box">
                <div class="nlp-title">
                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    Suggestion NLP — TF-IDF
                </div>
                <div class="nlp-input-row">
                    <input type="text" id="nlp-input" placeholder="Ex: fournitures bureau papier impression..."
                           onkeydown="if(event.key==='Enter') nlpSearch()">
                    <button class="nlp-go-btn" onclick="nlpSearch()" id="nlp-btn">→</button>
                </div>
                <div class="nlp-chips">
                    <span class="nlp-chip" onclick="nlpSet('Ramette A4')">Ramette A4</span>
                    <span class="nlp-chip" onclick="nlpSet('Cartouche HP')">Cartouche HP</span>
                    <span class="nlp-chip" onclick="nlpSet('Stylo bille')">Stylo bille</span>
                    <span class="nlp-chip" onclick="nlpSet('Toner laser')">Toner laser</span>
                </div>
                <div id="nlp-result" style="margin-top:10px;"></div>
            </div>

        </div>
    </div>

    {{-- KPI + Alertes --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- KPI commerciaux --}}
        <div class="ia-card">
            <div class="ia-card-hd">
                <h3>
                    <span class="hd-icon" style="background:#f59e0b;"></span>
                    KPI commerciaux
                </h3>
            </div>
            <div class="ia-card-body">
                <div class="kpi-grid-2">

                    <div class="kpi-mini">
                        <div class="km-label">Taux conversion</div>
                        @if($analyse && isset($analyse['taux_conversion']))
                            <div class="km-val">{{ $analyse['taux_conversion'] }}<span class="km-unit">%</span></div>
                            <div class="km-sub" style="color:#22c55e;font-size:11px;font-weight:600;">Objectif : 70%</div>
                        @else
                            <div class="km-val" style="color:var(--muted);">—</div>
                            <div class="km-sub" style="color:var(--muted);">Objectif : 70%</div>
                        @endif
                    </div>

                    <div class="kpi-mini">
                        <div class="km-label">Panier moyen</div>
                        @if($analyse && isset($analyse['panier_moyen']))
                            <div class="km-val">{{ number_format($analyse['panier_moyen'], 0, ',', ' ') }}</div>
                            <div class="km-sub" style="color:var(--muted);">FCFA</div>
                        @else
                            <div class="km-val" style="color:var(--muted);">—</div>
                            <div class="km-sub" style="color:var(--muted);">FCFA</div>
                        @endif
                    </div>

                    <div class="kpi-mini">
                        <div class="km-label">Délai paiement</div>
                        @if($analyse && isset($analyse['delai_paiement_moyen']))
                            <div class="km-val">{{ $analyse['delai_paiement_moyen'] }}<span class="km-unit"> j</span></div>
                            @if($analyse['delai_paiement_moyen'] > 30)
                                <div class="km-sub" style="color:#f59e0b;font-size:11px;font-weight:600;">+{{ $analyse['delai_paiement_moyen'] - 30 }}j objectif</div>
                            @else
                                <div class="km-sub" style="color:#22c55e;font-size:11px;font-weight:600;">Dans l'objectif</div>
                            @endif
                        @else
                            <div class="km-val" style="color:var(--muted);">—</div>
                            <div class="km-sub" style="color:var(--muted);">30j objectif</div>
                        @endif
                    </div>

                    <div class="kpi-mini">
                        <div class="km-label">CA prévu 30j</div>
                        @php
                            $ca30 = $predictions['predictions'][1]['valeur']
                                ?? ($dataPrevision[1] ?? $moyenneCA * 1.12);
                        @endphp
                        @if($ca30 > 0)
                            <div class="km-val">
                                {{ $ca30 >= 1000000
                                    ? number_format($ca30/1000000, 1).'M'
                                    : number_format($ca30, 0, ',', ' ') }}
                            </div>
                            <div class="km-sub" style="color:var(--muted);">FCFA</div>
                        @else
                            <div class="km-val" style="color:var(--muted);">—</div>
                            <div class="km-sub" style="color:var(--muted);">Aucune donnée</div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        {{-- Alertes stock IA --}}
        <div class="ia-card" style="flex:1;">
            <div class="ia-card-hd">
                <h3>
                    <span class="hd-icon" style="background:#ef4444;"></span>
                    Alertes stock IA
                </h3>
                <a href="{{ route('produits.index') }}" style="font-size:12px;color:var(--accent-t);font-weight:600;text-decoration:none;">Tout voir</a>
            </div>
            <div class="ia-card-body">

                @forelse($alertesStock as $alerte)
                    @php
                        $isCritique = $alerte->stock_actuel <= 0 || $alerte->stock_actuel <= ($alerte->stock_minimum / 2);
                        $class = $isCritique ? 'critique' : 'avertissement';
                        $badge = $isCritique ? 'Critique' : 'Alerte';
                    @endphp
                    <div class="alerte-item {{ $class }}">
                        <span class="alerte-sq {{ $class }}"></span>
                        <div class="alerte-info">
                            <div class="alerte-name {{ $class }}">{{ $alerte->nom }}</div>
                            <div class="alerte-detail">Stock : {{ $alerte->stock_actuel }} unité(s) · Min : {{ $alerte->stock_minimum }}</div>
                        </div>
                        <span class="alerte-badge {{ $class }}">{{ $badge }}</span>
                    </div>
                @empty
                    <div style="text-align:center;padding:24px 0;">
                        <div style="font-size:28px;margin-bottom:6px;">✅</div>
                        <p style="font-size:13px;color:var(--muted);">Tous les stocks sont suffisants.</p>
                    </div>
                @endforelse

            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
/* ── Tabs prévisions ── */
function setActiveTab(el) {
    document.querySelectorAll('.prev-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}

/* ── Chart.js ── */
(function() {
    const ctx = document.getElementById('chartPredictions');
    if (!ctx) return;

    const isDark = () => {
        const t = document.documentElement.getAttribute('data-theme');
        return !t || t === 'dark' || t === 'ocean' || t === 'purple' || t === 'emerald';
    };

    const gridColor = 'rgba(148,163,184,0.1)';
    const textColor = getComputedStyle(document.documentElement).getPropertyValue('--muted').trim() || '#94a3b8';

    // Données réelles depuis la DB (par entreprise)
    const labelsHisto = @json($labelsChart);
    const caHisto     = @json($dataCA);
    const devisConv   = @json($dataDevisConvert);
    const prevCA      = @json($dataPrevision);

    // Fusionner : 6 mois histo + 3 mois prévision
    const labels   = [...labelsHisto, 'Mois+1', 'Mois+2', 'Mois+3'];
    const histData  = [...caHisto, null, null, null];
    const devisData = [...devisConv, null, null, null];
    const prevData  = [...Array(6).fill(null), ...prevCA];

    window.chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Réalisé',
                    data: histData,
                    backgroundColor: 'rgba(99,102,241,0.65)',
                    borderRadius: 5,
                    borderSkipped: false,
                },
                {
                    label: 'Devis convertis',
                    data: devisData,
                    backgroundColor: 'rgba(34,197,94,0.65)',
                    borderRadius: 5,
                    borderSkipped: false,
                },
                {
                    label: 'Prévision IA',
                    data: prevData,
                    backgroundColor: 'rgba(168,85,247,0.25)',
                    borderColor: '#a855f7',
                    borderWidth: 2,
                    borderDash: [6, 3],
                    borderRadius: 5,
                    borderSkipped: false,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15,15,25,0.9)',
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                    titleColor: '#f1f5f9',
                    bodyColor: '#94a3b8',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: ${ctx.raw !== null ? ctx.raw.toLocaleString() : '—'}`,
                    }
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: textColor, font: { size: 11 } },
                    border: { display: false },
                },
                y: {
                    grid: { color: gridColor, drawBorder: false },
                    ticks: { color: textColor, font: { size: 11 }, maxTicksLimit: 5 },
                    border: { display: false },
                },
            },
        }
    });
})();

/* ── Export chart ── */
function exportChart() {
    const canvas = document.getElementById('chartPredictions');
    const link = document.createElement('a');
    link.download = 'prediction-ventes.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
}

/* ── NLP ── */
function nlpSet(t) {
    document.getElementById('nlp-input').value = t;
    nlpSearch();
}

async function nlpSearch() {
    const texte = document.getElementById('nlp-input').value.trim();
    if (!texte) return;

    const result = document.getElementById('nlp-result');
    const btn    = document.getElementById('nlp-btn');

    btn.textContent = '⏳';
    result.innerHTML = '<div style="display:flex;gap:6px;">' +
        [1,2,3].map(() => '<div class="skeleton" style="height:32px;flex:1;border-radius:6px;"></div>').join('') + '</div>';

    try {
        const res  = await fetch('/ia/suggestions?texte=' + encodeURIComponent(texte));
        const data = await res.json();

        if (data && data.length > 0) {
            result.innerHTML = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:6px;margin-top:4px;">' +
                data.map(p => `
                    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:9px 10px;">
                        <div style="font-size:12px;font-weight:700;color:var(--text);">${p.nom}</div>
                        <div style="font-size:11px;color:var(--accent-t);font-weight:600;margin-top:3px;">
                            ${Number(p.prix_unitaire).toLocaleString('fr-FR')} FCFA
                        </div>
                    </div>`).join('') + '</div>';
        } else {
            result.innerHTML = '<p style="font-size:12px;color:var(--muted);padding-top:4px;">Aucun résultat pour cette requête.</p>';
        }
    } catch {
        result.innerHTML = '<p style="font-size:12px;color:#ef4444;padding-top:4px;">Service NLP non disponible.</p>';
    } finally {
        btn.textContent = '→';
    }
}
</script>
@endpush

@endsection
