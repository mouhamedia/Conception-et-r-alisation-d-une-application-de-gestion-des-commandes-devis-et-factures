@extends('layouts.app')
@section('title','Devis')
@section('page-title','Devis')
@section('topbar-actions')
<a href="{{ route('devis.create') }}" class="btn btn-primary">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Nouveau devis
</a>
@endsection
@section('content')
@php
$total      = $devis->total();
$brouillons = $devis->getCollection()->where('statut','brouillon')->count();
$envoyes    = $devis->getCollection()->where('statut','envoye')->count();
$acceptes   = $devis->getCollection()->where('statut','accepte')->count();
$refuses    = $devis->getCollection()->where('statut','refuse')->count();
@endphp

<div class="pg-stats">
    <div class="ps">
        <div class="ps-icon" style="background:var(--primary-bg);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div><div class="ps-val">{{ $total }}</div><div class="ps-lbl">Total</div></div>
    </div>
    <div class="ps">
        <div class="ps-icon" style="background:var(--c-yellow-bg);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--c-yellow)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="ps-val" style="color:var(--c-yellow);">{{ $brouillons + $envoyes }}</div><div class="ps-lbl">En attente</div></div>
    </div>
    <div class="ps">
        <div class="ps-icon" style="background:var(--c-green-bg);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--c-green)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="ps-val" style="color:var(--c-green);">{{ $acceptes }}</div><div class="ps-lbl">Acceptés</div></div>
    </div>
    <div class="ps">
        <div class="ps-icon" style="background:var(--c-red-bg);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--c-red)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="ps-val" style="color:var(--c-red);">{{ $refuses }}</div><div class="ps-lbl">Refusés</div></div>
    </div>
</div>

<div class="toolbar">
    <div class="sb-i">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="var(--muted2)" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="si" placeholder="Numéro, client, email…" oninput="ft()">
    </div>
    <select class="f-sel" id="fs" onchange="ft()">
        <option value="">Tous les statuts</option>
        <option value="brouillon">Brouillon</option>
        <option value="envoye">Envoyé</option>
        <option value="accepte">Accepté</option>
        <option value="refuse">Refusé</option>
    </select>
    <span id="cnt" style="font-size:12px;color:var(--muted);white-space:nowrap;">{{ $total }} devis</span>
</div>

<div class="tc">
    @if($devis->isEmpty())
    <div style="padding:60px 20px;text-align:center;">
        <div style="width:56px;height:56px;background:var(--card2);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px;">Aucun devis</div>
        <div style="font-size:13px;color:var(--muted);margin-bottom:20px;">Créez votre premier devis pour commencer à vendre.</div>
        <a href="{{ route('devis.create') }}" class="btn btn-primary" style="margin:0 auto;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Créer un devis
        </a>
    </div>
    @else
    <table class="tbl" id="tbl">
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Client</th>
                <th>Créé par</th>
                <th style="text-align:right;">Total TTC</th>
                <th style="text-align:center;">Statut</th>
                <th style="text-align:center;">Expiration</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($devis as $d)
        @php $expired = $d->date_expiration && $d->date_expiration->isPast() && $d->statut !== 'accepte'; @endphp
        <tr data-s="{{ strtolower($d->numero.' '.$d->client_nom.' '.($d->client_email??'')) }}"
            data-statut="{{ $d->statut }}">
            <td>
                <a href="{{ route('devis.show',$d) }}" style="font-weight:700;color:var(--primary);text-decoration:none;">{{ $d->numero }}</a>
            </td>
            <td>
                <div style="font-weight:600;color:var(--text);">{{ $d->client_nom }}</div>
                @if($d->client_email)<div style="font-size:11px;color:var(--muted);margin-top:1px;">{{ $d->client_email }}</div>@endif
            </td>
            <td style="color:var(--muted);font-size:12px;">{{ $d->user?->prenom }} {{ $d->user?->name }}</td>
            <td style="text-align:right;font-weight:700;color:var(--text);">{{ number_format($d->total_ttc,0,',',' ') }}<span style="font-size:11px;color:var(--muted);margin-left:3px;">FCFA</span></td>
            <td style="text-align:center;">
                <span class="sp sp-{{ $d->statut }}">{{ ['brouillon'=>'Brouillon','envoye'=>'Envoyé','accepte'=>'Accepté','refuse'=>'Refusé'][$d->statut]??$d->statut }}</span>
            </td>
            <td style="text-align:center;font-size:12px;color:{{ $expired ? 'var(--c-red)' : 'var(--muted)' }};font-weight:{{ $expired ? '700' : '400' }};">
                {{ $d->date_expiration?->format('d/m/Y') ?? '—' }}
            </td>
            <td style="text-align:right;">
                <a href="{{ route('devis.show',$d) }}" style="font-size:12px;font-weight:600;color:var(--primary);text-decoration:none;padding:5px 11px;background:var(--primary-bg);border-radius:7px;">Voir →</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @if($devis->hasPages())<div style="padding:14px 20px;border-top:1px solid var(--border2);">{{ $devis->links() }}</div>@endif
    @endif
</div>
@endsection
@push('scripts')
<script>
function ft(){
    const q=document.getElementById('si').value.toLowerCase();
    const st=document.getElementById('fs').value;
    const rows=document.querySelectorAll('#tbl tbody tr');
    let v=0;
    rows.forEach(r=>{
        const ok=(!q||r.dataset.s.includes(q))&&(!st||r.dataset.statut===st);
        r.style.display=ok?'':'none';
        if(ok)v++;
    });
    const c=document.getElementById('cnt');
    if(c)c.textContent=v+' devis';
}
</script>
@endpush
