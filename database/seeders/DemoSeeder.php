<?php

namespace Database\Seeders;

use App\Models\Commande;
use App\Models\Devis;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Models\LigneCommande;
use App\Models\LigneDevis;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Utilisateurs
        $owner = User::create([
            'name' => 'Diallo',
            'prenom' => 'Mamadou',
            'email' => 'admin@gestipro.sn',
            'password' => Hash::make('password'),
            'telephone' => '+221 77 123 45 67',
        ]);

        $employe = User::create([
            'name' => 'Sow',
            'prenom' => 'Fatou',
            'email' => 'employe@gestipro.sn',
            'password' => Hash::make('password'),
            'telephone' => '+221 70 987 65 43',
        ]);

        // Entreprise
        $entreprise = Entreprise::create([
            'nom' => 'TechSenegal SARL',
            'siret' => 'SN-2025-001234',
            'email' => 'contact@techsenegal.sn',
            'telephone' => '+221 33 824 00 00',
            'adresse' => 'Plateau, Rue Carnot',
            'ville' => 'Dakar',
            'pays' => 'Sénégal',
            'devise' => 'FCFA',
        ]);

        $entreprise->users()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $entreprise->users()->attach($employe->id, ['role' => 'employee', 'joined_at' => now()]);

        // Produits
        $produits = [
            ['nom' => 'Ordinateur portable HP 14"', 'reference_sku' => 'HP-14-2025', 'prix_unitaire' => 450000, 'stock_actuel' => 15, 'stock_minimum' => 3, 'categorie' => 'Informatique'],
            ['nom' => 'Clavier mécanique RGB', 'reference_sku' => 'CLV-RGB-001', 'prix_unitaire' => 35000, 'stock_actuel' => 30, 'stock_minimum' => 5, 'categorie' => 'Périphériques'],
            ['nom' => 'Écran 24" Full HD', 'reference_sku' => 'ECR-24FHD', 'prix_unitaire' => 185000, 'stock_actuel' => 8, 'stock_minimum' => 2, 'categorie' => 'Écrans'],
            ['nom' => 'Souris sans-fil Logitech', 'reference_sku' => 'SRS-LOG-M325', 'prix_unitaire' => 18000, 'stock_actuel' => 50, 'stock_minimum' => 10, 'categorie' => 'Périphériques'],
            ['nom' => 'Switch 8 ports Cisco', 'reference_sku' => 'SW-CSC-8P', 'prix_unitaire' => 95000, 'stock_actuel' => 4, 'stock_minimum' => 5, 'categorie' => 'Réseau'],
            ['nom' => 'Câble réseau Cat6 5m', 'reference_sku' => 'CAB-CAT6-5M', 'prix_unitaire' => 5000, 'stock_actuel' => 100, 'stock_minimum' => 20, 'categorie' => 'Réseau'],
            ['nom' => 'Imprimante laser Brother', 'reference_sku' => 'IMP-BRO-HL', 'prix_unitaire' => 125000, 'stock_actuel' => 6, 'stock_minimum' => 2, 'categorie' => 'Impression'],
            ['nom' => 'Disque dur externe 1To', 'reference_sku' => 'HDD-EXT-1TO', 'prix_unitaire' => 55000, 'stock_actuel' => 20, 'stock_minimum' => 5, 'categorie' => 'Stockage'],
        ];

        $produitModels = [];
        foreach ($produits as $p) {
            $produitModels[] = $entreprise->produits()->create($p);
        }

        // Devis 1 - Accepté (génère commande + facture)
        $devis1 = $entreprise->devis()->create([
            'user_id' => $owner->id,
            'numero' => 'DEV-2025-0001',
            'client_nom' => 'Université Cheikh Anta Diop',
            'client_email' => 'dsi@ucad.sn',
            'statut' => 'accepte',
            'sous_total_ht' => 1400000,
            'tva' => 18,
            'total_ttc' => 1652000,
            'date_emission' => now()->subDays(20),
            'date_expiration' => now()->addDays(10),
        ]);

        LigneDevis::create(['devis_id' => $devis1->id, 'produit_id' => $produitModels[0]->id, 'quantite' => 3, 'prix_unitaire_snapshot' => 450000, 'sous_total' => 1350000]);
        LigneDevis::create(['devis_id' => $devis1->id, 'produit_id' => $produitModels[3]->id, 'quantite' => 3, 'prix_unitaire_snapshot' => 18000, 'sous_total' => 54000]);

        $commande1 = Commande::create([
            'entreprise_id' => $entreprise->id,
            'devis_id' => $devis1->id,
            'numero' => 'CMD-2025-0001',
            'client_nom' => 'Université Cheikh Anta Diop',
            'statut' => 'en_cours',
            'sous_total_ht' => 1400000,
            'tva' => 18,
            'total_ttc' => 1652000,
        ]);

        LigneCommande::create(['commande_id' => $commande1->id, 'produit_id' => $produitModels[0]->id, 'quantite' => 3, 'prix_unitaire_snapshot' => 450000, 'sous_total' => 1350000]);
        LigneCommande::create(['commande_id' => $commande1->id, 'produit_id' => $produitModels[3]->id, 'quantite' => 3, 'prix_unitaire_snapshot' => 18000, 'sous_total' => 54000]);

        Facture::create([
            'entreprise_id' => $entreprise->id,
            'commande_id' => $commande1->id,
            'numero' => 'FAC-2025-0001',
            'statut' => 'envoyee',
            'montant_paye' => 0,
            'date_echeance' => now()->addDays(15),
        ]);

        // Devis 2 - Brouillon
        $devis2 = $entreprise->devis()->create([
            'user_id' => $employe->id,
            'numero' => 'DEV-2025-0002',
            'client_nom' => 'Banque Populaire du Sénégal',
            'client_email' => 'achat@bps.sn',
            'statut' => 'envoye',
            'sous_total_ht' => 760000,
            'tva' => 18,
            'total_ttc' => 896800,
            'date_emission' => now()->subDays(5),
            'date_expiration' => now()->addDays(25),
        ]);

        LigneDevis::create(['devis_id' => $devis2->id, 'produit_id' => $produitModels[2]->id, 'quantite' => 4, 'prix_unitaire_snapshot' => 185000, 'sous_total' => 740000]);
        LigneDevis::create(['devis_id' => $devis2->id, 'produit_id' => $produitModels[1]->id, 'quantite' => 4, 'prix_unitaire_snapshot' => 35000, 'sous_total' => 140000]);

        // Devis 3 - Payé
        $devis3 = $entreprise->devis()->create([
            'user_id' => $owner->id,
            'numero' => 'DEV-2025-0003',
            'client_nom' => 'Orange Sénégal',
            'statut' => 'accepte',
            'sous_total_ht' => 285000,
            'tva' => 18,
            'total_ttc' => 336300,
            'date_emission' => now()->subDays(45),
            'date_expiration' => now()->subDays(15),
        ]);

        LigneDevis::create(['devis_id' => $devis3->id, 'produit_id' => $produitModels[4]->id, 'quantite' => 3, 'prix_unitaire_snapshot' => 95000, 'sous_total' => 285000]);

        $commande2 = Commande::create([
            'entreprise_id' => $entreprise->id,
            'devis_id' => $devis3->id,
            'numero' => 'CMD-2025-0002',
            'client_nom' => 'Orange Sénégal',
            'statut' => 'livree',
            'sous_total_ht' => 285000,
            'tva' => 18,
            'total_ttc' => 336300,
        ]);

        LigneCommande::create(['commande_id' => $commande2->id, 'produit_id' => $produitModels[4]->id, 'quantite' => 3, 'prix_unitaire_snapshot' => 95000, 'sous_total' => 285000]);

        Facture::create([
            'entreprise_id' => $entreprise->id,
            'commande_id' => $commande2->id,
            'numero' => 'FAC-2025-0002',
            'statut' => 'payee',
            'montant_paye' => 336300,
            'date_echeance' => now()->subDays(10),
            'payee_at' => now()->subDays(5),
        ]);

        $this->command->info('Données de démonstration créées !');
        $this->command->info('  Owner  : admin@gestipro.sn / password');
        $this->command->info('  Employé: employe@gestipro.sn / password');
    }
}
