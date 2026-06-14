from pydantic import BaseModel
from typing import Optional


class AnalyseRequest(BaseModel):
    entreprise_id: int


class AnalyseResponse(BaseModel):
    entreprise_id: int
    taux_conversion: float
    panier_moyen: float
    delai_paiement_moyen: int
    chiffre_affaires_total: float
    nb_devis: int
    nb_commandes: int
    nb_factures_payees: int
