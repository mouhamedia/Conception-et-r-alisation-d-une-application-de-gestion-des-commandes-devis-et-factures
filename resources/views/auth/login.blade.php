@extends('layouts.guest')

@section('title', 'Connexion — GestiPro')

@section('content')

<h1 class="card-title">Bon retour 👋</h1>
<p class="card-subtitle">Connectez-vous à votre compte GestiPro</p>

@if($errors->any())
<div class="alert-error">
    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
</div>
@endif

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('login.post') }}">
    @csrf

    <div class="form-group">
        <label for="email">Adresse email</label>
        <input type="email" id="email" name="email"
               value="{{ old('email') }}"
               placeholder="vous@exemple.com"
               required autofocus>
    </div>

    <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password"
               placeholder="••••••••"
               required>
    </div>

    <div class="remember-row">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">Se souvenir de moi</label>
    </div>

    <button type="submit" class="btn-submit">
        Se connecter
    </button>
</form>

<p class="card-footer-link">
    Pas encore de compte ?
    <a href="{{ route('register') }}">S'inscrire gratuitement</a>
</p>

@endsection
