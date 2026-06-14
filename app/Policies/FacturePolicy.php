<?php

namespace App\Policies;

use App\Models\Facture;
use App\Models\User;

class FacturePolicy
{
    public function view(User $user, Facture $facture): bool
    {
        return $user->entreprises()->where('entreprise_id', $facture->entreprise_id)->exists();
    }

    public function envoyer(User $user, Facture $facture): bool
    {
        return $user->entreprises()->where('entreprise_id', $facture->entreprise_id)->exists();
    }

    public function enregistrerPaiement(User $user, Facture $facture): bool
    {
        return $user->entreprises()->where('entreprise_id', $facture->entreprise_id)->exists();
    }
}
