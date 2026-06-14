<?php

namespace App\Listeners;

use App\Events\CommandeCreee;
use App\Services\NotificationService;

class EnvoyerNotification
{
    public function __construct(private NotificationService $notificationService) {}

    public function handle(CommandeCreee $event): void
    {
        $commande = $event->commande;
        $entreprise = $commande->entreprise;

        $this->notificationService->envoyerATous(
            $commande->entreprise_id,
            'commande_creee',
            'Nouvelle commande créée',
            "La commande {$commande->numero} a été générée automatiquement depuis un devis accepté.",
            ['commande_id' => $commande->id, 'numero' => $commande->numero]
        );
    }
}
