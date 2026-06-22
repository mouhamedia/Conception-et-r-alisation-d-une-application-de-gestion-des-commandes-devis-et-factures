@extends('layouts.guest')

@section('title', 'Créer un compte — GestiPro')

@section('content')

<h1 class="card-title">Créer votre compte</h1>
@if($invitation ?? null)
<p class="card-subtitle">Vous rejoignez <strong>{{ $invitation->entreprise->nom }}</strong> en tant que {{ $invitation->role === 'owner' ? 'Propriétaire' : 'Employé' }}</p>
@else
<p class="card-subtitle">Rejoignez GestiPro et gérez votre activité commerciale</p>
@endif

@if($errors->any())
<div class="alert-error">
    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
</div>
@endif

<form method="POST" action="{{ route('register.post') }}">
    @csrf

    <div class="form-grid">
        <div>
            <label for="prenom">Prénom *</label>
            <input type="text" id="prenom" name="prenom"
                   value="{{ old('prenom') }}"
                   placeholder="Mamadou"
                   required autofocus>
        </div>
        <div>
            <label for="name">Nom *</label>
            <input type="text" id="name" name="name"
                   value="{{ old('name') }}"
                   placeholder="Diallo"
                   required>
        </div>
    </div>

    <div class="form-group">
        <label for="email">Adresse email *</label>
        <input type="email" id="email" name="email"
               value="{{ old('email', $invitation->email ?? '') }}"
               placeholder="vous@exemple.com"
               @if($invitation ?? null) readonly @endif
               required>
    </div>

    <div class="form-group">
        <label for="telephone">Téléphone</label>
        <input type="tel" id="telephone" name="telephone"
               value="{{ old('telephone') }}"
               placeholder="+221 77 000 00 00">
    </div>

    <div class="form-group">
        <label for="password">Mot de passe *</label>
        <input type="password" id="password" name="password"
               placeholder="8 caractères minimum"
               required>
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirmer le mot de passe *</label>
        <input type="password" id="password_confirmation" name="password_confirmation"
               placeholder="••••••••"
               required>
    </div>

    <button type="submit" class="btn-submit">
        Créer mon compte
    </button>
</form>

<p class="card-footer-link">
    Déjà un compte ?
    <a href="{{ route('login') }}">Se connecter</a>
</p>

@endsection
