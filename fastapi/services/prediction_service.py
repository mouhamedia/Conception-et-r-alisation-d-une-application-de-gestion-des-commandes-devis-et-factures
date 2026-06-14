import os
import numpy as np
from datetime import datetime


class PredictionService:
    """
    Prédiction de ventes avec Random Forest + ARIMA simulé.
    En production: charger les modèles .pkl depuis models/
    """

    def predire(self, entreprise_id: int, periodes: int = 3) -> dict:
        # Simulation — remplacer par: joblib.load('models/rf_model.pkl')
        mois_actuels = [self._mois_suivant(i) for i in range(periodes)]

        # Valeurs simulées basées sur une tendance aléatoire réaliste
        np.random.seed(entreprise_id + 42)
        base = np.random.uniform(500000, 2000000)
        tendance = np.random.uniform(0.05, 0.15)

        predictions = []
        for i, periode in enumerate(mois_actuels):
            valeur = base * (1 + tendance) ** (i + 1)
            valeur += np.random.uniform(-50000, 100000)
            predictions.append({
                "periode": periode,
                "valeur": round(max(valeur, 100000), 2),
                "confiance": np.random.randint(72, 94),
            })

        return {
            "entreprise_id": entreprise_id,
            "predictions": predictions,
            "algorithme": "Random Forest + ARIMA",
        }

    def _mois_suivant(self, offset: int) -> str:
        from dateutil.relativedelta import relativedelta
        date = datetime.now() + relativedelta(months=offset + 1)
        mois_fr = [
            "", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
            "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
        ]
        return f"{mois_fr[date.month]} {date.year}"
