import os
from fastapi import Request
from fastapi.responses import JSONResponse
from starlette.middleware.base import BaseHTTPMiddleware


class APIKeyMiddleware(BaseHTTPMiddleware):
    async def dispatch(self, request: Request, call_next):
        if request.url.path in ["/health", "/docs", "/openapi.json", "/redoc"]:
            return await call_next(request)

        secret_key = os.getenv("FASTAPI_SECRET_KEY", "gestipro_secret_key_2025")
        api_key = request.headers.get("X-API-Key")

        if api_key != secret_key:
            return JSONResponse(
                status_code=401,
                content={"detail": "Clé API invalide ou manquante"},
            )

        return await call_next(request)
