<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProduitRequest;
use App\Models\Categorie;
use App\Models\Produit;
use App\Services\EntrepriseContextService;

class ProduitController extends Controller
{
    public function __construct(private EntrepriseContextService $contexte) {}

    public function index(\Illuminate\Http\Request $request)
    {
        $entreprise = $this->contexte->getEntreprise();

        $query = $entreprise->produits()->orderBy('nom');

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        $produits   = $query->paginate(20)->withQueryString();
        $categories = $entreprise->categories()->orderBy('nom')->pluck('nom');

        return view('produits.index', compact('entreprise', 'produits', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', Produit::class);
        $entreprise = $this->contexte->getEntreprise();
        $categories = $entreprise->categories()->orderBy('nom')->pluck('nom');

        return view('produits.create', compact('entreprise', 'categories'));
    }

    public function store(StoreProduitRequest $request)
    {
        $this->authorize('create', Produit::class);
        $entreprise = $this->contexte->getEntreprise();

        $entreprise->produits()->create($request->validated());

        /* Auto-créer la catégorie dans la table si elle n'existe pas encore */
        if ($request->filled('categorie')) {
            $entreprise->categories()->firstOrCreate(['nom' => trim($request->categorie)]);
        }

        return redirect()->route('produits.index')
            ->with('success', 'Produit créé avec succès.');
    }

    public function edit(Produit $produit)
    {
        $this->authorize('update', $produit);
        $this->contexte->verifierAppartenance($produit->entreprise_id);

        $entreprise = $this->contexte->getEntreprise();
        $categories = $entreprise->categories()->orderBy('nom')->pluck('nom');

        return view('produits.edit', compact('produit', 'categories'));
    }

    public function update(StoreProduitRequest $request, Produit $produit)
    {
        $this->authorize('update', $produit);
        $this->contexte->verifierAppartenance($produit->entreprise_id);

        $produit->update($request->validated());

        /* Auto-créer la catégorie si elle est nouvelle */
        if ($request->filled('categorie')) {
            $this->contexte->getEntreprise()
                ->categories()->firstOrCreate(['nom' => trim($request->categorie)]);
        }

        return redirect()->route('produits.index')
            ->with('success', 'Produit mis à jour.');
    }

    public function destroy(Produit $produit)
    {
        $this->authorize('delete', $produit);
        $this->contexte->verifierAppartenance($produit->entreprise_id);

        $produit->delete();

        return redirect()->route('produits.index')
            ->with('success', 'Produit supprimé.');
    }
}
