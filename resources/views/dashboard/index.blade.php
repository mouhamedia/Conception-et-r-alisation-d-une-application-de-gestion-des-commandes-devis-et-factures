@extends('layouts.app')

@section('title','Dashboard')
@section('page-title','Dashboard')

@section('topbar-actions')
<a href="{{ route('devis.create') }}" class="btn btn-primary">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Nouveau devis
</a>
@endsection

@section('content')
@php
$prenom = auth()->user()->prenom ?? explode(' ',auth()->user()->name)[0];
$ca    = $stats['chiffre_affaires_mois'] ?? 0;
$caPrev= $stats['chiffre_affaires_mois_precedent'] ?? 0;
$caEvol= $caPrev>0 ? round((($ca-$caPrev)/$caPrev)*100) : 0;
$labels    = collect($chartData)->pluck('label')->toJson();
$dataFact  = collect($chartData)->pluck('factures')->toJson();
$dataDevis = collect($chartData)->pluck('devis')->toJson();
@endphp

<style>
.g4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px;}
.g2l{display:grid;grid-template-columns:2fr 1.1fr;gap:14px;margin-bottom:18px;}
.g2r{display:grid;grid-template-columns:1.5fr 1fr;gap:14px;}
@media(max-width:1100px){.g4{grid-template-columns:repeat(2,1fr);}.g2l,.g2r{grid-template-columns:1fr;}}
</style>

{{-- Greeting --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:22px;font-weight:800;color:var(--text);line-height:1.2;">Bonjour, {{ $prenom }} 👋</h1>
        <p style="color:var(--muted);font-size:13px;margin-top:3px;text-transform:capitalize;">{{ now()->isoFormat('dddd D MMMM YYYY') }}</p>
    </div>
    @if($stats['devis_expirent_bientot'] > 0)
    <div style="display:flex;align-items:center;gap:8px;padding:9px 15px;background:var(--c-yellow-bg);border:1px solid var(--c-yellow-b);border-radius:10px;">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="var(--c-yellow)" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span style="font-size:12px;color:var(--c-yellow-t);font-weight:600;">{{ $stats['devis_expirent_bientot'] }} devis expirent dans 7 jours</span>
    </div>
    @endif
</div>

{{-- KPI cards --}}
<div class="g4">
    <div class="kpi" style="border-color:var(--primary-border);">
        <div class="kpi-head">
            <div class="kpi-lbl">CA ce mois</div>
            <div class="kpi-icon" style="background:var(--primary-bg);">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="kpi-val">{{ number_format($ca,0,',',' ') }}<span style="font-size:13px;font-weight:600;color:var(--muted);margin-left:4px;">DZD</span></div>
        @if($caEvol != 0)
        <span class="bp {{ $caEvol > 0 ? 'bp-g' : 'bp-r' }}">
            <svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $caEvol > 0 ? 'M7 17l9.2-9.2M17 17V7H7' : 'M17 7l-9.2 9.2M7 7v10h10' }}"/></svg>
            {{ abs($caEvol) }}% vs mois préc.
        </span>
        @else
        <span class="bp bp-b">Pas de données comparatives</span>
        @endif
    </div>

    <div class="kpi">
        <div class="kpi-head">
            <div class="kpi-lbl">Commandes actives</div>
            <div class="kpi-icon" style="background:var(--c-sky-bg);">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="var(--c-sky)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
        </div>
        <div class="kpi-val" style="color:var(--c-sky);">{{ $stats['commandes_en_cours'] }}</div>
        <span class="bp bp-b">En attente / En cours</span>
    </div>

    <div class="kpi">
        <div class="kpi-head">
            <div class="kpi-lbl">Devis émis</div>
            <div class="kpi-icon" style="background:var(--c-yellow-bg);">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="var(--c-yellow)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
        <div class="kpi-val" style="color:var(--c-yellow);">{{ $stats['devis_en_attente'] }}</div>
        <span class="bp bp-a">{{ $stats['devis_total'] }} au total</span>
    </div>

    <div class="kpi" style="border-color:var(--c-red-b);">
        <div class="kpi-head">
            <div class="kpi-lbl">Factures impayées</div>
            <div class="kpi-icon" style="background:var(--c-red-bg);">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="var(--c-red)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
        <div class="kpi-val" style="color:var(--c-red);">{{ $stats['factures_impayees'] }}</div>
        <div style="font-size:12px;color:var(--c-red);font-weight:600;margin-top:2px;">{{ number_format($stats['factures_montant_du'],0,',',' ') }} DZD dû</div>
    </div>
