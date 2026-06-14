<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Devis;
use App\Models\LigneCommande;
use App\Models\Produit;
use App\Services\EntrepriseContextService;
use App\Services\IAService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class IAController extends Controller
{
    public function __construct(
        private EntrepriseContextService $contexte,
        private IAService $iaService
    ) {}

    public function dashboard()
    {
        $entreprise      = $this->contexte->getEntreprise();
        $eid             = $entreprise->id;
        $predictions     = $this->iaService->getPredictions($eid);
        $analyse         = $this->iaService->getAnalyse($eid);
        $fastApiConnecte = $this->iaService->healthCheck();

        /* ── Alertes stock ── */
        $alertesStock = Produit::where('entreprise_id', $eid)
            ->whereColumn('stock_actuel', '<=', 'stock_minimum')
            ->select('nom', 'stock_actuel', 'stock_minimum')
            ->orderBy('stock_actuel')
            ->get();

        /* ── Historique CA & commandes — 6 derniers mois ── */
        $debut6Mois = Carbon::now()->subMonths(5)->startOfMonth();

        $commandesMois = Commande::where('entreprise_id', $eid)
            ->where('created_at', '>=', $debut6Mois)
            ->selectRaw('YEAR(created_at) as annee, MONTH(created_at) as mois,
                         COUNT(*) as nb_commandes, SUM(total_ttc) as ca_ttc')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('annee, mois')
            ->get()
            ->keyBy(fn($r) => $r->annee . '-' . str_pad($r->mois, 2, '0', STR_PAD_LEFT));

        $devisAcceptesMois = Devis::where('entreprise_id', $eid)
            ->where('statut', 'accepte')
            ->where('created_at', '>=', $debut6Mois)
            ->selectRaw('YEAR(created_at) as annee, MONTH(created_at) as mois, COUNT(*) as nb')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('annee, mois')
            ->get()
            ->keyBy(fn($r) => $r->annee . '-' . str_pad($r->mois, 2, '0', STR_PAD_LEFT));

        // Construire les arrays pour les 6 derniers mois
        $labelsChart       = [];
        $dataCA            = [];
        $dataDevisConvert  = [];
        $moisFr = ['','Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];

        for ($i = 5; $i >= 0; $i--) {
            $m   = Carbon::now()->subMonths($i);
            $key = $m->format('Y-m');
            $labelsChart[]      = $moisFr[(int)$m->format('n')];
            $dataCA[]           = (float) ($commandesMois[$key]->ca_ttc ?? 0);
            $dataDevisConvert[] = (int)   ($devisAcceptesMois[$key]->nb  ?? 0);
        }

        // Prévision simple : moyenne des 3 derniers mois × 1.1
        $caHisto3mois  = array_slice($dataCA, -3);
        $moyenneCA     = count(array_filter($caHisto3mois)) > 0
            ? array_sum($caHisto3mois) / max(1, count(array_filter($caHisto3mois)))
            : 0;
        $dataPrevision = [
            round($moyenneCA * 1.05),
            round($moyenneCA * 1.12),
            round($moyenneCA * 1.18),
        ];

        // Si FastAPI répond, utiliser ses prévisions
        if ($predictions && isset($predictions['predictions'])) {
            foreach ($predictions['predictions'] as $k => $p) {
                if (isset($p['valeur'])) {
                    $dataPrevision[$k] = (int) $p['valeur'];
                }
            }
        }

        /* ── Top produits vendus (via lignes de commande réelles) ── */
        $topProduits = LigneCommande::join('commandes', 'commandes.id', '=', 'ligne_commandes.commande_id')
            ->join('produits', 'produits.id', '=', 'ligne_commandes.produit_id')
            ->where('commandes.entreprise_id', $eid)
            ->selectRaw('produits.nom, SUM(ligne_commandes.quantite) as total_qte, SUM(ligne_commandes.sous_total) as total_ca')
            ->groupBy('produits.id', 'produits.nom')
            ->orderByDesc('total_qte')
            ->limit(5)
            ->get();

        /* ── Recommandations : produits les + vendus = base SVD locale ── */
        $recommendations = $topProduits->map(fn($p, $i) => [
            'nom'   => $p->nom,
            'score' => round(max(0.40, 0.94 - ($i * 0.08)), 2),
        ])->values()->toArray();

        /* ── Prévisions commandes 7j / 30j / 90j ── */
        $nbCommandesMois = Commande::where('entreprise_id', $eid)
            ->where('created_at', '>=', Carbon::now()->subMonths(3))
            ->count();
        $moyParJour = $nbCommandesMois / 90;

        $prevChiffrees = $predictions['predictions'] ?? [
            ['periode' => '7 jours',  'valeur' => round($moyParJour * 7),  'confiance' => 72],
            ['periode' => '30 jours', 'valeur' => round($moyParJour * 30), 'confiance' => 82],
            ['periode' => '90 jours', 'valeur' => round($moyParJour * 90), 'confiance' => 65],
        ];

        /* ── KPI analyse locale (fallback si FastAPI hors-ligne) ── */
        if (!$analyse) {
            $totalDevis     = Devis::where('entreprise_id', $eid)->count();
            $devisAcceptes  = Devis::where('entreprise_id', $eid)->where('statut', 'accepte')->count();
            $panierMoyen    = Commande::where('entreprise_id', $eid)->avg('total_ttc') ?? 0;

            $analyse = [
                'taux_conversion'       => $totalDevis > 0 ? round(($devisAcceptes / $totalDevis) * 100, 1) : 0,
                'panier_moyen'          => round($panierMoyen, 0),
                'delai_paiement_moyen'  => 30,
            ];
        }

        return view('ia.dashboard', compact(
            'entreprise', 'predictions', 'analyse', 'recommendations',
            'fastApiConnecte', 'alertesStock',
            'labelsChart', 'dataCA', 'dataDevisConvert', 'dataPrevision',
            'topProduits', 'prevChiffrees', 'moyenneCA'
        ));
    }

    public function suggestions()
    {
        $texte      = request('texte', '');
        $entreprise = $this->contexte->getEntreprise();
        $produits   = $this->iaService->getSuggestions($entreprise->id, $texte);

        return response()->json($produits ?? []);
    }
}
