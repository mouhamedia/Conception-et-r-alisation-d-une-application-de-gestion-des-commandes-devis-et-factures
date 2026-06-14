<?php

namespace App\Http\Controllers;

use App\Services\EntrepriseContextService;
use App\Services\IAService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private EntrepriseContextService $contexte,
        private IAService $iaService
    ) {}

    public function index()
    {
        $entreprise = $this->contexte->getEntreprise();
        $user = Auth::user();
        $role = $user->getRoleInEntreprise($entreprise->id);

        $stats = [
            'devis_total'          => $entreprise->devis()->count(),
            'devis_en_attente'     => $entreprise->devis()->whereIn('statut', ['brouillon', 'envoye'])->count(),
            'devis_expirent_bientot' => $entreprise->devis()
                ->where('statut', 'envoye')
                ->whereBetween('date_expiration', [now(), now()->addDays(7)])
                ->count(),
            'commandes_en_cours'   => $entreprise->commandes()->whereIn('statut', ['en_attente', 'en_cours'])->count(),
            'factures_impayees'    => $entreprise->factures()->whereIn('statut', ['envoyee', 'en_retard'])->count(),
            'factures_montant_du'  => $entreprise->factures()
                ->join('commandes', 'factures.commande_id', '=', 'commandes.id')
                ->whereIn('factures.statut', ['envoyee', 'en_retard'])
                ->sum(DB::raw('commandes.total_ttc - factures.montant_paye')),
            'chiffre_affaires_mois' => $entreprise->factures()
                ->join('commandes', 'factures.commande_id', '=', 'commandes.id')
                ->where('factures.statut', 'payee')
                ->whereMonth('factures.updated_at', now()->month)
                ->sum('commandes.total_ttc'),
            'chiffre_affaires_mois_precedent' => $entreprise->factures()
                ->join('commandes', 'factures.commande_id', '=', 'commandes.id')
                ->where('factures.statut', 'payee')
                ->whereMonth('factures.updated_at', now()->subMonth()->month)
                ->sum('commandes.total_ttc'),
        ];

        // Évolution mensuelle (6 mois) pour le graphique
        $chartData = $this->getChartData($entreprise->id);

        // Commandes récentes
        $commandes_recentes = $entreprise->commandes()
            ->with('facture')
            ->latest()
            ->limit(6)
            ->get();

        // Devis récents
        $devis_recents = $entreprise->devis()
            ->latest()
            ->limit(6)
            ->get();

        // Suggestion IA (ne bloque pas si FastAPI est hors-ligne)
        $suggestion_ia = $this->iaService->getPredictions($entreprise->id);

        // Entreprises de l'utilisateur (switcher)
        $mes_entreprises = $user->entreprises()->withPivot('role')->get();

        return view('dashboard.index', compact(
            'entreprise', 'role', 'stats', 'chartData',
            'commandes_recentes', 'devis_recents', 'suggestion_ia', 'mes_entreprises', 'user'
        ));
    }

    private function getChartData(int $entrepriseId): array
    {
        $mois = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois[] = [
                'label' => $date->isoFormat('MMM'),
                'factures' => DB::table('factures')
                    ->join('commandes', 'factures.commande_id', '=', 'commandes.id')
                    ->where('factures.entreprise_id', $entrepriseId)
                    ->where('factures.statut', 'payee')
                    ->whereYear('factures.updated_at', $date->year)
                    ->whereMonth('factures.updated_at', $date->month)
                    ->sum('commandes.total_ttc'),
                'devis' => DB::table('devis')
                    ->where('entreprise_id', $entrepriseId)
                    ->where('statut', 'accepte')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total_ttc'),
            ];
        }
        return $mois;
    }
}
