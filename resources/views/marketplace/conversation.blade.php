@extends('layouts.app')
@section('title', 'Conversation — ' . $demande->entrepriseSource->nom)
@section('page-title', 'Conversation')

@section('topbar-actions')
<a href="{{ url()->previous() }}"
   style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--card);border:1px solid var(--border);border-radius:9px;color:var(--muted);font-size:13px;font-weight:500;text-decoration:none;">
    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Retour
</a>
@endsection

@push('styles')
<style>
.conv-layout { display: grid; grid-template-columns: 1fr 300px; gap: 22px; align-items: start; }
@media(max-width: 900px) { .conv-layout { grid-template-columns: 1fr; } }

/* Thread */
.thread-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden; display: flex;
    flex-direction: column; max-height: calc(100vh - 140px);
}
.thread-header {
    padding: 18px 22px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 13px; flex-shrink: 0;
}
.thread-avatar {
    width: 42px; height: 42px; border-radius: 12px;
    background: var(--primary-bg); display: flex; align-items: center;
    justify-content: center; font-size: 16px; font-weight: 800;
    color: var(--primary); flex-shrink: 0;
}
.thread-messages {
    flex: 1; overflow-y: auto; padding: 20px;
    display: flex; flex-direction: column; gap: 14px;
    scroll-behavior: smooth;
}

/* Demande originale */
.orig-bubble {
    background: var(--card2); border: 1px solid var(--border);
    border-radius: 14px 14px 14px 4px; padding: 16px 18px;
    max-width: 85%; position: relative;
}
.orig-bubble::before {
    content: 'DEMANDE ORIGINALE';
    display: block; font-size: 9px; font-weight: 800;
    color: var(--muted); letter-spacing: .1em; margin-bottom: 8px;
}

/* Messages */
.msg-row { display: flex; gap: 10px; }
.msg-row.mine { flex-direction: row-reverse; }
.msg-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--primary-bg); display: flex; align-items: center;
    justify-content: center; font-size: 11px; font-weight: 800;
    color: var(--primary); flex-shrink: 0; margin-top: 2px;
}
.msg-row.mine .msg-avatar { background: var(--primary); color: #fff; }
.msg-bubble {
    background: var(--card2); border: 1px solid var(--border);
    border-radius: 14px 14px 14px 4px;
    padding: 11px 14px; max-width: 75%;
    font-size: 13px; color: var(--text); line-height: 1.55;
}
.msg-row.mine .msg-bubble {
    background: var(--primary); color: #fff;
    border: none; border-radius: 14px 14px 4px 14px;
}
.msg-meta {
    font-size: 11px; color: var(--muted); margin-top: 5px;
    display: flex; align-items: center; gap: 6px;
}
.msg-row.mine .msg-meta { justify-content: flex-end; }

/* Zone de saisie */
.reply-zone {
    border-top: 1px solid var(--border); padding: 16px 20px; flex-shrink: 0;
    background: var(--card);
}
.reply-input {
    width: 100%; padding: 10px 14px; background: var(--card2);
    border: 1.5px solid var(--border); border-radius: 12px;
    color: var(--text); font-size: 13px; font-family: inherit;
    resize: none; outline: none; transition: border-color .15s;
    min-height: 70px; max-height: 140px;
}
.reply-input:focus { border-color: var(--primary); }
.reply-actions { display: flex; align-items: center; justify-content: space-between; margin-top: 10px; }

/* Sidebar info */
.info-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 14px; padding: 18px; position: sticky; top: 82px;
}
.info-row {
    display: flex; justify-content: space-between;
    padding: 8px 0; border-bottom: 1px solid var(--border);
    font-size: 13px;
}
.info-row:last-child { border-bottom: none; padding-bottom: 0; }
.info-lbl { color: var(--muted); }
.info-val { font-weight: 600; color: var(--text2); }

/* Statut badge */
.s-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }

@keyframes slideUp { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }
.msg-anim { animation: slideUp .2s ease; }
</style>
@endpush

@section('content')
@php
$isSource = $demande->entreprise_source_id === $entreprise->id;
$autreEntreprise = $isSource ? $demande->entrepriseCible : $demande->entrepriseSource;
$statuts = [
    'en_attente' => ['label'=>'En attente',  'bg'=>'rgba(245,158,11,.12)', 'color'=>'#f59e0b'],
    'acceptee'   => ['label'=>'Acceptée',    'bg'=>'rgba(34,197,94,.12)',  'color'=>'#4ade80'],
    'refusee'    => ['label'=>'Refusée',     'bg'=>'rgba(239,68,68,.12)',  'color'=>'#f87171'],
    'devis_cree' => ['label'=>'Devis créé',  'bg'=>'rgba(37,99,235,.12)', 'color'=>'#60a5fa'],
];
$s = $statuts[$demande->statut] ?? $statuts['en_attente'];
@endphp

