@extends('layouts.app')
@section('title', $facture->numero)
@section('page-title', 'Facture ' . $facture->numero)

@section('topbar-actions')

@if($facture->statut === 'brouillon')
{{-- Bouton Envoyer → modal AJAX --}}
<div x-data="{
    open: false,
    email: '{{ addslashes($facture->commande?->client_email ?? '') }}',
    sending: false,
    err: '',
    async send() {
        this.sending = true; this.err = '';
        try {
            const res = await fetch('{{ route('factures.envoyer', $facture) }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ client_email: this.email }),
            });
            const data = await res.json();
            if (data.success) {
                this.open = false;
                gToast(data.message, 'success');
                document.getElementById('statutPill').textContent = 'Envoyée';
                document.getElementById('statutPill').style.cssText += ';background:rgba(14,165,233,.12);color:#38bdf8;';
                document.getElementById('topbarEnvoyer')?.remove();
            } else {
                this.err = data.message || 'Erreur inconnue.';
            }
        } catch(e) { this.err = 'Erreur réseau, réessayez.'; }
        finally { this.sending = false; }
    }
}" id="topbarEnvoyer">
    <button @click="open = true"
            style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:#059669;color:#fff;border:none;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
        </svg>
        Envoyer au client
    </button>

    {{-- Overlay --}}
    <div x-show="open" x-transition.opacity
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px;"
         @click.self="open=false">
        <div style="background:#fff;border-radius:18px;padding:28px 32px;width:100%;max-width:440px;box-shadow:0 24px 60px rgba(0,0,0,.2);" @click.stop>
            {{-- Titre --}}
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
                <div style="width:40px;height:40px;border-radius:11px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <div>
                    <div style="font-size:15px;font-weight:700;color:#111;">Envoyer la facture</div>
                    <div style="font-size:12px;color:#6b7280;">Le PDF sera joint automatiquement</div>
                </div>
            </div>

            {{-- Champ email --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Email du client</label>
                <input type="email" x-model="email" placeholder="client@exemple.com"
                       style="width:100%;padding:10px 13px;border:1.5px solid #e5e7eb;border-radius:9px;font-size:13px;outline:none;font-family:inherit;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#2563EB'" onblur="this.style.borderColor='#e5e7eb'">
                <p style="font-size:11px;color:#94a3b8;margin-top:5px;">
                    @if($facture->commande?->client_email) Pré-rempli depuis le devis · modifiable si besoin
                    @else Aucun email sur le devis · laissez vide pour juste changer le statut @endif
                </p>
            </div>

            {{-- Info facture --}}
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:9px;padding:11px 14px;margin-bottom:14px;font-size:12px;color:#1d4ed8;">
                <strong>{{ $facture->numero }}</strong> ·
                {{ number_format((float)($facture->commande?->total_ttc ?? 0), 0, ',', ' ') }} FCFA ·
                {{ $facture->commande?->client_nom ?? '—' }}
            </div>

            {{-- Erreur --}}
            <div x-show="err" x-text="err"
                 style="display:none;padding:9px 13px;background:#fef2f2;border:1px solid #fecaca;border-radius:9px;font-size:12px;color:#dc2626;margin-bottom:14px;"></div>

            {{-- Boutons --}}
            <div style="display:flex;gap:10px;">
                <button type="button" @click="open=false" :disabled="sending"
                        style="flex:1;padding:10px;border:1.5px solid #e5e7eb;border-radius:9px;font-size:13px;font-weight:600;color:#6b7280;background:#fff;cursor:pointer;">
                    Annuler
                </button>
                <button type="button" @click="send()" :disabled="sending"
                        style="flex:2;padding:10px;background:#059669;color:#fff;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
                    <span x-show="sending" style="display:none;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                    </span>
                    <span x-show="!sending">Envoyer →</span>
                    <span x-show="sending" style="display:none;">Envoi en cours...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<a href="{{ route('factures.pdf', $facture) }}" target="_blank"
   style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:var(--accent);color:#fff;border-radius:9px;font-size:13px;font-weight:600;text-decoration:none;">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    Télécharger PDF
</a>
@endsection

@push('styles')
<style>
/* ── Layout ── */
.fac-layout { display: grid; grid-template-columns: 1fr 300px; gap: 22px; align-items: start; }
@media(max-width:1024px) { .fac-layout { grid-template-columns: 1fr; } }

/* ── Document card ── */
.fac-doc {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    position: relative;
}
.fac-doc::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: var(--accent);
    border-radius: 4px 0 0 4px;
}

/* ── Header doc ── */
.doc-header {
    padding: 28px 30px 24px 36px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
}
.doc-num { font-size: 22px; font-weight: 800; color: var(--text); letter-spacing: -.3px; }
.doc-num-label { font-size: 11px; color: var(--muted); margin-top: 3px; font-weight: 500; }
.doc-amount { font-size: 30px; font-weight: 900; color: var(--accent-t); line-height: 1; }
.doc-amount-label { font-size: 11px; color: var(--muted); margin-top: 4px; text-align: right; }

/* ── Status badge ── */
.status-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 20px;
    font-size: 12px; font-weight: 700;
}
.status-pill .dot { width: 6px; height: 6px; border-radius: 50%; }

