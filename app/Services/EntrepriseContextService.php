<?php

namespace App\Services;

use App\Models\Entreprise;
use Illuminate\Support\Facades\Auth;

class EntrepriseContextService
{
    public function getEntreprise(): Entreprise
    {
        $entrepriseId = session('entreprise_id');

        if (!$entrepriseId) {
            abort(redirect()->route('entreprise.select'));
        }

        return Entreprise::findOrFail($entrepriseId);
    }

    public function verifierAppartenance(int $entrepriseId): void
    {
        if ((int) session('entreprise_id') !== $entrepriseId) {
            abort(403, 'Cette ressource n\'appartient pas à votre entreprise active.');
        }
    }

    public function getRoleActuel(): ?string
    {
        $entrepriseId = session('entreprise_id');
        if (!$entrepriseId) {
            return null;
        }

        return Auth::user()->getRoleInEntreprise((int) $entrepriseId);
    }

    public function isOwner(): bool
    {
        return $this->getRoleActuel() === 'owner';
    }
}
