<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Facture;

class FactureService
{
    public function creerDepuisCommande(Commande $commande): Facture
    {
        $numero = $this->genererNumero($commande->entreprise_id);

        return Facture::create([
            'entreprise_id' => $commande->entreprise_id,
            'commande_id' => $commande->id,
            'numero' => $numero,
            'statut' => 'brouillon',
            'montant_paye' => 0,
            'date_echeance' => now()->addDays(30)->toDateString(),
        ]);
    }

    private function genererNumero(int $entrepriseId): string
    {
        $annee = now()->year;
        $compteur = Facture::where('entreprise_id', $entrepriseId)
            ->whereYear('created_at', $annee)
            ->count() + 1;

        return sprintf('FAC-%d-%04d', $annee, $compteur);
    }
}
