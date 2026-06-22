@extends('layouts.guest')

@section('title', 'Créer une entreprise — GestiPro')

@php
$premiereEntreprise = auth()->user()->entreprises()->count() === 0;
@endphp

@section('content')

@if($premiereEntreprise)
<h1 class="card-title">Bienvenue sur GestiPro</h1>
<p class="card-subtitle">Créons votre première entreprise pour commencer à gérer devis, commandes et factures</p>
@else
<h1 class="card-title">Créer une nouvelle entreprise</h1>
<p class="card-subtitle">Renseignez les informations pour commencer</p>
@endif

@if($errors->any())
<div class="alert-error">
    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
</div>
@endif

<style>
.logo-upload-row { display: flex; align-items: center; gap: 16px; margin-bottom: 22px; }
.logo-upload-preview {
    width: 64px; height: 64px; border-radius: 14px; flex-shrink: 0;
    border: 1.5px solid #e5e7eb; background: #fafafa;
    display: flex; align-items: center; justify-content: center; overflow: hidden;
}
.logo-upload-preview img { max-width: 100%; max-height: 100%; object-fit: contain; padding: 4px; }
.logo-upload-btn {
    display: inline-flex; align-items: center; gap: 7px; padding: 9px 16px;
    background: #fafafa; border: 1.5px solid #e5e7eb; border-radius: 10px;
    font-size: 13px; font-weight: 600; color: #374151; cursor: pointer;
    transition: border-color .2s, color .2s;
}
.logo-upload-btn:hover { border-color: #1E3A8A; color: #1E3A8A; }
.logo-upload-hint { font-size: 11px; color: #9ca3af; margin-top: 6px; }
</style>

<form method="POST" action="{{ route('entreprise.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="logo-upload-row">
        <div class="logo-upload-preview" id="logoPreviewWrap">
            <svg id="logoPlaceholder" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9-4 9 4-9 4-9-4zm0 0v10l9 4m0-10v10m9-10v10l-9 4"/></svg>
        </div>
        <div>
            <label for="logoInput" class="logo-upload-btn">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Ajouter un logo
            </label>
            <input type="file" name="logo" id="logoInput" accept="image/*" style="display:none;" onchange="previewLogo(this)">
            <div class="logo-upload-hint">Optionnel · PNG, JPG, SVG · max 2 Mo</div>
        </div>
    </div>

    <div class="form-group">
        <label for="nom">Nom de l'entreprise *</label>
        <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required autofocus
               placeholder="Tech Sénégal SARL">
    </div>

    <div class="form-grid">
        <div class="form-group" style="margin-bottom:0;">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   placeholder="contact@entreprise.sn">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" value="{{ old('telephone') }}"
                   placeholder="+221 33 000 00 00">
        </div>
    </div>

    <div class="form-group">
        <label for="adresse">Adresse</label>
        <input type="text" id="adresse" name="adresse" value="{{ old('adresse') }}"
               placeholder="Rue Carnot, Plateau">
    </div>

    <div class="form-grid">
        <div class="form-group" style="margin-bottom:0;">
            <label for="ville">Ville</label>
            <input type="text" id="ville" name="ville" value="{{ old('ville', 'Dakar') }}">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="siret">NINEA / SIRET</label>
            <input type="text" id="siret" name="siret" value="{{ old('siret') }}"
                   placeholder="SN-2025-000000">
        </div>
    </div>

    <button type="submit" class="btn-submit" style="margin-top:6px;">
        {{ $premiereEntreprise ? 'Créer mon entreprise →' : 'Créer l\'entreprise →' }}
    </button>
</form>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.getElementById('logoPreviewWrap');
            wrap.innerHTML = '<img src="' + e.target.result + '" alt="Logo">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection
