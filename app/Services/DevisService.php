<?php

namespace App\Services;

use App\Models\Devis;
use App\Models\Entreprise;
use App\Models\Produit;
use App\Models\User;

class DevisService
{
    public function creer(Entreprise $entreprise, User $user, array $data): Devis
    {
        $numero = $this->genererNumero($entreprise->id);

        $devis = $entreprise->devis()->create([
            'user_id' => $user->id,
            'numero' => $numero,
            'client_nom' => $data['client_nom'],
            'client_email' => $data['client_email'] ?? null,
            'client_telephone' => $data['client_telephone'] ?? null,
            'client_adresse' => $data['client_adresse'] ?? null,
            'statut' => 'brouillon',
            'sous_total_ht' => 0,
            'tva' => 18,
            'total_ttc' => 0,
            'date_emission' => now()->toDateString(),
            'date_expiration' => $data['date_expiration'],
            'notes' => $data['notes'] ?? null,
        ]);

        $this->synchroniserLignes($devis, $data['lignes']);

        return $devis;
    }

    public function modifier(Devis $devis, array $data): Devis
    {
        $devis->update([
            'client_nom' => $data['client_nom'],
            'client_email' => $data['client_email'] ?? null,
            'client_telephone' => $data['client_telephone'] ?? null,
            'client_adresse' => $data['client_adresse'] ?? null,
            'date_expiration' => $data['date_expiration'],
            'notes' => $data['notes'] ?? null,
        ]);

        $this->synchroniserLignes($devis, $data['lignes']);

        return $devis->fresh();
    }

    private function synchroniserLignes(Devis $devis, array $lignes): void
    {
        $devis->lignes()->delete();

        $sousTotal = 0;

        foreach ($lignes as $ligne) {
            $produit = Produit::findOrFail($ligne['produit_id']);
            $sousLigne = $produit->prix_unitaire * $ligne['quantite'];

            $devis->lignes()->create([
                'produit_id' => $produit->id,
                'quantite' => $ligne['quantite'],
                'prix_unitaire_snapshot' => $produit->prix_unitaire,
                'sous_total' => $sousLigne,
            ]);

            $sousTotal += $sousLigne;
        }

        $totalTTC = $sousTotal * 1.18;

        $devis->update([
            'sous_total_ht' => $sousTotal,
            'total_ttc' => $totalTTC,
        ]);
    }

    private function genererNumero(int $entrepriseId): string
    {
        $annee = now()->year;
        $prefix = sprintf('DEV-%d-', $annee);

        $dernier = Devis::where('entreprise_id', $entrepriseId)
            ->where('numero', 'like', $prefix . '%')
            ->max('numero');

        $compteur = $dernier ? ((int) substr($dernier, strlen($prefix))) + 1 : 1;

        return sprintf('DEV-%d-%04d', $annee, $compteur);
    }
}
