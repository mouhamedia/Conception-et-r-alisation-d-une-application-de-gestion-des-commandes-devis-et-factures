<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\DemandeMessageController;
use App\Http\Controllers\CollaborateurController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\IAController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProduitController;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', fn() => view('welcome'))->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Invitations (publiques)
Route::get('/invitation/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');

// Téléchargement facture client (lien signé, sans compte)
Route::get('/facture/{facture}/telecharger', [\App\Http\Controllers\FactureController::class, 'telechargerPublic'])
    ->name('factures.telecharger-public')
    ->middleware('signed');

// Devis client (auth requis, sans contexte entreprise obligatoire)
Route::middleware('auth')->group(function () {
    Route::get('/devis/{devis}/client', [DevisController::class, 'afficherClient'])->name('devis.client');
    Route::patch('/devis/{devis}/accepter-client', [DevisController::class, 'accepterClient'])->name('devis.accepter-client');
    Route::patch('/devis/{devis}/refuser-client', [DevisController::class, 'refuserClient'])->name('devis.refuser-client');
});

// Sélection entreprise (auth requis, sans entreprise en session)
Route::middleware('auth')->group(function () {
    Route::get('/entreprise/creer', [EntrepriseController::class, 'create'])->name('entreprise.create');
    Route::post('/entreprise/creer', [EntrepriseController::class, 'store'])->name('entreprise.store');
    Route::get('/entreprise/selectionner', [EntrepriseController::class, 'showSelect'])->name('entreprise.select');
    Route::post('/entreprise/selectionner', [EntrepriseController::class, 'select'])->name('entreprise.switch');
});

// Routes protégées (auth + entreprise en session)
Route::middleware(['auth', 'entreprise'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Entreprise - paramètres
    Route::get('/entreprise/parametres', [EntrepriseController::class, 'edit'])->name('entreprise.edit');
    Route::put('/entreprise/parametres', [EntrepriseController::class, 'update'])->name('entreprise.update');
    Route::get('/entreprise/equipe', [EntrepriseController::class, 'equipe'])->name('entreprise.equipe');

    // Invitations (owner)
    Route::post('/invitations/inviter', [InvitationController::class, 'invite'])->name('invitations.invite');

    // Collaborateurs (owner)
    Route::delete('/collaborateurs/{userId}/retirer', [CollaborateurController::class, 'retirerMembre'])->name('collaborateurs.retirer');
    Route::patch('/collaborateurs/{userId}/role', [CollaborateurController::class, 'changerRole'])->name('collaborateurs.role');

    // Catégories
    Route::get('/categories', [CategorieController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategorieController::class, 'store'])->name('categories.store');
    Route::patch('/categories/{categorie}', [CategorieController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{categorie}', [CategorieController::class, 'destroy'])->name('categories.destroy');
    Route::get('/categories/liste', [CategorieController::class, 'liste'])->name('categories.liste');

    // Produits
    Route::resource('produits', ProduitController::class)->except(['show']);

    // Devis
    Route::get('/devis', [DevisController::class, 'index'])->name('devis.index');
    Route::get('/devis/creer', [DevisController::class, 'create'])->name('devis.create');
    Route::post('/devis', [DevisController::class, 'store'])->name('devis.store');
    Route::get('/devis/{devis}', [DevisController::class, 'show'])->name('devis.show');
    Route::get('/devis/{devis}/modifier', [DevisController::class, 'edit'])->name('devis.edit');
    Route::put('/devis/{devis}', [DevisController::class, 'update'])->name('devis.update');
    Route::match(['GET','PATCH'], '/devis/{devis}/valider', [DevisController::class, 'valider'])->name('devis.valider');
    Route::patch('/devis/{devis}/envoyer', [DevisController::class, 'envoyer'])->name('devis.envoyer');
    Route::delete('/devis/{devis}', [DevisController::class, 'destroy'])->name('devis.destroy');

    // Commandes
    Route::get('/commandes', [CommandeController::class, 'index'])->name('commandes.index');
    Route::get('/commandes/{commande}', [CommandeController::class, 'show'])->name('commandes.show');
    Route::patch('/commandes/{commande}/statut/{statut}', [CommandeController::class, 'updateStatut'])->name('commandes.statut');

    // Factures
    Route::get('/factures', [FactureController::class, 'index'])->name('factures.index');
    Route::get('/factures/{facture}', [FactureController::class, 'show'])->name('factures.show');
    Route::get('/factures/{facture}/pdf', [FactureController::class, 'pdf'])->name('factures.pdf');
    Route::patch('/factures/{facture}/envoyer', [FactureController::class, 'envoyer'])->name('factures.envoyer');
    Route::post('/factures/{facture}/renvoyer-email', [FactureController::class, 'renvoyerEmail'])->name('factures.renvoyer-email');
    Route::post('/factures/{facture}/paiement', [FactureController::class, 'enregistrerPaiement'])->name('factures.paiement');

    // Marketplace B2B
    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
    Route::post('/marketplace/demande', [MarketplaceController::class, 'store'])->name('marketplace.store');
    Route::get('/marketplace/demandes', [MarketplaceController::class, 'demandes'])->name('marketplace.demandes');
    Route::patch('/marketplace/demandes/{demande}/accepter', [MarketplaceController::class, 'accepter'])->name('marketplace.accepter');
    Route::patch('/marketplace/demandes/{demande}/refuser', [MarketplaceController::class, 'refuser'])->name('marketplace.refuser');

    // Messagerie des demandes de devis
    Route::get('/marketplace/demandes/{demande}/conversation', [DemandeMessageController::class, 'show'])->name('demandes.conversation');
    Route::post('/marketplace/demandes/{demande}/messages', [DemandeMessageController::class, 'store'])->name('demandes.messages.store');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/lire', [NotificationController::class, 'marquerLu'])->name('notifications.lire');
    Route::patch('/notifications/tout-lire', [NotificationController::class, 'marquerTousLus'])->name('notifications.tout-lire');

    // IA
    Route::get('/ia', [IAController::class, 'dashboard'])->name('ia.dashboard');
    Route::get('/ia/suggestions', [IAController::class, 'suggestions'])->name('ia.suggestions');
});
