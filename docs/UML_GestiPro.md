# GestiPro — Récapitulatif fonctionnel & Diagrammes UML

## 1. Que fait l'application ?

GestiPro est une application web **B2B multi-tenant** de gestion commerciale, avec un
microservice IA (FastAPI) en complément. Une "entreprise" est l'unité de travail : chaque
utilisateur peut appartenir à plusieurs entreprises, avec un rôle **owner** (propriétaire) ou
**employee** (collaborateur).

### Fonctionnalités principales

- **Authentification & multi-entreprise** : inscription, connexion, sélection/création
  d'entreprise, invitation de collaborateurs par email (token expirable 7 jours).
- **Catalogue produits** : produits classés par catégorie, gestion du stock (stock actuel /
  seuil minimum), prix unitaire.
- **Devis** : création, édition (tant que brouillon), envoi au client par email, le client
  accepte/refuse depuis un lien public, validation manuelle possible côté entreprise.
- **Commandes** : générées **automatiquement** dès qu'un devis est accepté ; suivi de statut
  (en attente → en cours → livrée / annulée).
- **Factures** : générées **automatiquement** dès qu'une commande est créée ; envoi au client,
  enregistrement de paiements (y compris partiels), téléchargement PDF (y compris via lien
  public signé).
- **Marketplace B2B** : une entreprise peut rechercher une autre entreprise et lui envoyer une
  **demande de devis**, échanger des messages, puis l'entreprise cible accepte (→ crée un
  devis) ou refuse.
- **Notifications** internes (in-app) pour tous les événements clés (commande créée, demande
  reçue/acceptée/refusée, message reçu, etc.).
- **Dashboard** : KPI (CA du mois, devis en attente, commandes en cours, factures impayées),
  graphique 6 mois, suggestions IA.
- **Module IA** : alertes de stock faible, prévisions de vente, top produits, recommandations
  (microservice FastAPI, avec fallback local si indisponible).

### Automatisation centrale (le cœur métier)

Le système repose sur une **chaîne d'événements Laravel** qui automatise tout le flux
commercial dès qu'un devis est accepté :

```
Devis accepté (DevisValide)
   → Commande créée automatiquement (CommandeCreee)
        → Facture générée automatiquement (FactureGeneree)
        → Stock décrémenté
        → Notification envoyée à l'équipe
```

---

## 2. Diagramme de cas d'utilisation

```mermaid
flowchart LR
    subgraph Acteurs
        Owner["Propriétaire (owner)"]
        Employee["Collaborateur (employee)"]
        Client["Client final (sans compte)"]
        AutreEntreprise["Entreprise partenaire (Marketplace)"]
    end

    subgraph "Cas d'utilisation GestiPro"
        UC1((S'inscrire / Se connecter))
        UC2((Créer / sélectionner une entreprise))
        UC3((Inviter un collaborateur))
        UC4((Gérer les produits & catégories))
        UC5((Créer un devis))
        UC6((Envoyer un devis au client))
        UC7((Accepter / refuser un devis))
        UC8((Valider un devis))
        UC9((Suivre une commande))
        UC10((Consulter une facture))
        UC11((Envoyer une facture))
        UC12((Enregistrer un paiement))
        UC13((Rechercher une entreprise))
        UC14((Envoyer une demande de devis))
        UC15((Discuter via messagerie))
        UC16((Accepter / refuser une demande))
        UC17((Consulter le dashboard / IA))
        UC18((Gérer les notifications))
    end

    Owner --> UC1
    Owner --> UC2
    Owner --> UC3
    Owner --> UC4
    Owner --> UC5
    Owner --> UC6
    Owner --> UC8
    Owner --> UC9
    Owner --> UC10
    Owner --> UC11
    Owner --> UC12
    Owner --> UC13
    Owner --> UC14
    Owner --> UC15
    Owner --> UC16
    Owner --> UC17
    Owner --> UC18

    Employee --> UC1
    Employee --> UC4
    Employee --> UC5
    Employee --> UC6
    Employee --> UC9
    Employee --> UC10
    Employee --> UC13
    Employee --> UC14
    Employee --> UC15
    Employee --> UC17
    Employee --> UC18

    Client --> UC7

    AutreEntreprise --> UC16
    AutreEntreprise --> UC15

    UC8 -.include.-> UC9
    UC7 -.include.-> UC9
    UC9 -.include.-> UC10
    UC16 -.extend.-> UC5
```

