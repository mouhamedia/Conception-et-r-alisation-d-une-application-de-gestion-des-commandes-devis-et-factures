<?php

namespace App\Listeners;

use App\Events\CommandeCreee;
use App\Events\DevisValide;
use App\Services\CommandeService;

class GenererCommande
{
    public function __construct(private CommandeService $commandeService) {}

    public function handle(DevisValide $event): void
    {
        $devis = $event->devis;

        // Guard : évite de créer une commande en double si l'événement est déclenché plusieurs fois
        if ($devis->commande()->exists()) {
            return;
        }

        $devis->load('lignes.produit');

        $commande = $this->commandeService->creerDepuisDevis($devis);

        event(new CommandeCreee($commande));
    }
}
