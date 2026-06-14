from fastapi import APIRouter
from schemas.recommandation_schema import (
    RecommandationRequest, RecommandationResponse,
    SuggestionRequest
)
from services.recommandation_service import RecommandationService
from typing import List

router = APIRouter()
service = RecommandationService()


@router.post("/recommendations", response_model=RecommandationResponse)
async def recommandations(request: RecommandationRequest):
    return service.recommander(request.entreprise_id, request.user_id, request.limit)


@router.post("/devis/suggestion")
async def suggestion_devis(request: SuggestionRequest):
    return service.suggerer_depuis_texte(request.entreprise_id, request.texte)