/* ── Parties grid ── */
.parties-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 0;
    border-bottom: 1px solid var(--border);
}
.party-col {
    padding: 20px 30px 20px 36px;
}
.party-col + .party-col {
    border-left: 1px solid var(--border);
    padding-left: 28px;
}
.party-tag {
    font-size: 9px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .12em; color: var(--muted); margin-bottom: 8px;
}
.party-name { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 3px; }
.party-meta { font-size: 12px; color: var(--muted); line-height: 1.7; }

/* ── Meta bar ── */
.meta-bar {
    display: flex; gap: 0;
    background: var(--card2);
    border-bottom: 1px solid var(--border);
    overflow: hidden;
}
.meta-cell {
    padding: 12px 20px 12px 36px;
    flex: 1;
}
.meta-cell + .meta-cell {
    border-left: 1px solid var(--border);
    padding-left: 20px;
}
.meta-cell .mc-label { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); margin-bottom: 3px; }
.meta-cell .mc-val   { font-size: 13px; font-weight: 700; color: var(--text); }
.meta-cell .mc-val.red { color: #ef4444; }
.meta-cell .mc-val.blue { color: var(--accent-t); }

/* ── Table ── */
.table-wrap { padding: 24px 30px 24px 36px; }
.fac-table { width: 100%; border-collapse: collapse; }
.fac-table thead tr { background: var(--accent); }
.fac-table th {
    padding: 10px 14px;
    font-size: 10px; font-weight: 700; color: #fff;
    text-transform: uppercase; letter-spacing: .08em;
    text-align: left;
}
.fac-table th.r { text-align: right; }
.fac-table th.c { text-align: center; }
.fac-table tbody tr { border-bottom: 1px solid var(--border); transition: background .1s; }
.fac-table tbody tr:last-child { border-bottom: 2px solid var(--border); }
.fac-table tbody tr:hover { background: var(--accent-bg); }
.fac-table td { padding: 14px; font-size: 13px; color: var(--text2); }
.fac-table td.r { text-align: right; }
.fac-table td.c { text-align: center; }
.fac-table .td-name { font-size: 13px; font-weight: 600; color: var(--text); }
.fac-table .td-ref  { font-size: 10px; color: var(--muted); font-family: monospace; margin-top: 2px; }
.qty-chip {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 32px; height: 24px; padding: 0 8px;
    background: var(--accent-bg); color: var(--accent-t);
    border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent);
    border-radius: 6px; font-size: 12px; font-weight: 700;
}