<div class="conv-layout" x-data="conversation()">

    {{-- ══ THREAD ══ --}}
    <div class="thread-card">

        {{-- Header --}}
        <div class="thread-header">
            <div class="thread-avatar">{{ mb_strtoupper(mb_substr($autreEntreprise->nom, 0, 2)) }}</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:14px;font-weight:700;color:var(--text);">{{ $autreEntreprise->nom }}</div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                    {{ $isSource ? 'Fournisseur potentiel' : 'Demandeur' }} ·
                    <span x-text="msgs.length + ' message(s)'"></span>
                </div>
            </div>
            <span class="s-badge" style="background:{{ $s['bg'] }};color:{{ $s['color'] }};">{{ $s['label'] }}</span>
        </div>

        {{-- Messages --}}
        <div class="thread-messages" id="threadMessages">

            {{-- Demande originale --}}
            <div>
                <div style="display:flex;gap:10px;">
                    <div class="msg-avatar" style="background:var(--primary-bg);color:var(--primary);font-size:10px;width:32px;height:32px;">
                        {{ mb_strtoupper(mb_substr($demande->entrepriseSource->nom, 0, 2)) }}
                    </div>
                    <div style="max-width:75%;">
                        <div class="orig-bubble">
                            <div style="font-size:13px;color:var(--text);line-height:1.55;white-space:pre-wrap;">{{ $demande->description }}</div>
                            @if($demande->budget)
                            <div style="margin-top:10px;padding:8px 12px;background:var(--primary-bg);border-radius:8px;font-size:12px;font-weight:700;color:var(--primary-text);">
                                Budget : {{ number_format((float)$demande->budget, 0, ',', ' ') }} FCFA
                            </div>
                            @endif
                        </div>
                        <div class="msg-meta">
                            <span style="font-weight:600;color:var(--text2);">{{ $demande->entrepriseSource->nom }}</span>
                            · {{ $demande->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Séparateur --}}
            @if($demande->messages->isNotEmpty())
            <div style="display:flex;align-items:center;gap:10px;padding:4px 0;">
                <div style="flex:1;height:1px;background:var(--border);"></div>
                <span style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;white-space:nowrap;">Conversation</span>
                <div style="flex:1;height:1px;background:var(--border);"></div>
            </div>
            @endif

            {{-- Messages du thread --}}
            <template x-for="msg in msgs" :key="msg.id">
                <div class="msg-row msg-anim" :class="msg.is_mine ? 'mine' : ''">
                    <div class="msg-avatar" x-text="msg.initiales"></div>
                    <div>
                        <div class="msg-bubble" x-text="msg.contenu"></div>
                        <div class="msg-meta">
                            <span style="font-weight:600;" :style="msg.is_mine ? '' : 'color:var(--text2);'" x-text="msg.entreprise_nom"></span>
                            <span x-text="'· ' + msg.created_at"></span>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Indicateur chargement --}}
            <div x-show="sending" style="display:none;text-align:center;padding:8px;">
                <span style="font-size:12px;color:var(--muted);">Envoi en cours…</span>
            </div>
        </div>

        {{-- Zone de réponse --}}
        @if($demande->statut !== 'refusee')
        <div class="reply-zone">
            <textarea class="reply-input" x-model="newMsg"
                      placeholder="Écrivez votre réponse…"
                      @keydown.ctrl.enter.prevent="send()"
                      @keydown.meta.enter.prevent="send()"
                      rows="3"></textarea>
            <div class="reply-actions">
                <span style="font-size:11px;color:var(--muted);">Ctrl+Entrée pour envoyer</span>
                <button type="button" @click="send()" :disabled="sending || !newMsg.trim()"
                        style="display:inline-flex;align-items:center;gap:7px;padding:9px 20px;background:var(--primary);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;transition:background .15s;"
                        onmouseover="if(!this.disabled)this.style.background='var(--primary-h)'" onmouseout="this.style.background='var(--primary)'">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span x-text="sending ? 'Envoi…' : 'Envoyer'"></span>
                </button>
            </div>
            <div x-show="errMsg" x-text="errMsg" style="display:none;font-size:12px;color:#f87171;margin-top:6px;"></div>
        </div>
        @else
        <div style="padding:14px 20px;background:rgba(239,68,68,.06);border-top:1px solid rgba(239,68,68,.15);font-size:12px;color:#f87171;font-weight:600;text-align:center;flex-shrink:0;">
            Cette demande a été refusée — la conversation est clôturée.
        </div>
        @endif
    </div>

    {{-- ══ SIDEBAR ══ --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Infos demande --}}
        <div class="info-card">
            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px;">Demande de devis</div>
            <div class="info-row">
                <span class="info-lbl">De</span>
                <span class="info-val">{{ $demande->entrepriseSource->nom }}</span>
            </div>
            <div class="info-row">
                <span class="info-lbl">À</span>
                <span class="info-val">{{ $demande->entrepriseCible->nom }}</span>
            </div>
            <div class="info-row">
                <span class="info-lbl">Date</span>
                <span class="info-val">{{ $demande->created_at->format('d/m/Y') }}</span>
            </div>
            @if($demande->budget)
            <div class="info-row">
                <span class="info-lbl">Budget</span>
                <span class="info-val" style="color:var(--primary);">{{ number_format((float)$demande->budget, 0, ',', ' ') }} FCFA</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-lbl">Statut</span>
                <span class="s-badge" style="background:{{ $s['bg'] }};color:{{ $s['color'] }};">{{ $s['label'] }}</span>
            </div>
        </div>

        {{-- Actions (pour l'entreprise cible en attente) --}}
        @if($demande->statut === 'en_attente' && $demande->entreprise_cible_id === $entreprise->id)
        <div class="info-card">
            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px;">Actions</div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <form method="POST" action="{{ route('marketplace.accepter', $demande) }}">
                    @csrf @method('PATCH')
                    <button type="submit" style="width:100%;padding:10px;background:#22c55e;color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;">
                        Accepter et créer un devis
                    </button>
                </form>
                <form method="POST" action="{{ route('marketplace.refuser', $demande) }}"
                      onsubmit="return confirm('Refuser cette demande ?')">
                    @csrf @method('PATCH')
                    <button type="submit" style="width:100%;padding:10px;background:rgba(239,68,68,.1);color:#f87171;border:1px solid rgba(239,68,68,.25);border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;">
                        Refuser la demande
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Devis créé --}}
        @if($demande->devis)
        <a href="{{ route('devis.show', $demande->devis) }}"
           style="display:flex;align-items:center;gap:10px;padding:14px 16px;background:var(--card);border:1px solid var(--border);border-radius:14px;text-decoration:none;transition:background .15s;"
           onmouseover="this.style.background='var(--card2)'" onmouseout="this.style.background='var(--card)'">
            <div style="width:36px;height:36px;border-radius:10px;background:var(--primary-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div style="font-size:13px;font-weight:700;color:var(--primary-text);">Voir le devis</div>
                <div style="font-size:11px;color:var(--muted);margin-top:1px;">{{ $demande->devis->numero }}</div>
            </div>
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="var(--muted)" stroke-width="2" style="margin-left:auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function conversation() {
    return {
        msgs: {{ Js::from($demande->messages->map(fn($m) => [
            'id'             => $m->id,
            'contenu'        => $m->contenu,
            'entreprise_nom' => $m->entreprise->nom,
            'user_nom'       => ($m->user->prenom ?? '') . ' ' . $m->user->name,
            'initiales'      => mb_strtoupper(mb_substr($m->entreprise->nom, 0, 2)),
            'created_at'     => $m->created_at->format('d/m/Y H:i'),
            'is_mine'        => $m->entreprise_id === $entreprise->id,
        ])) }},
        newMsg:  '',
        sending: false,
        errMsg:  '',

        async send() {
            if (!this.newMsg.trim()) return;
            this.sending = true; this.errMsg = '';
            try {
                const res = await fetch('{{ route('demandes.messages.store', $demande) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ contenu: this.newMsg }),
                });
                const data = await res.json();
                if (data.success) {
                    const m = data.message;
                    m.initiales = '{{ mb_strtoupper(mb_substr($entreprise->nom, 0, 2)) }}';
                    this.msgs.push(m);
                    this.newMsg = '';
                    this.$nextTick(() => {
                        const el = document.getElementById('threadMessages');
                        el.scrollTop = el.scrollHeight;
                    });
                } else {
                    this.errMsg = data.message || 'Erreur.';
                }
            } catch(e) { this.errMsg = 'Erreur réseau.'; }
            finally { this.sending = false; }
        },

        init() {
            this.$nextTick(() => {
                const el = document.getElementById('threadMessages');
                if (el) el.scrollTop = el.scrollHeight;
            });
        },
    };
}
</script>
@endpush