---

## 3. Diagramme de classes (simplifié)

```mermaid
classDiagram
    class User {
        +string name
        +string prenom
        +string email
        +string password
        +string telephone
        +string avatar
        +getRoleInEntreprise()
        +isOwnerOf(entrepriseId)
    }

    class Entreprise {
        +string nom
        +string siret
        +string email
        +string telephone
        +string adresse
        +string devise
    }

    class EntrepriseUser {
        +string role
        +datetime joined_at
    }

    class Produit {
        +string nom
        +string reference_sku
        +decimal prix_unitaire
        +int stock_actuel
        +int stock_minimum
        +string categorie
        +bool actif
        +isStockFaible()
    }

    class Categorie {
        +string nom
    }

    class Devis {
        +string numero
        +string client_nom
        +string client_email
        +string statut
        +decimal sous_total_ht
        +decimal tva
        +decimal total_ttc
        +date date_emission
        +date date_expiration
        +isExpire()
    }

    class LigneDevis {
        +int quantite
        +decimal prix_unitaire_snapshot
        +decimal sous_total
    }

    class Commande {
        +string numero
        +string statut
        +decimal total_ttc
    }

    class LigneCommande {
        +int quantite
        +decimal prix_unitaire_snapshot
        +decimal sous_total
    }

    class Facture {
        +string numero
        +string statut
        +decimal montant_paye
        +date date_echeance
        +datetime payee_at
        +montant_total()
        +montant_restant()
    }

    class DemandeDevis {
        +string description
        +decimal budget
        +string statut
    }

    class DemandeMessage {
        +string contenu
    }

    class Invitation {
        +string email
        +string role
        +string token
        +datetime expires_at
        +isExpired()
    }

    class Notification {
        +string type
        +string titre
        +string message
        +bool lu
    }

    User "1..*" -- "0..*" Entreprise : EntrepriseUser
    Entreprise "1" *-- "0..*" Produit
    Entreprise "1" *-- "0..*" Categorie
    Entreprise "1" *-- "0..*" Devis
    Entreprise "1" *-- "0..*" Commande
    Entreprise "1" *-- "0..*" Facture
    Entreprise "1" *-- "0..*" Invitation
    Entreprise "1" *-- "0..*" Notification
    Entreprise "1" -- "0..*" DemandeDevis : source/cible

    Devis "1" -- "1..*" LigneDevis
    LigneDevis "1" --> "1" Produit
    Devis "1" --> "0..1" Commande : génère
    Commande "1" -- "1..*" LigneCommande
    LigneCommande "1" --> "1" Produit
    Commande "1" --> "0..1" Facture : génère

    DemandeDevis "1" -- "0..*" DemandeMessage
    DemandeDevis "0..1" --> "0..1" Devis
    User "1" --> "0..*" Notification
```

---

## 4. Diagramme d'activité — flux Devis → Commande → Facture

```mermaid
flowchart TD
    Start([Début]) --> A[Créer devis brouillon]
    A --> B[Ajouter lignes : produits, quantités]
    B --> C{Envoyer au client ?}
    C -- Oui --> D[Devis = envoyé]
    D --> E[Client consulte le devis]
    E --> F{Décision client}
    F -- Refuse --> G[Devis = refusé]
    G --> End1([Fin])
    F -- Accepte --> H[Devis = accepté]
    C -- Validation interne --> H

    H --> I[Événement DevisValide]
    I --> J[Listener GenererCommande]
    J --> K[Création Commande - statut en_attente]
    K --> L[Événement CommandeCreee]

    L --> M[Listener GenererFacture]
    M --> N[Création Facture - statut brouillon]
    N --> O[Événement FactureGeneree]

    L --> P[Listener MettreAJourStock]
    P --> Q[Décrémenter stock des produits]

    L --> R[Listener EnvoyerNotification]
    R --> S[Notifier les collaborateurs]

    O --> T[Owner envoie la facture au client]
    T --> U[Facture = envoyée]
    U --> V{Paiement reçu ?}
    V -- Partiel --> W[Facture = envoyée, montant_paye mis à jour]
    W --> V
    V -- Total --> X[Facture = payée]
    X --> End2([Fin])
```

