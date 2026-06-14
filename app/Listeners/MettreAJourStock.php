<?php

namespace App\Listeners;

use App\Events\CommandeCreee;
use App\Services\StockService;

class MettreAJourStock
{
    public function __construct(private StockService $stockService) {}

    public function handle(CommandeCreee $event): void
    {
        $commande = $event->commande;
        $commande->load('lignes.produit');

        $this->stockService->decrementerStock($commande);
    }
}