/* ── Totaux ── */
.totals-wrap { padding: 0 30px 24px 36px; display: flex; justify-content: flex-end; }
.totals-box {
    width: 290px;
    background: var(--card2);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
}
.tot-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 11px 16px;
    border-bottom: 1px solid var(--border);
    font-size: 13px;
}
.tot-row:last-child { border-bottom: none; }
.tot-label { color: var(--muted); }
.tot-val   { font-weight: 600; color: var(--text); }
.tot-row.ttc {
    background: var(--accent);
    padding: 14px 16px;
}
.tot-row.ttc .tot-label { color: rgba(255,255,255,.8); font-weight: 700; font-size: 14px; }
.tot-row.ttc .tot-val   { color: #fff; font-size: 20px; font-weight: 900; }
.tot-row.restant .tot-label { color: #ef4444; font-weight: 700; }
.tot-row.restant .tot-val   { color: #ef4444; font-weight: 800; font-size: 15px; }

/* ── Paiement section ── */
.paiement-section { margin: 0 30px 28px 36px; }
.progress-bar-wrap { margin-bottom: 20px; }
.progress-bar-top {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 8px; font-size: 13px;
}
.progress-bar-bg {
    height: 8px; background: var(--card2);
    border: 1px solid var(--border); border-radius: 99px; overflow: hidden;
}
.progress-bar-fill {
    height: 100%; border-radius: 99px;
    background: linear-gradient(90deg, var(--accent), #38bdf8);
    transition: width .8s ease;
}
.progress-labels {
    display: flex; justify-content: space-between;
    margin-top: 6px; font-size: 11px; color: var(--muted);
}
.paiement-form {
    display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap;
    padding: 20px;
    background: var(--card2);
    border: 1px solid var(--border);
    border-radius: 12px;
}
.form-field { flex: 1; min-width: 160px; }
.form-label {
    display: block; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .07em;
    color: var(--muted); margin-bottom: 8px;
}
.form-input {
    width: 100%; padding: 10px 14px;
    background: var(--card); border: 1px solid var(--border);
    border-radius: 10px; color: var(--text); font-size: 14px;
    font-family: inherit; outline: none; box-sizing: border-box;
    transition: border-color .15s;
}
.form-input:focus { border-color: var(--accent); }
.btn-secondary {
    padding: 10px 16px; background: var(--card);
    border: 1px solid var(--border); border-radius: 10px;
    color: var(--muted); font-size: 13px; font-weight: 600;
    cursor: pointer; font-family: inherit; white-space: nowrap;
    transition: color .15s;
}
.btn-secondary:hover { color: var(--text); }
.btn-primary {
    padding: 10px 22px; background: #22c55e;
    border: none; border-radius: 10px;
    color: #fff; font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: inherit; white-space: nowrap;
    transition: background .15s;
}
.btn-primary:hover { background: #16a34a; }

/* ── Payée banner ── */
.paid-banner {
    margin: 0 30px 28px 36px;
    display: flex; align-items: center; gap: 16px;
    padding: 18px 22px;
    background: rgba(34,197,94,.07);
    border: 1px solid rgba(34,197,94,.2);
    border-radius: 12px;
}
.paid-icon {
    width: 40px; height: 40px; border-radius: 50%;
    background: rgba(34,197,94,.15);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* ── Sidebar ── */
.side-stack { display: flex; flex-direction: column; gap: 14px; position: sticky; top: 82px; }
.side-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 14px; padding: 18px;
}
.side-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; color: var(--muted); margin-bottom: 14px;
}
.side-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 8px 0; border-bottom: 1px solid var(--border2, rgba(255,255,255,.04));
    font-size: 13px;
}
.side-row:last-child { border-bottom: none; padding-bottom: 0; }
.side-row .sl { color: var(--muted); }
.side-row .sv { font-weight: 700; color: var(--text); }
.side-action {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 11px; border-radius: 12px;
    font-size: 13px; font-weight: 600;
    text-decoration: none; transition: all .15s;
    border: 1px solid var(--border);
    background: var(--card); color: var(--muted);
}
.side-action:hover { color: var(--text); background: var(--card2); }
.side-action.primary {
    background: var(--accent); color: #fff; border-color: transparent;
}
.side-action.primary:hover { background: var(--accent-h); }

@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
@php
    $badges = [
        'brouillon' => ['bg'=>'rgba(148,163,184,.15)', 'dot'=>'#94a3b8', 'color'=>'#94a3b8', 'label'=>'Brouillon'],
        'envoyee'   => ['bg'=>'rgba(14,165,233,.12)',  'dot'=>'#38bdf8', 'color'=>'#38bdf8', 'label'=>'Envoyée'],
        'payee'     => ['bg'=>'rgba(34,197,94,.12)',   'dot'=>'#4ade80', 'color'=>'#4ade80', 'label'=>'Payée'],
        'en_retard' => ['bg'=>'rgba(239,68,68,.12)',   'dot'=>'#f87171', 'color'=>'#f87171', 'label'=>'En retard'],
    ];
    $b           = $badges[$facture->statut] ?? $badges['brouillon'];
    $commande    = $facture->commande;
    $totalTTC    = (float)($commande?->total_ttc ?? 0);
    $sousTotal   = (float)($commande?->sous_total_ht ?? 0);
    $tauxTVA     = (float)($commande?->tva ?? 18);
    $montantTVA  = $sousTotal * $tauxTVA / 100;
    $montantPaye = (float)$facture->montant_paye;
    $restant     = max(0, $totalTTC - $montantPaye);
    $pct         = $totalTTC > 0 ? min(100, round($montantPaye / $totalTTC * 100)) : 0;
    $echeanceRetard = $facture->date_echeance && $facture->date_echeance->isPast() && $facture->statut !== 'payee';
    $lignes      = $commande ? $commande->lignes : collect();
@endphp

{{-- Toast container (AJAX notifications) --}}
<div id="toast-container" style="position:fixed;top:16px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;min-width:280px;"></div>

<script>
function gToast(message, type) {
    const c = document.getElementById('toast-container');
    const t = document.createElement('div');
    const isOk = type !== 'error';
    t.style.cssText = [
        'padding:13px 18px;border-radius:12px;font-size:13px;font-weight:600;',
        'display:flex;align-items:center;gap:10px;pointer-events:auto;',
        'box-shadow:0 8px 24px rgba(0,0,0,.18);animation:fadeUp .2s ease;',
        isOk
            ? 'background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.3);color:#4ade80;'
            : 'background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#f87171;',
    ].join('');
    t.innerHTML = `<span>${message}</span>`;
    c.appendChild(t);
    setTimeout(() => { t.style.opacity='0'; t.style.transition='opacity .3s'; setTimeout(()=>t.remove(),300); }, 4500);
}
@if(session('success'))
document.addEventListener('DOMContentLoaded', () => gToast('{{ session('success') }}', 'success'));
@endif
@if(session('error'))
document.addEventListener('DOMContentLoaded', () => gToast('{{ session('error') }}', 'error'));
@endif
</script>

{{-- Alerte retard échéance --}}

@if($echeanceRetard)
<div style="display:flex;align-items:center;gap:10px;padding:12px 18px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:12px;margin-bottom:18px;">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#f87171" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <span style="font-size:13px;color:#f87171;font-weight:600;">Échéance dépassée — paiement dû depuis le {{ $facture->date_echeance->format('d/m/Y') }}</span>
</div>
@endif


<div class="fac-layout">

    {{-- ══ DOCUMENT ══ --}}
    <div class="fac-doc">

        {{-- Header --}}
        <div class="doc-header">
            <div>
                <div class="doc-num">{{ $facture->numero }}</div>
                <div class="doc-num-label">
                    Créée le {{ $facture->created_at->format('d/m/Y') }}
                    @if($facture->date_echeance)
                        <span style="margin:0 6px;opacity:.4;">·</span>
                        Échéance :
                        <span style="color:{{ $echeanceRetard ? '#f87171' : 'var(--muted)' }};font-weight:{{ $echeanceRetard ? '700' : '400' }};">
                            {{ $facture->date_echeance->format('d/m/Y') }}
                        </span>
                    @endif
                </div>
                <div style="margin-top:12px;">
                    <span id="statutPill" class="status-pill" style="background:{{ $b['bg'] }};color:{{ $b['color'] }};">
                        <span class="dot" style="background:{{ $b['dot'] }};"></span>
                        {{ $b['label'] }}
                    </span>
                </div>
            </div>
            <div style="text-align:right;">
                <div class="doc-amount">{{ number_format($totalTTC, 0, ',', ' ') }} <span style="font-size:16px;font-weight:600;opacity:.7;">FCFA</span></div>
                <div class="doc-amount-label">Montant total TTC</div>
                @if($restant > 0 && $facture->statut !== 'payee')
                <div style="margin-top:8px;font-size:13px;font-weight:700;color:#ef4444;">
                    Restant dû : {{ number_format($restant, 0, ',', ' ') }} FCFA
                </div>
                @endif
            </div>
        </div>

        {{-- Émetteur / Client --}}
        <div class="parties-grid">
            <div class="party-col">
                <div class="party-tag">Émetteur</div>
                <div class="party-name">{{ $facture->entreprise->nom }}</div>
                <div class="party-meta">
                    @if($facture->entreprise->adresse){{ $facture->entreprise->adresse }}<br>@endif
                    @if($facture->entreprise->ville){{ $facture->entreprise->ville }}<br>@endif
                    @if($facture->entreprise->email){{ $facture->entreprise->email }}<br>@endif
                    @if($facture->entreprise->telephone){{ $facture->entreprise->telephone }}@endif
                </div>
            </div>
            <div class="party-col">
                <div class="party-tag">Facturé à</div>
                <div class="party-name">{{ $commande?->client_nom ?? '—' }}</div>
                @if($commande?->client_email || $commande?->client_telephone || $commande?->client_adresse)
                <div class="party-meta">
                    @if($commande?->client_adresse){{ $commande->client_adresse }}<br>@endif
                    @if($commande?->client_email){{ $commande->client_email }}<br>@endif
                    @if($commande?->client_telephone){{ $commande->client_telephone }}@endif
                </div>
                @endif
            </div>
        </div>

        {{-- Meta bar --}}
        <div class="meta-bar">
            <div class="meta-cell">
                <div class="mc-label">N° Facture</div>
                <div class="mc-val blue">{{ $facture->numero }}</div>
            </div>
            @if($commande)
            <div class="meta-cell">
                <div class="mc-label">Commande</div>
                <div class="mc-val">{{ $commande->numero }}</div>
            </div>
            @endif
            <div class="meta-cell">
                <div class="mc-label">Date</div>
                <div class="mc-val">{{ $facture->created_at->format('d/m/Y') }}</div>
            </div>
            @if($facture->date_echeance)
            <div class="meta-cell">
                <div class="mc-label">Échéance</div>
                <div class="mc-val {{ $echeanceRetard ? 'red' : '' }}">{{ $facture->date_echeance->format('d/m/Y') }}</div>
            </div>
            @endif
            <div class="meta-cell">
                <div class="mc-label">TVA</div>
                <div class="mc-val">{{ $tauxTVA }}%</div>
            </div>
        </div>

        {{-- Table produits --}}
        <div class="table-wrap">
            <table class="fac-table">
                <thead>
                    <tr>
                        <th style="width:44%;">Produit / Service</th>
                        <th class="r" style="width:18%;">Prix unit. HT</th>
                        <th class="c" style="width:10%;">Qté</th>
                        <th class="r" style="width:14%;">Total HT</th>
                        <th class="r" style="width:14%;">Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lignes as $ligne)
                    <tr>
                        <td>
                            <div class="td-name">{{ $ligne->produit?->nom ?? 'Produit supprimé' }}</div>
                            @if($ligne->produit?->reference_sku)
                            <div class="td-ref">Réf. {{ $ligne->produit->reference_sku }}</div>
                            @endif
                        </td>
                        <td class="r" style="color:var(--muted);">{{ number_format($ligne->prix_unitaire_snapshot, 0, ',', ' ') }} FCFA</td>
                        <td class="c"><span class="qty-chip">{{ $ligne->quantite }}</span></td>
                        <td class="r" style="color:var(--muted);">{{ number_format($ligne->sous_total, 0, ',', ' ') }} FCFA</td>
                        <td class="r" style="font-weight:700;color:var(--text);">{{ number_format($ligne->sous_total, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--muted);padding:28px;">Aucun article</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Totaux --}}
        <div class="totals-wrap">
            <div class="totals-box">
                <div class="tot-row">
                    <span class="tot-label">Sous-total HT</span>
                    <span class="tot-val">{{ number_format($sousTotal, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="tot-row">
                    <span class="tot-label">TVA ({{ $tauxTVA }}%)</span>
                    <span class="tot-val">{{ number_format($montantTVA, 0, ',', ' ') }} FCFA</span>
                </div>
                @if($montantPaye > 0 && $facture->statut !== 'payee')
                <div class="tot-row">
                    <span class="tot-label" style="color:#22c55e;">Déjà payé</span>
                    <span class="tot-val" style="color:#22c55e;">− {{ number_format($montantPaye, 0, ',', ' ') }} FCFA</span>
                </div>
                @endif
                <div class="tot-row ttc">
                    <span class="tot-label">Total TTC</span>
                    <span class="tot-val">{{ number_format($totalTTC, 0, ',', ' ') }} FCFA</span>
                </div>
                @if($restant > 0 && $facture->statut !== 'payee')
                <div class="tot-row restant" style="padding:12px 16px;border-top:none;">
                    <span class="tot-label">Restant dû</span>
                    <span class="tot-val">{{ number_format($restant, 0, ',', ' ') }} FCFA</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Payée --}}
        @if($facture->statut === 'payee')
        <div class="paid-banner">
            <div class="paid-icon">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#4ade80" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:700;color:#4ade80;">Facture intégralement payée</div>
                @if($facture->payee_at)
                <div style="font-size:12px;color:rgba(74,222,128,.7);margin-top:2px;">Reçu le {{ $facture->payee_at->format('d/m/Y à H:i') }}</div>
                @endif
            </div>
        </div>

        @elseif($restant > 0)
        {{-- Paiement partiel --}}
        <div class="paiement-section">
            <div class="progress-bar-wrap">
                <div class="progress-bar-top">
                    <span style="font-size:13px;font-weight:700;color:var(--text);">Progression du paiement</span>
                    <span style="font-size:13px;font-weight:700;color:var(--accent-t);">{{ $pct }}%</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width:{{ $pct }}%;"></div>
                </div>
                <div class="progress-labels">
                    <span>Payé : {{ number_format($montantPaye, 0, ',', ' ') }} FCFA</span>
                    <span>Restant : {{ number_format($restant, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>

            <form method="POST" action="{{ route('factures.paiement', $facture) }}" class="paiement-form">
                @csrf
                <div class="form-field">
                    <label class="form-label">Montant reçu (FCFA)</label>
                    <input type="number" name="montant_paye" class="form-input"
                           required min="1" max="{{ $restant }}" step="1"
                           placeholder="{{ number_format($restant, 0) }}">
                </div>
                <div style="display:flex;gap:8px;flex-shrink:0;">
                    <button type="submit" name="tout_payer" value="1" class="btn-secondary"
                            onclick="document.querySelector('[name=montant_paye]').value={{ $restant }}">
                        Tout payer
                    </button>
                    <button type="submit" class="btn-primary">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
        @endif

    </div>

    {{-- ══ SIDEBAR ══ --}}
    <div class="side-stack">

        {{-- Résumé financier --}}
        <div class="side-card">
            <div class="side-title">Résumé financier</div>
            <div class="side-row">
                <span class="sl">Statut</span>
                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $b['bg'] }};color:{{ $b['color'] }};">{{ $b['label'] }}</span>
            </div>
            <div class="side-row">
                <span class="sl">Total TTC</span>
                <span class="sv">{{ number_format($totalTTC, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="side-row">
                <span class="sl">Montant payé</span>
                <span style="font-weight:700;color:#4ade80;">{{ number_format($montantPaye, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="side-row">
                <span class="sl">Restant dû</span>
                <span style="font-weight:700;color:{{ $restant > 0 ? '#f87171' : '#4ade80' }};">{{ number_format($restant, 0, ',', ' ') }} FCFA</span>
            </div>
            @if($facture->date_echeance)
            <div class="side-row">
                <span class="sl">Échéance</span>
                <span style="font-weight:600;color:{{ $echeanceRetard ? '#f87171' : 'var(--text2)' }};">{{ $facture->date_echeance->format('d/m/Y') }}</span>
            </div>
            @endif

            @if($totalTTC > 0)
            <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border);">
                <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);margin-bottom:5px;">
                    <span>Paiement</span><span style="font-weight:700;color:var(--accent-t);">{{ $pct }}%</span>
                </div>
                <div style="height:6px;background:var(--card2);border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:{{ $pct >= 100 ? '#4ade80' : 'var(--accent-t)' }};border-radius:99px;transition:width .5s;"></div>
                </div>
            </div>
            @endif
        </div>

        {{-- Actions --}}
        <a href="{{ route('factures.pdf', $facture) }}" target="_blank" class="side-action primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Télécharger PDF
        </a>

        {{-- Renvoyer / Envoyer par email — AJAX --}}
        <div x-data="{
            open: false,
            email: '{{ addslashes($facture->commande?->client_email ?? '') }}',
            sending: false,
            err: '',
            async send() {
                if (!this.email) { this.err = 'Email requis.'; return; }
                this.sending = true; this.err = '';
                const url = '{{ $facture->statut === 'brouillon'
                    ? route('factures.envoyer', $facture)
                    : route('factures.renvoyer-email', $facture) }}';
                const method = '{{ $facture->statut === 'brouillon' ? 'PATCH' : 'POST' }}';
                try {
                    const res = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ client_email: this.email }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.open = false;
                        gToast(data.message, 'success');
                        @if($facture->statut === 'brouillon')
                        document.getElementById('statutPill').textContent = 'Envoyée';
                        document.getElementById('statutPill').style.cssText += ';background:rgba(14,165,233,.12);color:#38bdf8;';
                        document.getElementById('topbarEnvoyer')?.remove();
                        @endif
                    } else {
                        this.err = data.message || 'Erreur inconnue.';
                    }
                } catch(e) { this.err = 'Erreur réseau, réessayez.'; }
                finally { this.sending = false; }
            }
        }">
            <button @click="open=!open" class="side-action" style="width:100%;border:none;cursor:pointer;">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                {{ $facture->statut === 'brouillon' ? 'Envoyer par email' : 'Renvoyer par email' }}
            </button>

            <div x-show="open" x-transition style="display:none;background:var(--card2);border:1px solid var(--border);border-radius:12px;padding:16px;margin-top:8px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Email destinataire</label>
                <input type="email" x-model="email" required
                       placeholder="client@exemple.com"
                       style="width:100%;padding:8px 11px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:12px;outline:none;font-family:inherit;margin-bottom:10px;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#2563EB'" onblur="this.style.borderColor='#e5e7eb'">
                <div x-show="err" x-text="err"
                     style="display:none;font-size:11px;color:#f87171;margin-bottom:8px;"></div>
                <button type="button" @click="send()" :disabled="sending"
                        style="width:100%;padding:9px;background:#2563EB;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                    <span x-show="sending" style="display:none;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                    </span>
                    <span x-text="sending ? 'Envoi...' : 'Envoyer le PDF →'">Envoyer le PDF →</span>
                </button>
            </div>
        </div>

        {{-- Commande liée --}}
        @if($commande)
        <div class="side-card">
            <div class="side-title">Commande liée</div>
            <a href="{{ route('commandes.show', $commande) }}"
               style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--card2);border:1px solid var(--border);border-radius:10px;text-decoration:none;transition:background .15s;"
               onmouseover="this.style.background='var(--accent-bg)'" onmouseout="this.style.background='var(--card2)'">
                <div style="display:flex;align-items:center;gap:8px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="var(--accent-t)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span style="font-size:13px;font-weight:600;color:var(--accent-t);">{{ $commande->numero }}</span>
                </div>
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        @endif

        {{-- Retour --}}
        <a href="{{ route('factures.index') }}" class="side-action">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour aux factures
        </a>
    </div>

</div>
@endsection
