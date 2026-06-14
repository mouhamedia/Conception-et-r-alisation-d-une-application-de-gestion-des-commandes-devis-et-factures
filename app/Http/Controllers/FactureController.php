<?php

namespace App\Http\Controllers;

use App\Mail\FactureMail;
use App\Models\Facture;
use App\Services\EntrepriseContextService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class FactureController extends Controller
{
    public function __construct(private EntrepriseContextService $contexte) {}

    public function index()
    {
        $entreprise = $this->contexte->getEntreprise();
        $factures = $entreprise->factures()
            ->with('commande')
            ->latest()
            ->paginate(15);

        return view('factures.index', compact('entreprise', 'factures'));
    }

    public function show(Facture $facture)
    {
        $this->authorize('view', $facture);
        $this->contexte->verifierAppartenance($facture->entreprise_id);

        $facture->load('commande.lignes.produit', 'entreprise');

        return view('factures.show', compact('facture'));
    }

    public function pdf(Facture $facture)
    {
        $this->authorize('view', $facture);
        $this->contexte->verifierAppartenance($facture->entreprise_id);

        $facture->load('commande.lignes.produit', 'entreprise');

        $pdf = Pdf::loadView('factures.pdf', compact('facture'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("facture-{$facture->numero}.pdf");
    }

    /* Téléchargement public via lien signé (sans authentification) */
    public function telechargerPublic(Facture $facture)
    {
        $facture->load('commande.lignes.produit', 'entreprise');

        $pdf = Pdf::loadView('factures.pdf', compact('facture'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("facture-{$facture->numero}.pdf");
    }

    public function envoyer(Request $request, Facture $facture)
    {
        $this->authorize('envoyer', $facture);
        $this->contexte->verifierAppartenance($facture->entreprise_id);

        if ($facture->statut !== 'brouillon') {
            $err = 'Cette facture a déjà été envoyée.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $err], 422)
                : back()->with('error', $err);
        }

        $facture->load('commande.lignes.produit', 'entreprise');

        $downloadUrl = URL::signedRoute(
            'factures.telecharger-public',
            ['facture' => $facture->id],
            now()->addDays(30)
        );

        $clientEmail = $request->input('client_email') ?: $facture->commande?->client_email;
        $msg = "Facture {$facture->numero} marquée comme envoyée.";

        if ($clientEmail && filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($clientEmail)->send(new FactureMail($facture, $downloadUrl));
                $msg = "Facture {$facture->numero} envoyée par email à {$clientEmail}.";
            } catch (\Exception $e) {
                $facture->update(['statut' => 'envoyee']);
                $err = "Erreur email : {$e->getMessage()}";
                return $request->ajax()
                    ? response()->json(['success' => false, 'message' => $err], 500)
                    : back()->with('error', $err);
            }
        }

        $facture->update(['statut' => 'envoyee']);

        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('success', $msg);
    }

    /* Renvoyer l'email (facture déjà envoyée ou payée) */
    public function renvoyerEmail(Request $request, Facture $facture)
    {
        $this->authorize('view', $facture);
        $this->contexte->verifierAppartenance($facture->entreprise_id);

        $request->validate(['client_email' => 'required|email']);

        $facture->load('commande.lignes.produit', 'entreprise');

        $downloadUrl = URL::signedRoute(
            'factures.telecharger-public',
            ['facture' => $facture->id],
            now()->addDays(30)
        );

        try {
            Mail::to($request->client_email)->send(new FactureMail($facture, $downloadUrl));
        } catch (\Exception $e) {
            $err = "Erreur d'envoi : {$e->getMessage()}";
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $err], 500)
                : back()->with('error', $err);
        }

        $msg = "Facture renvoyée à {$request->client_email}.";
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('success', $msg);
    }

    public function enregistrerPaiement(Request $request, Facture $facture)
    {
        $this->authorize('enregistrerPaiement', $facture);
        $this->contexte->verifierAppartenance($facture->entreprise_id);

        $request->validate([
            'montant_paye' => 'required|numeric|min:0.01|max:' . $facture->montant_restant,
        ]);

        $nouveauMontant = (float) $facture->montant_paye + (float) $request->montant_paye;
        $totalTTC = (float) $facture->commande->total_ttc;

        $statut  = $nouveauMontant >= $totalTTC ? 'payee' : 'envoyee';
        $payeeAt = $nouveauMontant >= $totalTTC ? now() : null;

        $facture->update([
            'montant_paye' => $nouveauMontant,
            'statut'       => $statut,
            'payee_at'     => $payeeAt,
        ]);

        return back()->with('success', 'Paiement enregistré avec succès.');
    }
}
