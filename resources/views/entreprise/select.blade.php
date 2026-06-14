<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisir un espace — GestiPro</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; }
    body {
        font-family: system-ui, -apple-system, 'Segoe UI', sans-serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 32px 20px;
        background: #F1F5F9;
        -webkit-font-smoothing: antialiased;
    }
    body::before {
        content: '';
        position: fixed; inset: 0;
        background:
            radial-gradient(ellipse 900px 700px at 15% 15%, rgba(30,58,138,.07) 0%, transparent 60%),
            radial-gradient(ellipse 700px 600px at 85% 85%, rgba(37,99,235,.05) 0%, transparent 60%);
        pointer-events: none;
    }

    /* ─── LOGO ─── */
    .logo { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; text-decoration: none; position: relative; z-index: 1; }
    .logo-mark { width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg,#1E3A8A,#2563EB); display: flex; align-items: center; justify-content: center; font-size: 17px; font-weight: 900; color: #fff; box-shadow: 0 6px 18px rgba(30,58,138,.3); }
    .logo-name { font-size: 20px; font-weight: 900; color: #111; letter-spacing: -.4px; }
    .logo-badge { font-size: 10px; font-weight: 700; color: #2563EB; background: #eff6ff; padding: 2px 7px; border-radius: 5px; margin-left: 6px; }

    /* ─── CARD PRINCIPALE ─── */
    .card {
        width: 100%; max-width: 840px;
        background: #fff;
        border-radius: 24px;
        border: 1px solid rgba(30,58,138,.09);
        box-shadow: 0 24px 64px rgba(30,58,138,.1), 0 4px 16px rgba(0,0,0,.04);
        overflow: hidden;
        position: relative; z-index: 1;
    }

    /* ─── HEADER CARD ─── */
    .card-header {
        padding: 22px 32px;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .card-header-title { font-size: 15px; font-weight: 700; color: #111; }
    .card-header-sub { font-size: 12px; color: #94a3b8; margin-top: 1px; }
    .card-header-user { display: flex; align-items: center; gap: 8px; }
    .user-av { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg,#1E3A8A,#2563EB); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #fff; }
    .user-name { font-size: 12px; font-weight: 600; color: #374151; }
    .user-email { font-size: 11px; color: #9ca3af; }
    .logout-btn { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #9ca3af; text-decoration: none; padding: 5px 10px; border-radius: 7px; border: 1px solid #e5e7eb; background: none; cursor: pointer; font-family: inherit; transition: all .15s; }
    .logout-btn:hover { background: #fef2f2; border-color: #fecaca; color: #dc2626; }

    /* ─── SPLIT BODY ─── */
    .split { display: grid; grid-template-columns: 1fr 1fr; }

    /* ─── PANEL GAUCHE ─── */
    .panel-left { padding: 28px 28px 28px 32px; border-right: 1px solid #f1f5f9; }
    .panel-right { padding: 28px 32px 28px 28px; background: #fafbfc; }

    .panel-label {
        font-size: 10px; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: .08em; margin-bottom: 14px;
    }

    /* ─── LISTE ENTREPRISES ─── */
    .co-list { display: flex; flex-direction: column; gap: 7px; margin-bottom: 18px; }
    .co-item {
        display: flex; align-items: center; gap: 11px;
        padding: 11px 13px;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        cursor: pointer;
        transition: border-color .15s, background .15s;
        user-select: none;
    }
    .co-item:hover   { border-color: #bfdbfe; background: #f0f6ff; }
    .co-item.active  { border-color: #2563EB; background: #eff6ff; }
    .co-av { width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg,#1E3A8A,#2563EB); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0; }
    .co-info { flex: 1; min-width: 0; }
    .co-name { font-size: 13px; font-weight: 600; color: #111; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .co-meta { font-size: 11px; color: #9ca3af; margin-top: 1px; }
    .co-check { flex-shrink: 0; opacity: 0; transition: opacity .15s; }
    .co-item.active .co-check { opacity: 1; }

    /* Bouton continuer */
    .btn-continue {
        width: 100%; padding: 11px;
        background: linear-gradient(135deg,#1E3A8A,#2563EB);
        color: #fff; font-size: 13px; font-weight: 700;
        border: none; border-radius: 11px; cursor: pointer;
        font-family: inherit; transition: opacity .15s, transform .15s;
        box-shadow: 0 4px 14px rgba(37,99,235,.3);
        display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .btn-continue:hover { opacity: .9; transform: translateY(-1px); }
    .btn-continue:disabled { opacity: .4; cursor: not-allowed; transform: none; }

    .empty-state { text-align: center; padding: 24px 16px; }
    .empty-icon { width: 44px; height: 44px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; }
    .empty-title { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 4px; }
    .empty-sub { font-size: 12px; color: #9ca3af; }

    /* ─── PANEL DROIT ─── */
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .field { margin-bottom: 12px; }
    .field label { display: block; font-size: 11px; font-weight: 600; color: #4b5563; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 5px; }
    .field input {
        width: 100%; padding: 9px 12px;
        border: 1.5px solid #e2e8f0; border-radius: 9px;
        font-size: 13px; font-family: inherit; color: #111;
        background: #fff; outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .field input:focus { border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    .field input::placeholder { color: #cbd5e1; }

    .btn-create {
        width: 100%; padding: 11px;
        background: #059669;
        color: #fff; font-size: 13px; font-weight: 700;
        border: none; border-radius: 11px; cursor: pointer;
        font-family: inherit; transition: background .15s, transform .15s;
        box-shadow: 0 4px 14px rgba(5,150,105,.3);
        display: flex; align-items: center; justify-content: center; gap: 6px;
        margin-top: 4px;
    }
    .btn-create:hover { background: #047857; transform: translateY(-1px); }
    .btn-create:disabled { opacity: .6; cursor: not-allowed; transform: none; }

    /* ─── TOAST ─── */
    .toast {
        position: fixed; bottom: 28px; right: 28px;
        background: #111; color: #fff; border-radius: 12px;
        padding: 12px 18px; font-size: 13px; font-weight: 500;
        display: flex; align-items: center; gap: 9px;
        box-shadow: 0 8px 28px rgba(0,0,0,.2);
        opacity: 0; transform: translateY(10px);
        transition: opacity .3s, transform .3s;
        z-index: 999;
        pointer-events: none;
    }
    .toast.show { opacity: 1; transform: translateY(0); }
    .toast.success { background: #064e3b; }
    .toast.error { background: #7f1d1d; }

    /* ─── ERREURS ─── */
    .field-error { font-size: 11px; color: #dc2626; margin-top: 4px; }
    .form-error { background: #fef2f2; border: 1px solid #fecaca; border-radius: 9px; padding: 10px 12px; font-size: 12px; color: #991b1b; margin-bottom: 12px; }

    /* ─── FOOTER ─── */
    .page-footer { font-size: 11px; color: #94a3b8; text-align: center; margin-top: 18px; position: relative; z-index: 1; }

    @media (max-width: 680px) {
        .split { grid-template-columns: 1fr; }
        .panel-left { border-right: none; border-bottom: 1px solid #f1f5f9; }
        .card { max-width: 480px; }
        .form-row { grid-template-columns: 1fr; }
    }
    </style>
</head>
<body>

{{-- Logo --}}
<a href="{{ route('home') }}" class="logo">
    <div class="logo-mark">G</div>
    <div>
        <span class="logo-name">GestiPro</span>
        <span class="logo-badge">B2B</span>
    </div>
</a>

{{-- Card principale --}}
<div class="card">

    {{-- Header --}}
    <div class="card-header">
        <div>
            <div class="card-header-title">Choisir un espace de travail</div>
            <div class="card-header-sub">Sélectionnez ou créez une entreprise</div>
        </div>
        <div class="card-header-user" style="display:flex;align-items:center;gap:12px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <div class="user-av">{{ strtoupper(substr(auth()->user()->prenom ?? auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->prenom }} {{ auth()->user()->name }}</div>
                    <div class="user-email">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </div>

    {{-- Split --}}
    <div class="split">

        {{-- ── GAUCHE : sélection ── --}}
        <div class="panel-left">
            <div class="panel-label">Vos entreprises</div>

            <div class="co-list" id="coList">
                @forelse($entreprises as $e)
                <div class="co-item {{ $loop->first ? 'active' : '' }}"
                     data-id="{{ $e->id }}"
                     onclick="selectCo(this)">
                    <div class="co-av">{{ strtoupper(substr($e->nom,0,2)) }}</div>
                    <div class="co-info">
                        <div class="co-name">{{ $e->nom }}</div>
                        <div class="co-meta">
                            {{ $e->pivot->role === 'owner' ? 'Propriétaire' : 'Employé' }}
                            @if($e->ville) · {{ $e->ville }} @endif
                        </div>
                    </div>
                    <svg class="co-check" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#2563EB" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                @empty
                <div class="empty-state" id="emptyState">
                    <div class="empty-icon">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="empty-title">Aucune entreprise</div>
                    <div class="empty-sub">Créez votre premier espace à droite</div>
                </div>
                @endforelse
            </div>

            <input type="hidden" id="selectedId" value="{{ $entreprises->first()?->id ?? '' }}">

            <form method="POST" action="{{ route('entreprise.switch') }}" id="switchForm">
                @csrf
                <input type="hidden" name="entreprise_id" id="switchInput" value="{{ $entreprises->first()?->id ?? '' }}">
                <button type="submit" class="btn-continue" id="btnContinue" {{ $entreprises->isEmpty() ? 'disabled' : '' }}>
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    Continuer
                </button>
            </form>
        </div>

        {{-- ── DROITE : création ── --}}
        <div class="panel-right">
            <div class="panel-label">Nouvelle entreprise</div>

            <div id="formError" class="form-error" style="display:none;"></div>

            <form id="createForm" autocomplete="off" enctype="multipart/form-data">
                @csrf

                {{-- Logo upload --}}
                <div class="field" style="margin-bottom:16px;">
                    <label>Logo de l'entreprise</label>
                    <div id="logoZone" onclick="document.getElementById('fLogo').click()"
                         style="border:2px dashed #e2e8f0;border-radius:11px;padding:16px;text-align:center;cursor:pointer;transition:border-color .15s,background .15s;position:relative;">
                        <div id="logoPlaceholder">
                            <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="1.5" style="margin:0 auto 6px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <div style="font-size:12px;color:#64748b;font-weight:500;">Cliquez pour ajouter un logo</div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px;">PNG, JPG, SVG · max 2 Mo</div>
                        </div>
                        <img id="logoPreview" src="" alt="" style="display:none;max-height:64px;max-width:180px;margin:0 auto;border-radius:6px;object-fit:contain;">
                    </div>
                    <input type="file" name="logo" id="fLogo" accept="image/*" style="display:none;" onchange="previewLogo(this)">
                </div>

                <div class="field">
                    <label>Nom de l'entreprise *</label>
                    <input type="text" name="nom" id="fNom" placeholder="Tech Sénégal SARL" required autofocus>
                </div>
                <div class="form-row">
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" id="fEmail" placeholder="contact@entreprise.sn">
                    </div>
                    <div class="field">
                        <label>Téléphone</label>
                        <input type="tel" name="telephone" id="fTel" placeholder="+221 77 000 00 00">
                    </div>
                </div>
                <div class="field">
                    <label>Adresse</label>
                    <input type="text" name="adresse" id="fAdr" placeholder="Rue Carnot, Plateau">
                </div>
                <div class="form-row">
                    <div class="field">
                        <label>Ville</label>
                        <input type="text" name="ville" id="fVille" value="Dakar" placeholder="Dakar">
                    </div>
                    <div class="field">
                        <label>NINEA / SIRET</label>
                        <input type="text" name="siret" id="fSiret" placeholder="SN-2025-000000">
                    </div>
                </div>

                <button type="submit" class="btn-create" id="btnCreate">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Créer l'entreprise
                </button>
            </form>
        </div>

    </div>
</div>

{{-- Toast --}}
<div class="toast" id="toast"></div>

<p class="page-footer">© {{ date('Y') }} GestiPro · Application B2B · Sénégal</p>

<script>
const CSRF  = document.querySelector('meta[name="csrf-token"]').content;
const STORE = '{{ route('entreprise.store') }}';

/* ── Sélection d'une entreprise ── */
function selectCo(el) {
    document.querySelectorAll('.co-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
    const id = el.dataset.id;
    document.getElementById('selectedId').value  = id;
    document.getElementById('switchInput').value = id;
    document.getElementById('btnContinue').disabled = false;
}

/* ── Soumission AJAX création (FormData pour supporter les fichiers) ── */
document.getElementById('createForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnCreate');
    btn.disabled = true;
    btn.innerHTML = '<svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="animation:spin .7s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Création...';

    const formData = new FormData(this);

    document.getElementById('formError').style.display = 'none';

    try {
        const res = await fetch(STORE, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });

        const data = await res.json();

        if (!res.ok) {
            const msgs = data.errors
                ? Object.values(data.errors).flat().join(' · ')
                : (data.message || 'Erreur lors de la création.');
            document.getElementById('formError').textContent = msgs;
            document.getElementById('formError').style.display = 'block';
            return;
        }

        /* Succès : ajouter la carte dans la liste */
        const ent = data.entreprise;

        /* Retirer l'état vide si présent */
        const empty = document.getElementById('emptyState');
        if (empty) empty.remove();

        /* Désélectionner les autres */
        document.querySelectorAll('.co-item').forEach(i => i.classList.remove('active'));

        /* Créer la nouvelle carte */
        const initials = ent.nom.substring(0, 2).toUpperCase();
        const card = document.createElement('div');
        card.className = 'co-item active';
        card.dataset.id = ent.id;
        card.onclick = function() { selectCo(this); };
        const avatarHtml = ent.logo_url
            ? `<img src="${ent.logo_url}" alt="" style="width:34px;height:34px;border-radius:9px;object-fit:contain;border:1px solid #e5e7eb;padding:2px;background:#fff;">`
            : `<div class="co-av">${initials}</div>`;
        card.innerHTML = `
            ${avatarHtml}
            <div class="co-info">
                <div class="co-name">${ent.nom}</div>
                <div class="co-meta">Propriétaire${ent.ville ? ' · ' + ent.ville : ''}</div>
            </div>
            <svg class="co-check" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#2563EB" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        `;
        document.getElementById('coList').appendChild(card);

        /* Mettre à jour les champs hidden */
        document.getElementById('selectedId').value  = ent.id;
        document.getElementById('switchInput').value = ent.id;
        document.getElementById('btnContinue').disabled = false;

        /* Vider le formulaire */
        this.reset();
        document.getElementById('fVille').value = 'Dakar';

        showToast('Entreprise créée avec succès !', 'success');

    } catch (err) {
        showToast('Erreur réseau. Veuillez réessayer.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Créer l\'entreprise';
    }
});

/* ── Toast ── */
function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.className = 'toast ' + type;
    t.innerHTML = (type === 'success'
        ? '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'
        : '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>')
        + msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
}

/* Animation spinner */
const style = document.createElement('style');
style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
document.head.appendChild(style);

/* ── Preview logo ── */
function previewLogo(input) {
    const zone = document.getElementById('logoZone');
    const ph   = document.getElementById('logoPlaceholder');
    const img  = document.getElementById('logoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.style.display = 'block';
            ph.style.display  = 'none';
            zone.style.borderColor = '#2563EB';
            zone.style.background  = '#eff6ff';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/* Hover sur la zone logo */
const logoZone = document.getElementById('logoZone');
logoZone.addEventListener('mouseenter', () => { if (!document.getElementById('logoPreview').style.display || document.getElementById('logoPreview').style.display === 'none') logoZone.style.borderColor = '#94a3b8'; });
logoZone.addEventListener('mouseleave', () => { if (!document.getElementById('logoPreview').style.display || document.getElementById('logoPreview').style.display === 'none') logoZone.style.borderColor = '#e2e8f0'; });
</script>
</body>
</html>
