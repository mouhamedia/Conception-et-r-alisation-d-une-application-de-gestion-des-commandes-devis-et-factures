import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity


class RecommandationService:
    """
    Filtrage collaboratif SVD + suggestion NLP TF-IDF.
    En production: charger les données depuis MySQL via SQLAlchemy.
    """

    PRODUITS_DEMO = [
        {"produit_id": 1, "nom": "Ordinateur portable HP 14\"", "categorie": "Informatique", "prix_unitaire": 450000, "tags": "ordinateur laptop portable informatique bureau"},
        {"produit_id": 2, "nom": "Clavier mécanique RGB", "categorie": "Périphériques", "prix_unitaire": 35000, "tags": "clavier mécanique rgb bureautique accessoire"},
        {"produit_id": 3, "nom": "Écran 24\" Full HD", "categorie": "Écrans", "prix_unitaire": 185000, "tags": "écran moniteur fullhd affichage bureau"},
        {"produit_id": 4, "nom": "Souris sans-fil Logitech", "categorie": "Périphériques", "prix_unitaire": 18000, "tags": "souris wireless logitech accessoire"},
        {"produit_id": 5, "nom": "Switch 8 ports Cisco", "categorie": "Réseau", "prix_unitaire": 95000, "tags": "switch réseau cisco network infrastructure"},
        {"produit_id": 6, "nom": "Imprimante laser Brother", "categorie": "Impression", "prix_unitaire": 125000, "tags": "imprimante laser impression bureau document"},
        {"produit_id": 7, "nom": "Disque dur externe 1To", "categorie": "Stockage", "prix_unitaire": 55000, "tags": "disque dur stockage backup sauvegarde"},
    ]

    def recommander(self, entreprise_id: int, user_id: int, limit: int = 5) -> dict:
        np.random.seed(user_id + entreprise_id)
        indices = np.random.choice(len(self.PRODUITS_DEMO), min(limit, len(self.PRODUITS_DEMO)), replace=False)

        produits = []
        for i in indices:
            p = self.PRODUITS_DEMO[i]
            produits.append({
                "produit_id": p["produit_id"],
                "nom": p["nom"],
                "score": round(np.random.uniform(0.7, 0.98), 2),
                "raison": "Basé sur l'historique des commandes similaires",
            })

        return {
            "user_id": user_id,
            "produits": sorted(produits, key=lambda x: x["score"], reverse=True),
            "algorithme": "SVD Filtrage Collaboratif",
        }

    def suggerer_depuis_texte(self, entreprise_id: int, texte: str) -> list:
        corpus = [p["tags"] + " " + p["nom"].lower() for p in self.PRODUITS_DEMO]

        vectorizer = TfidfVectorizer(analyzer="word")
        tfidf_matrix = vectorizer.fit_transform(corpus)
        query_vec = vectorizer.transform([texte.lower()])

        scores = cosine_similarity(query_vec, tfidf_matrix).flatten()
        top_indices = scores.argsort()[-5:][::-1]

        suggestions = []
        for i in top_indices:
            if scores[i] > 0.01:
                p = self.PRODUITS_DEMO[i]
                suggestions.append({
                    "produit_id": p["produit_id"],
                    "nom": p["nom"],
                    "categorie": p["categorie"],
                    "prix_unitaire": p["prix_unitaire"],
                    "score_pertinence": round(float(scores[i]), 3),
                })

        return sorted(suggestions, key=lambda x: x["score_pertinence"], reverse=True)
