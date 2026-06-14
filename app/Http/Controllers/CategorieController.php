<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Services\EntrepriseContextService;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function __construct(private EntrepriseContextService $contexte) {}

    public function index()
    {
        $entreprise = $this->contexte->getEntreprise();
        $categories = $entreprise->categories()
            ->withCount(['produits' => fn($q) => $q->where('entreprise_id', $entreprise->id)])
            ->orderBy('nom')
            ->get();

        return view('categories.index', compact('entreprise', 'categories'));
    }

    public function store(Request $request)
    {
        $entreprise = $this->contexte->getEntreprise();

        $request->validate([
            'nom' => ['required', 'string', 'max:100'],
        ]);

        $nom = trim($request->nom);

        $existe = $entreprise->categories()->where('nom', $nom)->first();
        if ($existe) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cette catégorie existe déjà.'], 422);
            }
            return back()->with('error', 'Cette catégorie existe déjà.');
        }

        $categorie = $entreprise->categories()->create(['nom' => $nom]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'categorie' => ['id' => $categorie->id, 'nom' => $categorie->nom],
                'message'   => "Catégorie « {$nom} » créée.",
            ]);
        }

        return back()->with('success', "Catégorie « {$nom} » créée.");
    }

    public function update(Request $request, Categorie $categorie)
    {
        $this->contexte->verifierAppartenance($categorie->entreprise_id);

        $request->validate(['nom' => ['required', 'string', 'max:100']]);
        $ancien = $categorie->nom;
        $nouveau = trim($request->nom);

        if ($ancien === $nouveau) {
            return $request->ajax()
                ? response()->json(['success' => true, 'message' => 'Aucun changement.'])
                : back();
        }

        /* Renommer sur tous les produits */
        $categorie->entreprise->produits()
            ->where('categorie', $ancien)
            ->update(['categorie' => $nouveau]);

        $categorie->update(['nom' => $nouveau]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'nom' => $nouveau, 'message' => "Renommée en « {$nouveau} »."]);
        }

        return back()->with('success', "Catégorie renommée en « {$nouveau} ».");
    }

    public function destroy(Request $request, Categorie $categorie)
    {
        $this->contexte->verifierAppartenance($categorie->entreprise_id);

        /* Retirer la catégorie des produits concernés */
        $categorie->entreprise->produits()
            ->where('categorie', $categorie->nom)
            ->update(['categorie' => null]);

        $categorie->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Catégorie supprimée."]);
        }

        return back()->with('success', 'Catégorie supprimée.');
    }

    /* Endpoint JSON pour le combobox produit */
    public function liste()
    {
        $entreprise = $this->contexte->getEntreprise();
        $categories = $entreprise->categories()->orderBy('nom')->pluck('nom');
        return response()->json($categories);
    }
}
