from fastapi import APIRouter
from schemas.analyse_schema import AnalyseRequest, AnalyseResponse
from services.analyse_service import AnalyseService

router = APIRouter()
service = AnalyseService()


@router.post("/analyse", response_model=AnalyseResponse)
async def analyse(request: AnalyseRequest):
    return service.analyser(request.entreprise_id)
