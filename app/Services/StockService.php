<?php

namespace App\Services;

use App\Models\Commande;

class StockService
{
    public function decrementerStock(Commande $commande): void
    {
        foreach ($commande->lignes as $ligne) {
            $produit = $ligne->produit;
            $produit->decrement('stock_actuel', $ligne->quantite);
        }
    }

    public function restaurerStock(Commande $commande): void
    {
        foreach ($commande->lignes as $ligne) {
            $produit = $ligne->produit;
            $produit->increment('stock_actuel', $ligne->quantite);
        }
    }
}
