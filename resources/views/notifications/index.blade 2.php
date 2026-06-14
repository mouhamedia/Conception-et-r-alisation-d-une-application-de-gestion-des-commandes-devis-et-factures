@extends('layouts.app')
@section('title','Notifications')
@section('page-title','Notifications')
@section('topbar-actions')
@if($notifications->isNotEmpty())
<form method="POST" action="{{ route('notifications.tout-lire') }}">
    @csrf @method('PATCH')
    <button type="submit" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--card);border:1px solid var(--border);border-radius:9px;color:var(--muted);font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;transition:all .15s;" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Tout marquer comme lu
    </button>
</form>
@endif
@endsection
@section('content')
<style>
.notif-grid{display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:start;}
@media(max-width:900px){.notif-grid{grid-template-columns:1fr;}}
.notif-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:16px 20px;display:flex;align-items:flex-start;gap:14px;transition:background .15s;}
.notif-card:hover{background:var(--card2);}
.notif-card.unread{border-left:3px solid var(--accent);}
.notif-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.stat-mini{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;}
.sm-row{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px;}
.sm-row:last-child{border-bottom:none;padding-bottom:0;}
</style>

@php
$nonLues = $notifications->where('lu',false)->count();
$types = ['devis_envoye'=>0,'commande_creee'=>0,'facture_generee'=>0,'stock_faible'=>0];
foreach($notifications as $n) { if(isset($types[$n->type])) $types[$n->type]++; }
@endphp

<div class="notif-grid">
    {{-- Liste --}}
    <div style="display:flex;flex-direction:column;gap:10px;">
        @forelse($notifications as $notif)
        @php
        $icons = [
            'devis_envoye'    => ['bg'=>'var(--accent-bg)','stroke'=>'var(--accent-t)','path'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            'commande_creee'  => ['bg'=>'rgba(59,130,246,.14)','stroke'=>'#60a5fa','path'=>'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
            'facture_generee' => ['bg'=>'rgba(34,197,94,.14)','stroke'=>'#4ade80','path'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
            'stock_faible'    => ['bg'=>'rgba(239,68,68,.14)','stroke'=>'#f87171','path'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ];
        $ic = $icons[$notif->type] ?? ['bg'=>'rgba(245,158,11,.14)','stroke'=>'#fbbf24','path'=>'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'];
        @endphp
        <div class="notif-card {{ !$notif->lu ? 'unread' : '' }}">
            <div class="notif-icon" style="background:{{ $ic['bg'] }};">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="{{ $ic['stroke'] }}" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ic['path'] }}"/></svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                    <div>
                        <div style="font-size:14px;font-weight:{{ !$notif->lu ? '700' : '600' }};color:var(--text);">{{ $notif->titre }}</div>
                        <div style="font-size:13px;color:var(--muted);margin-top:3px;line-height:1.5;">{{ $notif->message }}</div>
                    </div>
                    <div style="flex-shrink:0;text-align:right;">
                        <div style="font-size:11px;color:var(--muted2,rgba(148,163,184,.3));">{{ $notif->created_at->diffForHumans() }}</div>
                        @if(!$notif->lu)
                        <form method="POST" action="{{ route('notifications.lire',$notif) }}" style="margin-top:5px;">
                            @csrf @method('PATCH')
                            <button type="submit" style="font-size:11px;color:var(--accent-t);background:none;border:none;cursor:pointer;font-family:inherit;font-weight:600;padding:0;">Marquer lu</button>
                        </form>
                        @else
                        <div style="margin-top:5px;display:flex;align-items:center;gap:3px;font-size:11px;color:var(--muted2,rgba(148,163,184,.3));">
                            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Lu
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:60px 20px;text-align:center;">
            <div style="width:56px;height:56px;background:var(--card2);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px;">Aucune notification</div>
            <div style="font-size:13px;color:var(--muted);">Vous êtes à jour ! Les notifications apparaissent ici automatiquement.</div>
        </div>
        @endforelse

        @if($notifications->hasPages())
        <div>{{ $notifications->links() }}</div>
        @endif
    </div>

    {{-- Panneau stats --}}
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div class="stat-mini">
            <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:14px;">Résumé</div>
            <div class="sm-row">
                <span style="color:var(--muted);">Non lues</span>
                <span style="font-weight:700;color:{{ $nonLues > 0 ? '#f87171' : 'var(--text)' }};">{{ $nonLues }}</span>
            </div>
            <div class="sm-row">
                <span style="color:var(--muted);">Total</span>
                <span style="font-weight:600;color:var(--text2);">{{ $notifications->total() }}</span>
            </div>
            <div class="sm-row">
                <span style="color:var(--muted);">Devis envoyés</span>
                <span style="font-weight:600;color:var(--accent-t);">{{ $types['devis_envoye'] }}</span>
            </div>
            <div class="sm-row">
                <span style="color:var(--muted);">Commandes</span>
                <span style="font-weight:600;color:var(--accent-t);">{{ $types['commande_creee'] }}</span>
            </div>
            <div class="sm-row">
                <span style="color:var(--muted);">Factures</span>
                <span style="font-weight:600;color:#4ade80;">{{ $types['facture_generee'] }}</span>
            </div>
            <div class="sm-row">
                <span style="color:var(--muted);">Alertes stock</span>
                <span style="font-weight:600;color:#f87171;">{{ $types['stock_faible'] }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