---

## 5. Diagramme de séquence — Acceptation d'un devis par le client

```mermaid
sequenceDiagram
    actor Client
    participant Web as Vue afficherClient
    participant DC as DevisController
    participant Event as Events Laravel
    participant L1 as GenererCommande
    participant CS as CommandeService
    participant L2 as GenererFacture
    participant FS as FactureService
    participant L3 as MettreAJourStock
    participant SS as StockService
    participant L4 as EnvoyerNotification
    participant NS as NotificationService
    participant DB as Base de données

    Client->>Web: Ouvre le lien du devis
    Web->>DC: GET /devis/{id}/client
    DC->>DB: Charger Devis + lignes
    DB-->>DC: Devis
    DC-->>Web: Affiche devis (boutons Accepter/Refuser)

    Client->>Web: Clique "Accepter"
    Web->>DC: PATCH /devis/{id}/accepter-client
    DC->>DB: Devis.statut = 'accepte'
    DC->>Event: event(new DevisValide(devis))

    Event->>L1: handle(DevisValide)
    L1->>L1: Guard anti-doublon (commande existe ?)
    L1->>CS: creerDepuisDevis(devis)
    CS->>DB: INSERT Commande (en_attente)
    CS->>DB: Copier lignes devis -> lignes_commande
    CS-->>L1: Commande créée
    L1->>Event: event(new CommandeCreee(commande))

    par Facture
        Event->>L2: handle(CommandeCreee)
        L2->>L2: Guard anti-doublon (facture existe ?)
        L2->>FS: creerDepuisCommande(commande)
        FS->>DB: INSERT Facture (brouillon, echeance +30j)
        FS-->>L2: Facture créée
        L2->>Event: event(new FactureGeneree(facture))
    and Stock
        Event->>L3: handle(CommandeCreee)
        L3->>SS: decrementerStock(commande)
        SS->>DB: UPDATE Produit.stock_actuel -= quantite
    and Notification
        Event->>L4: handle(CommandeCreee)
        L4->>NS: envoyerATous(entreprise, "commande_creee")
        NS->>DB: INSERT Notification (par collaborateur)
    end

    DC-->>Client: Confirmation "Devis accepté"
```

---

## 6. Diagramme de séquence — Demande de devis Marketplace B2B

```mermaid
sequenceDiagram
    actor UserA as Utilisateur (Entreprise A)
    participant MC as MarketplaceController
    participant DB as Base de données
    participant NS as NotificationService
    actor UserB as Utilisateur (Entreprise B)

    UserA->>MC: POST /marketplace/demande (entreprise_cible=B, description, budget)
    MC->>DB: Vérifie pas de demande en_attente existante
    MC->>DB: INSERT DemandeDevis (statut=en_attente)
    MC->>NS: envoyerATous(B, "demande_devis")
    NS->>DB: INSERT Notification (pour chaque collaborateur de B)
    MC-->>UserA: Confirmation envoi

    UserB->>MC: GET /marketplace/demandes (reçues)
    MC->>DB: SELECT demandes où entreprise_cible = B
    MC-->>UserB: Liste demandes

    alt Accepter
        UserB->>MC: PATCH /marketplace/demandes/{id}/accepter
        MC->>DB: DemandeDevis.statut = 'acceptee'
        MC->>NS: notifier(A, "demande_acceptee")
        MC-->>UserB: Redirection vers création devis (lié à la demande)
    else Refuser
        UserB->>MC: PATCH /marketplace/demandes/{id}/refuser
        MC->>DB: DemandeDevis.statut = 'refusee'
        MC->>NS: notifier(A, "demande_refusee")
        MC-->>UserB: Confirmation refus
    end
```
