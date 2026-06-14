@extends('layouts.app')

@section('title', 'Équipe')
@section('page-title', 'Gestion de l\'équipe')
@section('page-subtitle', $entreprise->nom . ' · ' . $membres->count() . ' membre(s)')

@section('topbar-actions')
@can('viewTeam', $entreprise)
<button onclick="document.getElementById('inviteSection').scrollIntoView({behavior:'smooth'})" class="btn-primary">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
    Inviter
</button>
@endcan
@endsection

@section('content')
@php
$invitationsEnAttente = $entreprise->invitations()->whereNull('accepted_at')->where('expires_at','>',now())->get();
$nbOwners = $membres->where('pivot.role','owner')->count();
$nbEmployes = $membres->where('pivot.role','employee')->count();
@endphp

<style>
.eq-grid{display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;}
@media(max-width:1000px){.eq-grid{grid-template-columns:1fr;}}

.eq-card{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;transition:background 0.25s;}
.eq-head{display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:1px solid var(--border);}
.eq-head-title{font-size:14px;font-weight:700;color:var(--text);}
.eq-badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}
.eq-badge-blue{background:var(--accent-bg);color:var(--accent-t);}
.eq-badge-amber{background:rgba(245,158,11,0.14);color:#fbbf24;}
.eq-badge-green{background:rgba(34,197,94,0.14);color:#4ade80;}

.membre-row{display:flex;align-items:center;gap:14px;padding:14px 22px;border-bottom:1px solid var(--border2,rgba(255,255,255,0.04));transition:background 0.15s;}
.membre-row:last-child{border-bottom:none;}
.membre-row:hover{background:var(--accent-bg);}
.membre-av{width:42px;height:42px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:white;font-size:14px;font-weight:700;flex-shrink:0;}

.inv-row{display:flex;align-items:center;gap:14px;padding:12px 22px;border-bottom:1px solid var(--border2,rgba(255,255,255,0.04));}
.inv-row:last-child{border-bottom:none;}

.form-label{display:block;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:6px;}
.form-input{width:100%;padding:10px 14px;background:var(--card2);border:1.5px solid var(--border);border-radius:10px;color:var(--text);font-size:13px;outline:none;transition:border-color 0.15s,background 0.15s;font-family:inherit;}
.form-input:focus{border-color:var(--accent);background:var(--card);}
.form-input::placeholder{color:var(--muted);}

.stat-mini{background:var(--card2);border:1px solid var(--border);border-radius:12px;padding:16px;display:flex;align-items:center;gap:12px;}
.stat-mini-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.stat-mini-val{font-size:22px;font-weight:800;color:var(--text);line-height:1;}
.stat-mini-lbl{font-size:11px;color:var(--muted);margin-top:2px;}

.action-btn{font-size:12px;font-weight:600;padding:5px 10px;border-radius:7px;border:none;cursor:pointer;transition:all 0.15s;font-family:inherit;}
.action-btn-ghost{background:var(--card2);color:var(--muted);border:1px solid var(--border);}
.action-btn-ghost:hover{background:var(--accent-bg);color:var(--accent-t);border-color:var(--accent);}
.action-btn-danger{background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2);}
.action-btn-danger:hover{background:rgba(239,68,68,0.2);}
</style>

<div class="eq-grid">

    {{-- Colonne gauche --}}
    <div style="display:flex;flex-direction:column;gap:18px;">

        {{-- Membres actifs --}}
        <div class="eq-card">
            <div class="eq-head">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:9px;background:var(--accent-bg);display:flex;align-items:center;justify-content:center;">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="var(--accent-t)" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="eq-head-title">Membres actifs</span>
                    <span class="eq-badge eq-badge-blue">{{ $membres->count() }}</span>
                </div>
            </div>

            @foreach($membres as $membre)
            @php $isMe = $membre->id === auth()->id(); @endphp
            <div class="membre-row">
                <div class="membre-av" style="background:{{ $isMe ? 'var(--accent)' : 'var(--card2)' }};{{ !$isMe ? 'border:2px solid var(--border);color:var(--text);' : '' }}">
                    {{ strtoupper(substr($membre->prenom ?? $membre->name, 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span style="font-size:14px;font-weight:600;color:var(--text);">{{ $membre->prenom }} {{ $membre->name }}</span>
                        @if($isMe)<span style="font-size:10px;color:var(--muted);font-style:italic;">(vous)</span>@endif
                    </div>
                    <div style="font-size:12px;color:var(--muted);margin-top:1px;">{{ $membre->email }}</div>
                    <div style="font-size:11px;color:var(--muted2,rgba(148,163,184,0.4));margin-top:2px;">
                        Membre depuis {{ \Carbon\Carbon::parse($membre->pivot->joined_at)->format('d/m/Y') }}
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                    <span class="eq-badge {{ $membre->pivot->role==='owner' ? 'eq-badge-amber' : 'eq-badge-blue' }}">
                        {{ $membre->pivot->role==='owner' ? 'Propriétaire' : 'Employé' }}
                    </span>
                    @if(!$isMe)
                    @can('viewTeam', $entreprise)
                    <form method="POST" action="{{ route('collaborateurs.role',$membre->id) }}" style="display:inline;">
                        @csrf @method('PATCH')
                        <input type="hidden" name="role" value="{{ $membre->pivot->role==='owner'?'employee':'owner' }}">
                        <button type="submit" class="action-btn action-btn-ghost">
                            {{ $membre->pivot->role==='owner' ? '→ Employé' : '→ Propriétaire' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('collaborateurs.retirer',$membre->id) }}" style="display:inline;"
                          onsubmit="return confirm('Retirer {{ $membre->prenom }} de l\'équipe ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-btn action-btn-danger">Retirer</button>
                    </form>
                    @endcan
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Formulaire invitation --}}
        @can('viewTeam', $entreprise)
        <div class="eq-card" id="inviteSection">
            <div class="eq-head">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:9px;background:rgba(34,197,94,0.14);display:flex;align-items:center;justify-content:center;">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#4ade80" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </div>
                    <span class="eq-head-title">Inviter un collaborateur</span>
                </div>
            </div>
            <div style="padding:22px;">
                <form method="POST" action="{{ route('invitations.invite') }}">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 160px auto;gap:14px;align-items:end;">
                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" required class="form-input" placeholder="collaborateur@exemple.com" value="{{ old('email') }}">
                        </div>
                        <div>
                            <label class="form-label">Rôle</label>
                            <select name="role" class="form-input">
                                <option value="employee">Employé</option>
                                <option value="owner">Propriétaire</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn-primary" style="white-space:nowrap;padding:10px 20px;">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Envoyer l'invitation
                            </button>
                        </div>
                    </div>
                    <p style="font-size:12px;color:var(--muted);margin-top:12px;display:flex;align-items:center;gap:5px;">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Un email d'invitation sera envoyé avec un lien valable 7 jours.
                    </p>
                </form>
            </div>
        </div>
        @endcan

        {{-- Invitations en attente --}}
        @can('viewTeam', $entreprise)
        @if($invitationsEnAttente->isNotEmpty())
        <div class="eq-card">
            <div class="eq-head">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:9px;background:rgba(245,158,11,0.14);display:flex;align-items:center;justify-content:center;">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#fbbf24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="eq-head-title">Invitations en attente</span>
                    <span class="eq-badge eq-badge-amber">{{ $invitationsEnAttente->count() }}</span>
                </div>
            </div>
            @foreach($invitationsEnAttente as $inv)
            <div class="inv-row">
                <div style="width:38px;height:38px;border-radius:50%;background:rgba(245,158,11,0.14);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#fbbf24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $inv->email }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:1px;">
                        {{ $inv->role==='owner'?'Propriétaire':'Employé' }} · Expire le {{ $inv->expires_at->format('d/m/Y') }}
                    </div>
                </div>
                <span class="eq-badge eq-badge-amber">En attente</span>
            </div>
            @endforeach
        </div>
        @endif
        @endcan
    </div>

    {{-- Colonne droite --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Stats --}}
        <div class="eq-card" style="padding:20px;">
            <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:14px;">Aperçu de l'équipe</div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <div class="stat-mini">
                    <div class="stat-mini-icon" style="background:var(--accent-bg);">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--accent-t)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <div class="stat-mini-val">{{ $membres->count() }}</div>
                        <div class="stat-mini-lbl">Membres au total</div>
                    </div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-icon" style="background:rgba(245,158,11,0.14);">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#fbbf24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div class="stat-mini-val">{{ $nbOwners }}</div>
                        <div class="stat-mini-lbl">Propriétaire(s)</div>
                    </div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-icon" style="background:var(--accent-bg);">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--accent-t)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <div class="stat-mini-val">{{ $nbEmployes }}</div>
                        <div class="stat-mini-lbl">Employé(s)</div>
                    </div>
                </div>
                @if($invitationsEnAttente->isNotEmpty())
                <div class="stat-mini" style="border-color:rgba(245,158,11,0.25);">
                    <div class="stat-mini-icon" style="background:rgba(245,158,11,0.14);">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#fbbf24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="stat-mini-val" style="color:#fbbf24;">{{ $invitationsEnAttente->count() }}</div>
                        <div class="stat-mini-lbl">Invitation(s) en attente</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Info card --}}
        <div class="eq-card" style="padding:20px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                <div style="width:28px;height:28px;border-radius:8px;background:var(--accent-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="var(--accent-t)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span style="font-size:13px;font-weight:700;color:var(--text);">À savoir</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <div style="display:flex;align-items:flex-start;gap:8px;font-size:12px;color:var(--muted);">
                    <div style="width:5px;height:5px;border-radius:50%;background:var(--accent);flex-shrink:0;margin-top:5px;"></div>
                    <span>Le <strong style="color:var(--text);">Propriétaire</strong> peut inviter des membres et modifier les rôles.</span>
                </div>
                <div style="display:flex;align-items:flex-start;gap:8px;font-size:12px;color:var(--muted);">
                    <div style="width:5px;height:5px;border-radius:50%;background:var(--accent);flex-shrink:0;margin-top:5px;"></div>
                    <span>L'<strong style="color:var(--text);">Employé</strong> accède aux données mais ne peut pas gérer l'équipe.</span>
                </div>
                <div style="display:flex;align-items:flex-start;gap:8px;font-size:12px;color:var(--muted);">
                    <div style="width:5px;height:5px;border-radius:50%;background:var(--accent);flex-shrink:0;margin-top:5px;"></div>
                    <span>Les invitations expirent après <strong style="color:var(--text);">7 jours</strong>.</span>
                </div>
            </div>
        </div>

        {{-- Entreprise info --}}
        <div class="eq-card" style="padding:20px;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <div style="width:44px;height:44px;border-radius:12px;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:white;flex-shrink:0;">
                    {{ strtoupper(substr($entreprise->nom,0,2)) }}
                </div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:var(--text);">{{ $entreprise->nom }}</div>
                    <div style="font-size:11px;color:var(--muted);">{{ $entreprise->email ?? '—' }}</div>
                </div>
            </div>
            <a href="{{ route('entreprise.edit') }}" style="display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;background:var(--card2);border:1px solid var(--border);border-radius:10px;color:var(--muted);font-size:12px;font-weight:600;text-decoration:none;transition:all 0.15s;" onmouseover="this.style.background='var(--accent-bg)';this.style.color='var(--accent-t)'" onmouseout="this.style.background='var(--card2)';this.style.color='var(--muted)'">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Paramètres de l'entreprise
            </a>
        </div>
    </div>
</div>
@endsection
