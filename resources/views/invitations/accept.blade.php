@extends('layouts.guest')

@section('title', 'Invitation — GestiPro')

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 text-center">

    @if(session('info'))
    <div class="mb-6 px-4 py-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-800 text-sm text-left">
        {{ session('info') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm text-left">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
    @endif

    <div class="w-16 h-16 mx-auto bg-red-100 rounded-2xl flex items-center justify-center mb-5">
        <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>

    <h1 class="text-xl font-bold text-gray-900 mb-2">Invitation invalide ou expirée</h1>
    <p class="text-gray-500 text-sm mb-6 max-w-xs mx-auto">
        Ce lien d'invitation n'est plus valide. Demandez à l'administrateur de vous envoyer un nouveau lien.
    </p>

    <a href="{{ route('login') }}"
       class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1E3A8A] text-white text-sm font-semibold rounded-xl hover:bg-blue-800 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
        </svg>
        Aller à la connexion
    </a>
</div>
@endsection
