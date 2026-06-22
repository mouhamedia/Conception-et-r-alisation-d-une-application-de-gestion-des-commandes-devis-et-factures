@extends('layouts.app')
@section('title','Demandes de devis')
@section('page-title','Demandes de devis')
@section('page-subtitle','Demandes reçues et envoyées')

@section('topbar-actions')
<a href="{{ route('marketplace.index') }}"
   style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:var(--accent);color:white;border-radius:9px;font-size:13px;font-weight:600;text-decoration:none;">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    Chercher des partenaires
</a>
@endsection

@push('styles')
<style>
.tabs{display:flex;gap:0;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:4px;margin-bottom:24px;width:fit-content;}
.tab{padding:8px 20px;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;border:none;background:none;color:var(--muted);transition:all .15s;}
.tab.on{background:var(--accent);color:white;}
.dem-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px 22px;margin-bottom:12px;display:flex;align-items:flex-start;gap:16px;transition:border-color .2s;}
.dem-card:hover{border-color:var(--accent);}
.dem-avatar{width:42px;height:42px;border-radius:10px;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:white;flex-shrink:0;}
.dem-nom{font-size:14px;font-weight:700;color:var(--text);}
.dem-desc{font-size:13px;color:var(--muted);margin-top:4px;line-height:1.5;}
.dem-meta{display:flex;align-items:center;gap:10px;margin-top:8px;flex-wrap:wrap;}
.dem-date{font-size:11px;color:var(--muted2);}
.dem-budget{font-size:12px;font-weight:600;color:var(--acc2);background:rgba(245,158,11,.12);padding:2px 9px;border-radius:20px;}
.dem-sp{display:inline-flex;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;}
.dem-actions{display:flex;gap:8px;margin-left:auto;flex-shrink:0;flex-direction:column;align-items:flex-end;}
.btn-accept{padding:7px 14px;background:#16a34a;color:white;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;}
.btn-accept:hover{background:#15803d;}
.btn-refuse{padding:7px 14px;background:#FEF2F2;color:#DC2626;border:1px solid #FECACA;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;}
.btn-refuse:hover{background:#DC2626;color:white;border-color:#DC2626;}
.btn-devis{padding:7px 14px;background:var(--accent);color:white;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.btn-devis:hover{background:var(--accent-h);}
.empty-state{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:50px 20px;text-align:center;}
</style>
@endpush

@section('content')

<div class="tabs">
    <button class="tab on" id="tabRecues" onclick="showTab('recues')">
        Reçues
        @php $nbEnAttente = $recues->getCollection()->where('statut','en_attente')->count(); @endphp
        @if($nbEnAttente > 0)
        <span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;background:#ef4444;color:white;border-radius:9px;font-size:10px;font-weight:700;padding:0 5px;margin-left:6px;">{{ $nbEnAttente }}</span>
        @endif
    </button>
    <button class="tab" id="tabEnvoyees" onclick="showTab('envoyees')">Envoyées</button>
</div>

{{-- ── Demandes reçues ── --}}
<div id="panelRecues">
@if($recues->isEmpty())
<div class="empty-state">
    <div style="width:52px;height:52px;background:var(--card2);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"/></svg>
    </div>
    <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px;">Aucune demande reçue</div>
    <div style="font-size:13px;color:var(--muted);">Les autres entreprises peuvent vous envoyer des demandes depuis le Marketplace.</div>
</div>
@else
@foreach($recues as $d)
@php
    $colors = ['en_attente'=>['#FFFBEB','#92400E'],'acceptee'=>['#ECFDF5','#065F46'],'refusee'=>['#FEF2F2','#991B1B'],'devis_cree'=>['#EDE9FE','#5B21B6']];
    $c = $colors[$d->statut] ?? ['#F8FAFC','#475569'];
@endphp
<div class="dem-card">
    <div class="dem-avatar">{{ strtoupper(substr($d->entrepriseSource->nom, 0, 2)) }}</div>
    <div style="flex:1;min-width:0;">
        <div class="dem-nom">{{ $d->entrepriseSource->nom }}</div>
        <div class="dem-desc">{{ $d->description }}</div>
        <div class="dem-meta">
            <span class="dem-sp" style="background:{{ $c[0] }};color:{{ $c[1] }};">
                {{ ['en_attente'=>'En attente','acceptee'=>'Acceptée','refusee'=>'Refusée','devis_cree'=>'Devis créé'][$d->statut]??$d->statut }}
            </span>
            @if($d->budget)<span class="dem-budget">Budget : {{ number_format($d->budget, 0, ',', ' ') }} FCFA</span>@endif
            <span class="dem-date">{{ $d->created_at->diffForHumans() }} · par {{ $d->user->nom_complet }}</span>
        </div>
    </div>
    <div class="dem-actions">
        @if($d->statut === 'en_attente')
        <form method="POST" action="{{ route('marketplace.accepter', $d) }}" style="margin:0;">
            @csrf @method('PATCH')
            <button type="submit" class="btn-accept">Accepter et créer un devis</button>
        </form>
        <form method="POST" action="{{ route('marketplace.refuser', $d) }}" style="margin:0;">
            @csrf @method('PATCH')
            <button type="submit" class="btn-refuse" onclick="return confirm('Refuser cette demande ?')">Refuser</button>
        </form>
        @elseif($d->statut === 'acceptee' && $d->devis)
        <a href="{{ route('devis.show', $d->devis) }}" class="btn-devis">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Voir le devis
        </a>
        @elseif($d->statut === 'acceptee')
        <a href="{{ route('devis.create', ['demande_id' => $d->id]) }}" class="btn-devis">
            Créer le devis →
        </a>
        @endif
    </div>
</div>
@endforeach
@if($recues->hasPages())<div style="margin-top:16px;">{{ $recues->links() }}</div>@endif
@endif
</div>

{{-- ── Demandes envoyées ── --}}
<div id="panelEnvoyees" style="display:none;">
@if($envoyees->isEmpty())
<div class="empty-state">
    <div style="width:52px;height:52px;background:var(--card2);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
    </div>
    <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px;">Aucune demande envoyée</div>
    <div style="font-size:13px;color:var(--muted);">
        <a href="{{ route('marketplace.index') }}" style="color:var(--accent-t);text-decoration:none;font-weight:600;">Rechercher des partenaires →</a>
    </div>
</div>
@else
@foreach($envoyees as $d)
@php
    $colors = ['en_attente'=>['#FFFBEB','#92400E'],'acceptee'=>['#ECFDF5','#065F46'],'refusee'=>['#FEF2F2','#991B1B'],'devis_cree'=>['#EDE9FE','#5B21B6']];
    $c = $colors[$d->statut] ?? ['#F8FAFC','#475569'];
@endphp
<div class="dem-card">
    <div class="dem-avatar">{{ strtoupper(substr($d->entrepriseCible->nom, 0, 2)) }}</div>
    <div style="flex:1;min-width:0;">
        <div class="dem-nom">→ {{ $d->entrepriseCible->nom }}</div>
        <div class="dem-desc">{{ $d->description }}</div>
        <div class="dem-meta">
            <span class="dem-sp" style="background:{{ $c[0] }};color:{{ $c[1] }};">
                {{ ['en_attente'=>'En attente','acceptee'=>'Acceptée','refusee'=>'Refusée','devis_cree'=>'Devis reçu'][$d->statut]??$d->statut }}
            </span>
            @if($d->budget)<span class="dem-budget">Budget : {{ number_format($d->budget, 0, ',', ' ') }} FCFA</span>@endif
            <span class="dem-date">{{ $d->created_at->diffForHumans() }}</span>
        </div>
    </div>
    @if($d->devis)
    <div class="dem-actions">
        <a href="{{ route('devis.client', $d->devis) }}" class="btn-devis">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Voir le devis reçu
        </a>
    </div>
    @endif
</div>
@endforeach
@if($envoyees->hasPages())<div style="margin-top:16px;">{{ $envoyees->links() }}</div>@endif
@endif
</div>

@endsection
@push('scripts')
<script>
function showTab(name){
    document.getElementById('panelRecues').style.display = name==='recues' ? '' : 'none';
    document.getElementById('panelEnvoyees').style.display = name==='envoyees' ? '' : 'none';
    document.getElementById('tabRecues').className = 'tab' + (name==='recues' ? ' on' : '');
    document.getElementById('tabEnvoyees').className = 'tab' + (name==='envoyees' ? ' on' : '');
}
</script>
@endpush
