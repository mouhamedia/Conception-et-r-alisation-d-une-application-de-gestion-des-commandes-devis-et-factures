# GestiPro — Application de Gestion Commerciale B2B

Application web de gestion des commandes, devis et factures pour entreprises B2B avec marketplace inter-entreprises et module d'intelligence artificielle.

Développée dans le cadre d'un projet de fin d'études (mémoire de licence).

**Stack :** Laravel 13 · PHP 8.3 · MySQL 8 · Alpine.js · FastAPI (Python 3.11)

---

## Fonctionnalités

| Module | Description |
|---|---|
| **Authentification** | Inscription, connexion, gestion multi-sessions |
| **Multi-entreprises** | Créer/rejoindre plusieurs entreprises, switcher en sidebar |
| **Produits & Catégories** | Catalogue produits avec gestion de stock et alertes, catégories gérables |
| **Devis** | Création, envoi client, acceptation/refus, conversion automatique en commande |
| **Commandes** | Suivi de statut, génération automatique depuis devis accepté |
| **Factures** | Génération PDF (logo inclus), envoi email avec lien sécurisé 30j, paiement partiel |
| **Marketplace B2B** | Demandes de devis entre entreprises + messagerie par conversation |
| **Notifications** | Centre de notifications avec résumé par type |
| **Équipe** | Invitations par email, rôles propriétaire / membre |
| **Prédictions IA** | Module FastAPI (scikit-learn) pour prévisions et recommandations |

---

## Prérequis

- **PHP** 8.3+ avec extensions : `pdo_mysql`, `mbstring`, `xml`, `gd`, `zip`, `fileinfo`
- **Composer** 2.x
- **MySQL** 8.0+ (ou MariaDB 10.6+)
- **Node.js** 18+ et npm *(optionnel — pour compiler les assets front)*
- **Python** 3.11+ *(uniquement pour le module IA FastAPI)*
- **MAMP / XAMPP** ou tout serveur local avec MySQL

---

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/mouhamedia/Conception-et-r-alisation-d-une-application-de-gestion-des-commandes-devis-et-factures.git gestipro
cd gestipro
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Ouvrez `.env` et adaptez la configuration MySQL :

```dotenv
APP_NAME=GestiPro
APP_URL=http://localhost:8000
APP_LOCALE=fr

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306          # 8889 si vous utilisez MAMP
DB_DATABASE=gestipro
DB_USERNAME=root
DB_PASSWORD=root      # votre mot de passe MySQL

MAIL_MAILER=log       # les emails s'écrivent dans storage/logs/laravel.log
```

### 4. Créer la base de données

Dans MySQL (via MAMP, phpMyAdmin ou terminal) :

```sql
CREATE DATABASE gestipro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Exécuter les migrations

```bash
php artisan migrate
```

### 6. Créer le lien de stockage public (pour les logos)

```bash
php artisan storage:link
```

### 7. Lancer l'application

```bash
php artisan serve
```

L'application est disponible sur **http://localhost:8000**

---

## Module IA — FastAPI (optionnel)

Le module d'intelligence artificielle tourne séparément sur le port **8001**.  
Sans lui, l'application fonctionne normalement (la page "Prédictions IA" utilise des calculs de secours locaux).

```bash
cd fastapi

# Créer un environnement virtuel
python3 -m venv venv
source venv/bin/activate        # Windows : venv\Scripts\activate

# Installer les dépendances Python
pip install -r requirements.txt

# Lancer le serveur FastAPI
uvicorn main:app --reload --port 8001
```

---

## Premier démarrage

1. Ouvrez **http://localhost:8000/register** et créez votre compte
2. Créez votre première entreprise (nom, adresse, logo optionnel)
3. Vous arrivez sur le **Dashboard**
4. Commencez par ajouter des **catégories** puis des **produits**
5. Créez un **devis**, envoyez-le, validez-le → une commande et une facture sont générées automatiquement

### Tester la Marketplace B2B

Pour tester les échanges entre entreprises :
1. Créez un **second compte** dans un autre navigateur (ou en navigation privée)
2. Créez une deuxième entreprise avec ce compte
3. Dans le premier compte → **Marketplace** → envoyez une demande de devis
4. Dans le second compte → **Notifications** → ouvrez la conversation et répondez

---

## Structure du projet

```
gestipro/
├── app/
│   ├── Events/               # DevisValide, CommandeCreee, FactureGeneree
│   ├── Http/
│   │   ├── Controllers/      # Tous les controllers Laravel
│   │   ├── Middleware/       # CheckEntrepriseAccess, CheckRole
│   │   └── Requests/         # Form Requests (validation)
│   ├── Listeners/            # GenererCommande, GenererFacture, MettreAJourStock
│   ├── Mail/                 # FactureMail (PDF en pièce jointe)
│   ├── Models/               # Modèles Eloquent
│   ├── Policies/             # DevisPolicy, FacturePolicy, ProduitPolicy...
│   └── Services/             # EntrepriseContextService, NotificationService, IAService...
├── database/
│   └── migrations/           # Toutes les migrations SQL
├── resources/
│   └── views/
│       ├── layouts/          # app.blade.php (sidebar + topbar)
│       ├── auth/             # Login, Register
│       ├── dashboard/        # Tableau de bord
│       ├── produits/         # Catalogue + formulaires
│       ├── categories/       # Gestion des catégories
│       ├── devis/            # Devis + vue client
│       ├── commandes/        # Commandes
│       ├── factures/         # Factures + PDF
│       ├── marketplace/      # B2B + conversation messages
│       ├── notifications/    # Centre de notifications
│       ├── entreprise/       # Paramètres + équipe + sélection
│       └── emails/           # Templates emails HTML
├── routes/
│   └── web.php               # Toutes les routes
├── storage/
│   └── app/public/logos/     # Logos des entreprises (après storage:link)
└── fastapi/                  # Module IA Python indépendant
    ├── main.py
    ├── routes/
    ├── services/
    └── requirements.txt
