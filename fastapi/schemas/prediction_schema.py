from pydantic import BaseModel
from typing import List, Optional


class PredictionRequest(BaseModel):
    entreprise_id: int
    periodes: int = 3


class PredictionItem(BaseModel):
    periode: str
    valeur: float
    confiance: int


class PredictionResponse(BaseModel):
    entreprise_id: int
    predictions: List[PredictionItem]
    algorithme: str = "Random Forest + ARIMA"
