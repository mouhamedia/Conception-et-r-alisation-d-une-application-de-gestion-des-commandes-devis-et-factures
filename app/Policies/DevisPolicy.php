<?php

namespace App\Policies;

use App\Models\Devis;
use App\Models\User;

class DevisPolicy
{
    public function create(User $user): bool
    {
        $entrepriseId = session('entreprise_id');
        return $entrepriseId && $user->entreprises()->where('entreprise_id', $entrepriseId)->exists();
    }

    public function view(User $user, Devis $devis): bool
    {
        return $user->entreprises()->where('entreprise_id', $devis->entreprise_id)->exists();
    }

    public function update(User $user, Devis $devis): bool
    {
        if (!in_array($devis->statut, ['brouillon'])) {
            return false;
        }
        return $user->entreprises()->where('entreprise_id', $devis->entreprise_id)->exists();
    }

    public function validate(User $user, Devis $devis): bool
    {
        // Autoriser aussi "accepte" sans commande (récupération après échec)
        $statutOk = $devis->statut === 'envoye'
            || ($devis->statut === 'accepte' && $devis->commande === null);

        return $statutOk && $user->isOwnerOf($devis->entreprise_id);
    }

    public function delete(User $user, Devis $devis): bool
    {
        if (!in_array($devis->statut, ['brouillon', 'refuse'])) {
            return false;
        }
        return $user->isOwnerOf($devis->entreprise_id);
    }
}
