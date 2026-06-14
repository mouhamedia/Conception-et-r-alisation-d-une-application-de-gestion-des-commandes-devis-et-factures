from pydantic import BaseModel
from typing import List, Optional


class RecommandationRequest(BaseModel):
    entreprise_id: int
    user_id: int
    limit: int = 5


class ProduitRecommande(BaseModel):
    produit_id: int
    nom: str
    score: float
    raison: Optional[str] = None


class RecommandationResponse(BaseModel):
    user_id: int
    produits: List[ProduitRecommande]
    algorithme: str = "SVD Filtrage Collaboratif"


class SuggestionRequest(BaseModel):
    entreprise_id: int
    texte: str


class ProduitSuggere(BaseModel):
    produit_id: int
    nom: str
    categorie: Optional[str] = None
    prix_unitaire: float
    score_pertinence: float
