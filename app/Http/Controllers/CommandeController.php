<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Services\EntrepriseContextService;

class CommandeController extends Controller
{
    public function __construct(private EntrepriseContextService $contexte) {}

    public function index()
    {
        $entreprise = $this->contexte->getEntreprise();
        $commandes = $entreprise->commandes()
            ->with('devis', 'facture')
            ->latest()
            ->paginate(15);

        return view('commandes.index', compact('entreprise', 'commandes'));
    }

    public function show(Commande $commande)
    {
        $this->contexte->verifierAppartenance($commande->entreprise_id);

        $commande->load('lignes.produit', 'devis', 'facture');

        return view('commandes.show', compact('commande'));
    }

    public function updateStatut(Commande $commande, string $statut)
    {
        $this->contexte->verifierAppartenance($commande->entreprise_id);

        $statutsValides = ['en_attente', 'en_cours', 'livree', 'annulee'];

        if (!in_array($statut, $statutsValides)) {
            abort(422, 'Statut invalide.');
        }

        $commande->update(['statut' => $statut]);

        return back()->with('success', "Commande mise à jour : {$statut}.");
    }
}
