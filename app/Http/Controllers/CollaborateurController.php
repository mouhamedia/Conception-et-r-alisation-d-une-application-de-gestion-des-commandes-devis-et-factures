<?php

namespace App\Http\Controllers;

use App\Services\EntrepriseContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollaborateurController extends Controller
{
    public function __construct(private EntrepriseContextService $contexte) {}

    public function retirerMembre(int $userId)
    {
        $entreprise = $this->contexte->getEntreprise();
        $this->authorize('viewTeam', $entreprise);

        if ($userId === Auth::id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas vous retirer vous-même.']);
        }

        $entreprise->users()->detach($userId);

        return back()->with('success', 'Collaborateur retiré de l\'équipe.');
    }

    public function changerRole(Request $request, int $userId)
    {
        $request->validate(['role' => 'required|in:owner,employee']);
        $entreprise = $this->contexte->getEntreprise();
        $this->authorize('viewTeam', $entreprise);

        $entreprise->users()->updateExistingPivot($userId, ['role' => $request->role]);

        return back()->with('success', 'Rôle mis à jour.');
    }
}
