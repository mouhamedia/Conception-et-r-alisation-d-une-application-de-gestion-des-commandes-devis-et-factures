@extends('layouts.app')
@section('title','Commandes')
@section('page-title','Commandes')
@section('content')
@php
$total     = $commandes->total();
$enAttente = $commandes->getCollection()->where('statut','en_attente')->count();
$enCours   = $commandes->getCollection()->where('statut','en_cours')->count();
$livrees   = $commandes->getCollection()->where('statut','livree')->count();
$annulees  = $commandes->getCollection()->where('statut','annulee')->count();
@endphp

<div class="pg-stats">
    <div class="ps">
        <div class="ps-icon" style="background:var(--primary-bg);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        </div>
        <div><div class="ps-val">{{ $total }}</div><div class="ps-lbl">Total</div></div>
    </div>
    <div class="ps">
        <div class="ps-icon" style="background:var(--c-yellow-bg);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--c-yellow)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="ps-val" style="color:var(--c-yellow);">{{ $enAttente }}</div><div class="ps-lbl">En attente</div></div>
    </div>
    <div class="ps">
        <div class="ps-icon" style="background:var(--primary-bg);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div><div class="ps-val" style="color:var(--primary);">{{ $enCours }}</div><div class="ps-lbl">En cours</div></div>
    </div>
    <div class="ps">
        <div class="ps-icon" style="background:var(--c-green-bg);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--c-green)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div><div class="ps-val" style="color:var(--c-green);">{{ $livrees }}</div><div class="ps-lbl">Livrées</div></div>
    </div>
</div>

<div class="toolbar">
    <div class="sb-i">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="var(--muted2)" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="si" placeholder="Référence, client…" oninput="ft()">
    </div>
    <select class="f-sel" id="fs" onchange="ft()">
        <option value="">Tous les statuts</option>
        <option value="en_attente">En attente</option>
        <option value="en_cours">En cours</option>
        <option value="livree">Livrée</option>
        <option value="annulee">Annulée</option>
    </select>
    <span id="cnt" style="font-size:12px;color:var(--muted);white-space:nowrap;">{{ $total }} commande(s)</span>
</div>

<div class="tc">
    @if($commandes->isEmpty())
    <div style="padding:60px 20px;text-align:center;">
        <div style="width:56px;height:56px;background:var(--card2);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        </div>
        <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px;">Aucune commande</div>
        <div style="font-size:13px;color:var(--muted);">Les commandes sont générées automatiquement lors de l'acceptation d'un devis.</div>
    </div>
    @else
    <table class="tbl" id="tbl">
        <thead>
            <tr>
                <th>Référence</th>
                <th>Client</th>
                <th style="text-align:right;">Total TTC</th>
                <th style="text-align:center;">Statut</th>
                <th style="text-align:center;">Facture</th>
                <th style="text-align:center;">Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($commandes as $c)
        <tr data-s="{{ strtolower($c->reference.' '.$c->client_nom) }}" data-statut="{{ $c->statut }}">
            <td>
                <a href="{{ route('commandes.show',$c) }}" style="font-weight:700;color:var(--primary);text-decoration:none;">{{ $c->reference }}</a>
            </td>
            <td style="font-weight:600;color:var(--text);">{{ $c->client_nom }}</td>
            <td style="text-align:right;font-weight:700;color:var(--text);">{{ number_format($c->total_ttc,0,',',' ') }}<span style="font-size:11px;color:var(--muted);margin-left:3px;">DZD</span></td>
            <td style="text-align:center;">
                <span class="sp sp-{{ $c->statut }}">{{ ['en_attente'=>'En attente','en_cours'=>'En cours','livree'=>'Livrée','annulee'=>'Annulée'][$c->statut]??$c->statut }}</span>
            </td>
            <td style="text-align:center;">
                @if($c->facture)
                <a href="{{ route('factures.show',$c->facture) }}" style="font-size:12px;font-weight:600;color:var(--primary);text-decoration:none;">{{ $c->facture->numero }}</a>
                @else
                <span style="color:var(--muted2);">—</span>
                @endif
            </td>
            <td style="text-align:center;font-size:12px;color:var(--muted);">{{ $c->created_at->format('d/m/Y') }}</td>
            <td style="text-align:right;">
                <a href="{{ route('commandes.show',$c) }}" style="font-size:12px;font-weight:600;color:var(--primary);text-decoration:none;padding:5px 11px;background:var(--primary-bg);border-radius:7px;">Voir →</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @if($commandes->hasPages())<div style="padding:14px 20px;border-top:1px solid var(--border2);">{{ $commandes->links() }}</div>@endif
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
    if(c)c.textContent=v+' commande(s)';
}
</script>
@endpush
