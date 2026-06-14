<?php

namespace App\Providers;

use App\Events\CommandeCreee;
use App\Events\DevisValide;
use App\Listeners\EnvoyerNotification;
use App\Listeners\GenererCommande;
use App\Listeners\GenererFacture;
use App\Listeners\MettreAJourStock;
use App\Models\Devis;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Models\Produit;
use App\Policies\DevisPolicy;
use App\Policies\EntreprisePolicy;
use App\Policies\FacturePolicy;
use App\Policies\ProduitPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Policies
        Gate::policy(Entreprise::class, EntreprisePolicy::class);
        Gate::policy(Produit::class, ProduitPolicy::class);
        Gate::policy(Devis::class, DevisPolicy::class);
        Gate::policy(Facture::class, FacturePolicy::class);

        // Events → Listeners
        Event::listen(DevisValide::class, GenererCommande::class);
        Event::listen(CommandeCreee::class, GenererFacture::class);
        Event::listen(CommandeCreee::class, MettreAJourStock::class);
        Event::listen(CommandeCreee::class, EnvoyerNotification::class);
    }
}
