from fastapi import APIRouter
from schemas.prediction_schema import PredictionRequest, PredictionResponse
from services.prediction_service import PredictionService

router = APIRouter()
service = PredictionService()


@router.post("/predictions", response_model=PredictionResponse)
async def predictions(request: PredictionRequest):
    return service.predire(request.entreprise_id, request.periodes)
