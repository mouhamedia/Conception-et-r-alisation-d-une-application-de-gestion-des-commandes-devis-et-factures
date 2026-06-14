<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Devis;

class CommandeService
{
    public function creerDepuisDevis(Devis $devis): Commande
    {
        $numero = $this->genererNumero($devis->entreprise_id);

        $commande = Commande::create([
            'entreprise_id' => $devis->entreprise_id,
            'devis_id' => $devis->id,
            'numero' => $numero,
            'client_nom' => $devis->client_nom,
            'statut' => 'en_attente',
            'sous_total_ht' => $devis->sous_total_ht,
            'tva' => $devis->tva,
            'total_ttc' => $devis->total_ttc,
            'notes' => $devis->notes,
        ]);

        foreach ($devis->lignes as $ligne) {
            $commande->lignes()->create([
                'produit_id' => $ligne->produit_id,
                'quantite' => $ligne->quantite,
                'prix_unitaire_snapshot' => $ligne->prix_unitaire_snapshot,
                'sous_total' => $ligne->sous_total,
            ]);
        }

        return $commande;
    }

    private function genererNumero(int $entrepriseId): string
    {
        $annee = now()->year;
        $prefix = sprintf('CMD-%d-', $annee);
        $dernier = Commande::where('entreprise_id', $entrepriseId)
            ->where('numero', 'like', $prefix . '%')
            ->max('numero');
        $compteur = $dernier ? ((int) substr($dernier, strlen($prefix))) + 1 : 1;
        return sprintf('CMD-%d-%04d', $annee, $compteur);
    }
}
