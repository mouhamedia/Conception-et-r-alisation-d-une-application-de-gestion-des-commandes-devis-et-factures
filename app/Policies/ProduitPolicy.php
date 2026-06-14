<?php

namespace App\Policies;

use App\Models\Produit;
use App\Models\User;

class ProduitPolicy
{
    public function create(User $user): bool
    {
        $entrepriseId = session('entreprise_id');
        return $entrepriseId && $user->entreprises()->where('entreprise_id', $entrepriseId)->exists();
    }

    public function update(User $user, Produit $produit): bool
    {
        return $user->entreprises()
            ->where('entreprise_id', $produit->entreprise_id)
            ->exists();
    }

    public function delete(User $user, Produit $produit): bool
    {
        return $user->isOwnerOf($produit->entreprise_id);
    }
}
