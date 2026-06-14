@extends('layouts.guest')

@section('title', 'Créer une entreprise — GestiPro')

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Créer votre entreprise</h1>
    <p class="text-gray-500 text-sm mb-6">Renseignez les informations de votre entreprise pour commencer</p>

    @if($errors->any())
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
        @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('entreprise.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom de l'entreprise *</label>
            <input type="text" name="nom" value="{{ old('nom') }}" required autofocus
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]"
                   placeholder="Tech Sénégal SARL">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]"
                       placeholder="contact@entreprise.sn">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                <input type="tel" name="telephone" value="{{ old('telephone') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]"
                       placeholder="+221 33 000 00 00">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse</label>
            <input type="text" name="adresse" value="{{ old('adresse') }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]"
                   placeholder="Rue Carnot, Plateau">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ville</label>
                <input type="text" name="ville" value="{{ old('ville', 'Dakar') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">NINEA / SIRET</label>
                <input type="text" name="siret" value="{{ old('siret') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]"
                       placeholder="SN-2025-000000">
            </div>
        </div>
        <button type="submit"
                class="w-full py-2.5 bg-[#1E3A8A] text-white font-semibold rounded-lg hover:bg-blue-800 transition-colors text-sm">
            Créer l'entreprise →
        </button>
    </form>
</div>
@endsection
