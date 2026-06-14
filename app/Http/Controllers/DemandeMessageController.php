<?php

namespace App\Http\Controllers;

use App\Models\DemandeDevis;
use App\Models\DemandeMessage;
use App\Services\EntrepriseContextService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandeMessageController extends Controller
{
    public function __construct(
        private EntrepriseContextService $contexte,
        private NotificationService $notificationService
    ) {}

    public function show(DemandeDevis $demande)
    {
        $entreprise = $this->contexte->getEntreprise();

        /* Seules les deux entreprises concernées peuvent voir */
        if ($demande->entreprise_source_id !== $entreprise->id
            && $demande->entreprise_cible_id !== $entreprise->id) {
            abort(403);
        }

        $demande->load([
            'entrepriseSource',
            'entrepriseCible',
            'user',
            'messages.entreprise',
            'messages.user',
            'devis',
        ]);

        return view('marketplace.conversation', compact('demande', 'entreprise'));
    }

    public function store(Request $request, DemandeDevis $demande)
    {
        $entreprise = $this->contexte->getEntreprise();

        if ($demande->entreprise_source_id !== $entreprise->id
            && $demande->entreprise_cible_id !== $entreprise->id) {
            abort(403);
        }

        $request->validate(['contenu' => 'required|string|max:2000']);

        $msg = DemandeMessage::create([
            'demande_id'    => $demande->id,
            'entreprise_id' => $entreprise->id,
            'user_id'       => Auth::id(),
            'contenu'       => $request->contenu,
        ]);

        $msg->load('entreprise', 'user');

        /* Notifier l'autre partie */
        $autreId = $demande->entreprise_source_id === $entreprise->id
            ? $demande->entreprise_cible_id
            : $demande->entreprise_source_id;

        $this->notificationService->envoyerATous(
            $autreId,
            'message_recu',
            'Nouveau message de ' . $entreprise->nom,
            \Str::limit($request->contenu, 80),
            ['demande_id' => $demande->id]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id'              => $msg->id,
                    'contenu'         => $msg->contenu,
                    'entreprise_nom'  => $msg->entreprise->nom,
                    'user_nom'        => $msg->user->prenom . ' ' . $msg->user->name,
                    'created_at'      => $msg->created_at->format('d/m/Y H:i'),
                    'is_mine'         => true,
                ],
            ]);
        }

        return back()->with('success', 'Message envoyé.');
    }
}