</div>

{{-- Chart + Actions rapides --}}
<div class="g2l">
    <div class="ch-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
            <div>
                <div style="font-size:14px;font-weight:700;color:var(--text);">Évolution des 6 derniers mois</div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">Factures encaissées & Devis convertis</div>
            </div>
            <div style="display:flex;gap:12px;">
                <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--muted);">
                    <div style="width:10px;height:10px;border-radius:3px;background:var(--primary);flex-shrink:0;"></div> Factures
                </div>
                <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--muted);">
                    <div style="width:10px;height:10px;border-radius:3px;background:var(--primary-bg);border:1px solid var(--primary);flex-shrink:0;"></div> Devis
                </div>
            </div>
        </div>
        <div style="position:relative;height:200px;"><canvas id="chartEvol"></canvas></div>
    </div>

    <div class="ch-card" style="display:flex;flex-direction:column;gap:8px;">
        <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;">Actions rapides</div>
        <a href="{{ route('devis.create') }}" class="qb">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Créer un devis
        </a>
        <a href="{{ route('produits.create') }}" class="qb">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Ajouter un produit
        </a>
        <a href="{{ route('entreprise.equipe') }}" class="qb">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Inviter un membre
        </a>
        <a href="{{ route('factures.index') }}" class="qb">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Relances en retard
        </a>
        <a href="{{ route('ia.dashboard') }}" class="qb">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H4a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2h-1"/></svg>
            Prédictions IA
        </a>
    </div>
</div>

