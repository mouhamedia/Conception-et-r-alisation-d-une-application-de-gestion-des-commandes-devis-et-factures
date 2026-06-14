from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from routes import prediction, recommandation, analyse
from middleware.api_key_auth import APIKeyMiddleware

app = FastAPI(
    title="GestiPro IA API",
    description="Microservice d'intelligence artificielle pour GestiPro",
    version="1.0.0",
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:8000"],
    allow_methods=["POST", "GET"],
    allow_headers=["*"],
)

app.add_middleware(APIKeyMiddleware)

app.include_router(prediction.router, prefix="/api", tags=["Prédictions"])
app.include_router(recommandation.router, prefix="/api", tags=["Recommandations"])
app.include_router(analyse.router, prefix="/api", tags=["Analyse"])


@app.get("/health", tags=["Santé"])
async def health():
    return {"status": "ok", "service": "GestiPro IA", "version": "1.0.0"}
