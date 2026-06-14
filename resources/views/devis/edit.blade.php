@extends('layouts.app')

@section('title', 'Modifier ' . $devis->numero)
@section('page-title', 'Modifier le devis ' . $devis->numero)

@section('content')
<div class="max-w-4xl" x-data="devisForm({{ json_encode($devis->lignes->map(fn($l) => ['produit_id' => (string)$l->produit_id, 'quantite' => $l->quantite, 'prix_unitaire' => (float)$l->prix_unitaire_snapshot, 'sous_total' => (float)$l->sous_total])) }})">
    <div class="bg-white rounded-xl border border-gray-200 p-8">
        <form method="POST" action="{{ route('devis.update', $devis) }}" class="space-y-6" @submit="prepareSubmit">
            @csrf @method('PUT')

            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Informations client</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom du client *</label>
                        <input type="text" name="client_nom" value="{{ old('client_nom', $devis->client_nom) }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="client_email" value="{{ old('client_email', $devis->client_email) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                        <input type="tel" name="client_telephone" value="{{ old('client_telephone', $devis->client_telephone) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date d'expiration *</label>
                        <input type="date" name="date_expiration" value="{{ old('date_expiration', $devis->date_expiration->format('Y-m-d')) }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Produits</h3>
                    <button type="button" @click="ajouterLigne()" class="text-sm text-[#1E3A8A] font-medium hover:underline">
                        + Ajouter une ligne
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(ligne, index) in lignes" :key="index">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <select :name="'lignes[' + index + '][produit_id]'"
                                        x-model="ligne.produit_id"
                                        @change="mettreAJourPrix(index)"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                                    <option value="">Sélectionner un produit</option>
                                    @foreach($produits as $p)
                                    <option value="{{ $p->id }}" data-prix="{{ $p->prix_unitaire }}">
                                        {{ $p->nom }} ({{ number_format($p->prix_unitaire, 0, ',', ' ') }} FCFA)
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-24">
                                <input type="number" :name="'lignes[' + index + '][quantite]'"
                                       x-model.number="ligne.quantite"
                                       @input="calculerTotal()"
                                       min="1" placeholder="Qté"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                            </div>
                            <div class="w-36 text-right">
                                <span class="text-sm font-semibold text-gray-900" x-text="formatMontant(ligne.sous_total)"></span>
                                <p class="text-xs text-gray-400">FCFA</p>
                            </div>
                            <button type="button" @click="supprimerLigne(index)" class="text-gray-400 hover:text-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="mt-4 border-t border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Sous-total HT</span>
                        <span x-text="formatMontant(sousTotal) + ' FCFA'"></span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>TVA (18%)</span>
                        <span x-text="formatMontant(montantTVA) + ' FCFA'"></span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-900 text-base">
                        <span>Total TTC</span>
                        <span x-text="formatMontant(totalTTC) + ' FCFA'"></span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                <textarea name="notes" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">{{ old('notes', $devis->notes) }}</textarea>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-[#1E3A8A] text-white text-sm font-semibold rounded-lg hover:bg-blue-800 transition-colors">
                    Enregistrer les modifications
                </button>
                <a href="{{ route('devis.show', $devis) }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const produitsData = @json($produits->keyBy('id'));

function devisForm(lignesInitiales) {
    return {
        lignes: lignesInitiales.length ? lignesInitiales : [{ produit_id: '', quantite: 1, prix_unitaire: 0, sous_total: 0 }],
        sousTotal: 0,
        montantTVA: 0,
        totalTTC: 0,

        init() { this.calculerTotal(); },

        ajouterLigne() {
            this.lignes.push({ produit_id: '', quantite: 1, prix_unitaire: 0, sous_total: 0 });
        },

        supprimerLigne(index) {
            if (this.lignes.length > 1) {
                this.lignes.splice(index, 1);
                this.calculerTotal();
            }
        },

        mettreAJourPrix(index) {
            const produitId = this.lignes[index].produit_id;
            if (produitId && produitsData[produitId]) {
                this.lignes[index].prix_unitaire = parseFloat(produitsData[produitId].prix_unitaire);
            }
            this.calculerTotal();
        },

        calculerTotal() {
            this.lignes.forEach(l => {
                l.sous_total = l.prix_unitaire * (l.quantite || 0);
            });
            this.sousTotal = this.lignes.reduce((s, l) => s + l.sous_total, 0);
            this.montantTVA = this.sousTotal * 0.18;
            this.totalTTC = this.sousTotal * 1.18;
        },

        formatMontant(v) {
            return new Intl.NumberFormat('fr-FR').format(Math.round(v));
        },

        prepareSubmit() { return true; }
    }
}
</script>
@endpush
@endsection
