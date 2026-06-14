@extends('layouts.app')
@section('title','Factures')
@section('page-title','Factures')
@section('content')
@php
$total      = $factures->total();
$brouillons = $factures->getCollection()->where('statut','brouillon')->count();
$envoyees   = $factures->getCollection()->where('statut','envoyee')->count();
$payees     = $factures->getCollection()->where('statut','payee')->count();
$retard     = $factures->getCollection()->where('statut','en_retard')->count();
$partielles = $factures->getCollection()->filter(fn($f) => $f->montant_paye > 0 && $f->statut !== 'payee')->count();
$montantDu  = $factures->getCollection()->whereIn('statut',['envoyee','en_retard'])->sum(fn($f)=>($f->commande?->total_ttc??0)-$f->montant_paye);
@endphp

<div class="pg-stats" style="grid-template-columns:repeat(5,1fr);">
    <div class="ps">
        <div class="ps-icon" style="background:var(--primary-bg);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div><div class="ps-val">{{ $total }}</div><div class="ps-lbl">Total</div></div>
    </div>
    <div class="ps">
        <div class="ps-icon" style="background:var(--c-green-bg);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--c-green)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="ps-val" style="color:var(--c-green);">{{ $payees }}</div><div class="ps-lbl">Payées</div></div>
    </div>
    <div class="ps">
        <div class="ps-icon" style="background:var(--primary-bg);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="ps-val" style="color:var(--primary);">{{ $envoyees }}</div><div class="ps-lbl">Envoyées</div></div>
    </div>
    <div class="ps" style="{{ $partielles > 0 ? 'border-color:var(--c-yellow-b);' : '' }}">
        <div class="ps-icon" style="background:var(--c-yellow-bg);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--c-yellow)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="ps-val" style="{{ $partielles > 0 ? 'color:var(--c-yellow);' : '' }}">{{ $partielles }}</div><div class="ps-lbl">Part. payées</div></div>
    </div>
    <div class="ps" style="{{ $retard > 0 ? 'border-color:var(--c-red-b);' : '' }}">
        <div class="ps-icon" style="background:var(--c-red-bg);">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--c-red)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <div class="ps-val" style="{{ $retard > 0 ? 'color:var(--c-red);' : '' }}">{{ $retard }}</div>
            <div class="ps-lbl">En retard</div>
            @if($montantDu > 0)<div style="font-size:10px;color:var(--c-red);font-weight:600;margin-top:1px;">{{ number_format($montantDu,0,',',' ') }} DZD dû</div>@endif
        </div>
    </div>
</div>

<div class="toolbar">
    <div class="sb-i">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="var(--muted2)" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="si" placeholder="Numéro, client…" oninput="ft()">
    </div>
    <select class="f-sel" id="fs" onchange="ft()">
        <option value="">Tous les statuts</option>
        <option value="brouillon">Brouillon</option>
        <option value="envoyee">Envoyée</option>
        <option value="partielle">Partiellement payée</option>
        <option value="payee">Payée</option>
        <option value="en_retard">En retard</option>
    </select>
    <span id="cnt" style="font-size:12px;color:var(--muted);white-space:nowrap;">{{ $total }} facture(s)</span>
</div>

<div class="tc">
    @if($factures->isEmpty())
    <div style="padding:60px 20px;text-align:center;">
        <div style="width:56px;height:56px;background:var(--card2);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px;">Aucune facture</div>
        <div style="font-size:13px;color:var(--muted);">Les factures sont générées automatiquement lors de l'acceptation d'un devis.</div>
    </div>
    @else
    <table class="tbl" id="tbl">
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Client</th>
                <th style="text-align:right;">Montant TTC</th>
                <th style="text-align:right;">Payé</th>
                <th style="text-align:center;">Statut</th>
                <th style="text-align:center;">Échéance</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($factures as $f)
        @php
            $overdue  = $f->date_echeance && $f->date_echeance->isPast() && $f->statut !== 'payee';
            $partielle = $f->montant_paye > 0 && $f->statut !== 'payee';
        @endphp
        <tr data-s="{{ strtolower($f->numero.' '.($f->commande?->client_nom??'')) }}" data-statut="{{ $f->statut }}" data-partielle="{{ $partielle ? 'partielle' : '' }}">
            <td>
                <a href="{{ route('factures.show',$f) }}" style="font-weight:700;color:var(--primary);text-decoration:none;">{{ $f->numero }}</a>
            </td>
            <td style="font-weight:600;color:var(--text);">{{ $f->commande?->client_nom ?? '—' }}</td>
            <td style="text-align:right;font-weight:700;color:var(--text);">{{ number_format($f->commande?->total_ttc??0,0,',',' ') }}<span style="font-size:11px;color:var(--muted);margin-left:3px;">DZD</span></td>
            <td style="text-align:right;color:var(--muted);">{{ number_format($f->montant_paye,0,',',' ') }}<span style="font-size:11px;margin-left:3px;">DZD</span></td>
            <td style="text-align:center;">
                @if($partielle)
                    <span class="sp sp-en_attente">Paiement partiel</span>
                @else
                    <span class="sp sp-{{ $f->statut }}">{{ ['brouillon'=>'Brouillon','envoyee'=>'Envoyée','payee'=>'Payée','en_retard'=>'En retard','annulee'=>'Annulée'][$f->statut]??$f->statut }}</span>
                @endif
            </td>
            <td style="text-align:center;font-size:12px;color:{{ $overdue ? 'var(--c-red)' : 'var(--muted)' }};font-weight:{{ $overdue ? '700' : '400' }};">
                {{ $f->date_echeance?->format('d/m/Y') ?? '—' }}
                @if($overdue)<div style="font-size:10px;color:var(--c-red);">En retard</div>@endif
            </td>
            <td style="text-align:right;">
                <a href="{{ route('factures.show',$f) }}" style="font-size:12px;font-weight:600;color:var(--primary);text-decoration:none;padding:5px 11px;background:var(--primary-bg);border-radius:7px;">Voir →</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @if($factures->hasPages())<div style="padding:14px 20px;border-top:1px solid var(--border2);">{{ $factures->links() }}</div>@endif
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
        let statMatch = !st || r.dataset.statut===st || (st==='partielle' && r.dataset.partielle==='partielle');
        const ok=(!q||r.dataset.s.includes(q))&&statMatch;
        r.style.display=ok?'':'none';
        if(ok)v++;
    });
    const c=document.getElementById('cnt');
    if(c)c.textContent=v+' facture(s)';
}
</script>
@endpush
