import numpy as np


class AnalyseService:
    """
    KPIs commerciaux calculés depuis les données de l'entreprise.
    En production: connecter à MySQL via SQLAlchemy.
    """

    def analyser(self, entreprise_id: int) -> dict:
        np.random.seed(entreprise_id + 99)

        nb_devis = np.random.randint(15, 80)
        nb_commandes = int(nb_devis * np.random.uniform(0.3, 0.7))
        nb_factures_payees = int(nb_commandes * np.random.uniform(0.5, 0.9))
        panier_moyen = np.random.uniform(150000, 1200000)
        ca_total = nb_factures_payees * panier_moyen

        return {
            "entreprise_id": entreprise_id,
            "taux_conversion": round(nb_commandes / max(nb_devis, 1) * 100, 1),
            "panier_moyen": round(panier_moyen, 0),
            "delai_paiement_moyen": int(np.random.uniform(10, 35)),
            "chiffre_affaires_total": round(ca_total, 0),
            "nb_devis": nb_devis,
            "nb_commandes": nb_commandes,
            "nb_factures_payees": nb_factures_payees,
        }
