<?php

namespace App\Policies;

use App\Models\Entreprise;
use App\Models\User;

class EntreprisePolicy
{
    public function update(User $user, Entreprise $entreprise): bool
    {
        return $user->isOwnerOf($entreprise->id);
    }

    public function delete(User $user, Entreprise $entreprise): bool
    {
        return $user->isOwnerOf($entreprise->id);
    }

    public function viewTeam(User $user, Entreprise $entreprise): bool
    {
        return $user->isOwnerOf($entreprise->id);
    }
}