```

---

## Schéma des relations

```
User ──(M:M)── Entreprise ──(1:N)── Produit
    [pivot: role]     │         └── Categorie
                      │
                      ├──(1:N)── Devis ──(1:N)── LigneDevis ──► Produit
                      │              └──(1:1)──► Commande ──(1:N)── LigneCommande
                      │                               └──(1:1)──► Facture
                      │
                      ├──(1:N)── Notification
                      ├──(1:N)── Invitation
                      └──(1:N)── DemandeDevis (Marketplace)
                                      └──(1:N)── DemandeMessage
```

---

## Flux commercial automatisé

```
Devis accepté (par l'équipe ou par le client)
    └─► event(DevisValide)
            └─► Listener: GenererCommande
                    └─► CommandeService::creerDepuisDevis()
                    └─► event(CommandeCreee)
                            ├─► Listener: GenererFacture
                            │       └─► FactureService::creerDepuisCommande()
                            ├─► Listener: MettreAJourStock
                            │       └─► StockService::decrementerStock()
                            └─► Listener: EnvoyerNotification
                                    └─► NotificationService::envoyerATous()
```

---

## Variables d'environnement

| Variable | Description | Valeur recommandée |
|---|---|---|
| `DB_HOST` | Hôte MySQL | `127.0.0.1` |
| `DB_PORT` | Port MySQL | `3306` (MAMP: `8889`) |
| `DB_DATABASE` | Nom de la base | `gestipro` |
| `DB_USERNAME` | Utilisateur MySQL | `root` |
| `DB_PASSWORD` | Mot de passe MySQL | selon votre config |
| `MAIL_MAILER` | Driver email | `log` (dev) ou `smtp` (prod) |
| `APP_URL` | URL de l'application | `http://localhost:8000` |

---

## Commandes utiles

```bash
# Lancer le serveur de développement
php artisan serve

# Repartir de zéro (supprime toutes les données)
php artisan migrate:fresh

# Vider les caches
php artisan config:clear && php artisan cache:clear

# Lister toutes les routes
php artisan route:list

# Recréer le lien storage (si les images ne s'affichent pas)
php artisan storage:link

# Voir les emails envoyés (mode log)
tail -f storage/logs/laravel.log | grep -A 20 "Message-ID"
```

---

## Dépendances principales

| Package | Version | Usage |
|---|---|---|
| `laravel/framework` | ^13.8 | Framework PHP |
| `barryvdh/laravel-dompdf` | ^3.1 | Génération PDF des factures |
| `alpinejs` | 3.14.1 | Réactivité frontend (CDN, pas de compilation) |
| `fastapi` | 0.111.0 | API Python pour le module IA |
| `scikit-learn` | 1.5.0 | Modèle de prédictions (SVD, régression) |
| `pandas` | 2.2.2 | Traitement des données |

---

## Architecture MVC

### Services métier

| Service | Responsabilité |
|---|---|
| `EntrepriseContextService` | Accès à l'entreprise active en session, vérification d'appartenance |
| `DevisService` | Création, modification, calcul des totaux, numérotation |
| `CommandeService` | Création depuis devis, copie des lignes avec snapshots de prix |
| `FactureService` | Création depuis commande, calcul échéance |
| `StockService` | Décrémentation / restauration du stock |
| `InvitationService` | Génération token, envoi email, acceptation |
| `NotificationService` | Création notifications pour un ou tous les membres |
| `IAService` | Client HTTP vers FastAPI avec fallback local |

### Événements & Listeners

| Événement | Listeners déclenchés |
|---|---|
| `DevisValide` | `GenererCommande` |
| `CommandeCreee` | `GenererFacture`, `MettreAJourStock`, `EnvoyerNotification` |
| `FactureGeneree` | *(notification incluse dans EnvoyerNotification)* |

### Sécurité

- **Middleware `entreprise`** — vérifie que l'utilisateur appartient à l'entreprise en session sur toutes les routes protégées
- **Policies Eloquent** — contrôle par objet (DevisPolicy, FacturePolicy, ProduitPolicy, EntreprisePolicy)
- **Form Requests** — validation avant chaque action (StoreProduitRequest, StoreDevisRequest, StoreEntrepriseRequest…)
- **Signed URLs** — liens de téléchargement factures valables 30 jours sans authentification

---

## Auteur

Projet de fin d'études — Licence Informatique

**Mouhamedia** — [GitHub](https://github.com/mouhamedia)
