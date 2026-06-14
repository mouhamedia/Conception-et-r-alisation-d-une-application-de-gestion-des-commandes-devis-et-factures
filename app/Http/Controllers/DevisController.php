<?php

namespace App\Http\Controllers;

use App\Events\DevisValide;
use App\Http\Requests\StoreDevisRequest;
use App\Http\Requests\ValidateDevisRequest;
use App\Models\Devis;
use App\Models\User;
use App\Services\DevisService;
use App\Services\EntrepriseContextService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class DevisController extends Controller
{
    public function __construct(
        private EntrepriseContextService $contexte,
        private DevisService $devisService,
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        $entreprise = $this->contexte->getEntreprise();
        $devis = $entreprise->devis()
            ->with('user')
            ->latest()
            ->paginate(15);

        return view('devis.index', compact('entreprise', 'devis'));
    }

    public function create()
    {
        $this->authorize('create', Devis::class);
        $entreprise = $this->contexte->getEntreprise();
        $produits = $entreprise->produits()->where('actif', true)->get();

        return view('devis.create', compact('entreprise', 'produits'));
    }

    public function store(StoreDevisRequest $request)
    {
        $this->authorize('create', Devis::class);
        $entreprise = $this->contexte->getEntreprise();

        $devis = $this->devisService->creer($entreprise, Auth::user(), $request->validated());

        return redirect()->route('devis.show', $devis)
            ->with('success', "Devis {$devis->numero} créé avec succès.");
    }

    public function show(Devis $devis)
    {
        $this->authorize('view', $devis);
        $this->contexte->verifierAppartenance($devis->entreprise_id);

        $devis->load('lignes.produit', 'user', 'commande');

        return view('devis.show', compact('devis'));
    }

    public function edit(Devis $devis)
    {
        $this->authorize('update', $devis);
        $this->contexte->verifierAppartenance($devis->entreprise_id);

        $entreprise = $this->contexte->getEntreprise();
        $produits = $entreprise->produits()->where('actif', true)->get();
        $devis->load('lignes.produit');

        return view('devis.edit', compact('devis', 'produits'));
    }

    public function update(StoreDevisRequest $request, Devis $devis)
    {
        $this->authorize('update', $devis);
        $this->contexte->verifierAppartenance($devis->entreprise_id);

        $this->devisService->modifier($devis, $request->validated());

        return redirect()->route('devis.show', $devis)
            ->with('success', 'Devis mis à jour.');
    }

    public function valider(Devis $devis)
    {
        if (request()->isMethod('GET')) {
            return redirect()->route('devis.show', $devis);
        }

        $this->authorize('validate', $devis);
        $this->contexte->verifierAppartenance($devis->entreprise_id);

        if ($devis->statut !== 'accepte') {
            $devis->update(['statut' => 'accepte']);
        }

        // Ne déclenche l'événement que si la commande n'existe pas encore
        if (!$devis->commande()->exists()) {
            event(new DevisValide($devis));
        }

        return redirect()->route('devis.show', $devis)
            ->with('success', 'Devis validé ! Commande et facture générées automatiquement.');
    }

    public function envoyer(Devis $devis)
    {
        $this->authorize('update', $devis);
        $this->contexte->verifierAppartenance($devis->entreprise_id);

        $devis->update(['statut' => 'envoye']);
        $devis->load('entreprise');

        $this->notificationService->envoyerATous(
            $devis->entreprise_id,
            'devis_envoye',
            'Devis envoyé — ' . $devis->numero,
            'Le devis ' . $devis->numero . ' pour ' . $devis->client_nom . ' a été marqué comme envoyé. Montant : ' . number_format($devis->total_ttc, 0, ',', ' ') . ' DZD.',
            ['devis_id' => $devis->id]
        );

        // Notifier le client s'il a un compte dans le système
        if ($devis->client_email) {
            $clientUser = User::where('email', $devis->client_email)->first();
            if ($clientUser && $clientUser->id !== Auth::id()) {
                $clientEntreprise = $clientUser->entreprises()->first();
                if ($clientEntreprise) {
                    $expediteur = $devis->entreprise->nom ?? Auth::user()->nom_complet;
                    $this->notificationService->envoyer(
                        $clientUser,
                        $clientEntreprise->id,
                        'devis_recu',
                        'Devis reçu — ' . $devis->numero,
                        'Vous avez reçu un devis de ' . $expediteur . '. Montant : ' . number_format($devis->total_ttc, 0, ',', ' ') . ' DZD.',
                        ['devis_id' => $devis->id]
                    );
                }
            }
        }

        return redirect()->route('devis.show', $devis)
            ->with('success', 'Devis marqué comme envoyé.');
    }

    public function afficherClient(Devis $devis)
    {
        if (Auth::user()->email !== $devis->client_email) {
            abort(403, 'Ce devis ne vous est pas adressé.');
        }
        $devis->load('lignes.produit', 'entreprise');
        return view('devis.client', compact('devis'));
    }

    public function accepterClient(Devis $devis)
    {
        if (Auth::user()->email !== $devis->client_email) {
            abort(403);
        }
        if ($devis->statut !== 'envoye') {
            return redirect()->route('devis.client', $devis)
                ->with('error', 'Ce devis ne peut plus être modifié.');
        }

        $devis->update(['statut' => 'accepte']);
        $devis->load('entreprise');

        // Ne déclenche l'événement que si la commande n'existe pas encore
        if (!$devis->commande()->exists()) {
            event(new DevisValide($devis));
        }

        $this->notificationService->envoyerATous(
            $devis->entreprise_id,
            'devis_accepte',
            'Devis accepté — ' . $devis->numero,
            $devis->client_nom . ' a accepté le devis ' . $devis->numero . '. La commande a été générée automatiquement.',
            ['devis_id' => $devis->id]
        );

        return redirect()->route('notifications.index')
            ->with('success', 'Vous avez accepté le devis ' . $devis->numero . '. La commande a été créée.');
    }

    public function refuserClient(Devis $devis)
    {
        if (Auth::user()->email !== $devis->client_email) {
            abort(403);
        }
        if ($devis->statut !== 'envoye') {
            return redirect()->route('devis.client', $devis)
                ->with('error', 'Ce devis ne peut plus être modifié.');
        }

        $devis->update(['statut' => 'refuse']);
        $devis->load('entreprise');

        $this->notificationService->envoyerATous(
            $devis->entreprise_id,
            'devis_refuse',
            'Devis refusé — ' . $devis->numero,
            $devis->client_nom . ' a refusé le devis ' . $devis->numero . '.',
            ['devis_id' => $devis->id]
        );

        return redirect()->route('notifications.index')
            ->with('success', 'Vous avez refusé le devis ' . $devis->numero . '.');
    }

    public function destroy(Devis $devis)
    {
        $this->authorize('delete', $devis);
        $this->contexte->verifierAppartenance($devis->entreprise_id);

        $devis->delete();

        return redirect()->route('devis.index')
            ->with('success', 'Devis supprimé.');
    }
}
