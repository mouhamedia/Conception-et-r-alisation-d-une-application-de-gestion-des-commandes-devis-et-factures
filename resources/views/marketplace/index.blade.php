@extends('layouts.app')
@section('title','Marketplace B2B')
@section('page-title','Marketplace B2B')
@section('page-subtitle','Recherchez des partenaires et envoyez des demandes de devis')

@section('topbar-actions')
<a href="{{ route('marketplace.demandes') }}"
   style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:var(--card);border:1.5px solid var(--border);color:var(--text);border-radius:9px;font-size:13px;font-weight:600;text-decoration:none;">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Mes demandes
</a>
@endsection

@push('styles')
<style>
.mkt-search{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px 28px;margin-bottom:24px;}
.mkt-search-row{display:flex;gap:12px;}
.mkt-inp{flex:1;padding:11px 16px;background:var(--card2);border:1.5px solid var(--border);border-radius:10px;color:var(--text);font-size:14px;outline:none;font-family:inherit;}
.mkt-inp:focus{border-color:var(--accent);}
.mkt-inp::placeholder{color:var(--muted);}
.mkt-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-bottom:28px;}
.mkt-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;transition:border-color .2s,box-shadow .2s;cursor:default;}
.mkt-card:hover{border-color:var(--accent);box-shadow:0 4px 20px rgba(91,94,244,.12);}
.mkt-avatar{width:48px;height:48px;border-radius:12px;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:white;margin-bottom:14px;flex-shrink:0;}
.mkt-nom{font-size:15px;font-weight:700;color:var(--text);margin-bottom:3px;}
.mkt-meta{font-size:12px;color:var(--muted);margin-bottom:12px;}
.mkt-tag{display:inline-flex;align-items:center;padding:2px 9px;background:var(--accent-bg);color:var(--accent-t);border-radius:20px;font-size:11px;font-weight:600;margin-bottom:12px;}
.btn-send{width:100%;padding:9px 0;background:var(--accent);color:white;border:none;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;}
.btn-send:hover{background:var(--accent-h);}
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;align-items:center;justify-content:center;}
.modal-bg.open{display:flex;}
.modal{background:var(--card);border:1px solid var(--border);border-radius:18px;padding:28px 32px;width:480px;max-width:95vw;box-shadow:var(--shadow);}
.modal-title{font-size:17px;font-weight:800;color:var(--text);margin-bottom:6px;}
.modal-sub{font-size:13px;color:var(--muted);margin-bottom:20px;}
.form-row{margin-bottom:16px;}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;}
.form-ctrl{width:100%;padding:10px 14px;background:var(--card2);border:1.5px solid var(--border);border-radius:10px;color:var(--text);font-size:13px;outline:none;font-family:inherit;resize:vertical;}
.form-ctrl:focus{border-color:var(--accent);}
.modal-actions{display:flex;gap:10px;margin-top:20px;}
.btn-cancel{flex:1;padding:10px;background:var(--card2);border:1.5px solid var(--border);border-radius:9px;color:var(--muted);font-size:13px;font-weight:600;cursor:pointer;}
.btn-submit{flex:2;padding:10px;background:var(--accent);color:white;border:none;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;}
.btn-submit:hover{background:var(--accent-h);}
.hist-title{font-size:14px;font-weight:700;color:var(--text);margin-bottom:14px;}
.hist-item{display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border2);}
.hist-item:last-child{border-bottom:none;}
.hist-sp{display:inline-flex;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;}
</style>
@endpush

@section('content')

{{-- Barre de recherche --}}
<div class="mkt-search">
    <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:16px;">
        Rechercher une entreprise partenaire
    </div>
    <form method="GET" action="{{ route('marketplace.index') }}" class="mkt-search-row">
        <input type="text" name="q" value="{{ $query }}" class="mkt-inp" placeholder="Nom de l'entreprise, ville, email…">
        <button type="submit" class="btn-send" style="width:auto;padding:11px 24px;">Rechercher</button>
        @if($query)
        <a href="{{ route('marketplace.index') }}" style="padding:11px 16px;background:var(--card2);border:1.5px solid var(--border);border-radius:10px;color:var(--muted);text-decoration:none;font-size:13px;font-weight:600;white-space:nowrap;">Effacer</a>
        @endif
    </form>
</div>

