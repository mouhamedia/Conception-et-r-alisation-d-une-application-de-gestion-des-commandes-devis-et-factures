@extends('layouts.app')

@section('title', 'Paramètres')
@section('page-title', 'Paramètres de l\'entreprise')

@section('content')
<div style="max-width:680px;">

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px;">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="card" style="padding:28px 32px;">

        <form method="POST" action="{{ route('entreprise.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            {{-- ── Logo ── --}}
            <div style="margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid var(--border);">
                <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px;">Logo de l'entreprise</div>

                <div style="display:flex;align-items:center;gap:20px;">
                    {{-- Aperçu actuel --}}
                    <div id="logoPreviewWrap" style="width:80px;height:80px;border-radius:14px;border:1.5px solid var(--border);background:var(--card2);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">
                        @if($entreprise->logo && Storage::disk('public')->exists($entreprise->logo))
                            <img id="logoImg" src="{{ Storage::url($entreprise->logo) }}" alt="{{ $entreprise->nom }}"
                                 style="max-width:100%;max-height:100%;object-fit:contain;padding:6px;">
                        @else
                            <div id="logoInitials" style="font-size:22px;font-weight:800;color:var(--primary);">
                                {{ strtoupper(substr($entreprise->nom,0,2)) }}
                            </div>
                        @endif
                    </div>

                    <div style="flex:1;">
                        <label for="logoInput"
                               style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:var(--card);border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-weight:600;color:var(--text2);cursor:pointer;transition:all .15s;"
                               onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                               onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text2)'">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $entreprise->logo ? 'Changer le logo' : 'Ajouter un logo' }}
                        </label>
                        <input type="file" name="logo" id="logoInput" accept="image/*" style="display:none;" onchange="previewLogo(this)">
                        <div style="font-size:11px;color:var(--muted);margin-top:6px;">PNG, JPG, SVG · max 2 Mo · Apparaît sur les factures PDF</div>
                    </div>
                </div>
            </div>

            {{-- ── Informations ── --}}
            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px;">Informations</div>

            <div style="display:flex;flex-direction:column;gap:14px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px;">Nom de l'entreprise *</label>
                    <input type="text" name="nom" value="{{ old('nom', $entreprise->nom) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px;">Email</label>
                        <input type="email" name="email" value="{{ old('email', $entreprise->email) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px;">Téléphone</label>
                        <input type="tel" name="telephone" value="{{ old('telephone', $entreprise->telephone) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px;">Adresse</label>
                    <input type="text" name="adresse" value="{{ old('adresse', $entreprise->adresse) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px;">Ville</label>
                        <input type="text" name="ville" value="{{ old('ville', $entreprise->ville) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px;">Pays</label>
                        <input type="text" name="pays" value="{{ old('pays', $entreprise->pays) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px;">NINEA / SIRET</label>
                    <input type="text" name="siret" value="{{ old('siret', $entreprise->siret) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                </div>
            </div>

            @if($errors->any())
            <div style="margin-top:14px;padding:10px 14px;background:var(--c-red-bg);border:1px solid var(--c-red-b);border-radius:8px;font-size:12px;color:var(--c-red);">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
            @endif

            <div style="margin-top:22px;padding-top:20px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            </div>
        </form>

    </div>
</div>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.getElementById('logoPreviewWrap');
            const init = document.getElementById('logoInitials');
            const img  = document.getElementById('logoImg');

            if (!img) {
                const newImg = document.createElement('img');
                newImg.id = 'logoImg';
                newImg.style.cssText = 'max-width:100%;max-height:100%;object-fit:contain;padding:6px;';
                if (init) init.style.display = 'none';
                wrap.appendChild(newImg);
                newImg.src = e.target.result;
            } else {
                img.src = e.target.result;
                img.style.display = 'block';
                if (init) init.style.display = 'none';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
