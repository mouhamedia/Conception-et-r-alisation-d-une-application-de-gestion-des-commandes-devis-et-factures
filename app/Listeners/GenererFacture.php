<?php

namespace App\Listeners;

use App\Events\CommandeCreee;
use App\Events\FactureGeneree;
use App\Services\FactureService;

class GenererFacture
{
    public function __construct(private FactureService $factureService) {}

    public function handle(CommandeCreee $event): void
    {
        $commande = $event->commande;

        // Guard : évite de créer une facture en double
        if ($commande->facture()->exists()) {
            return;
        }

        $facture = $this->factureService->creerDepuisCommande($commande);

        event(new FactureGeneree($facture));
    }
}
