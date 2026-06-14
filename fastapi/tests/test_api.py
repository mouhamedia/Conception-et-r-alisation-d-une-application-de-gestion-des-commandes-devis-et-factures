"""
Tests d'intégration pour le microservice FastAPI GestiPro IA.
Lancer : cd fastapi && pytest tests/ -v
"""
import os
import pytest
from fastapi.testclient import TestClient

os.environ["FASTAPI_SECRET_KEY"] = "test_secret_key"

from main import app  # noqa: E402

client = TestClient(app)
HEADERS = {"X-API-Key": "test_secret_key"}


def test_health():
    response = client.get("/health")
    assert response.status_code == 200
    data = response.json()
    assert data["status"] == "ok"
    assert "service" in data


def test_predictions():
    response = client.post(
        "/api/predictions",
        json={"entreprise_id": 1, "periodes": 3},
        headers=HEADERS,
    )
    assert response.status_code == 200
    data = response.json()
    assert "predictions" in data
    assert len(data["predictions"]) == 3
    assert "algorithme" in data


def test_predictions_sans_cle_api():
    response = client.post("/api/predictions", json={"entreprise_id": 1})
    assert response.status_code == 401


def test_analyse():
    response = client.post(
        "/api/analyse",
        json={"entreprise_id": 1},
        headers=HEADERS,
    )
    assert response.status_code == 200
    data = response.json()
    assert "taux_conversion" in data
    assert "panier_moyen" in data


def test_recommendations():
    response = client.post(
        "/api/recommendations",
        json={"entreprise_id": 1, "user_id": 1, "limit": 3},
        headers=HEADERS,
    )
    assert response.status_code == 200
    data = response.json()
    assert "produits" in data
    assert len(data["produits"]) <= 3


def test_suggestion_devis():
    response = client.post(
        "/api/devis/suggestion",
        json={"entreprise_id": 1, "texte": "ordinateur portable bureau"},
        headers=HEADERS,
    )
    assert response.status_code == 200
    data = response.json()
    assert isinstance(data, list)
    if data:
        assert "nom" in data[0]
        assert "prix_unitaire" in data[0]