{{-- Table commandes + IA --}}
<div class="g2r">
    <div class="tc">
        <div class="tc-head">
            <span class="tc-ht">Commandes récentes</span>
            <a href="{{ route('commandes.index') }}" class="tc-hl">Voir tout →</a>
        </div>
        @if($commandes_recentes->isEmpty())
        <div style="padding:32px 20px;text-align:center;color:var(--muted);font-size:13px;">Aucune commande pour le moment</div>
        @else
        <table class="tbl">
            <thead><tr><th>Référence</th><th>Client</th><th>Montant</th><th>Statut</th></tr></thead>
            <tbody>
            @foreach($commandes_recentes as $cmd)
            <tr>
                <td><a href="{{ route('commandes.show',$cmd) }}" style="color:var(--primary);font-weight:600;text-decoration:none;">{{ $cmd->reference }}</a></td>
                <td style="color:var(--muted);">{{ $cmd->client_nom }}</td>
                <td style="font-weight:600;color:var(--text);">{{ number_format($cmd->total_ttc,0,',',' ') }}<span style="color:var(--muted);font-size:11px;margin-left:3px;">DZD</span></td>
                <td>
                    <span class="sp sp-{{ $cmd->statut }}">{{ match($cmd->statut){'en_attente'=>'En attente','en_cours'=>'En cours','livree'=>'Livrée','annulee'=>'Annulée',default=>ucfirst($cmd->statut)} }}</span>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- IA Card --}}
    <div style="background:var(--card);border:1px solid var(--primary-border);border-radius:14px;padding:20px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:36px;height:36px;border-radius:10px;background:var(--primary-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H4a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2h-1"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:700;color:var(--text);">Suggestion IA</div>
                <span class="badge badge-info" style="margin-top:3px;font-size:9px;padding:2px 7px;letter-spacing:.05em;">FastAPI</span>
            </div>
        </div>

        @if($suggestion_ia && isset($suggestion_ia['predictions']) && count($suggestion_ia['predictions'])>0)
            @foreach(array_slice($suggestion_ia['predictions'],0,3) as $pred)
            <div style="padding:11px;background:var(--card2);border:1px solid var(--border);border-radius:10px;margin-bottom:8px;">
                @if(isset($pred['produit']))<div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:3px;">{{ $pred['produit'] }}</div>@endif
                @if(isset($pred['message']))<div style="font-size:12px;color:var(--muted);">{{ $pred['message'] }}</div>@endif
                @if(isset($pred['confiance']))
                <div style="margin-top:6px;">
                    <div style="height:3px;background:var(--border);border-radius:2px;overflow:hidden;">
                        <div style="height:100%;width:{{ round($pred['confiance']*100) }}%;background:var(--primary);border-radius:2px;"></div>
                    </div>
                    <div style="font-size:10px;color:var(--muted);margin-top:3px;">Confiance : {{ round($pred['confiance']*100) }}%</div>
                </div>
                @endif
            </div>
            @endforeach
            <a href="{{ route('ia.dashboard') }}" style="display:block;text-align:center;padding:9px;background:var(--primary-bg);border:1px solid var(--primary-border);border-radius:9px;color:var(--primary);font-size:12px;font-weight:600;text-decoration:none;margin-top:4px;">
                Voir toutes les prédictions →
            </a>
        @else
            <div style="text-align:center;padding:16px 0;">
                <div style="width:40px;height:40px;border-radius:10px;background:var(--card2);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div style="font-size:13px;color:var(--muted);line-height:1.5;">Service IA hors ligne</div>
                <div style="font-size:11px;color:var(--muted2);margin-top:4px;">Démarrez FastAPI sur le port 8001</div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const labels = {!! $labels !!};
    const dataFact = {!! $dataFact !!};
    const dataDevis = {!! $dataDevis !!};

    function getCss(v){ return getComputedStyle(document.documentElement).getPropertyValue(v).trim(); }

    let chart;
    function buildChart(){
        const ctx = document.getElementById('chartEvol');
        if(!ctx) return;
        if(chart) chart.destroy();
        const primary = getCss('--primary') || '#2563EB';
        const primaryBg = getCss('--primary-bg') || '#EFF6FF';
        const border = getCss('--border') || '#E2E8F0';
        const muted = getCss('--muted') || '#64748B';
        chart = new Chart(ctx,{
            type:'bar',
            data:{
                labels,
                datasets:[
                    {label:'Factures encaissées',data:dataFact,backgroundColor:primary,borderRadius:6,borderSkipped:false,barPercentage:0.55,categoryPercentage:0.7},
                    {label:'Devis convertis',data:dataDevis,backgroundColor:primaryBg,borderRadius:6,borderSkipped:false,barPercentage:0.55,categoryPercentage:0.7,borderWidth:1.5,borderColor:primary}
                ]
            },
            options:{
                responsive:true,maintainAspectRatio:false,
                interaction:{mode:'index',intersect:false},
                plugins:{
                    legend:{display:false},
                    tooltip:{
                        backgroundColor:'rgba(15,23,42,.9)',
                        borderColor:'rgba(0,0,0,.08)',borderWidth:1,
                        titleColor:'#f1f5f9',bodyColor:'rgba(148,163,184,.9)',padding:10,
                        callbacks:{label:c=>' '+c.dataset.label+': '+new Intl.NumberFormat('fr').format(c.raw)+' DZD'}
                    }
                },
                scales:{
                    x:{grid:{color:border,drawBorder:false},ticks:{color:muted,font:{size:11}}},
                    y:{grid:{color:border,drawBorder:false},ticks:{color:muted,font:{size:11},callback:v=>new Intl.NumberFormat('fr').format(v)},beginAtZero:true}
                }
            }
        });
    }

    buildChart();
})();
</script>
@endpush