<div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start;">

    {{-- Résultats --}}
    <div>
        @if($entreprises->isEmpty())
        <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:60px 20px;text-align:center;">
            <div style="width:52px;height:52px;background:var(--card2);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px;">
                {{ $query ? 'Aucun résultat pour « '.$query.' »' : 'Aucune autre entreprise trouvée' }}
            </div>
            <div style="font-size:13px;color:var(--muted);">Essayez un autre terme de recherche.</div>
        </div>
        @else
        <div style="font-size:12px;color:var(--muted);margin-bottom:12px;">
            {{ $entreprises->total() }} entreprise(s) trouvée(s)
            @if($query) pour « <strong style="color:var(--text);">{{ $query }}</strong> »@endif
        </div>
        <div class="mkt-grid">
            @foreach($entreprises as $ent)
            <div class="mkt-card">
                <div class="mkt-avatar">{{ strtoupper(substr($ent->nom, 0, 2)) }}</div>
                <div class="mkt-nom">{{ $ent->nom }}</div>
                <div class="mkt-meta">
                    @if($ent->ville)📍 {{ $ent->ville }}@endif
                    @if($ent->email) · {{ $ent->email }}@endif
                </div>
                @if($ent->devise)<span class="mkt-tag">{{ $ent->devise }}</span>@endif
                <button class="btn-send" onclick="openModal({{ $ent->id }}, '{{ addslashes($ent->nom) }}')">
                    Envoyer une demande de devis
                </button>
            </div>
            @endforeach
        </div>
        {{ $entreprises->links() }}
        @endif
    </div>

    {{-- Historique demandes récentes --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;">
        <div class="hist-title">Mes demandes récentes</div>
        @if($mesDemandes->isEmpty())
        <div style="font-size:13px;color:var(--muted);text-align:center;padding:20px 0;">
            Aucune demande envoyée encore.
        </div>
        @else
        @foreach($mesDemandes as $d)
        <div class="hist-item">
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $d->entrepriseCible->nom }}</div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $d->created_at->diffForHumans() }}</div>
            </div>
            @php $colors = ['en_attente'=>['#FFFBEB','#92400E'],'acceptee'=>['#ECFDF5','#065F46'],'refusee'=>['#FEF2F2','#991B1B'],'devis_cree'=>['#EDE9FE','#5B21B6']]; $c=$colors[$d->statut]??['#F8FAFC','#475569']; @endphp
            <span class="hist-sp" style="background:{{ $c[0] }};color:{{ $c[1] }};">
                {{ ['en_attente'=>'En attente','acceptee'=>'Acceptée','refusee'=>'Refusée','devis_cree'=>'Devis créé'][$d->statut]??$d->statut }}
            </span>
        </div>
        @endforeach
        <a href="{{ route('marketplace.demandes') }}" style="display:block;text-align:center;font-size:12px;color:var(--accent-t);text-decoration:none;margin-top:14px;font-weight:600;">
            Voir toutes mes demandes →
        </a>
        @endif
    </div>
</div>

{{-- Modal envoi demande --}}
<div class="modal-bg" id="modalBg" onclick="if(event.target===this)closeModal()">
    <div class="modal">
        <div class="modal-title" id="modalTitle">Demande de devis</div>
        <div class="modal-sub" id="modalSub">Décrivez votre besoin et envoyez votre demande.</div>
        <form method="POST" action="{{ route('marketplace.store') }}" id="demandeForm">
            @csrf
            <input type="hidden" name="entreprise_cible_id" id="modalEntrepriseId">
            <div class="form-row">
                <label class="form-label">Description de votre besoin *</label>
                <textarea name="description" class="form-ctrl" rows="4" placeholder="Décrivez les produits ou services souhaités, les quantités, délais…" required minlength="10"></textarea>
            </div>
            <div class="form-row">
                <label class="form-label">Budget estimé (optionnel)</label>
                <input type="number" name="budget" class="form-ctrl" placeholder="Ex : 500000" min="0" step="0.01">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn-submit">Envoyer la demande</button>
            </div>
        </form>
    </div>
</div>

@endsection
@push('scripts')
<script>
function openModal(id, nom){
    document.getElementById('modalEntrepriseId').value = id;
    document.getElementById('modalTitle').textContent = 'Demande à ' + nom;
    document.getElementById('modalSub').textContent = 'Décrivez votre besoin à ' + nom + '.';
    document.getElementById('modalBg').classList.add('open');
    document.querySelector('#demandeForm textarea').focus();
}
function closeModal(){
    document.getElementById('modalBg').classList.remove('open');
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeModal(); });
</script>
@endpush
