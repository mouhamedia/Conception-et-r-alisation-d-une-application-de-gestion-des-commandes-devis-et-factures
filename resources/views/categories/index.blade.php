@extends('layouts.app')
@section('title', 'Catégories')
@section('page-title', 'Gestion des catégories')

@section('topbar-actions')
<a href="{{ route('produits.index') }}"
   style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--card);border:1px solid var(--border);border-radius:9px;color:var(--muted);font-size:13px;font-weight:500;text-decoration:none;">
    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
    Voir les produits
</a>
@endsection

@push('styles')
<style>
.cat-layout { display: grid; grid-template-columns: 1fr 360px; gap: 22px; align-items: start; }
@media(max-width: 900px) { .cat-layout { grid-template-columns: 1fr; } }

/* Liste */
.cat-list { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
.cat-list-header {
    padding: 18px 22px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.cat-list-title { font-size: 14px; font-weight: 700; color: var(--text); }
.cat-count { font-size: 12px; color: var(--muted); background: var(--card2); padding: 3px 10px; border-radius: 20px; border: 1px solid var(--border); }

.cat-item {
    display: flex; align-items: center; gap: 14px;
    padding: 14px 22px;
    border-bottom: 1px solid var(--border);
    transition: background .1s;
}
.cat-item:last-child { border-bottom: none; }
.cat-item:hover { background: var(--card2); }

.cat-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: var(--primary-bg);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.cat-name { font-size: 14px; font-weight: 600; color: var(--text); }
.cat-sub  { font-size: 11px; color: var(--muted); margin-top: 2px; }
.cat-badge {
    margin-left: auto;
    font-size: 11px; font-weight: 700;
    padding: 3px 10px; border-radius: 20px;
    background: var(--primary-bg); color: var(--primary-text);
    flex-shrink: 0;
}
.cat-actions { display: flex; gap: 6px; flex-shrink: 0; }
.cat-btn {
    width: 30px; height: 30px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    border: 1px solid var(--border); background: var(--card);
    cursor: pointer; transition: all .15s;
}
.cat-btn:hover { background: var(--card2); }
.cat-btn.danger:hover { background: #fef2f2; border-color: #fecaca; }
.cat-btn.danger:hover svg { stroke: #dc2626; }

/* Formulaire création */
.cat-form-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 16px; padding: 24px; position: sticky; top: 82px;
}
.cat-form-title {
    font-size: 14px; font-weight: 700; color: var(--text);
    margin-bottom: 4px;
    display: flex; align-items: center; gap: 10px;
}
.cat-form-sub { font-size: 12px; color: var(--muted); margin-bottom: 20px; }

/* Toast */
@keyframes fadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@section('content')

{{-- Toast container --}}
<div id="toast-container" style="position:fixed;top:16px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;min-width:260px;"></div>
<script>
function gToast(message, type) {
    const c = document.getElementById('toast-container');
    const t = document.createElement('div');
    const ok = type !== 'error';
    t.style.cssText = 'padding:12px 16px;border-radius:11px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px;pointer-events:auto;box-shadow:0 8px 24px rgba(0,0,0,.15);animation:fadeUp .2s ease;' +
        (ok ? 'background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.3);color:#059669;'
            : 'background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#dc2626;');
    t.innerHTML = `<span>${message}</span>`;
    c.appendChild(t);
    setTimeout(() => { t.style.opacity='0'; t.style.transition='opacity .3s'; setTimeout(()=>t.remove(),300); }, 4000);
}
@if(session('success')) document.addEventListener('DOMContentLoaded',()=>gToast('{{ session('success') }}','success')); @endif
@if(session('error'))   document.addEventListener('DOMContentLoaded',()=>gToast('{{ session('error') }}','error'));   @endif
</script>

<div class="cat-layout" x-data="catManager()">

    {{-- ══ LISTE ══ --}}
    <div class="cat-list">
        <div class="cat-list-header">
            <span class="cat-list-title">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:6px;"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Catégories
            </span>
            <span class="cat-count" x-text="cats.length + ' catégorie(s)'"></span>
        </div>

        {{-- Empty state --}}
        <template x-if="cats.length === 0">
            <div style="padding:48px 20px;text-align:center;">
                <div style="width:52px;height:52px;border-radius:14px;background:var(--primary-bg);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;">Aucune catégorie</div>
                <div style="font-size:12px;color:var(--muted);">Créez votre première catégorie à droite →</div>
            </div>
        </template>

        {{-- Liste des catégories --}}
        <template x-for="cat in cats" :key="cat.id">
            <div class="cat-item">
                <div class="cat-icon">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>

                {{-- Mode lecture --}}
                <div style="flex:1;min-width:0;" x-show="editId !== cat.id">
                    <div class="cat-name" x-text="cat.nom"></div>
                    <div class="cat-sub">
                        <span x-text="(cat.produits_count ?? 0) + ' produit(s)'"></span>
                    </div>
                </div>

                {{-- Mode édition inline --}}
                <div style="flex:1;min-width:0;" x-show="editId === cat.id" style="display:none;">
                    <input type="text" x-model="editNom"
                           @keydown.enter.prevent="saveEdit(cat)"
                           @keydown.escape="editId=null"
                           style="width:100%;padding:6px 10px;border:1.5px solid var(--primary);border-radius:8px;font-size:13px;font-weight:600;color:var(--text);background:var(--card);outline:none;font-family:inherit;">
                    <div style="font-size:11px;color:var(--muted);margin-top:3px;">Entrée pour valider · Échap pour annuler</div>
                </div>

                <span class="cat-badge" x-text="(cat.produits_count ?? 0) + ' produit(s)'" x-show="editId !== cat.id"></span>

                <div class="cat-actions">
                    {{-- Bouton éditer / valider --}}
                    <button class="cat-btn" x-show="editId !== cat.id" @click="startEdit(cat)" title="Renommer">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button class="cat-btn" x-show="editId === cat.id" @click="saveEdit(cat)" title="Valider" style="display:none;background:var(--primary-bg);border-color:var(--primary-border);">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    {{-- Supprimer --}}
                    <button class="cat-btn danger" @click="deletecat(cat)" title="Supprimer" x-show="editId !== cat.id">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

    {{-- ══ FORMULAIRE ══ --}}
    <div class="cat-form-card">
        <div class="cat-form-title">
            <div style="width:34px;height:34px;border-radius:10px;background:var(--primary-bg);display:flex;align-items:center;justify-content:center;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            Nouvelle catégorie
        </div>
        <div class="cat-form-sub">Organisez vos produits par familles ou types.</div>

        {{-- Champ nom --}}
        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:7px;">
                Nom de la catégorie
            </label>
            <input type="text" x-model="newNom"
                   @keydown.enter.prevent="create()"
                   placeholder="Ex : Électronique, Vêtements…"
                   style="width:100%;padding:10px 14px;background:var(--card2);border:1.5px solid var(--border);border-radius:10px;color:var(--text);font-size:13px;outline:none;font-family:inherit;transition:border-color .15s;"
                   onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
        </div>

        {{-- Message erreur --}}
        <div x-show="createErr" x-text="createErr"
             style="display:none;padding:9px 12px;background:#fef2f2;border:1px solid #fecaca;border-radius:9px;font-size:12px;color:#dc2626;margin-bottom:14px;"></div>

        {{-- Bouton créer --}}
        <button type="button" @click="create()" :disabled="creating || !newNom.trim()"
                style="width:100%;padding:11px;background:var(--primary);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .15s;"
                onmouseover="if(!this.disabled)this.style.background='var(--primary-h)'" onmouseout="this.style.background='var(--primary)'">
            <span x-show="creating" style="display:none;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
            </span>
            <span x-show="!creating">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </span>
            <span x-text="creating ? 'Création...' : 'Créer la catégorie'"></span>
        </button>

        {{-- Aperçu catégories existantes --}}
        <template x-if="cats.length > 0">
            <div style="margin-top:20px;padding-top:18px;border-top:1px solid var(--border);">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px;">
                    Déjà créées
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    <template x-for="cat in cats" :key="cat.id">
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;background:var(--primary-bg);color:var(--primary-text);border-radius:20px;font-size:11px;font-weight:600;">
                            <svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <span x-text="cat.nom"></span>
                        </span>
                    </template>
                </div>
            </div>
        </template>
    </div>

</div>
@endsection

@push('scripts')
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
<script>
function catManager() {
    return {
        cats:      {{ Js::from($categories) }},
        newNom:    '',
        creating:  false,
        createErr: '',
        editId:    null,
        editNom:   '',

        async create() {
            if (!this.newNom.trim()) return;
            this.creating = true; this.createErr = '';
            try {
                const res = await fetch('{{ route('categories.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ nom: this.newNom.trim() }),
                });
                const data = await res.json();
                if (data.success) {
                    this.cats.push({ ...data.categorie, produits_count: 0 });
                    this.newNom = '';
                    gToast(data.message, 'success');
                } else {
                    this.createErr = data.message || 'Erreur.';
                }
            } catch(e) { this.createErr = 'Erreur réseau.'; }
            finally { this.creating = false; }
        },

        startEdit(cat) {
            this.editId  = cat.id;
            this.editNom = cat.nom;
        },

        async saveEdit(cat) {
            const nom = this.editNom.trim();
            if (!nom) return;
            try {
                const res = await fetch(`/categories/${cat.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ nom }),
                });
                const data = await res.json();
                if (data.success) {
                    cat.nom = data.nom ?? nom;
                    this.editId = null;
                    gToast(data.message, 'success');
                } else {
                    gToast(data.message || 'Erreur.', 'error');
                }
            } catch(e) { gToast('Erreur réseau.', 'error'); }
        },

        async deletecat(cat) {
            const nb = cat.produits_count ?? 0;
            const msg = nb > 0
                ? `Supprimer « ${cat.nom} » ? Les ${nb} produit(s) associés perdront leur catégorie.`
                : `Supprimer « ${cat.nom} » ?`;
            if (!confirm(msg)) return;
            try {
                const res = await fetch(`/categories/${cat.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.cats = this.cats.filter(c => c.id !== cat.id);
                    gToast(data.message, 'success');
                }
            } catch(e) { gToast('Erreur réseau.', 'error'); }
        },
    };
}
</script>
@endpush
