"""
Prétraitement des données brutes pour les modèles ML.
"""
import pandas as pd
import numpy as np
from datetime import datetime


def preparer_serie_temporelle(df_commandes: pd.DataFrame) -> pd.DataFrame:
    """
    Agrège les commandes par mois pour l'entraînement ARIMA.
    Retourne un DataFrame avec colonnes: mois, ca_mensuel.
    """
    if df_commandes.empty:
        return pd.DataFrame(columns=["mois", "ca_mensuel"])

    df = df_commandes.copy()
    df["created_at"] = pd.to_datetime(df["created_at"])
    df["mois"] = df["created_at"].dt.to_period("M")

    serie = (
        df.groupby("mois")["total_ttc"]
        .sum()
        .reset_index()
        .rename(columns={"total_ttc": "ca_mensuel"})
    )
    return serie.sort_values("mois")


def preparer_matrice_interactions(df_commandes: pd.DataFrame) -> pd.DataFrame:
    """
    Construit la matrice user × produit pour SVD.
    Retourne un DataFrame pivoté avec produit_id en colonnes.
    """
    if df_commandes.empty or "produit_id" not in df_commandes.columns:
        return pd.DataFrame()

    df = df_commandes.dropna(subset=["produit_id"])
    if df.empty:
        return pd.DataFrame()

    matrice = df.pivot_table(
        index="commande_id",
        columns="produit_id",
        values="quantite",
        aggfunc="sum",
        fill_value=0,
    )
    return matrice


def calculer_kpis(df_devis: pd.DataFrame, df_factures: pd.DataFrame) -> dict:
    """
    Calcule les KPIs commerciaux principaux.
    """
    nb_devis = len(df_devis)
    nb_acceptes = len(df_devis[df_devis["statut"] == "accepte"]) if not df_devis.empty else 0
    taux_conversion = round(nb_acceptes / max(nb_devis, 1) * 100, 1)

    panier_moyen = 0.0
    if not df_factures.empty and "total_ttc" in df_factures.columns:
        factures_payees = df_factures[df_factures["statut"] == "payee"]
        panier_moyen = float(factures_payees["total_ttc"].mean()) if not factures_payees.empty else 0.0

    delai_moyen = 0
    if not df_factures.empty:
        df_f = df_factures.copy()
        df_f["date_echeance"] = pd.to_datetime(df_f["date_echeance"], errors="coerce")
        df_f["payee_at"] = pd.to_datetime(df_f["payee_at"], errors="coerce")
        df_f["delai"] = (df_f["payee_at"] - df_f["date_echeance"]).dt.days.abs()
        delai_moyen = int(df_f["delai"].mean()) if df_f["delai"].notna().any() else 0

    return {
        "taux_conversion": taux_conversion,
        "panier_moyen": round(panier_moyen, 0),
        "delai_paiement_moyen": delai_moyen,
        "nb_devis": nb_devis,
        "nb_commandes": nb_acceptes,
    }


def normaliser_texte(texte: str) -> str:
    """Normalisation simple pour TF-IDF."""
    import re
    texte = texte.lower().strip()
    texte = re.sub(r"[^a-zàâäéèêëïîôùûüç\s]", " ", texte)
    texte = re.sub(r"\s+", " ", texte)
    return texte
