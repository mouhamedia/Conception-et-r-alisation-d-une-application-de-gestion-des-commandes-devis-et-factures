<?php

namespace App\Http\Controllers;

use App\Models\DemandeDevis;
use App\Models\Entreprise;
use App\Services\EntrepriseContextService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketplaceController extends Controller
{
    public function __construct(
        private EntrepriseContextService $contexte,
        private NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $entreprise = $this->contexte->getEntreprise();
        $query = $request->get('q', '');

        $entreprises = Entreprise::where('id', '!=', $entreprise->id)
            ->when($query, fn($q) => $q->where(function ($sq) use ($query) {
                $sq->where('nom', 'like', "%{$query}%")
                   ->orWhere('ville', 'like', "%{$query}%")
                   ->orWhere('email', 'like', "%{$query}%");
            }))
            ->orderBy('nom')
            ->paginate(12)
            ->withQueryString();

        $mesDemandes = DemandeDevis::where('entreprise_source_id', $entreprise->id)
            ->with('entrepriseCible')
            ->latest()
            ->limit(5)
            ->get();

        return view('marketplace.index', compact('entreprise', 'entreprises', 'query', 'mesDemandes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entreprise_cible_id' => 'required|exists:entreprises,id',
            'description'         => 'required|string|min:10|max:1000',
            'budget'              => 'nullable|numeric|min:0',
        ]);

        $entreprise = $this->contexte->getEntreprise();

        if ((int) $request->entreprise_cible_id === $entreprise->id) {
            return back()->with('error', 'Vous ne pouvez pas envoyer une demande à votre propre entreprise.');
        }

        $dejaSoumise = DemandeDevis::where('entreprise_source_id', $entreprise->id)
            ->where('entreprise_cible_id', $request->entreprise_cible_id)
            ->where('statut', 'en_attente')
            ->exists();

        if ($dejaSoumise) {
            return back()->with('error', 'Vous avez déjà une demande en attente auprès de cette entreprise.');
        }

        $demande = DemandeDevis::create([
            'entreprise_source_id' => $entreprise->id,
            'entreprise_cible_id'  => $request->entreprise_cible_id,
            'user_id'              => Auth::id(),
            'description'          => $request->description,
            'budget'               => $request->budget ?: null,
            'statut'               => 'en_attente',
        ]);

        $cible = Entreprise::findOrFail($request->entreprise_cible_id);
        $this->notificationService->envoyerATous(
            $cible->id,
            'demande_devis',
            'Nouvelle demande de devis',
            $entreprise->nom . ' vous a envoyé une demande de devis : ' . \Str::limit($request->description, 80),
            ['demande_id' => $demande->id]
        );

        return back()->with('success', 'Demande envoyée à ' . $cible->nom . ' avec succès.');
    }

    public function demandes(Request $request)
    {
        $entreprise = $this->contexte->getEntreprise();

        $recues = DemandeDevis::where('entreprise_cible_id', $entreprise->id)
            ->with(['entrepriseSource', 'user', 'devis'])
            ->latest()
            ->paginate(15);

        $envoyees = DemandeDevis::where('entreprise_source_id', $entreprise->id)
            ->with(['entrepriseCible', 'devis'])
            ->latest()
            ->paginate(15);

        return view('marketplace.demandes', compact('entreprise', 'recues', 'envoyees'));
    }

    public function accepter(Request $request, DemandeDevis $demande)
    {
        $entreprise = $this->contexte->getEntreprise();

        if ($demande->entreprise_cible_id !== $entreprise->id) {
            abort(403);
        }

        if ($demande->statut !== 'en_attente') {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $demande->update(['statut' => 'acceptee']);

        $this->notificationService->envoyerATous(
            $demande->entreprise_source_id,
            'demande_acceptee',
            'Demande de devis acceptée',
            $entreprise->nom . ' a accepté votre demande de devis. Un devis va être préparé.',
            ['demande_id' => $demande->id]
        );

        return redirect()->route('devis.create', ['demande_id' => $demande->id])
            ->with('success', 'Demande acceptée. Créez le devis pour ' . $demande->entrepriseSource->nom . '.');
    }

    public function refuser(DemandeDevis $demande)
    {
        $entreprise = $this->contexte->getEntreprise();

        if ($demande->entreprise_cible_id !== $entreprise->id) {
            abort(403);
        }

        if ($demande->statut !== 'en_attente') {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $demande->update(['statut' => 'refusee']);

        $this->notificationService->envoyerATous(
            $demande->entreprise_source_id,
            'demande_refusee',
            'Demande de devis refusée',
            $entreprise->nom . ' n\'a pas pu donner suite à votre demande de devis.',
            ['demande_id' => $demande->id]
        );

        return back()->with('success', 'Demande refusée.');
    }
}
